<?php
/**
 * 委托队列操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/09
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class DelegateListModel {
    private static $_TABLE = 'delegate_list';

    public static function addDelegate($data) {
        if (empty($data['uid']) || empty($data['sid'])) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //更新委托记录
    public static function updateDelegate($id, $data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}