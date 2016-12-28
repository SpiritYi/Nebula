<?php
/**
 * Leo 供货商信息model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class LeoCompanyModel {
    private static $_TABLE = 'leo_company';

    public static function addRecord($data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    public static function getBatchInfo($cidArr) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('cid', 'name', 'website', 'phone', 'address'),
            array(array('cid', 'IN', $cidArr), array('status', '=', 0)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //获取公司列表
    public static function getList($offset, $limit) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('cid', 'name', 'website', 'phone', 'address'),
            array(array('status', '=', 0)), array($offset, $limit));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    public static function updateInfo($cid, $data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('cid', '=', $cid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}