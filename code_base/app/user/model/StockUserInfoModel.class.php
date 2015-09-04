<?php
/**
 * stock user info model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/21
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockUserInfoModel {

    private static $_TABLE = 'user_info';

    public static function addUser() {
        $handler = BaseStockModel::getDBHandle();
        $sql = SqlBuilderNamespace::buildInsert();
    }

    /**
     * 获取用户信息
     * @param $v string     //查询value
     * @param $byField string //指定查询字段
     */
    public static function selectUserInfo($v, $byField = 'username') {
        if (empty($v) || empty($byField)) {
            return array();
        }
        $handler = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'username', 'nickname', 'email',
                'qq', 'money'), array(array($byField, '=', $v)));
        $res = DBMysqlNamespace::query($handler, $sqlString);
        return $res;
    }

    public static function selectUserPwd($username) {
        if (empty($username)) {
            return array();
        }
        $handler = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'username', 'password', 'session_expire'), array(
            array('username', '=', $username)
        ));
        $res = DBMysqlNamespace::query($handler, $sqlString);
        return $res;
    }

    public static function updateUserInfo($userid, $data) {
        $handle = BaseStockModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('uid', '=', $userid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
