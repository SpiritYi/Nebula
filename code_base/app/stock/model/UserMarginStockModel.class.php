<?php
/**
 * 用户信用交易持股model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/08
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class UserMarginStockModel {
    private static $_TABLE = 'user_margin_stock';
    
    public static function addRecord($data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
    
    //交易完成删除持股记录
    public static function deleteHoldings($uid, $msrid) {
        if (empty($uid) || empty($msrid)) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildDeleteSql(self::$_TABLE, array(array('id', '=', $msrid), array('uid', '=', $uid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
    
    //根据id 获取用户持股
    public static function selectHoldingsById($uid, $msrid) {
        if (empty($uid) || empty($msrid)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'count', 'strike_price', 'cost', 'loss_limit'),
            array(array('id', '=', $msrid), array('uid', '=', $uid)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
    
    //获取用户所有有效持股
    public static function selectAllHoldings($uid) {
        if (empty($uid)) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'count', 'strike_price', 'cost'),
            array(array('uid', '=', $uid)), array(), array('id' => 'asc'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
        
    }
    
    //更新持股数据
    public static function updateUserHoldings($uid, $sid, $data) {
        if (empty($uid) || empty($sid)) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('uid', '=', $uid),
            array('sid', '=', $sid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}