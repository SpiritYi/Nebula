<?php
/**
 * Stock 主模板页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

abstract class StockMaster extends PageBase {

    public function __construct() {
        //页面请求均需验证
        /*
        $this->userInfo = $this->accessVerify();
        if (empty($this->userInfo)) {
            $originUri = $_SERVER['REQUEST_URI'];
            if (empty($originUri)) {
                $originUri = '/';
            }
            header('location:/passport?loc=' . urlencode($originUri));     //跳转到认证页面, 附带原先连接
        }
        */
        PageBase::render('/stock_master.php');
    }

    abstract public function loadHead();

    abstract public function action();
}
