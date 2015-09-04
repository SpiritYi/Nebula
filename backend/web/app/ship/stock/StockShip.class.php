<?php
/**
 * 股票操作后台页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 * @copyright nebula-fund.com
 */

class StockShipPage extends BackendMaster {
    public function loadHead() {
        $this->staExport('<title>股票交易导航</title>');
        $this->staExport('css/stock/global.css');
    }

    public function action() {
        require_once BACKEND_WEB . '/model/user/StockUserBKModel.class.php';
        $this->stockUserList = StockUserBKModel::selectUserList();
        $this->render('/ship/stock/stock_ship.php');
    }
}
