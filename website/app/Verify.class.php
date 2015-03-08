<?php
/**
 * 验证身份页面
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/08
 * @copyright nebula.com
 */

class VerifyPage extends EmptyPage {

    public function loadHead() {
        $this->headExport('<title>Nebula Verification</title>');
        $this->headExport('Verify.css');
    }

    public function action() {
        $this->render('verify.php');
    }
}
