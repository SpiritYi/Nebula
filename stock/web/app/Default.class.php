<?php
/**
 * 首页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

class DefaultPage extends StockMaster {
    public function loadHead() {

    }

    public function action() {
        require_once CODE_BASE . '/util/secret/AesEncrypt.class.php';
        $str = "hello world";
        $encStr = AesEncrypt::encrypt($str);
        $decStr = AesEncrypt::decrypt($encStr);
        var_dump($encStr, $decStr);
        $this->render('/default.php');
    }
}