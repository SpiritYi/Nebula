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

    public function loadHead() {
        $$this->op = HttpUtil::getParam('op');
        $tileStr = $this->op == self::OP_SELL ? '委托卖出' : '委托买入';
        $this->staExport('<title>' . $tileStr . '</title>');
    }

    public function action() {
        if ($this->op == self::OP_SELL) {
            //获取用户持股列表
            require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
            $this->userStockList = UserStockNamespace::getUserStockList();
        }
        //获取用户委托列表
        require_once CODE_BASE . '/app/stock/model/DelegateListModel.class.php';
        $this->dlgList = DelegateListModel::getUserDlgList($user['uid'], $this->op == self::OP_BUY ? 1 : -1);
        $this->render('/exchange/delegate.php');
    }
}