<?php
/**
 * 用户资产接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/01/16
 * @copyright nebula-fund.com
 */

class PropertyRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/property/' => array(
                'GET' => 'getPropertyLine',         //获取总资产曲线
            ),
        );
    }

    public function getPropertyLineAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请刷新页面，重新登录');
        }
        $startTime = strtotime('-3 month');
        require_once CODE_BASE . '/app/stock/UserPropertyNamespace.class.php';
        require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
        $list = UserPropertyNamespace::getUserPropertyList($user['uid'], $startTime);
        $userData = array();
        foreach ($list as $item) {
            $userData[date('Y/m/d', $item['time'])] = floatval(StockCompanyNamespace::showPrice($item['money']));
        }
        $shQuery = StockCompanyNamespace::getCompanyClosingPriceList('699001', $startTime);
        $shData = array();
        foreach ($shQuery as $i => $shItem) {
            $shData[date('Y/m/d', $shItem['time'])] = floatval(StockCompanyNamespace::showPrice($shItem['closing_price']));
        }
        //合并出最完整的日期列表
        $dateList = array_unique(array_merge(array_keys($userData), array_keys($shData)));
        sort($dateList);

        $myList = array();
        $shList = array();
        foreach ($dateList as $dateItem) {
            $myList[] = isset($userData[$dateItem]) ? $userData[$dateItem] : null;
            $shList[] = isset($shData[$dateItem]) ? $shData[$dateItem] : null;
        }
        $resData = array(
            'date_list' => $dateList,
            'charts_list' => array(
                array(
                    'name' => '总资产',
                    'type' => 'line',
                    'yAxis' => 0,
                    'data' => $myList,
                    'marker' => ['enabled' => false],
                ),
                array(
                    'name' => '上证指数',
                    'type' => 'line',
                    'yAxis' => 1,
                    'data' => $shList,
                    'marker' => ['enabled' => false],
                )
            ),
        );
        $this->output(200, $resData);
    }
}