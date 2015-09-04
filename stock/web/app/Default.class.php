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
        $this->render('/default.php');
    }
}
