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
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, array('count(1) as total'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //批量获取公司信息
    public static function getBatchInfo($sidArr) {
        if (empty($sidArr) || !is_array($sidArr)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, array('sid', 'sname', 'symbol', 'sspell'), array(array('sid', 'IN', $sidArr)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //根据sid 排序获取公司列表, 扫所有公司
    public static function getCompanyListOrderSid($startSid, $limit) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('sid', 'symbol'),
            array(array('sid', '>', $startSid)), array($limit), array('sid' => 'asc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //ajax 公司联想列表
    public static function getSuggestionList($field, $value) {
        if (!in_array($field, ['sid', 'sspell']) || empty($value)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::getTable(), array('sid', 'sname', 'sspell'), array(array($field, 'like', sprintf('%%%s%%', $value))),
                array(0, 10));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    public static function getTable() {
        return self::$_TABLE;
    }
}
