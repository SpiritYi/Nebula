<?php
/**
 * 用户持股页面
 * @auhtor Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/03
 * @copyright nebula-fund.com
 */

class HoldingsPage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>我的股份</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
        $this->stockList = UserStockNamespace::getUserStockList($this->userInfo['uid']);
        $this->render('/stock/holdings.php');
    }
}
