<?php
/**
 * 外汇资产，backend 操作数据库model
 * @author Yihong Chen <jinglingyueyue@gamil.com>
 * @version 2016/09/30
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class ForexAssetBKModel {
    private static $_TABLE = 'forex_asset';

    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    public static function getLatestRecord() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'asset', 'time'), array(), array(0, 1),
                array('time' => 'desc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}