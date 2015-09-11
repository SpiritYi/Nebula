<?php
/**
 * 用户股票操作相关
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/03
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/UserStockModel.class.php';

class UserStockNamespace {

    //获取用户持股列表
    public static function getUserStockList($uid) {
        $holdList = UserStockModel::selectStockList($uid);
        if (empty($holdList)) {
            return array();
        }
        //批量处理持股公司信息
        $sidArr = array();
        foreach ($holdList as $item) {
            $sidArr[] = $item['sid'];
        }
        require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
        $snameArr = array();
        $companyList = StockCompanyModel::getBatchInfo($sidArr);
        if (!empty($companyList)) {
            foreach ($companyList as $info) {
                $snameArr[$info['sid']] = $info['sname'];
            }
        }
        //格式化持股表格列表
        $tableList = array();
        foreach ($holdList as $item) {
            if (isset($snameArr[$item['sid']])) {
                $item['sname'] = $snameArr[$item['sid']];
                $item['per_cost'] = !empty($item['count']) ? $item['cost'] / $item['count'] : '';
            }
            $tableList[] = $item;
        }
        return $tableList;
    }

    /**
     * 更新用户持股
     * @param $count int    //持股数量，> 0 买进，< 0 卖出
     * @param $cost float   //花费， < 0 卖出
     * @param $isAvailable  bool    //是否立即可用
     */
    public static function setUserHolding($uid, $sid, $count, $cost, $isAvailable = false) {
        $opFlag = false;
        //添加持股
        $userStock = UserStockModel::selectStockBySid($uid, $sid);
        if (!empty($userStock)) {   //更新持股
            $hold = $userStock[0];
            $update = array(
                'count' => $hold['count'] + $count,
                'cost' => $hold['cost'] + $cost,
                'time' => time(),
            );
            if ($isAvailable) {
                $update['available_count'] = $hold['available_count'] + $count;
            }
            $opFlag = UserStockModel::updateUserStock($uid, $sid, $update);
        } else {    //添加持股
            $data = array(
                'uid' => $uid,
                'sid' => $sid,
                'count' => $count,
                'cost' => $cost,
                'time' => time(),
            );
            if ($isAvailable) {
                $data['available_count'] = $count;
            }
            $opFlag = UserStockModel::addUserStock($data);
        }
        return $opFlag;
    }
}
