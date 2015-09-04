<?php
/**
 * 用户持股表操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/03
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class UserStockModel {
    private static $_TABLE = 'user_stock';

    /**
     * 添加一个持股
     * @param $data
     *          - uid
     *          - sid
     */
    public static function addUserStock($data) {
        if (empty($data['uid']) || empty($data['sid'])) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //获取用户单个持股信息
    public static function selectStockBySid($uid, $sid) {
        if (empty($uid) || empty($sid)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'count', 'cost', 'loss_limit'),
                array(array('uid', '=', $uid), array('sid', '=', $sid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //获取用户持股列表
    public static function selectStockList($uid) {
        if (empty($uid)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'sid', 'count', 'cost', 'loss_limit'),
                array(array('uid', '=', $uid)), array(), array('time' => 'ASC'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //更新持股数据
    public static function updateUserStock($uid, $sid, $data) {
        if (empty($uid) || empty($sid)) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('uid', '=', $uid),
                array('sid', '=', $sid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}
