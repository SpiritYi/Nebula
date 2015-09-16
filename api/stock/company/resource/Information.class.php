<?php
/**
 * 公司信息接口
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/31
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/stock/model/StockCompanyModel.class.php';
require_once CODE_BASE . '/app/stock/StockCompanyNamespace.class.php';

class InformationRes extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/information/' => array(
                'GET' => 'GetCompanyInfo',  //查询公司信息
            ),
            '/information/market/:sid/' => array(
                'GET' => 'GetCompanyPrice', //获取单个公司的市场价格信息
            ),
        );
    }

    public function GetCompanyInfoAction() {
        $user = $this->getStockSessionUser();
        if (empty($user) && !$this->adminSessionCheck()) {
            $this->output(403, '', '请重新登录', 40301);
        }

        $type = HttpUtil::getParam('type');
        switch ($type) {
            case 'suggestion':  //选择公司ajax 联想
                $query = HttpUtil::getParam('query');
                $queryField = 'sspell';
                if (is_numeric($query)) {
                    $queryField = 'sid';
                }
                $res = StockCompanyModel::getSuggestionList($queryField, strtolower($query));
                $sugList = array();
                $objList = array();
                foreach ($res as $item) {
                    $objList[$item['sname']] = $item;
                    $sugList[] = $item[$queryField] . ' ' . $item['sname'];
                }
                $this->output(200, array('obj' => $objList, 'show' => $sugList));
                break;

            case 'price_list':      //批量获取公司报价
                $this->_getCompanyPrice();
                break;
        }
        $this->output(400, '请求参数错误');
    }

    //批量获取公司现价
    private function _getCompanyPrice() {
        $sids = HttpUtil::getParam('sids');
        $sidArr = explode(',', $sids);
        if (empty($sidArr)) {
            $this->output(400, '', '股票代码参数错误');
        }

        $res = StockCompanyNamespace::getCompanyMarketInfo($sidArr);
        if (empty($res)) {
            $this->output(500, '获取数据失败');
        } else {
            $this->output(200, $res);
        }
    }

    public function GetCompanyPriceAction() {
        $user = $this->getStockSessionUser();
        if (empty($user) && !$this->adminSessionCheck()) {
            $this->output(403, '', '请重新登录');
        }

        $sid = HttpUtil::getParam('sid');
        $marketInfo = StockCompanyNamespace::getCompanyMarketInfo(array($sid));
        $this->output(200, $marketInfo[$sid]);
    }
}
