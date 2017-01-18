<?php
/**
 * 处理交易脚本
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/09
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';
require_once CODE_BASE . '/util/http/HttpUtil.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/DelegateListModel.class.php';
require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';

class DealExchange extends CronBase {
    public function setCycleConfig() {
        return '* 9-15 * *';  //9点到15点每分钟开启
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_deal_exchange_run');

        $this->exchange();

        Logger::logInfo(__CLASS__ . ', end', 'cron_deal_exchange_run');
    }

    public function exchange() {
        $curMin = date('i');
        for ($s = 0; $s < 60; $s ++) {
            sleep(1);
            if (date('i') != $curMin) {     //每次启动只负责当前分钟
                return true;
            }
            if (!DBConfig::IS_DEV_ENV) {    //开发环境跳过
                if (date('s') % 5 != 0) {   //每5秒启动一次
                    continue;
                }
                $openFlag = StockCompanyNamespace::isExchangeHour();
                if (!$openFlag) {   //非开市时间不交易
                    continue;
                }
                if (time() > strtotime('15:05')) {  //结束交易时间，退出脚本
                    return true;
                }
            }
            $dlglist = DelegateListCronModel::selectAvailableList();
            if (empty($dlglist)) {
                if (DBConfig::IS_DEV_ENV) {     //开发环境没有委托列表退出
                    break;
                }
                continue;
            }
            //收集公司信息
            $sidArr = array();
            foreach ($dlglist as $item) {
                $sidArr[] = $item['sid'];
            }
            $detailList = StockCompanyNamespace::getCompanyDetail($sidArr);
            if (empty($detailList)) {
                continue;
            }

            //撮合交易, 跟据委托循环驱动
            foreach ($dlglist as $dlgItem) {
                if (!array_key_exists($dlgItem['sid'], $detailList)) {
                    continue;
                }
                //判断能否成交
                $availableV = array(
                    'count' => 0,
                    'match' => array(),     //配对的价格
                );    //可成交数据

                foreach ($detailList[$dlgItem['sid']]['outright_list'] as $record) {
                    //比对委托时间
                    if ($dlgItem['update_t'] >= $record['tstamp'] || date('Ymd', $record['tstamp']) != date('Ymd')) {
                        continue;
                    }
                    //买卖判断
                    if (($dlgItem['price'] == -1) ||    //现价委托
                        ($dlgItem['direction'] == ExchangeModel::DIRECTION_BUY && $record['price'] <= $dlgItem['price']) ||    //成交价格 <= 委托价格，均可成交
                        ($dlgItem['direction'] == ExchangeModel::DIRECTION_SELL && $record['price'] >= $dlgItem['price'])) {    
                            $availableV['count'] += $record['volume'] * 100;
                            $availableV['match'][$record['price']] = $record['volume'] * 100;       //记录各价格段可成交量
                    }
                }
//                $availableV = array(
//                    'count' => 1000,
//                    'match' => ['12.88' => 1000]
//                );
                if ($availableV['count'] == 0) { //无法成交
                    continue;
                }
                //排序最优价格
                $dlgItem['direction'] == ExchangeModel::DIRECTION_BUY ? ksort($availableV['match']) : krsort($availableV['match']);

                $unfreezeMoney = 0;             //该笔交易涉及到的解冻金额
                $dlgUpdate = array();
                if ($availableV['count'] >= $dlgItem['count']) {     //全部成交
                    $dlgUpdate = array(
                        'status' => -1,
                        'update_t' => time(),
                    );
                } else {        //部分成交
                    $dlgUpdate = array(
                        'count' => $dlgItem['count'] - $availableV['count'],
                        'update_t' => time(),
                    );
                }
                //买入涉及冻结金额处理
                if ($dlgItem['direction'] == ExchangeModel::DIRECTION_BUY) {
                    $delCount = $availableV['count'] > $dlgItem['count'] ? $dlgItem['count'] : $availableV['count'];
                    $unfreezeMoney = $delCount * $dlgItem['freeze_money'] / $dlgItem['count'];
                    $dlgUpdate['freeze_money'] = $dlgItem['freeze_money'] - $unfreezeMoney;
                }
                //更新委托数据
                $flag = DelegateListModel::updateDelegate($dlgItem['id'], $dlgUpdate);
                if (!$flag) {
                    Logger::logInfo(json_encode($dlgItem), 'cron_update_dlgt_error');   //失败日志
                }
                //计算成交花费
                $dlgCount = $dlgItem['count'];  //委托数量
                $allMoney = 0;  //涉及总金额
                foreach ($availableV['match'] as $price => $vol) {
                    $exchangeRecord = array(
                        'uid' => $dlgItem['uid'],
                        'sid' => $dlgItem['sid'],
                        'del_id' => $dlgItem['id'],
                        'delegate_price' => $dlgItem['price'],
                        'strike_price' => $price,
                        'direction' => $dlgItem['direction'],
                        'commission' => 0,      //佣金
                        'tax' => 0,             //税款
                        'earn' => 0,            //盈亏
                        'time' => time(),
                    );
                    $money = 0;     //当前收入、支出金额

                    $realTurnV = $dlgCount > $vol ? $vol : $dlgCount;       //实际成交量
                    $turnover = $realTurnV * $price;            //成交额
                    $exchangeRecord['count'] = $realTurnV;     //成交量
                    $exchangeRecord['commission'] += $turnover * 0.001;     //固定千分之一佣金
                    
                    $dlgCount -= $vol;
                    //委买
                    if ($dlgItem['direction'] == ExchangeModel::DIRECTION_BUY) {
                        $money = $turnover + $exchangeRecord['commission'];
                    } else if ($dlgItem['direction'] == ExchangeModel::DIRECTION_SELL) {   //委卖
                        //计算税金
                        $exchangeRecord['tax'] = $turnover * 0.001;         //固定千分之一印花税
                        $money = $turnover - $exchangeRecord['commission'] - $exchangeRecord['tax'];

                        //计算盈亏
                        $perCosting = $this->_getUserStockPerCosting($dlgItem['uid'], $dlgItem['sid']);
                        $exchangeRecord['earn'] = ($price - $perCosting) * $realTurnV - $exchangeRecord['commission'] - $exchangeRecord['tax'];        //盈亏
                    }
                    //逐笔记录交易记录
                    $addFlag = ExchangeCronModel::addRecord($exchangeRecord);
                    if (!$addFlag) {
                        Logger::logInfo('添加交易记录 失败。' . json_encode($exchangeRecord), 'cron_deal_exchange_exchange_error');
                    }
                    $allMoney += $money;

                    if ($dlgCount <= 0) {   //已经成交完成
                        $dlgCount = 0;
                        break;
                    }
                }
                //更新资产
                $userProperty = StockUserNamespace::getUserInfoById($dlgItem['uid']);
                $updateProperty = array(
                    'uid' => $dlgItem['uid'],
                    'money' => $allMoney * $dlgItem['direction'] * -1 + $userProperty['money'],
                    'usable_money' => $allMoney * $dlgItem['direction'] * -1 + $userProperty['usable_money'] + $unfreezeMoney,      //归还解冻金额
                );
                $updateFlag = StockUserNamespace::setUserInfo($dlgItem['uid'], $updateProperty);
                if (!$updateFlag) {
                    Logger::logInfo('更新用户资产失败。' . json_encode($updateProperty), 'cron_deal_exchange_property_error');
                }
                //更新持股记录
                $opFlag = UserStockNamespace::setUserHolding($dlgItem['uid'], $dlgItem['sid'], ($dlgItem['count'] - $dlgCount) * $dlgItem['direction'], 
                        $allMoney * $dlgItem['direction']);
                if (!$opFlag) {
                    Logger::logInfo('更新持股记录失败。' . json_encode($dlgItem), 'cron_deal_exchange_holding_error');
                }
            }
        }
        return true;
    }

    //获取用户持股单位成本
    private function _getUserStockPerCosting($uid, $sid) {
        require_once CODE_BASE . '/app/stock/model/UserStockModel.class.php';
        $stock = UserStockModel::selectStockBySid($uid, $sid);
        if (empty($stock)) {
            return 0;
        }
        return $stock[0]['cost'] / $stock[0]['count'];
    }
}

$instance = new DealExchange();

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';
class DelegateListCronModel {
    private static $_TABLE = 'delegate_list';

    //获取所有有效委托
    public static function selectAvailableList() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'count', 'price', 'freeze_money', 'direction', 'update_t'),
                array(array('status', '=', 0)), array(), array('id' => 'ASC'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}

class ExchangeCronModel {
    private static $_TABLE = 'exchange';

    //添加交易记录
    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
