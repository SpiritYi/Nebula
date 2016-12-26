<?php
/**
 * 每日统计数据模板
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class DayStatisticsModel {
    private static $_TABLE = 'day_statistics';

    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}