<?php
/**
 * 用户积分操作model
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/11/25
 * @copyright ganji.com
 */

require_once CODE_BASE2 . '/app/mobile_client/model/credit/baseCreditModel.class.php';

class UserCreditModel {
    private static $_TABLE_NAME = 'user_credit';

    //添加用户积分记录
    public static function addUserRecord($data) {
        if ($data['user_id'] <= 0) {
            return false;
        }
        $handle = baseCreditModel::getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE_NAME, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //获取用户积分数据
    public static function getUserCredit($loginId) {
        $handle = baseCreditModel::getDbHandler(true);
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE_NAME, '*', 
                array(array('user_id', '=', $loginId)));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        if (empty($result)) {   //从库没有查主库
            $handle = baseCreditModel::getDbHandler(false);
            $result = DBMysqlNamespace::query($handle, $sqlString);
        }
        return $result;
    }

    //更新用户积分
    public static function updateUserCredit($data) {
        $baseModel = new baseCreditModel();
        $handle = $baseModel->getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE_NAME, $data, array(array('user_id', '=', $data['user_id'])));
        return DBMysqlNamespace::execute($handle, $sqlString);
    }
}