<?php
/**
 * 用户持股页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/03
 * @copyright nebula-fund.com
 */

class HoldingsPage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>我的股份</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/user/StockUserNamespace.class.php';
        $this->userProperty = StockUserNamespace::getUserInfoById($this->userInfo['uid']);

        //获取最近资产数据
        require_once CODE_BASE . '/app/stock/model/MoneySnapshotModel.class.php';
        $lastDay = MoneySnapshotModel::getRecentShot($this->userInfo['uid'], strtotime(date('Y/m/d')) - 1);
        $lastWeek = MoneySnapshotModel::getRecentShot($this->userInfo['uid'], strtotime('-1 sunday'));
        $lastMonth = MoneySnapshotModel::getRecentShot($this->userInfo['uid'], strtotime(date('Y/m') . '/01 00:00:00') - 1);
        $lastYear = MoneySnapshotModel::getRecentShot($this->userInfo['uid'], strtotime(date('Y') . '/01/01') - 1);
        $this->lastProperty = array(
            'last_day' => empty($lastDay) ? 0 : $lastDay[0]['money'],
            'last_week' => empty($lastWeek) ? 0 : $lastWeek[0]['money'],
            'last_month' => empty($lastMonth) ? 0 : $lastWeek[0]['money'],
            'last_year' => empty($lastYear) ? 0 : $lastYear[0]['money'],
        );

        require_once CODE_BASE . '/app/stock/UserStockNamespace.class.php';
        $this->stockList = UserStockNamespace::getUserStockList($this->userInfo['uid']);
        $this->render('/stock/holdings.php');
    }
}
