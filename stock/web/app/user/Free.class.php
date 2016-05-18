<?php
/**
 * 轻松倒计时
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/04/13
 * @copyright nebula-fund.com
 */

require_once STOCK_WEB . '/app/StockEmpty.class.php';

class FreePage extends StockEmpty {
    public function loadHead() {
        $this->staExport('<title>小小旭</title>');
    }

    public function action() {
        $this->endTstamp = strtotime('2017/12/31');
        $this->endTimeConfig = [
            '2016/06/04',
            '2016/08/05',
            '2017/12/31',
        ];
        $this->render('/user/free.php');
    }
}