<?php
/**
 * backend 默认页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

class BackendDefaultPage extends BackendMaster {
    public function loadHead() {
        $this->staExport('<title>后台管理</title>');
    }

    public function action() {
        $this->render('/backend_default.php');
    }
}
