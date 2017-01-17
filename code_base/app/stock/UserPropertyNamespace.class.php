<?php
/**
 * 用户资产操作类
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/01/16
 * @copyright nebula-fund.com
 */

class UserPropertyNamespace {

    //获取用户资产记录列表
    public static function getUserPropertyList($uid, $startT) {
        if (empty($uid)) {
            return array();
        }
        if ($startT <= 0) {
            $startT = strtotime('-3 month');
        }
        require_once CODE_BASE . '/app/stock/model/MoneySnapshotModel.class.php';
        $list = MoneySnapshotModel::getShotList($uid, $startT);
        return $list;
    }
}