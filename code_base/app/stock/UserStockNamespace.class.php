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
            }
            $tableList[] = $item;
        }
        return $tableList;
    }
}
