<?php
/**
 * 用户信用交易操作相关
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2017/05/08
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/UserMarginStockModel.class.php';

class UserMarginStockNamespace {
    
    const MARGIN_COMMSION = 0.0007;     //万7 的佣金
    const MARGIN_TAX = 0.001;           //千分之一的印花税
    
    //信用交易融券卖出
    public static function setUserHoldings($uid, $sid, $count, $price, $cost) {
        $data = array(
            'uid' => $uid,
            'sid' => $sid,
            'count' => $count,
            'strike_price' => $price,
            'cost' => $cost,
            'available_count' => $count,
            'time' => time(),
        );
        $opFlag = UserMarginStockModel::addRecord($data);
        return $opFlag;
    }
    
    public static function getMarginHoldings($uid) {
        $holdList = UserMarginStockModel::selectAllHoldings($uid);
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
}