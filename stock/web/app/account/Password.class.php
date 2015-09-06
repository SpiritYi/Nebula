<?php
/**
 * 修改密码
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/09/04
 * @copyright nebula-fund.com
 */

class PasswordPage extends StockMaster {
    public function loadHead() {
        $this->staExport('<title>修改密码</title>');
    }

    public function action() {
        $this->render('/account/password.php');
    }
}
