<?php
/**
 * 轻松倒计时
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/04/13
 * @copyright nebula-fund.com
 */

require_once STOCK_WEB . '/app/StockEmpty.class.php';

class FreePage extends StockEmpty {
    public $loveTime;
    
    public function loadHead() {
        $this->staExport('<title>小小旭</title>');
    }

    public function action() {
        $this->endTimeConfig = [
            '2018/06/01',
        ];
        
        $this->loveTime = $this->diffDate('2015/10/20', date('Y/m/d'));
        
        $this->render('/user/free.php');
    }
    
    public function diffDate($date1, $date2) {
        $datetime1 = new DateTime($date1);
        $datetime2 = new DateTime($date2);
        $interval = $datetime1->diff($datetime2);
        $time['y']         = $interval->format('%Y');
        $time['m']         = $interval->format('%m');
        $time['d']         = $interval->format('%d');
        $time['h']         = $interval->format('%H');
        $time['i']         = $interval->format('%i');
        $time['s']         = $interval->format('%s');
        $time['a']         = $interval->format('%a');    // 两个时间相差总天数
        return $time;
    }
}