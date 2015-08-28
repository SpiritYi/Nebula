<?php
/**
 * 公司信息操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockCompanyModel {
    private static $_TABLE = 'stock_company';

    //获取已经记录的公司总数
    public static function getCompanyCount() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, 'count(1) as total');
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //批量获取公司信息
    public static function getBatchInfo($sidArr) {
        if (empty($sidArr) || !is_array($sidArr)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, 'sid, sname, symbol, sspell', array(array('sid', 'IN', $sidArr)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    public static function getTable() {
        return self::$_TABLE;
    }
}