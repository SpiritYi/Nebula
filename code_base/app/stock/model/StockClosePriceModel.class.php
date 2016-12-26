<?php
/**
 * 公司收盘价历史记录model, 每年一个表
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/23
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockClosePriceModel {

    //批量插入多条记录
    public static function saveRecord($dataArr) {
        if (empty($dataArr)) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = "INSERT INTO " . self::_getTable() . '(sid, date, opening_price, price, highest, lowest, price_diff, price_diff_rate, time) VALUES ';
        $sqlItemArr = array();
        foreach ($dataArr as $dItem) {
            $strArr = array("'" . $dItem['sid'] . "'", $dItem['date'], $dItem['opening_price'], $dItem['price'], $dItem['highest'], $dItem['lowest'], $dItem['price_diff'], $dItem['price_diff_rate'], $dItem['time']);
            $sqlItemArr[] = '(' . implode(',', $strArr) . ')';
        }
        $sqlString .= implode(',', $sqlItemArr);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    public static function getRecordListOrderSid($date, $sid, $limit) {
        if (empty($date)) {
            $date = date('Ymd');
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::_getTable(), array('sid', 'date', 'price', 'price_diff', 'price_diff_rate'),
            array(array('date', '=', $date), array('sid', '>', $sid)), array($limit));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    private static function _getTable() {
        return sprintf('stock_close_price_%s', date('Y'));
    }
}