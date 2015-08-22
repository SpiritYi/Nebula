<?php
/**
 * Stock 登录页面
 * @auhtor Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

require_once STOCK_WEB . '/app/StockEmpty.class.php';

class LoginPage extends StockEmpty {
    public function loadHead() {
        $this->staExport('<title>Nebula Stock</title>');
    }

    public function action() {
        $this->userInfo = $this->stockAccessVerify();
        if (!empty($this->userInfo)) {
            require_once CODE_BASE . '/util/http/HttpUtil.class.php';
            $loc = HttpUtil::getParam('loc');
            $loc = empty($loc) ? '/' : $loc;
            header('location:' . $loc);
        }
        $this->render('/account/login.php');
    }
}
