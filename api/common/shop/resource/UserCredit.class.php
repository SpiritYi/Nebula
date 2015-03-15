<?php
/**
 * 用户积分相关操作接口
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2014/12/01
 * @copyright ganji.com
 */

require_once CODE_BASE2 . '/app/mobile_client/ClientUserCreditNamespace.class.php';
require_once CODE_BASE2 . '/app/mobile_client/ClientCreditProductNamespace.class.php';


class UserCredit extends ResourceBase {

    public function setUriMatchConfig() {
        return array(
            '/usercredit/:user_id/' => array(
                'GET' => 'getUserCredit',           //获取用户总积分
                'POST' => 'earnCredit',             //做任务
            ),
            '/usercredit/:user_id/details/' => array(
                'GET' => 'getUserCreditDetailList', //获取用户积分明细
            ),
            '/usercredit/:user_id/products/' => array(
                'GET' => 'getUserProductList',      //获取用户购买过的产品列表
                'POST' => 'buyProduct',             //兑换积分商品
            ),
            '/usercredit/:user_id/orders/' => array(
                'POST' => 'createOrder',            //创建购买订单
            ),
            '/usercredit/:user_id/orders/:order_id/' => array(
                'PUT' => 'payOrder',
                'DELETE' => 'cancelOrder',
            ),
        );
    }
    public function setParamConfig() {
        return array(
            'getUserCredit' => array(
                'user_id' => 'int',
            ),
            'earnCredit' => array(
                'user_id' => 'int',
                'task_id' => 'int',
            ),
            'getUserCreditDetailList' => array(
                'user_id' => 'int',
                'page_index' => 'int',
            ),
            'getUserProductList' => array(
                'user_id' => 'int',
            ),
            'buyProduct' => array(
                'user_id' => 'int',
                'product_id' => 'int',
            ),
            'createOrder' => array(
                'user_id' => 'int',
                'product_id' => 'int',
            ),
            'cancelOrder' => array(
                'user_id' => 'int',
                'order_id' => 'int',
            ),
            'payOrder' => array(
                'user_id' => 'int',
                'order_id' => 'int',
                'express_consignee' => 'string',
                'express_phone' => 'string',
                'express_address' => 'string',
            ),
        );
    }
    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'user_id':
                if (empty($this->_param['user_id'])) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_NOT_FOUND);
                }
                $token = clientPara::getArg('token');
                $flag = clientPara::auth_check($this->_param[$paramKey], $token);
                if (!$flag) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_CHECK_LOGIN);
                }
                break;

            case 'express_consignee':
            case 'express_phone':
            case 'express_address':
            case 'task_id':
            case 'order_id':
            case 'product_id':
                if (empty($this->_param[$paramKey])) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_PARAM, '', $paramKey . ' 参数错误');
                }
                break;
        }
    }

    //获取用户总积分
    public function getUserCreditAction() {
        $userCredit = ClientUserCreditNamespace::getUserCredit($this->_param['user_id']);
        if (empty($userCredit)) {
            $res = ResourceBase::formatReturn(404, '', 'user data empty.');
            ResourceBase::display($res);exit;
        }
        $res = ResourceBase::formatReturn(200, $userCredit, '');
        ResourceBase::display($res);
    }

    //赚取积分
    public function earnCreditAction() {
        //校验任务id
        $taskList = ClientUserCreditNamespace::getTaskList();
        if (!array_key_exists($this->_param['task_id'], $taskList)) {
            $res = ResourceBase::formatReturn(400, '', 'task id is not available.', -1);
            ResourceBase::display($res);exit;
        }
        //校验任务状态
        $installId = clientPara::getArg('userId');
        if (!empty($installId) && !is_numeric($installId)) {
            require_once CODE_BASE2 . '/app/mobile_client/util/UUIDprocess.class.php';
            $installId = UUIDprocess::decryptUUId($installId);
        }
        if (!ClientUserCreditNamespace::checkUserTaskStatus($this->_param['user_id'], $this->_param['task_id'], $installId)) {
            $res = ResourceBase::formatReturn(400, '', 'task status not available.', -2);
            ResourceBase::display($res);exit;
        }
        //赚取积分
        $taskInfo = $taskList[$this->_param['task_id']];
        $flag = ClientUserCreditNamespace::earnCredit($this->_param['user_id'], $this->_param['task_id'], $installId);
        if ($flag) {
            $userCredit = ClientUserCreditNamespace::getUserCredit($this->_param['user_id']);
            $res = ResourceBase::formatReturn(201, $userCredit, sprintf('+%d积分', $taskInfo['credit']));
            ResourceBase::display($res);exit;
        } else {
            $res = ResourceBase::formatReturn(500, '', 'server operate failed.', -5);
            ResourceBase::display($res);exit;
        }
    }

    public function getUserCreditDetailListAction() {
        $pageIndex = $this->_param['page_index'];
        if ($pageIndex < 0) {
            $pageIndex = 0;
        }
        $detailList = ClientUserCreditNamespace::getUserCreditDetailList($this->_param['user_id'], $pageIndex);
        require_once CODE_BASE2 . '/app/mobile_client/model/credit/UserCreditModel.class.php';
        $userCredit = UserCreditModel::getUserCredit($this->_param['user_id']);
        $returnData = array(
            'credit' => $userCredit[0]['credit'],
            'list' => $detailList,
        );
        $res = ResourceBase::formatReturn(200, $returnData, '');
        ResourceBase::display($res);exit;
    }

    /**
     * 获取用户购买积分商品的列表
     * GET /api/common/shop/usercredit/140298739/products/
     */
    public function getUserProductListAction() {
        $productList = ClientUserCreditNamespace::getUserProductList($this->_param['user_id']);
        $res = ResourceBase::formatReturn(200, $productList, '');
        ResourceBase::display($res);exit;
    }

    /**
     * 购买商品
     * POST /api/common/shop/UserCredit/140298739/products/
     * @param $product_id int
     */
    public function buyProductAction() {
        $productId = $this->_param['product_id'];
        $productInfo = ClientCreditProductNamespace::getProductById($productId);
        if (empty($productInfo)) {
            $res = ResourceBase::formatReturn(400, '', 'product id is not available.', -1);
            ResourceBase::display($res);exit;
        }
        //获取用户积分
        $userCredit = ClientUserCreditNamespace::getUserCredit($this->_param['user_id']);
        if ($userCredit['credit'] < $productInfo['price']) {
            $res = ResourceBase::formatReturn(400, '', 'credit not enough.', -2);
            ResourceBase::display($res);exit;
        }
        //兑换奖品
        $flag = ClientCreditProductNamespace::buyProduct($this->_param['user_id'], $productId);
        // $flag = 10;
        if (in_array((int)$flag, array(-1, -2, -3))) {
            $res = ResourceBase::formatReturn(400, $flag, 'product error.', -1);
            ResourceBase::display($res);exit;
        }
        if ($flag == -10) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_BUY_STRICT);
            ResourceBase::display($res);return;
        }
        if ($flag == -4) {
            $res = ResourceBase::formatReturn(500, $flag, 'change inventory failed.', -2);
            ResourceBase::display($res);exit;
        }
        if (!$flag) {
            $res = ResourceBase::formatReturn(500, $flag, 'bought failed.', -2);
            ResourceBase::display($res);exit;
        }
        //返回积分
        // $userCredit = ClientUserCreditNamespace::getUserCredit($this->URI_DATA['id']);
        $productInfo['sold'] ++;
        $productInfo['product_code'] = $flag[0]['product_code'];
        $productInfo['bought_time'] = time();
        $productInfo['code_expire'] = $productInfo['product_type'] == 1 ? $productInfo['bought_time'] + 7 * 24 * 3600 : $productInfo['code_expire'];
        $res = ResourceBase::formatReturn(201, $productInfo, '');
        ResourceBase::display($res);exit;
    }

    /**
     * 购买前创建订单, 锁定商品
     * POST /api/common/shop/usercredit/:id/orders/
     * @param product_id int
     */
    public function createOrderAction() {
        //获取用户快递信息
        $expressInfo = ClientUserCreditNamespace::getUserExpressInfo($this->_param['user_id']);
        //检测是否有现成有效订单
        require_once CODE_BASE2 . '/app/mobile_client/model/credit/CreditProductOrderModel.class.php';
        $orderInfo = CreditProductOrderModel::getValidProductOrder($this->_param['user_id'], $this->_param['product_id']);
        if (!empty($orderInfo)) {
            //有现成订单直接刷新订单时间，返回订单id
            $flag = CreditProductOrderModel::setOrderStatus($orderInfo[0]['id'], CreditProductOrderModel::VALID);
            if ($flag) {
                $data = $this->_formatOrderReturn($orderInfo[0]['id'], $expressInfo);
                $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS_201, '', '', $data);
                ResourceBase::display($res);return;
            }
        }
        require_once CODE_BASE2 . '/app/mobile_client/ClientCreditProductNamespace.class.php';
        $orderId = ClientCreditProductNamespace::createOrder($this->_param['user_id'], $this->_param['product_id']);
        if ($orderId == -10) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_BUY_STRICT);
            ResourceBase::display($res);return;
        }
        if ($orderId <= 0) {
            Logger::logError('积分商城 创建订单失败。返回值：'  . $orderId . var_export($this->_param, true), 'credit.order');
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM, '', '创建订单失败。', $orderId);
            ResourceBase::display($res);return;
        }
        $data = $this->_formatOrderReturn($orderId, $expressInfo);
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS_201, '', '', $data);
        ResourceBase::display($res);return;
    }
    private function _formatOrderReturn($orderId, $expressInfo) {
        if (empty($expressInfo)) {
            return array('order_id' => $orderId);
        } else {
            return array(
                'order_id' => $orderId,
                'express_consignee' => $expressInfo[0]['express_consignee'],
                'express_phone' => $expressInfo[0]['express_phone'],
                'express_address' => $expressInfo[0]['express_address'],
            );
        }
    }

    /**
     * 取消订单
     * DELETE /api/common/shop/usercredit/:user_id/order/:order_id/
     */
    public function cancelOrderAction() {
        require_once CODE_BASE2 . '/app/mobile_client/model/credit/CreditProductOrderModel.class.php';
        $orderInfo = CreditProductOrderModel::selectOrderById($this->_param['order_id']);
        if (empty($orderInfo)) {
            Logger::logError('取消订单失败。无效订单id.' . var_export($this->_param, true), 'credit.order');
            $res = ResourceBase::formatRes(CommonErrCode::ERR_NOT_FOUND, '', '订单id 无效。');
            ResourceBase::display($res);return;
        }
        if ($orderInfo[0]['user_id'] != $this->_param['user_id']) {
            Logger::logError('取消订单，用户不匹配。' . var_export($this->_param, true), 'credit.order');
            $res = ResourceBase::formatRes(CommonErrCode::ERR_NOT_FOUND, '', '用户、订单信息不匹配。');
            ResourceBase::display($res);return;
        }
        $flag = ClientCreditProductNamespace::cancelOrder($this->_param['order_id']);
        if ($flag) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS);
            ResourceBase::display($res);return;
        } else {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM);
            ResourceBase::display($res);return;
        }
    }

    /**
     * 支付订单
     * PUT /api/common/shop/usercredit/:user_id/orders/:order_id/
     */
    public function payOrderAction() {
        //处理快递信息
        $expressInfo = array(
            'express_consignee' => $this->_param['express_consignee'],
            'express_phone' => $this->_param['express_phone'],
            'express_address' => $this->_param['express_address'],
        );
        $saveFlag = ClientUserCreditNamespace::setUserExpressInfo($this->_param['user_id'], $expressInfo);
        if (!$saveFlag) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM, '', '保存快递信息失败。');
            ResourceBase::display($res);return;
        }
        //支付订单
        $flag = ClientCreditProductNamespace::payOrder($this->_param['user_id'], $this->_param['order_id']);
        if ($flag == -5) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_PARAM, '', '积分余额不足。', -2);
            ResourceBase::display($res);return;
        }
        if ($flag == -4 || !$flag) {
            $res = ResourceBase::formatRes(CommonErrCode::ERR_SYSTEM, '', '支付失败。', $flag);
            ResourceBase::display($res);return;
        }
        $productInfo = ClientCreditProductNamespace::getProductById($flag[0]['product_id']);
        $productInfo['product_code'] = $flag[0]['product_code'];
        $productInfo['bought_time'] = time();
        $productInfo['code_expire'] = $productInfo['product_type'] == 1 ? $productInfo['bought_time'] + 7 * 24 * 3600 : $productInfo['code_expire'];
        //付上快递信息
        $data = array_merge($productInfo, $expressInfo);
        $res = ResourceBase::formatRes(CommonErrCode::ERR_SUCCESS, '', '', $data);
        ResourceBase::display($res);return;
    }
}
