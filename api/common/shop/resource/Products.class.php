<?php
/**
 * 积分商品接口
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/12/02
 * @copyright ganji.com
 */

require_once CODE_BASE2 . '/app/mobile_client/ClientCreditProductNamespace.class.php';

class Products extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/products/' => array(
                'GET' => 'getProductList',      //获取在线商品列表
            ),
            '/products/:id/' => array(
                'GET' => 'getProductById',      //获取商品详情
            ),
        );
    }

    //获取在线商品列表
    public function getProductListAction() {
        $listType = clientPara::getArg('type');
        //限定获取列表的类型
        if (!in_array($listType, array('all', 'hot'))) {
            $listType = 'all';
        }
        switch ($listType) {
            case 'all':
                $this->_getAllProductList();
                break;
           
            case 'hot':
                $this->_getHotProductList();
                break;
       }
    }
    private function _getAllProductList() {
        $productsList = ClientCreditProductNamespace::getProductList();
        $res = ResourceBase::formatReturn(200, $productsList, '');
        ResourceBase::display($res);exit;
    }
    //获取当前热卖列表
    private function _getHotProductList() {
        $hotList = ClientCreditProductNamespace::getRecentBuyList();
        $res = ResourceBase::formatReturn(200, $hotList, '');
        ResourceBase::display($res);exit;
    }

    public function getProductByIdAction() {
        $productInfo = ClientCreditProductNamespace::getProductById($this->URI_DATA['id']);
        if (empty($productInfo)) {
            $res = ResourceBase::formatReturn(404, '', 'product not found.', -1);
            ResourceBase::display($res);exit;
        }
        $res = ResourceBase::formatReturn(200, $productInfo, '');
        ResourceBase::display($res);exit;
    }
}