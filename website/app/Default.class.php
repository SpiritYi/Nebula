<?php
/**
 * www.nabula.com 跟节点默认展示页, 方便router 统一处理
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/07
 * @copyright nebula.com
 */

require_once WEBSITE . '/app/Master.class.php';

class DefaultPage extends Master {
    public function loadHead() {}

    public function loadContent() {
        echo 'this is a default page';
    }

    public function action() {

    }
}