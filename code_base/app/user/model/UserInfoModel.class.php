<?php
/**
 * 用户信息操作model
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/27
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class UserInfoModel {
    private static $_TABLE = 'user_info';

    public static function selectUserInfoByName($username) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'username', 'nickname', 'session_expire', 'active_time'),
                array(array('username', '=', $username)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    public static function updateUserInfo($userid, $data) {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('id', '=', $userid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}
