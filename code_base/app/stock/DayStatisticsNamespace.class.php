<?php
/**
 * 每日统计相关操作
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/23
 * @copyright nebula-fund.com
 */

class DayStatisticsNamspace {
    //各统计类型常量
    const PRICE_UP_A = 101;         //涨停
    const PRICE_UP_B = 102;         //涨幅5%-10%
    const PRICE_UP_C = 103;         //涨幅3%-5%
    const PRICE_DELIST = 104;       //停牌
    const PRICE_DOWN_A = 201;       //跌停
    const PRICE_DOWN_B = 202;       //跌幅5%-10%
    const PRICE_DOWN_C = 203;       //跌幅3%-5%
    const PRICE_HIGH = 301;         //价格超过10元

    /**
     * 根据一个公司数据分析出所属type
     * @param array $info       //stock_close_price 表中记录信息
     * @return array
     */
    public static function mapType($info) {
        $typeArr = array();
        if (empty($info)) {
            return $typeArr;
        }
        //涨跌幅
        if ($info['price_diff_rate'] >= 9.95) {             //涨停
            $typeArr[] = self::PRICE_UP_A;
        } else if ($info['price_diff_rate'] >= 5) {         //5% ~ 10%
            $typeArr[] = self::PRICE_UP_B;
        } else if ($info['price_diff_rate'] >= 3) {         //3-5%
            $typeArr[] = self::PRICE_UP_C;
        } else if ($info['price_diff_rate'] == -100) {      //停牌
            $typeArr[] = self::PRICE_DELIST;
        } else if ($info['price_diff_rate'] <= -9.95) {     //跌停
            $typeArr[] = self::PRICE_DOWN_A;
        } else if ($info['price_diff_rate'] <= -5 ) {       //-5% ~ -10%
            $typeArr[] = self::PRICE_DOWN_B;
        } else if ($info['price_diff_rate'] <= -3) {        //-3% ~ -5%
            $typeArr[] = self::PRICE_DOWN_C;
        }
        //价格判断
        if ($info['price'] >= 10) {
            $typeArr[] = self::PRICE_HIGH;
        }
        return $typeArr;
    }
}