<?php
/**
 * 用户数据分析页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/01/16
 * @copyright nebula-fund.com
 */

class AnalysisPage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>数据分析</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/stock/model/ExchangeModel.class.php';
        $list = ExchangeModel::getRecentList($this->userInfo['uid'], strtotime(date('Y/m/01', strtotime('-6 month'))));
        $list = $this->_mergeExchangeRecord($list);

        $monthClassify = array();
        foreach ($list as $lItem) {
            $month = date('Ym', $lItem['time']);
            $monthClassify[$month][] = $lItem;
        }
        $statis = array();
        foreach ($monthClassify as $month => $monthList) {
            $statis[$month] = $this->_monthStatistics($monthList);
        }

        $this->render('/user/analysis.php');
    }

    //合并同一委托拆分执行的交易记录
    private function _mergeExchangeRecord($list) {
        $mergeList = array();
        foreach ($list as $item) {
            $delegateId = $item['del_id'] == 0 ? $item['id'] . $item['time'] : $item['del_id'];    //历史异常数据没法合并的直接当做单个统计
            if (!isset($mergeList[$delegateId])) {
                $mergeList[$delegateId] = $item;
            } else {
                //合并记录,主要处理 佣金,税,盈亏收益
                $mergeList[$delegateId]['count'] += $item['count'];
                $mergeList[$delegateId]['commission'] += $item['commission'];
                $mergeList[$delegateId]['tax'] += $item['tax'];
                $mergeList[$delegateId]['earn'] += $item['earn'];
            }
        }
        return array_values($mergeList);
    }

    //分开统计每个月交易情况
    private function _monthStatistics($list) {
        $statis = array(
            'exchange_count' => count($list),
            'buy_count' => 0,
            'sell_count' => 0,
            'tax_sum' => 0,
            'commission_sum' => 0,
            'earn_sum' => 0,
        );
        foreach ($list as $lItem) {
            if ($lItem['direction'] == ExchangeModel::DIRECTION_BUY) {
                $statis['buy_count'] ++;
            }
            if ($lItem['direction'] == ExchangeModel::DIRECTION_SELL) {
                $statis['sell_count'] ++;
            }
            $statis['tax_sum'] += $lItem['tax'];
            $statis['commission_sum'] += $lItem['commission'];
            $statis['earn_sum'] += $lItem['earn'];
        }
        return $statis;
    }
}