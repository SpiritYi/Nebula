<?php
/**
 * 融资融券页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/09
 * @copyright nebula-fund.com
 */

class MarginPage extends StockMaster {
    
    public $tableList;
    
    public function loadHead() {
        $this->staExport('<title>融资融券</title>');
    }
    
    public function action() {
        require_once CODE_BASE . '/app/stock/UserMarginStockNamespace.class.php';
        $this->tableList = UserMarginStockNamespace::getMarginHoldings($this->userInfo['uid']);
        $this->render('/stock/margin.php');
    }
}