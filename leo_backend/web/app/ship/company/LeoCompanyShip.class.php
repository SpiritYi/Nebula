<?php
/**
 * 公司相关数据配置首页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/03
 * @copyright nebula-fund.com
 */

class LeoCompanyShipPage extends LeoBackendMaster {
    public $companyList;

    public function loadHead() {
        $this->staExport('<title>公司数据管理导航</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/leo/company/LeoCompanyNamespace.class.php';
        $this->companyList = LeoCompanyNamespace::getList(0, 10);

        $this->render('/ship/company/company_ship.php');
    }
}
