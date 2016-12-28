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

    public function addRecord($data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    public function updateInfo($cid, $data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('id', '=', $cid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}