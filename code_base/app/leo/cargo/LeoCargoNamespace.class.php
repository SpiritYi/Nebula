<?php
/**
 * leo 项目物品相关接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/29
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/leo/model/LeoCargoModel.class.php';
require_once CODE_BASE . '/app/es/LeoEsNamespace.class.php';

class LeoCargoNamespace {

    public static function addRecord($data) {
        if (empty($data)) {
            return true;
        }
        $data['update_t'] = time();
        $res = LeoCargoModel::addRecord($data);
        if ($res > 0) {     //返回保存的id成功, 存储数据到es
            $esItem = $data;
            $esItem['id'] = $res;
            $companyId = $data['company_id'];
            $companyInfo = LeoCompanyNamespace::getBatchInfo([$companyId]);
            $esItem['company_name'] = $companyInfo[$companyId]['name'];
            $esRes = LeoEsNamespace::addRecord($res, $esItem);
        }
        return $res;
    }

    //删除记录,实际是把状态改成-1
    public static function delRecord($id) {
        if (empty($id) || !is_numeric($id)) {
            return false;
        }
        $data = array('status' => -1, 'update_t' => time());
        $res = LeoCargoModel::updateRecord($id, $data);
        //删除es 记录
        $esRes = LeoEsNamespace::delRecord($id);
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
        //更新es 数据
        $esItem = $data;
        if (isset($data['company_id'])) {
            $companyId = $data['company_id'];
            $companyInfo = LeoCompanyNamespace::getBatchInfo([$companyId]);
            $esItem['company_name'] = $companyInfo[$companyId]['name'];
        }
        $esRes = LeoEsNamespace::updateRecord($id, $esItem);

        $res = LeoCargoModel::updateRecord($id, $data);
        return $res;
    }

    //格式化价格, 保留两位小数
    public static function showPrice($price, $fLen = 2) {
        return sprintf('%.' . $fLen . 'f', $price);
    }
}