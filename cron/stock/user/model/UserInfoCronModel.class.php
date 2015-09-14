<?php
/**
 * 脚本使用用户资料model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/14
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseStockModel.class.php';

class UserInfoCronModel {
    private static $_TABLE = 'user_info';

    //获取用户列表
    public static function getUserList() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('uid', 'money'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //随着所有委托被清理，所有可用资金重置
    public static function resetUsableMoney() {
        $handle = BaseStockModel::getDBHandle();
        $sqlString = "UPDATE " . self::$_TABLE . " SET usable_money = money WHERE usable_money != money";
        $flag = DBMysqlNamespace::execute($handle, $sqlString);
        return $flag;
    }
}