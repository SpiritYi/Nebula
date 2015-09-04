<?php
/**
 * 股票用户后台使用model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class StockUserBKModel {
    private static $_TABLE = 'user_info';

    //获取用户列表
    public static function selectUserList() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'nickname', 'money'),
                array(), array());
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}
