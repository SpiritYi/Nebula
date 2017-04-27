<?php
/**
 * 收益接口
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

class EarningsRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/earnings/' => array(
                'GET' => 'getEarningsList',     //获取收益列表柱状图数据
            ),
            '/earnings/line/' => array(
                'GET' => 'getEarningsLine',     //获取回报累计收益曲线图数据
            ),
        );
    }

    public function getEarningsListAction() {
        //按年查询
        $year = HttpUtil::getParam('y');
        if (empty($year) || ($year < 2014 || 2100 < $year )) {
            $year = date('Y');
        }
        $start = strtotime($year . '/1/1 0:0:0');
        $end = strtotime(($year + 1) . '/1/1 0:0:0');

        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $myList = EarningsRateModel::getEarningsReteList(EarningsRateModel::MY_EARN_TYPE, $start, $end);
        $myData = array();
        $dateList = array();
        foreach ($myList as $key => $item) {
            $dateList[] = date('Y/m', $item['date_m']);
            $myData[] = (float)$item['rate'];
        }
        //获取上证指数数据
        $szList = EarningsRateModel::getEarningsReteList(EarningsRateModel::SH_EARN_TYPE, $start, $end);
        $szData = array();
        foreach ($szList as $item) {
            $szData[] = (float)$item['rate'];
        }
        //获取创业板指数
        $cyList = EarningsRateModel::getEarningsReteList(EarningsRateModel::CY_EARN_TYPE, $start, $end);
        $cyData = array();
        foreach ($cyList as $item) {
            $cyData[] = (float)$item['rate'];
        }
        $resData = array(
            'date_list' => $dateList,
            'charts_list' => array(
                array(
                    'name' => EarningsRateModel::$TYPE_NAME[EarningsRateModel::MY_EARN_TYPE],
                    'data' => $myData,
                ),
                array(
                    'name' => EarningsRateModel::$TYPE_NAME[EarningsRateModel::SH_EARN_TYPE],
                    'data' => $szData,
                ),
            ),
        );
        if (!empty($cyData)) {
            $resData['charts_list'][] = array(
                'name' => EarningsRateModel::$TYPE_NAME[EarningsRateModel::CY_EARN_TYPE],
                'data' => $cyData,
            );
        }
        $resData = ResourceBase::formatReturn(200, $resData, 'OK');
        ResourceBase::display($resData);
    }

    public function getEarningsLineAction() {
        $year = HttpUtil::getParam('y');
        if (empty($year) || ($year < 2014 || 2100 < $year )) {
            $year = date('Y');
        }
        $start = strtotime($year . '/1/1 0:0:0');
        $end = strtotime(($year + 1) . '/1/1 0:0:0');

        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $myRateList = EarningsRateModel::getEarningsReteList(EarningsRateModel::MY_EARN_TYPE, $start, $end);
        $shRateList = EarningsRateModel::getEarningsReteList(EarningsRateModel::SH_EARN_TYPE, $start, $end);
        $cyRateList = EarningsRateModel::getEarningsReteList(EarningsRateModel::CY_EARN_TYPE, $start, $end);
        $total = 10000;     //模拟起始投资10000 收益累计
        $line = array($total);
        $dateList = array();
        foreach ($myRateList as $item) {
            $dateList[] = date('Y/m', $item['date_m']);
            $total = $total + $total * floatval($item['rate']) * 0.01;
            $line[] = (float)sprintf('%d', $total);
        }
        array_unshift($dateList, $dateList[0]);

        $total = 10000;
        $shLineList = array($total);
        foreach ($shRateList as $shItem) {
            $total = $total + $total * floatval($shItem['rate']) * 0.01;
            $shLineList[] = floatval(sprintf('%d', $total));
        }
    
        $total = 10000;
        $cyLineList = array($total);
        foreach ($cyRateList as $shItem) {
            $total = $total + $total * floatval($shItem['rate']) * 0.01;
            $cyLineList[] = floatval(sprintf('%d', $total));
        }

        $resData = array(
            'date_list' => $dateList,
            'charts_list' => array(
                array(
                    'name' => '投资1W收益',
                    'data' => $line,
                ),
                array(
                    'name' => '上证指数收益',
                    'data' => $shLineList,
                ),
            ),
        );
        if (count($cyLineList) > 1) {
            $resData['charts_list'][] = array(
                'name' =>'创业板指收益',
                'data' => $cyLineList,
            );
        }
        $resData = ResourceBase::formatReturn(200, $resData, 'OK');
        ResourceBase::display($resData);
    }
}
