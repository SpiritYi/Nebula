<?php
/**
 * 保存所有股票的收盘价格
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/23
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/StockClosePriceModel.class.php';
require_once CODE_BASE . '/app/stock/DayStatisticsNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/DayStatisticsModel.class.php';

class CollectAllClosePoint extends CronBase {
    public function setCycleConfig() {
        return '5 15 * *';  //15点5分开启
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_close_price_run');

        $this->loopCollect();
        $this->dayStatistice();

        Logger::logInfo(__CLASS__ . ', end', 'cron_close_price_run');
    }

    public function loopCollect() {
        $openFlag = StockCompanyNamespace::isExchangeDay();
        if (!$openFlag) {   //当天不交易不处理
            return true;
        }
        $cmpCount = StockCompanyNamespace::getCompanyCount();
        if (empty($cmpCount)) {
            $cmpCount = 4000;
        }
        $loopFlag = true;
        $startSid = '';
        $limit = 10;
        $loopCount = 0;     //循环总数计数, 防止死循环
        while ($loopFlag) {
            $loopCount += $limit;
            if ($loopCount > $cmpCount + 10 * $limit) {
                break;
            }
            $cmpList = StockCompanyModel::getCompanyListOrderSid($startSid, $limit);
            if (empty($cmpList)) {
                continue;
            }
            $sidArr = array();
            foreach ($cmpList as $cmpItem) {
                $sidArr[] = $cmpItem['sid'];
                $startSid = $cmpItem['sid'];
            }
            if (in_array('699001', $sidArr)) {          //读到最后上证指数最大,结束循环
                $loopFlag = false;
            }
            $priceList = StockCompanyNamespace::getCompanyMarketInfo($sidArr);
            $saveData = array();
            foreach ($priceList as $priceItem) {
                $saveData[] = array(
                    'sid' => $priceItem['sid'],
                    'date' => date('Ymd'),
                    'opening_price' => $priceItem['opening_price'],
                    'price' => $priceItem['price'],
                    'highest' => $priceItem['highest'],
                    'lowest' => $priceItem['lowest'],
                    'price_diff' => $priceItem['price_diff'],
                    'price_diff_rate' => $priceItem['price_diff_rate'],
                    'time' => strtotime(date('Y/m/d 15:00:00')),
                );
            }
            $res = StockClosePriceModel::saveRecord($saveData);

            sleep(1);
        }
        return true;
    }

    public function dayStatistice() {
        $dealDate = date('Ymd');
//        $dealDate = '20170426';
        if ($dealDate == date('Ymd')) {
            $openFlag = StockCompanyNamespace::isExchangeDay();
            if (!$openFlag) {   //当天不交易不处理
                return true;
            }
        }

        $cmpCount = StockCompanyNamespace::getCompanyCount();
        if (empty($cmpCount)) {
            $cmpCount = 3000;
        }
        $loopFlag = true;
        $startSid = '';
        $limit = 100;
        $loopCount = 0;             //循环总数计数, 防止死循环
        $emptyLoopCount = 0;        //数据库请求返回空循环计数
        $statisticsArr = array();
        while ($loopFlag) {
            $loopCount += $limit;
            if ($loopCount > $cmpCount + 5 * $limit) {
                break;
            }
            $priceList = StockClosePriceModel::getRecordListOrderSid($dealDate, $startSid, $limit);
            if (empty($priceList)) {
                $emptyLoopCount ++;
                if ($emptyLoopCount > 5) {
                    break;
                }
                continue;
            }
            $emptyLoopCount = 0;

            foreach ($priceList as $priceItem) {            //分析每个公司归属统计类型
                if ($priceItem['sid'] == '699001') {        //读到最大上证指数,结束
                    $loopFlag = false;
                }
                $startSid = $priceItem['sid'];
                $mapType = DayStatisticsNamspace::mapType($priceItem);
                if (empty($mapType)) {
                    continue;
                }
                foreach ($mapType as $type) {               //统计该公司的走势类型
                    if (!isset($statisticsArr[$type])) {
                        $statisticsArr[$type] = 1;
                    } else {
                        $statisticsArr[$type] ++;
                    }
                }
            }
        }

        //统计完成,存储数据
        if (!empty($statisticsArr)) {
            foreach ($statisticsArr as $type => $count) {
                $sData = array(
                    'date' => $dealDate,
                    'type' => $type,
                    'count' => $count,
                );
                $res = DayStatisticsModel::addRecord($sData);
                if (!$res) {
                    Logger::logError(json_encode($sData), 'cron_close_price_statistics');
                }
            }
        }
        return true;
    }
}

$instance = new CollectAllClosePoint();