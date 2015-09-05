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
        $this->render('/user/message.php');
    }
}