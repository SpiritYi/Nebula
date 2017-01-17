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
        $this->render('/user/analysis.php');
    }
}