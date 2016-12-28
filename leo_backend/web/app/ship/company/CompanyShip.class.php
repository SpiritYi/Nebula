<?php
/**
 * 公司相关数据配置首页
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/05/03
 * @copyright nebula-fund.com
 */

class CompanyShipPage extends BackendMaster {
    public function loadHead() {
        $this->staExport('<title>公司数据管理导航</title>');
    }

    public function action() {
        require_once API . '/v1/company/model/EarningsRateModel.class.php';
        $this->rateTypeList = EarningsRateModel::$TYPE_NAME;

        require_once CODE_BASE . '/model/company/ArticleModel.class.php';
        $this->articleTypeList = ArticleModel::$TYPE_NAME;

        $this->render('/ship/company/company_ship.php');
    }
}
