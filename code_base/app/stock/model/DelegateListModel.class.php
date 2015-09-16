<?php
/**
 * 委托队列操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/09
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class DelegateListModel {
    private static $_TABLE = 'delegate_list';

    public static $STATUS_VALUE = array(
        'available' => 0,       //正常可用状态
        'deal' => 1,            //已成交
        'expire' => -1,         //没有成交，自然过期
        'cancel' => -2,         //主动撤销
    );

    public static function addDelegate($data) {
        if (empty($data['uid']) || empty($data['sid'])) {
            return false;
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //查询用户的某个委托详情
    public static function getDelegateInfo($id) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'sid'), array(array('id', '=', $id)));
        $res = DBMysqlNamespace::query($handle, $query);
        return $res;
    }

    /**
     * 获取用户有效的委买、委卖列表
     * @param $uid int
     * @param $dirc int     //交易方向，1 委买，-1 委卖
     */
    public static function getUserDlgList($uid, $dirc) {
        if (empty($uid) || !in_array($dirc, [1, -1])) {
            return array();
        }
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'uid', 'sid', 'direction', 'price', 'count', 'time'),
                array(array('uid', '=', $uid), array('direction', '=', $dirc), array('status', '=', 0)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //更新委托记录
    public static function updateDelegate($id, $data) {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}