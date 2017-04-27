<?php
/**
 * 行情统计后台接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/26
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/DayStatisticsNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/DayStatisticsModel.class.php';

class StatisticsBKRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/statisticsbk/' => array(      //获取统计列表
                'GET' => 'getLineList',
            ),
        );
    }

    public function getLineListAction() {
        if (!$this->adminSessionCheck()) {
            $this->output(403, '', '操作权限不足');
        }
        $cat = HttpUtilEx::getParam('category');
        $startDate = date('Ymd', strtotime('-3 month'));
        $endDate = date('Ymd');
        $lineConfig = array();
        switch ($cat) {
            case 'price_limit':         //涨跌停统计曲线
                $lineConfig = array(
                    [
                        'type' => DayStatisticsNamspace::PRICE_UP_A,
                        'name' => '涨停数',
                        'color' => '#F00',
                    ],
                    [
                        'type' => DayStatisticsNamspace::PRICE_DOWN_A,
                        'name' => '跌停数',
                        'color' => '#0F0',
                    ]

                );
                break;
            case 'price_huge':          //涨跌幅大
                $lineConfig = array(
                    [
                        'type' => DayStatisticsNamspace::PRICE_UP_B,
                        'name' => '涨5%~10%',
                        'color' => '#F00',
                    ],
                    [
                        'type' => DayStatisticsNamspace::PRICE_UP_C,
                        'name' => '涨3%~5%',
                        'color' => '#F0F',
                    ],
                    [
                        'type' => DayStatisticsNamspace::PRICE_DOWN_B,
                        'name' => '跌5%~10%',
                        'color' => '#0F0',
                    ],
                    [
                        'type' => DayStatisticsNamspace::PRICE_DOWN_C,
                        'name' => '跌3%~5%',
                        'color' => '#3CF',
                    ],
                );
                break;
            case 'price_delist':
                $lineConfig = array(
                    [
                        'type' => DayStatisticsNamspace::PRICE_DELIST,
                        'name' => '停牌数',
                        'color' => '#CBCBCB'
                    ]
                );
                break;
            case 'price_high':
                $lineConfig = array(
                    [
                        'type' => DayStatisticsNamspace::PRICE_HIGH,
                        'name' => '高价股数(> 10元)',
                        'color' => '#F00',
                    ]
                );
                break;
        }
        $resData = $this->_getChartsData($lineConfig, $startDate, $endDate);
        $this->output(200, $resData);
    }

    /**
     * 通用处理charts 展示数据,只需配置数据线type, 支持自动扩展
     * @param array $config             //展示哪些type 线的配置
     *          - type, name, color
     * @param $startDate
     * @param $endDate
     * @return array
     */
    private function _getChartsData($config, $startDate, $endDate) {
        if (empty($config)) {
            return array();
        }
        $lineArr = array();
        $dateArr = array();
        //循环处理多个type
        foreach ($config as $cItem) {
            $dataList = DayStatisticsModel::getList($cItem['type'], $startDate, $endDate);
            $typeLine = array();
            foreach ($dataList as $dataItem) {
                $itemDate = date('Y/m/d', strtotime($dataItem['date']));
                if (!in_array($itemDate, $dateArr)) {       //保留所有日期数据
                    $dateArr[$dataItem['date']] = $itemDate;
                }
                $typeLine[$itemDate] = intval($dataItem['count']);
            }
            $lineArr[$cItem['type']] = $typeLine;
        }
        ksort($dateArr);
        //格式化, 补充空日期数据
        foreach ($dateArr as $date) {
            foreach ($lineArr as $type => $line) {
                if (!isset($lineArr[$type][$date])) {
                    $lineArr[$type][$date] = 0;
                }
            }
        }
        //组装返回数据
        $chartsList = array();
        $colorList = array();
        foreach ($config as $cItem) {
            $colorList[] = $cItem['color'];
            ksort($lineArr[$cItem['type']]);                //重新按日期排序好
            $chartsList[] = array(
                'name' => $cItem['name'],
                'data' => array_values($lineArr[$cItem['type']]),
            );
        }
        $resData = array(
            'date_list' => array_values($dateArr),
            'color_list' => $colorList,
            'charts_list' => $chartsList,
        );
        return $resData;
    }
}