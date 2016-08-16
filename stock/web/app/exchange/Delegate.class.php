<?php
/**
 * 委托页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/16
 * @copyright nebula-fund.com
 */

class DelegatePage extends StockMaster {

    const OP_BUY = 'buy';
    const OP_SELL = 'sell';

    public $userStockList = array();

    public function loadHead() {
        $this->op = HttpUtil::getParam('op');
        if (empty($this->op)) {
            $this->op = self::OP_BUY;
        }
        $tileStr = $this->op == self::OP_SELL ? '委托卖出' : '委托买入';
        $this->staExport('<title>' . $tileStr . '</title>');
    }

    public function action() {
        if ($this->op == self::OP_SELL) {
            //获取用户持股列表
            require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
            $this->userStockList = UserStockNamespace::getUserStockList($this->userInfo['uid']);
        }
        $this->render('/exchange/delegate.php');
    }
}
