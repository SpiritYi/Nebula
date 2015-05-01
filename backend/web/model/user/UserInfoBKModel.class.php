<?php
/**
 * backend 下user_info model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class UserInfoBKModel {
    private static $_TABLE = 'user_info';

    //获取所有用户的列表
    public static function getAllUser() {
        $handle = BaseMainModel::getDbHandle();
        $sqlString = SqlbuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'nickname'));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }
}
