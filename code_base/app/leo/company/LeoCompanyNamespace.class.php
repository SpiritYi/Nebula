<?php
/**
 * Leo 项目供应商公司管理接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/leo/model/LeoCompanyModel.class.php';

class LeoCompanyNamespace {

    /**
     * 添加公司记录
     * @param array $data
     * @return bool
     */
    public static function addRecord($data) {
        if (empty($data)) {
            return false;
        }
        $data['update_t'] = time();
        $res = LeoCompanyModel::addRecord($data);
        return $res;
    }

    public static function delRecord($cid) {
        if (empty($cid) || !is_numeric($cid)) {
            return false;
        }
        $update = array('status' => -1, 'update_t' => time());
        $res = LeoCompanyNamespace::updateInfo($cid, $update);
        return $res;
    }

    //获取指定id 详细信息
    public static function getBatchInfo($cidArr) {
        $list = LeoCompanyModel::getBatchInfo($cidArr);
        $infoList = array();
        foreach ($list as $item) {
            $infoList[$item['cid']] = $item;
        }
        return $infoList;
    }

    public static function getList($offset, $limit) {
        $list = LeoCompanyModel::getList($offset, $limit);
        if (empty($list)) {
            return array();
        }
        $companyList = array();
        foreach ($list as $lItem) {
            $companyList[$lItem['cid']] = $lItem;
        }
        return $companyList;
    }

    public static function updateInfo($cid, $data) {
        if (empty($data)) {
            return true;
        }
        if (!isset($data['update_t'])) {
            $data['update_t'] = time();
        }
        $res = LeoCompanyModel::updateInfo($cid, $data);
        return $res;
    }
}