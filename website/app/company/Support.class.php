<?php
/**
 * 用户意见建议支持页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/18
 * @copyright nebula-fund.com
 */

class SupportPage extends Master {
    public function loadHead() {
        $this->staExport('<title>服务信箱</title>');
        $this->staExport('/css/company/support.css');
    }

    public function action() {
        $this->render('company/support.php');
    }
}
