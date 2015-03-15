<?php
/**
 * 公司收益展示页面
 * @auhtor chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

class EarningsPage extends Master {
    public function loadHead() {
        $this->headExport('<title>投资收益</title>');
    }

    public function action() {
        $this->render('company/earnings.php');
    }
}
