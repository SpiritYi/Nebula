<?php
/**
 * backend 使用， 用户资产记录model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class PropertyRecordBKModel {
    private static $_TABLE = 'property_record';

    public static function addRecord($data) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
