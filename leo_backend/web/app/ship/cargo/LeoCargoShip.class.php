<?php
/**
 * 货物管理后台
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/29
 * @copyright nebula-fund.com
 */

class LeoCargoShipPage extends LeoBackendMaster {
    public $companyList;
    public $cargoList;

    public function loadHead() {
        $this->staExport('<title>物品管理</title>');
    }

    public function action() {
        require_once CODE_BASE . '/app/leo/company/LeoCompanyNamespace.class.php';
        $this->companyList = LeoCompanyNamespace::getList(0, 10);
        require_once CODE_BASE . '/app/leo/cargo/LeoCargoNamespace.class.php';
        $this->cargoList = LeoCargoNamespace::getList(0, 10);

        $this->render('/ship/cargo/cargo_ship.php');
    }
}