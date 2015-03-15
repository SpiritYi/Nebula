<?php
/**
 * 公司说明，关于我们页
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';

class AboutPage extends Master {
    public function loadHead() {
        $this->headExport('<title>关于公司</title>');
    }

    public function action() {
        $this->render('company/about.php');
    }
}
