<?php
/**
 * 公司操作相关的namespace
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
require_once CODE_BASE . '/util/http/HttpUtil.class.php';

class StockCompanyNamespace {
    //解析公司正常股票数据
    public static function parseData($str) {
        $reg = '/^var hq_str_(sh|sz)([\d]{6})="(.*)";$/';
        $arr = array();
        if (!preg_match($reg, $str, $arr)) {
            return array();
        }
        if (empty($arr[3])) {
            return array();
        }
        $fieldArr = explode(',', $arr[3]);
        //处理上证指数
        if ($arr[1] . $arr[2] == 'sh000001') {
            $arr[2] = '699001';
        }
        $info = array(
            'sid' => $arr[2],
            'opening_price' => (float)$fieldArr[1],        //今日开盘价
            'ysd_closing_price' => (float)$fieldArr[2],    //昨日收盘价
            'price' => self::showPrice($fieldArr[3]),                   //现价
            'highest' => (float)$fieldArr[4],              //最高价
            'lowest' => (float)$fieldArr[5],               //最低价
            'time' => strtotime($fieldArr[30] . ' ' . $fieldArr[31]),   //最后更新时间
        );
        $info['price_diff'] = self::showPrice($info['price'] - $info['ysd_closing_price']);             //涨跌价差
        $info['price_diff_rate'] = self::showPrice(round($info['price_diff'] / $info['ysd_closing_price'] * 100, 2));    //涨跌幅
        return $info;
    }

    /**
     * 解析公司详细数据
     * @return array
     *          - outright_list array   //逐笔记录数组
     */
    public static function parseDetailData($str) {
        $reg = '/^v_(sh|zs)([\d]{6})="(.*)";$/';
        $arr = array();
        if (!preg_match($reg, $str, $arr)) {
            return array();
        }
        $fieldArr = explode('~', $arr[3]);
        if ($arr[1] . $arr[2] == 'sh000001') {  //处理上证指数
            $arr[2] = $fieldArr[2] = '699001';
        }
        $outrightStrArr = explode('|', $fieldArr[29]); //逐笔成交记录数组
        $recordArr = array();
        foreach ($outrightStrArr as $record) {
            $dArr = explode("/", $record);
            $outrightInfo = array(
                't' => $dArr[0],
                'tstamp' => strtotime($dArr[0]),
                'price' => $dArr[1],        //成交价格
                'volume' => $dArr[2],       //成交量(手)
                'direction' => $dArr[3],    //交易方向, B, S
                'turnover' => $dArr[4],     //成交额
                'series_id' => $dArr[5],    //交易序列id
            );
            $recordArr[] = $outrightInfo;
        }
        $detailData = array(
            'sid' => $fieldArr[2],
            'ysd_closing_price' => $fieldArr[4],    //昨收
            'outright_list' => $recordArr,  //逐笔数组
        );
        return $detailData;
    }

    //输出价格默认保留小数点两位
    public static function showPrice($price, $fLen = 2) {
        return sprintf('%.' . $fLen . 'f', $price);
    }

    /**
     * 获取公司基本信息
     * @param array $sidArr
     * @return array
     *          - sid, sname, symbol
     */
    public static function getCompanyInfo($sidArr) {
        if (!is_array($sidArr)) {
            return array();
        }
        $companyList = StockCompanyModel::getBatchInfo($sidArr);
        if (empty($companyList)) {
            return array();
        }
        $resData = array();
        foreach ($companyList as $key => $item) {
            $resData[$item['sid']] = $item;
        }
        return $resData;
    }

    /**
     * 批量获取公司报价信息
     * @return array
     *          - sid, sname, opening_price, ysd_closing_price, price, highest, lowest, price_diff, price_diff_rate
     */
    public static function getCompanyMarketInfo($sidArr) {
        if (!is_array($sidArr)) {
            return array();
        }
        //查询公司symbol
        $companyList = StockCompanyModel::getBatchInfo($sidArr);
        if (empty($companyList)) {
            return array();
        }
        $symbolArr = array();
        $snameArr = array();
        foreach ($companyList as $info) {
            $symbolArr[] = $info['symbol'];
            $snameArr[$info['sid']] = $info['sname'];
        }
        //远程获取公司报价
        $url = sprintf(DBConfig::STOCK_COMPANY_DATA_URL, implode(',', $symbolArr));
        $rspStr = HttpUtil::curlget($url, array());
        if (empty($rspStr)) {
            return array();
        }
        //解析报价数据
        $strArr = explode("\n", $rspStr);
        $res = array();
        foreach ($strArr as $dataStr) {
            if (empty($dataStr)) {
                continue;
            }
            $dataInfo = self::parseData($dataStr);
            if (!empty($dataInfo)) {
                $dataInfo['sname'] = $snameArr[$dataInfo['sid']];
                $res[$dataInfo['sid']] = $dataInfo;
            }
        }
        return $res;
    }

    //批量获取详细数据, 逐笔成交明细
    public static function getCompanyDetail($sidArr) {
        //批量获取公司信息
        $symbolArr = array();
        $companyList = StockCompanyModel::getBatchInfo($sidArr);
        foreach ($companyList as $info) {
            $symbolArr[] = $info['symbol'];
        }
        $dataUrl = sprintf(DBConfig::STOCK_COMPANY_DETAIL_URL, implode(',', $symbolArr));
        $dataStr = HttpUtil::curlget($dataUrl);
        if (empty($dataStr)) {
            return false;
        }
        $strArr = explode("\n", $dataStr);
        $detailList = array();
        //解析数据字符串
        foreach ($strArr as $strItem) {
            if (empty($strItem)) {
                continue;
            }
            $detailData = self::parseDetailData($strItem);
            if (!empty($detailData)) {
                $detailList[$detailData['sid']] = $detailData;
            }
        }
        return $detailList;
    }

    //获取现在公司总数
    public static function getCompanyCount() {
        $count = StockCompanyModel::getCompanyCount();
        return !empty($count[0]['total']) ? intval($count[0]['total']) : 0;
    }

    //是否交易时间
    public static function isExchangeHour() {
        $isExchange = false;
        $t = time();
        if (!in_array(date('w'), [0, 6]) &&
            ((strtotime('09:25') < $t && $t < strtotime('11:35')) ||
            (strtotime('12:55') < $t && $t < strtotime('15:05')))) {
            require_once CODE_BASE . '/app/stock/model/StockPointModel.class.php';
            $dayData = StockPointModel::selectDayPoint('699001', date('Ymd'));
            if (!empty($dayData)) {
                $isExchange = true;
            }
        }
        return $isExchange;
    }
    //是否开市日, 交易时间开始后判断才有效
    public static function isExchangeDay() {
        $isExchange = false;
        if (!in_array(date('w'), [0, 6])) {
            require_once CODE_BASE . '/app/stock/model/StockPointModel.class.php';
            $dayData = StockPointModel::selectDayPoint('699001', date('Ymd'));
            if (!empty($dayData)) {
                $isExchange = true;
            }
        }
        return $isExchange;
    }

}
