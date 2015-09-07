<?php
/**
 * 用户消息页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/02
 * @copyright nebula-fund.com
 */

require_once STOCK_WEB . '/app/StockEmpty.class.php';

class MessagePage extends StockEmpty {
    public function loadHead() {
        $this->staExport('<title>Tinker</title>');
    }

    public function action() {
        //页面请求需验证
        $this->userInfo = $this->stockAccessVerify();
        if (empty($this->userInfo)) {
            $originUri = $_SERVER['REQUEST_URI'];
            if (empty($originUri)) {
                $originUri = '/';
            }
            header('location:/account/login?loc=' . urlencode($originUri));     //跳转到认证页面, 附带原先连接
        }
        $this->render('/user/message.php');
    }
}