<?php
/**
 * backend 默认页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

class LeoBackendDefaultPage extends LeoBackendMaster {
    public function loadHead() {
        $this->staExport('<title>后台管理</title>');
    }

    public function action() {
        $this->render('/leo_backend_default.php');
    }
}
