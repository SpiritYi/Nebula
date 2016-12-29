<?php
/**
 * leo 项目物品操作model
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/29
 * @copyright nebula-fund.com
 */

class LeoCargoModel {
    private static $_TABLE = 'leo_cargo';

    public static function addRecord($data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildInsertSql(self::$_TABLE, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

    //分页取所有物品数据
    public static function getList($offset, $limit) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'name', 'content', 'price', 'company_id', 'update_t', 'desc_website'),
            array(array('status', '=', 0)), array($offset, $limit));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //根据id 批量获取物品信息
    public static function getBatchInfo($idArr) {
        if (empty($idArr) || !is_array($idArr)) {
            return array();
        }
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildSelectSql(self::$_TABLE, array('id', 'name', 'content', 'price', 'company_id', 'update_t', 'desc_website'),
            array(array('id', 'IN', $idArr), array('status', '=', 0)));
        $res = DBMysqlNamespace::query($handle, $sqlString);
        return $res;
    }

    //更新数据
    public static function updateRecord($id, $data) {
        $handle = BaseMainModel::getDBHandle();
        $sqlString = SqlBuilderNamespace::buildUpdateSql(self::$_TABLE, $data, array(array('id', '=', $id)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
}