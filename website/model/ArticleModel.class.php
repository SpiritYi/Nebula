<?php
/**
 * 公告类基类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/21
 * @copyright nebula.com
 */

require_once CODE_BASE . '/app/db/BaseMainModel.class.php';

class ArticleModel {
    private static $_TABLE = 'notice';
    public static $NOTICE_TYPE = 0;

    public static function getNoticeList() {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('*'),
                array(array('status', '=', 0), array('type', '=', self::$NOTICE_TYPE)), array(), array('p_time' => 'DESC'));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        return $result;
    }

    public static function getArticleInfo($id) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'title', 'brief', 'template', 'p_time'),
                array(array('id', '=', $id)));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        return $result;
    }

    public static function getTable() {
        return self::$_TABLE;
    }
}
