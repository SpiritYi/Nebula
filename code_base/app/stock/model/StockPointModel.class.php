<?php
/**
 * 股票价格、指数model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/08
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockPointModel {
    private static $_TABLE = 'stock_point';

    /**
     * 查询当天指数
     */
    public static function selectDayPoint($sid, $date) {
        if (empty($sid)) {
            return array();
        }
        if (empty($date)) {
            $date = date('Ymd');
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('sid', 'date', 'opening_price', 'closing_price',
                'highest', 'lowest', 'time'), array(array('sid', '=', $sid), array('date', '=', $date)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}