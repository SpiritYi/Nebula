<?php
/**
 * leo 项目物品相关接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/29
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/leo/model/LeoCargoModel.class.php';

class LeoCargoNamespace {

    public static function addRecord($data) {
        if (empty($data)) {
            return true;
        }
        $data['update_t'] = time();
        $res = LeoCargoModel::addRecord($data);
        return $res;
    }

    //分页获取所有数据
    public static function getList($offset, $limit) {
        $queryList = LeoCargoModel::getList($offset, $limit);
        $formatRes = self::_formatDBQueryList($queryList);
        return array_values($formatRes);
    }

    //根据id 批量查询信息
    public static function getBatchInfo($idArr) {
        $queryList = LeoCargoModel::getBatchInfo($idArr);
        $formatRes = self::_formatDBQueryList($queryList);
        return $formatRes;
    }

    //统一格式化数据库查询出来的数据
    private static function _formatDBQueryList($queryList) {
        if (empty($queryList)) {
            return array();
        }
        //批量格式化公司
        $cmpIdArr = array();
        foreach ($queryList as $qItem) {
            $cmpIdArr[] = $qItem['company_id'];
        }
        require_once CODE_BASE . '/app/leo/company/LeoCompanyNamespace.class.php';
        $cmpBatchInfo = LeoCompanyNamespace::getBatchInfo($cmpIdArr);
        //回填公司信息到物品
        $cargoList = array();
        foreach ($queryList as $qItem) {
            $qItem['price'] = self::showPrice($qItem['price']);
            $qItem['company_name'] = $cmpBatchInfo[$qItem['company_id']]['name'];
            $cargoList[$qItem['id']] = $qItem;
        }
        return $cargoList;
    }

    public static function updateRecord($id, $data) {
        if (empty($id) || empty($data)) {
            return true;
        }
        if (!isset($data['update_t'])) {
            $data['update_t'] = time();
        }
        $res = LeoCargoModel::updateRecord($id, $data);
        return $res;
    }

    //格式化价格, 保留两位小数
    public static function showPrice($price, $fLen = 2) {
        return sprintf('%.' . $fLen . 'f', $price);
    }
}