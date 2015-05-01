<?php
/**
 * 用户申购合约展示页面
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/04/04
 * @copyright nebula-feed.com
 */

require_once WEBSITE . '/app/user/UserMaster.class.php';

class ContractPage extends UserMaster {
    public function loadHead() {
        $this->staExport('<title>基金合约</title>');
    }

    public function userAction() {
        $userInfo = $this->getSessionUser();
        $template = '/user/contract/user_%s.php';
        $tempFile = sprintf($template, $userInfo['id']);

        if (!$this->templateFileExists($tempFile)) {  //载入默认模板文件
            $tempFile = sprintf($template, 'default');
        }
        $this->render($tempFile);
        $navActive = '
            <script type="text/javascript">
                seajs.use(["NB"], function(NB) {
                    NB.navActive($("#nav_u_contract"));
                });
            </script>
        ';
        $this->staExport($navActive);
    }
}
