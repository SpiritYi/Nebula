<?php
/**
 * 每天执行一次整理数据
 *      - 清理委托处理
 *      - 用户资产快照
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/10
 * @copyright nebule-fund.com
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';

require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CRON . '/stock/user/model/UserInfoCronModel.class.php'

class DaySettle extends CronBase {
    public function setCycleConfig() {
        return '5 15 * *';  //每天下午15：05 收市后执行一次
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_day_settle_run');

        $this->clearupDelegate();
        $this->snapshotUserProperty();

        Logger::logInfo(__CLASS__ . ', end', 'cron_day_settle_run');
    }

    //清理委托列表
    public function clearupDelegate() {
        $exchangeFlag = StockCompanyNamespace::isExchangeDay();
        if (!$exchangeFlag) {
            return false;
        }
        //所有委托过期处理
        $expFlag = DelegateListCronModel::expireList();
        if (!$expFlag) {
            Logger::logInfo('expire delegate failed.', 'cron_day_settle_expire_error');
        } else {
            $resetFlag = UserInfoCronModel::resetUsableMoney();
            if (!$resetFlag) {
                Logger::logInfo('reset usable money failed', 'cron_day_settle_reset_error');
            }
        }
        //所有可用股票重置
        $resetFlag = UserStockCronModel::resetAvailableCount();
        if (!$resetFlag) {
            Logger::logInfo('reset available count failed', 'cron_day_settle_reset_error');
        }
    }

    //每日统计用户资产
    public function snapshotUserProperty() {
        $exchangeFlag = StockCompanyNamespace::isExchangeDay();
        if (!$exchangeFlag) {
            return false;
        }
        
        $userList = UserInfoCronModel::getUserList();
        if (empty($userList)) {
            return false;
        }
        require_once CODE_BASE . '/app/stock/model/UserStockModel.class.php';
        foreach ($userList as $userItem) {
            //获取用户持股列表
            $stockList = UserStockModel::selectStockList($userItem['uid']);
            if (empty($stockList)) {
                continue;
            }
            //处理sid
            $sidArr = array();
            foreach ($stockList as $item) {
                $sidArr[] = $item['sid'];
            }
            //批量获取报价
            $marketList = StockCompanyNamespace::getCompanyMarketInfo($sidArr);
            if (empty($marketList)) {
                Logger::logInfo('get batch company market info failed.', 'corn_day_settle_error');
                continue;
            }
            //计算总市值
            $allValue = 0;
            foreach ($stockList as $item) {
                if (!isset($marketList[$item['sid']])) {
                    Logger::logInfo('get market info failed', 'cron_day_settle_error');
                    continue;
                }
                $mInfo = $marketList[$item['sid']];
                $allValue += $item['count'] * $mInfo['price'];
            }
            //计算总资产
            $allProperty = $userItem['money'] + $allValue;
            //保存snapshot
            $snap = array(
                'uid' => $userItem['uid'],
                'money' => $allProperty,
                'time' => strtotime('15:00:00'),
            );
            $snapFlag = MoneySnapshotCronModel::addSnapshot($snap);
            if (!$snapFlag) {
                Logger::logInfo('save snapshot failed.', 'cron_day_settle_error');
            }
        }
    }
}

$instance = new DaySettle();

class DelegateListCronModel {
    private static $_TABLE = 'delegate_list';

    //让所有有效队列过期
    public static function expireList() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, array('status' => -1), array(array('status', '=', 0)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}

class UserStockCronModel {
    private static $_TABLE = 'user_stock';

    //收市后，恢复可用股数
    public static function resetAvailableCount() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = "UPDATE " . self::$_TABLE . " SET available_count = count WHERE available_count != count";
        $flag = DBMysqlNamespace::execute($handle, $sqlString);
        return $flag;
    }
}

class MoneySnapshotCronModel {
    private static $_TABLE = 'money_snapshot';

    //保存资产快照
    public static function addSnapshot($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}

