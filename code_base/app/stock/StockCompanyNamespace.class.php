<?php
/**
 * 公司操作相关的namespace
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';

class StockCompanyNamespace {
    //解析公司正常股票数据
    public static function parseData($str) {
        $reg = '/^var hq_str_(sh|sz)([\d]{6})="(.*)";$/';
        $arr = array();
        if (!preg_match($reg, $str, $arr)) {
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
        $info['price_diff_rate'] = round($info['price_diff'] / $info['ysd_closing_price'] * 100, 2);    //涨跌幅
        return $info;
    }

    //解析公司原信息数据, 获取字母拼音
    public static function parseiData($str) {
        $reg = '/^var hq_str_(sh|sz)([\d]{6})_i="(.*)";$/';
        $arr = array();
        if (!preg_match($reg, $str, $arr)) {
            return array();
        }
        $fieldArr = explode(',', $arr[3]);
        $info = array(
            'sid' => $arr[2],
            'sspell' => $fieldArr[1],
        );
        return $info;
    }

    //输出价格默认保留小数点两位
    public static function showPrice($price, $fLen = 2) {
        return sprintf('%.' . $fLen . 'f', $price);
    }

    //获取一个公司市场报价信息
    public static function getCompanyMarketInfo($sid) {
        //查询公司symbol
        $company = StockCompanyModel::getBatchInfo(array($sid));
        if (empty($company)) {
            return false;
        }
        $url = sprintf(DBConfig::STOCK_COMPANY_DATA_URL, $company[0]['symbol']);
        $str = HttpUtil::curlget($url, array());
        // $str = 'var hq_str_sh600111="北方稀土,12.50,13.07,13.15,13.46,12.41,13.14,13.18,85085802,1106796739,6000,13.14,8800,13.13,900,13.12,1900,13.09,72499,13.08,262200,13.18,170250,13.19,583019,13.20,59400,13.21,61100,13.22,2015-09-02,15:04:08,00";';
        if (empty($str)) {
            return false;
        }
        $resp = self::parseData($str);
        if (empty($resp)) {
            return false;
        }
        $resp['sname'] = $company[0]['sname'];
        return $resp;
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

}
