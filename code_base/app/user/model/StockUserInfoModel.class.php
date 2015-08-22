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

    public function addUser() {
        $handler = BaseStockModel::getDBHandle();
        $sql = SqlBuilderNamespace::buildInsert();
    }

    public function selectUserInfo($v, $byField) {
        if (!empty($filter) || !is_array($filter)) {
            return array();
        }
        $handler = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'username', 'email', ''), array(
            array($byField, '=', $v)));
        $res = DBMysqlNamespace::execute($handler, $sqlString);
        return $res;
    }
}