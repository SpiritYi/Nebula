<?php
/**
 * 用户交易记录页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/06/10
 * @copyright nebula_fund.com
 */

class ExchangePage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>交易记录</title>');
    }

    public function action() {
        $this->render('/stock/exchange.php');
    }
}
