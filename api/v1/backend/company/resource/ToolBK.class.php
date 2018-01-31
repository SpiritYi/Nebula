<?php
/**
 * 后台工具接口api
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2018/01/31
 * @copyright nebula-fund.com
 */

class ToolBKRes extends ResourceBase {
    
    public function setUriMatchConfig() {
        return array(
            '/toolbk/price/' => array(
                'GET' => 'getPriceRate',            //计算价格角度线
            ),
        );
    }
    
    public function getPriceRateAction() {
        $priceStr = HttpUtilEx::getParam('price');
        $priceArr = explode(' ', $priceStr);
        $data = $this->_dealStock($priceArr[0], isset($priceArr['1']) ? $priceArr['1'] : '+');
        $this->output(200, $data, '提交成功');
    }
    
    private function _dealStock($originPrice, $direction = '+') {
        if ($originPrice <= 0) {
            return false;
        }
        
        $rate = [1/2, 1/4, 1/8, 1/16];
        if ($direction == '-') {
            $rate = [-1/16, -1/8, -1/4, -1/2];
        }
        $dataArr = array();
        foreach ($rate as $rItem) {
            $endPrice = $originPrice * (1 + $rItem);
            $dataArr[] = array(
                'fraction' => sprintf('1/%d', 1/abs($rItem)),
                'percent' => sprintf('%.2f%%', $rItem * 100),
                'end_price' => sprintf('%.2f', $endPrice),
                'spread' => sprintf('%.2f', $endPrice - $originPrice),
            );
        }
        return $dataArr;
    }
}