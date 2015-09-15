<?php
/**
 * 更新所有上市公司信息接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../../CronBase.class.php';
require_once CODE_BASE . '/util/http/HttpUtil.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';
require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';

class GetAllCompanyCron extends CronBase {

    public function setCycleConfig() {
        return '0 4 * *';   //每天凌晨更新所有公司数据
    }

    public function run() {
        Logger::logInfo(__CLASS__ . ', start', 'cron_update_company_run');

        $this->updateCompanyData();
        // $this->test();

        Logger::logInfo(__CLASS__ . ', end', 'cron_update_company_run');
    }

    public function test() {
        sleep(60);
        var_dump('test run');exit;
    }

    //增量更新公司数据
    public function updateCompanyData() {
        $localCount = StockCompanyModel::getCompanyCount();
        $companyCount = $this->_getRemoteCompanyCount();
        if ($companyCount <= 0 || $companyCount <= $localCount[0]['total']) {
            if (rand(0, 4) < 4) {   //平均5天更新变更名称的
                return true;    //不需要更新
            }
        }
        //分页更新公司数据
        $pageCount = 100;
        $index = 1;
        $delCount = 0;
        $flag = true;
        while ($flag) {
            $list = $this->_getRemoteCompanyList($index, $pageCount);
            if (empty($list)) {
                $flag = false;
                break;
            }
            if (count($list['items']) < $pageCount) {
                $flag = false;
            }
            $index ++;

            $sidiArr = array();
            $companyList = array();
            foreach ($list['items'] as $item) {
                $sidiArr[] = $item[0] . '_i';
                $companyList[$item[1]] = array(
                    'sid' => $item[1],
                    'sname' => str_replace(' ', '', $item[2]),
                    'symbol' => $item[0],
                );
            }
            $companyiList = $this->_getMultCompanyi($sidiArr);
            $readyList = array();   //准备插入数据库的数据
            foreach ($companyiList as $sid => $sspell) {
                $itemInfo = $companyList[$sid];
                $itemInfo['sspell'] = $sspell;
                $readyList[$sid] = $itemInfo;
            }
            //已经存在的数据如果有变更做更新
            $localInfoList = array();
            $localCompanyList = StockCompanyModel::getBatchInfo(array_keys($companyList));
            if (!empty($localCompanyList)) {
                foreach ($localCompanyList as $localItem) {
                    $localInfoList[$localItem['sid']] = $localItem;
                }
            }
            //存储数据
            foreach ($readyList as $sid => $readyItem) {
                var_dump(date('Y/m/d H:i:s') . '  ' . $sid);
                if (isset($localInfoList[$sid])) {  //已有数据做比较更新
                    $localItem = $localInfoList[$sid];
                    $change = array();
                    foreach ($readyItem as $key => $val) {
                        if ($localItem[$key] != $readyItem[$key]) {
                            $change[$key] = $val;
                        }
                    }
                    if (!empty($change)) {
                        $change['time'] = time();
                        $res = $this->_updateCompanyInfo($sid, $change);
                        if (!$res) {
                            Logger::logError(json_encode($readyItem), 'cron_company_update_error');
                        }
                    }
                } else {    //新加数据
                    $readyItem['time'] = time();
                    $res = $this->_addCompanyInfo($readyItem);
                    if (!$res) {
                        Logger::logError(json_encode($readyItem), 'cron_company_add_error');
                    }
                }
            }

            sleep(5);
        }
    }

    //获取当前线上所有公司总数
    private function _getRemoteCompanyCount() {
        $data = $this->_getRemoteCompanyList(0, 2);
        if (empty($data)) {
            return 0;
        }
        return $data['count'];
    }

    private function _getRemoteCompanyList($index, $count) {
        $url = sprintf(DBConfig::STOCK_COMPANY_LIST_URL, $index, $count);
        // $data = file_get_contents(CRON . '/data/all_company.json');
        $data = HttpUtil::curlget($url, array());
        $dataArr = json_decode($data, true);
        if (empty($dataArr) || (isset($dataArr['code']) && $dataArr['code'] > 0)) {  //获取数据为空
            return array();
        }
        return $dataArr[0];
    }

    //获取公司拼音
    private function _getMultCompanyi($sidiArr) {
        $url = sprintf(DBConfig::STOCK_COMPANY_DATA_URL, implode(',', $sidiArr));
        $str = HttpUtil::curlget($url, array(), array('Content-Type:text/html'));
        // $str = file_get_contents(CRON . '/data/company_i.txt');
        if (empty($str)) {
            return array();
        }
        $strArr = explode("\n", $str);
        $sspellList = array();
        foreach ($strArr as $strItem) {
            $infoi = StockCompanyNamespace::parseiData($strItem);
            if (empty($infoi)) {
                continue;
            }
            $sspellList[$infoi['sid']] = $infoi['sspell'];
        }
        return $sspellList;
    }

    //保存公司数据
    private function _addCompanyInfo($data) {
        $table = StockCompanyModel::getTable();
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildInsertSql($table, $data);
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }
    //更新公司信息
    private function _updateCompanyInfo($sid, $data) {
        $table = StockCompanyModel::getTable();
        $handle = BaseStockModel::getDBHandle();
        $sqlString = SqlbuilderNamespace::buildUpdateSql($table, $data, array(array('sid', '=', $sid)));
        $res = DBMysqlNamespace::execute($handle, $sqlString);
        return $res;
    }

}

$instance = new GetAllCompanyCron();
