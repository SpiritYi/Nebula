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

    const TYPE_NOTICE = 0;      //公告
    const TYPE_ARTICLE = 1;     //普通文章

    public static $TYPE_NAME = array(
        self::TYPE_NOTICE => '公告',
        self::TYPE_ARTICLE => '普通文章',
    );

    /**
     * 获取公告列表
     * @param $status int | string      //指定状态，’all' 获取所有状态
     */
    public static function getNoticeList($status = 0) {
        $filter = array(
            array('type', '=', self::TYPE_NOTICE)
        );
        if ($status !== 'all') {
            $filter[] = array('status', '=', $status);
        }
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('*'), $filter, array(), array('p_time' => 'DESC'));
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
