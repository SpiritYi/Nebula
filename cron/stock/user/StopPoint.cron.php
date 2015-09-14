<?php
/**
 * 止损、止盈提示
 * @auhtor Yihong Chen 
 * @version 2015/09/14
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';

require_once CRON . '/stock/user/model/UserInfoCronModel.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
require_once CODE_BASE . '/app/stock/StockMsgNamespace.class.php';

class StopPoint extends CronBase {
    public function setCycleConfig() {
        return '30 9 * *';  //9点半开启
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_stop_point_run');

        $this->scan();

        Logger::logInfo(__CLASS__ . ', end', 'cron_stop_point_run');
    }

    public function scan() {
        while(true) {
            sleep(1);
            var_dump('while');
            if (!DBConfig::IS_DEV_ENV) {    //开发环境跳过
                if (date('s') % 5 != 0) {   //每5秒启动一次
                    continue;
                }
                if (date('s') == 0) {   //间隔每分钟刷新交易状态
                    $openFlag = StockCompanyNamespace::isExchangeHour();
                    if (!$openFlag) {   //非开市时间不交易
                        continue;
                    }
                }
                if (time() > strtotime('15:05')) {  //结束交易时间，退出脚本
                    return true;
                }
            }

            //获取需要处理的用户列表
            $userList = UserInfoCronModel::getUserList();
            if (empty($userList)) {
                return false;
            }
            foreach ($userList as $userItem) {
                //获取用户止损列表
                $stockList = UserStockNamespace::getUserStockList($userItem['uid']);
                if (empty($stockList)) {
                    continue;
                }
                //合并持股公司
                $sidArr = array();
                foreach ($stockList as $sItem) {
                    if ($sItem['loss_limit'] > 0) {
                        $sidArr[] = $sItem['sid'];
                    }
                }
                if (empty($sidArr)) {
                    continue;
                }
                //获取成交细节
                $detailList = StockCompanyNamespace::getCompanyDetail($sidArr);
                if (empty($detailList)) {
                    continue;
                }
                foreach ($stockList as $stockItem) {
                    if (!array_key_exists($stockItem['sid'], $detailList)) {
                        continue;
                    }
                    $sDetail = $detailList[$stockItem['sid']];
                    foreach ($sDetail['outright_list'] as $record) {
                        //5分钟内提醒过了不再提醒
                        if ($record['tstamp'] - $stockItem['limit_alert_time'] < 300) {
                            continue;
                        }
                        //判断是否触发止损提醒
                        if ($stockItem['loss_limit'] >= $record['price']) {
                            $msgFlag = StockMsgNamespace::sendMsg($userItem['uid'], '止损提醒', $stockItem['sname'] . ' ' . $record['price']. ' ' .
                                    StockCompanyNamespace::showPrice(($record['price'] - $sDetail['ysd_closing_price']) / $sDetail['ysd_closing_price'] * 100) .
                                    '%, 触发止损。');
                            if (!$msgFlag) {
                                Logger::logInfo('msg send failed.' . $userItem['uid'], 'cron_stop_point_msg');
                            }
                            //更新提醒时间
                            $tFlag = UserStockModel::updateUserStock($userItem['uid'], $stockItem['sid'], array('limit_alert_time' => time()));
                            break;
                        }
                    }
                }
            }
        }
    }
}

$instance = new StopPoint();