<?php
/**
 * 公司信息接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/31
 * @copyright nebula-fund.com
 */

class InformationRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/information/' => array(
                'GET' => 'GetCompanyInfo',  //查询公司信息
            ),
        );
    }

    public function GetCompanyInfoAction() {
        $user = $this->getStockSessionUser();
        if (empty($user)) {
            $this->output(403, '', '请重新登录');
        }

        $type = HttpUtil::getParam('type');
        switch ($type) {
            case 'suggestion':  //选择公司ajax 联想
                $query = HttpUtil::getParam('query');
                $queryField = 'sspell';
                if (is_numeric($query)) {
                    $queryField = 'sid';
                }
                require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
                $res = StockCompanyModel::getSuggestionList($queryField, strtolower($query));
                $sugList = array();
                $objList = array();
                foreach ($res as $item) {
                    $objList[$item[$queryField]] = $item;
                    $sugList[] = $item[$queryField] . ' ' . $item['sname'];
                }
                $this->output(200, array('obj' => $objList, 'show' => $sugList));
                break;
        }
        $this->output(400, '请求参数错误');
    }
}
