<?php
/**
 * backend 空页面类
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

abstract class LeoBackendEmpty extends PageBase {
    public function __construct() {
        PageBase::render('/leo_backend_empty.php');
    }

    abstract function loadHead();

    abstract function action();
}
