<?php
/**
 * stock message 操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/02
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/StockMessageModel.class.php';

class StockMsgNamespace {
    //发送一条消息
    public static function sendMsg($to, $title, $content) {
        $msg = array(
            'uid' => $to,
            'title' => $title,
            'content' => $content,
            'status' => 0,
        );
        return StockMessageModel::addMessage($msg);
    }
}