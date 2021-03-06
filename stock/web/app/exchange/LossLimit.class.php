<?php
/**
 * 设置止损页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/29
 * @copyright nebula-fund.com
 */

class LossLimitPage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>设置止损提醒</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
        $this->userStockList = UserStockNamespace::getUserStockList($this->userInfo['uid']);
        $this->render('/exchange/loss_limit.php');
    }
}
