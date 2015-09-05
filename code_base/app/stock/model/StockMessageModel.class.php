<?php
/**
 * stock message model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/02
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockMessageModel {
    private static $_TABLE = 'message';

    public static function addMessage($data) {
        if (empty($data['uid'])) {
            return false;
        }
        $data['send_time'] = time();
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //获取未读消息列表
    public static function getUnreadList($uid) {
        if (empty($uid)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'title', 'content', 'send_time'), 
                array(array('uid', '=', $uid), array('status', '=', 0)), array(10), array('id' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //标记一个消息为已读
    public static function readMsg($uid, $id) {
        if (empty($uid) || empty($id)) {
            return false;
        }
        $data = array(
            'status' => -1,
            'update_time' => time(),
        );
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('uid', '=', $uid), array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}