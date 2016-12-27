<?php
/**
 * 股票统计数据集合航母
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/26
 * @copyright nebula-fund.com
 */

class StatisticsCVPage extends BackendMaster {
    public $statisticsBlock;

    public function loadHead() {
        $this->staExport('<title>股票全盘统计</title>');
    }

    public function action() {
        $this->statisticsBlock = array(
            [
                'category' => 'price_limit',
                'title' => '涨跌停统计',
            ],
            [
                'category' => 'price_huge',
                'title' => '涨跌幅统计',
            ],
//            [
//                'category' => 'price_big',
//                'title' => '涨跌3% ~ 5%统计',
//            ],
            [
                'category' => 'price_delist',
                'title' => '停牌家数',
            ],
            [
                'category' => 'price_high',
                'title' => '高价股数',
            ]
        );
        $this->render('/ship/stock/statistics_cv.php');
    }
}