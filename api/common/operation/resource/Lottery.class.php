<?php
/**
 * 运营抽奖活动：群聊推广
 * @author chenwei5 <chenwei5@ganji.com>
 * @version 2015/01/17
 * @copyright ganji.com
 */

require_once realpath(CLIENT_API . '/common/operation/model/LotteryModel.class.php');

class Lottery extends ResourceBase {
    public function setUriMatchConfig() {
        return array(
            '/lottery/:user_id/' => array(
                'GET' => 'getMasterData', //获取首页数据
            ),
            '/lottery/:user_id/products/' => array(
                'GET' => 'getUserProductList', //获取用户奖品列表
                'POST' => 'runLottery', //抽奖
            ),
            '/lottery/:user_id/register/' => array(
                'POST' => 'enrollBenefits', //群主福利，用户报名
            ),
            '/lottery/:user_id/userinfo/' => array(
                'GET' => 'readUserInfo', //获取用户信息
                'POST' => 'createUserInfo', //保存用户信息
                'PUT' => 'updateUserInfo', //修改用户信息
            ),
            '/lottery/:user_id/pinchepv' => array(
                'GET' => 'getPinChePv', //获取拼车pv
            ),
        );
    }
    
    public function setParamConfig() {
        return array(
            'getMasterData' => array(
            ),
            'getUserProductList' => array(
                'page' => 'int',
            ),
            'runLottery' => array(
            ),
            'readUserInfo' => array(
            ),
            'createUserInfo' => array(
                'express_consignee' => 'string',
                'express_phone' => 'string',
                'express_address' => 'string',
            ),
            'updateUserInfo' => array(
                'express_consignee' => 'string',
                'express_phone' => 'string',
                'express_address' => 'string',
            ),
            'enrollBenefits' => array(
                'benefits_type' => 'int',
            ),
            'getPinChePv' => array(
            ),
        );
    }
    public function checkParam($paramKey) {
        switch ($paramKey) {
            case 'benefits_type':
                if (!in_array($this->_param[$paramKey], array(1, 2))) {
                    return ResourceBase::formatRes(CommonErrCode::ERR_URI_NOT_FOUND, '', 'benefits_type error.');
                }
                break;
        }
        return true;
    }

    public function getMasterDataAction(){
        $userId = $this->URI_DATA['user_id'];
        if(!$this->_authUser($userId)){
            $userId = 0;
        }
        $taskTimes = LotteryModel::getTaskTimes($userId);
        $availableLotteryTimes = LotteryModel::getUserAvailableLotteryTimes($userId);
        $userEnrollStatus = LotteryModel::getUserEnrollStatus($userId);
            
        //$allProductList = LotteryModel::getAllProductList();
        $recentWinningUserList = LotteryModel::getRecentWinningUserList();
        
        $data = array(
            'task_times' => $taskTimes,
            'lottery_times' => $availableLotteryTimes,
            'benefits_status' => $userEnrollStatus,
            //'product_list' => $allProductList,
            'winning_list' => $recentWinningUserList,
        );
        $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );
        self::display( $res ); return;
    }
    
    /**
     * 获取用户摇中奖品列表
     * @return type
     */
    public function getUserProductListAction() {
        $userId = $this->URI_DATA['user_id'];
        if (!$this->_authUser($userId)) {
            $res = self::formatRes(CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display($res);return;
        }
        $page = empty($this->_param['page']) ? 1 : $this->_param['page'];
        
        $data = array();
        $data = LotteryModel::getUserProductList($userId,$page);
        $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );
        self::display( $res ); return;
    }
    
    /**
     * 摇奖
     * @return type
     */
    public function runLotteryAction() {
        $userId = $this->URI_DATA['user_id'];
        if (!$this->_authUser($userId)) {
            $res = self::formatRes(CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display($res);return;
        }
        $availableLotteryTimes = LotteryModel::getUserAvailableLotteryTimes($userId);
        $data = array();
        if($availableLotteryTimes > 0){
            $data['price'] = LotteryModel::runLottery($userId);
            $availableLotteryTimes = LotteryModel::getUserAvailableLotteryTimes($userId);
        }
        if ($data['price']) {
            $data['lottery_times'] = $availableLotteryTimes;
            $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '', $data);
            self::display($res);return;
        } else {
            $res = self::formatRes(CommonErrCode::ERR_SYSTEM, '', '没有摇奖机会！', $data);
            self::display($res);return;
        }
    }
    
    /**
     * 报名群主福利
     * @return type
     */
    public function enrollBenefitsAction(){
        $type = $this->_param['benefits_type'];
        
        $userId = $this->URI_DATA['user_id'];
        if (!$this->_authUser($userId)) {
            $res = self::formatRes(CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display($res);return;
        }
        $flags = LotteryModel::getUserEnrollStatus($userId);
        if($flags[$type]){
            $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '');
            self::display($res);return;
        }
        $groups = LotteryModel::getUserGroups($userId);
        if(empty($groups)){
            $res = self::formatRes(CommonErrCode::ERR_GROUP_OWNER, '', '');
            self::display($res);return;
        }
            
        $data = LotteryModel::enrollBenefits($userId, $type, $groups);
        if ($data) {
            $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '');
            self::display($res);return;
        } else {
            $res = self::formatRes(CommonErrCode::ERR_SYSTEM, '', '');
            self::display($res);return;
        }
    }
    
    /**
     * 获取用户收货地址信息
     * @return type
     */
    public function readUserInfoAction(){
        $userId = $this->URI_DATA['user_id'];
        if(!$this->_authUser($userId)){
            $res = self::formatRes( CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display( $res ); return;
        }
        
        $data = array();
        $data = LotteryModel::getUserCredit($userId);
        $res = self::formatRes( CommonErrCode::ERR_SUCCESS, '', '', $data );
        self::display( $res ); return;
    }
    
    /**
     * 保存用户收货地址信息
     * @return type
     */
    public function createUserInfoAction() {
        $userId = $this->URI_DATA['user_id'];
        if (!$this->_authUser($userId)) {
            $res = self::formatRes(CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display($res);return;
        }

        $data = array();
        $data['user_id'] = $this->URI_DATA['user_id'];
        $data['express_consignee'] = $this->_param['express_consignee'];
        $data['express_phone'] = $this->_param['express_phone'];
        $data['express_address'] = $this->_param['express_address'];

        $data = LotteryModel::saveUserRecord($data);
        if ($data) {
            $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '');
            self::display($res);return;
        } else {
            $res = self::formatRes(CommonErrCode::ERR_SYSTEM, '', '');
            self::display($res);return;
        }
    }

    /**
     * 修改用户收货地址信息
     * @return type
     */
    public function updateUserInfoAction() {
        $userId = $this->URI_DATA['user_id'];
        if (!$this->_authUser($userId)) {
            $res = self::formatRes(CommonErrCode::ERR_CHECK_LOGIN, '', '');
            self::display($res); return;
        }

        $data = array();
        $data['user_id'] = $this->URI_DATA['user_id'];
        $data['express_consignee'] = $this->_param['express_consignee'];
        $data['express_phone'] = $this->_param['express_phone'];
        $data['express_address'] = $this->_param['express_address'];

        $data = LotteryModel::saveUserRecord($data);
        if ($data) {
            $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '');
            self::display($res);return;
        } else {
            $res = self::formatRes(CommonErrCode::ERR_SYSTEM, '', '');
            self::display($res);return;
        }
    }

    /**
     * 获取运营拼车活动页面pv
     * @return type
     */
    public function getPinChePvAction(){
        $data = LotteryModel::getPinChePv();
        $res = self::formatRes(CommonErrCode::ERR_SUCCESS, '', '',array('pv'=>$data));
        self::display($res);return;
    }

    /**
     * 校验当前请求的用户是否登录
     * @param int $userId 用户id
     * @return boolean 登录返回true，未登录返回false
     */
    private function _authUser($userId = 0) {
        $token = clientPara::getArg('token');
        if (empty($userId)) {
            $userId = $this->URI_DATA['user_id'];
        }

        $flag = clientPara::auth_check($userId, $token);
        return $flag;
    }
}