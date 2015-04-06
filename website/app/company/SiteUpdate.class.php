<?php
/**
 * 网站版本更新页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/06
 * @copyright nebula-fund.com
 */

require_once WEBSITE . '/app/Master.class.php';

class SiteUpdatePage extends Master {
    public function loadHead() {
        $this->staExport('<title>网站更新</title>');
    }

    public function action() {
        $this->render('/company/site_update.php');
    }
}
