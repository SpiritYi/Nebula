<?php
/**
 * 春节贺卡内容model
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2015/01/21
 * @copyright @ganji.com
 */

require_once CODE_BASE2 . '/app/mobile_client/model/baseConsoleModel.class.php';

class GreetingCardsModel {
    private static $_TABLE_NAME = 'greeting_cards';

    //创建贺卡
    public static function createGreetingCard($data) {
        $handle = baseConsoleModel::getDbHandler(false);
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE_NAME, $data);
        $res = DBMysqlNamespace::insertAndGetID($handle, $sqlString);
        return $res;
    }

    public static function getGreetingCard($cardId) {
        $cardId = (int)$cardId;
        if (empty($cardId))
            return false;
        $handle = baseConsoleModel::getDbHandler(true);
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE_NAME, array('id', 'content', 'create_time'),
                array(array('id', '=', $cardId)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        if (empty($res)) {  //查主库
            $handle = baseConsoleModel::getDbHandler(false);
            $res = DBMysqlNamespace::query($handle, $sqlString);
        }
        return $res;
    }
}
