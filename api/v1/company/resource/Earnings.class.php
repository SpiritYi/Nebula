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
                'GET' => 'getEarningsList',     //获取收益列表
            ),
            '/earnings/line/' => array(
                'GET' => 'getEarningsLine',     //获取回报累计收益曲线图数据
            ),
        );
    }

    public function getEarningsListAction() {
        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $myList = EarningsRateModel::getEarningsReteList(EarningsRateModel::MY_EARN_TYPE);
        $myData = array();
        $dateList = array();
        foreach ($myList as $key => $item) {
            $dateList[] = date('Y/m', $item['date_m']);
            $myData[] = (float)$item['rate'];
        }
        $szList = EarningsRateModel::getEarningsReteList(EarningsRateModel::SH_EARN_TYPE);
        $szData = array();
        foreach ($szList as $item) {
            $szData[] = (float)$item['rate'];
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
        $resData = ResourceBase::formatReturn(200, $resData, 'OK');
        ResourceBase::display($resData);
    }

    public function getEarningsLineAction() {
        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $myRateList = EarningsRateModel::getEarningsReteList(EarningsRateModel::MY_EARN_TYPE);
        // $myRateList = EarningsRateModel::getEarningsReteList(EarningsRateModel::SH_EARN_TYPE);
        $total = 10000;     //模拟起始投资10000 收益累计
        $line = array($total);
        $dateList = array();
        foreach ($myRateList as $item) {
            $dateList[] = date('Y/m', $item['date_m']);
            $total = $total + $total * (float)$item['rate'] * 0.01;
            $line[] = (float)sprintf('%d', $total);
        }
        array_unshift($dateList, $dateList[0]);
        $resData = array(
            'date_list' => $dateList,
            'charts_list' => array(
                array(
                    'name' => '投资1W曲线',
                    'data' => $line,
                ),
            ),
        );
        $resData = ResourceBase::formatReturn(200, $resData, 'OK');
        ResourceBase::display($resData);
    }
}
