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
        );
    }

    public function getEarningsListAction() {
        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $myList = EarningsRateModel::getEarningsReteList(2);
        $myData = array();
        $dateList = array();
        foreach ($myList as $key => $item) {
            $dateList[] = date('Y/m', $item['date_m']);
            $myData[] = (float)$item['rate'];
        }
        $szList = EarningsRateModel::getEarningsReteList(1);
        $szData = array();
        foreach ($szList as $item) {
            $szData[] = (float)$item['rate'];
        }
        $resData = array(
            'date_list' => $dateList,
            'charts_list' => array(
                array(
                    'name' => '星云',
                    'data' => $myData,
                ),
                array(
                    'name' => '上证指数',
                    'data' => $szData,
                ),
            ),
        );
        $resData = ResourceBase::formatReturn(200, $resData, 'OK');
        ResourceBase::display($resData);
    }
}
