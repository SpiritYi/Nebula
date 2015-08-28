<?php
/**
 * 公司操作相关的namespace
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/25
 * @copyright nebula-fund.com
 */

class StockCompanyNamespace {
    //解析公司正常股票数据
    public static function parseData($str) {
        $reg = '/^var hq_str_([sh|sz][\d]{6})="(.*)";';
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

}