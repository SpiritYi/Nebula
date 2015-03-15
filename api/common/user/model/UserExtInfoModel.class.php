<?php
/**
 * 客户端用户扩展信息model
 * @author jiajianming@ganji.com
 * @since 2013-07-31
 * @copyright Copyright (c) 2005-2014 GanJi(http://www.ganji.com)
 */
require_once CODE_BASE2 . '/interface/pay/PayCenterInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserPostInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserFavoriteInterface.class.php';
require_once CODE_BASE2 . '/app/wanted_findjob/TuiguangNamespace.class.php';
require_once CODE_BASE2 . '/interface/jobcenter/UserDownloadPointsInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserBizInterface.class.php';
require_once CODE_BASE2 . '/app/user2/UserNamespace.class.php';
require_once CODE_BASE2 . '/interface/jobcenter/ReceiveInterviewInterface.class.php';
require_once CODE_BASE2 . '/interface/webim/WebImInterface.class.php';
require_once CLIENT_API . '/common/user/model/UserBangInfoModel.class.php';

class UserExtInfoModel {
    /**
     * 获得用户的扩展信息
     * @param <int> $userId 用户的ucId
     * @return <array> $extInfo;
     */
    public function getExtUserInfo($userId) {
        $extInfo = array();
        //自助置顶用户账户余额
        $account = self::getUserAccount($userId);
        //我发布的帖子数
        $res = UserPostInterface::getPostList($userId, 0, 0, 100, 3);
        $postNum = $res['count'] ? (int)$res['count'] : 0;
        //我的收藏数
        $res = UserFavoriteInterface::getFavoriteList($userId, 0, 0, 3);
        $favoriteNum = $res['count'] ? (int)$res['count'] : 0;

        //获得用户剩余下载简历点数
        $remainResumeNum = self::_getDownloadResumeCount($userId);

        //获取用户imId（群聊Id）
        $res  = WebImInterface::getImIdByUserId( $userId );
        $imId = ( is_array($res) && isset($res['imId']) ) ? $res['imId'] : 0;

        //获得用户的B属性（商户属性）
        $bizInfo = UserBizInterface::getBizTypeList($userId);
        $extInfo = array(
                'account'           => ($account->balance/100),
                'post_num'          => $postNum,
                'fav_num'           => $favoriteNum,
                'remain_resume_num' => $remainResumeNum,
                'biz_info'          => self::_formateBizType($bizInfo),
                'im_id'             => $imId,
        );
        if (in_array(HeaderCheck::$param['customerId'],array(860,730))) {
            $extInfo['interviewUnreadCount'] = self::_getInterviewUnreadCount($userId);
        }
        if(in_array(HeaderCheck::$param['customerId'],array(785,885))) {
            //赶集叮咚帮帮
            $bangList = UserBangInfoModel::getUserBangList($userId);
            if(array_search(1, $bangList)) {
                $bang = $bangList;
            } else {
                $bang = UserBangInfoModel::judgePostBang($userId);
            }
            $extInfo['bang'] = $bang;
        }
        return $extInfo;
    }
    /*
	* 通过用户ID得到用户还能下载多少份简历
	* @param string $userId
	* add by wanghao <wanghao3@ganji.com>
	*/
	private function _getDownloadResumeCount($userId) {
		if (empty($userId)) {
			return 0;
		} else {
			//获取用户的免费以及推广后台下载点
			$ret = TuiguangNamespace::getResumeDownloadCountByUserId($userId);
			$count1 = ( !isset($ret['cauuse']) || empty($ret['canuse']) ) ? 0 : $ret['canuse'];
			//获取会员中心可以下载的量
			$ret = UserDownloadPointsInterface::getUserDownloadPointsByUserId($userId);
			$count2 = ($ret['ret'] == -1)? 0 : $ret['ret']['download_points'];
			//下载量是他们的和
			return $count1 + $count2;
		}
		return 0;
	}
    /**
     * 格式化biz属性，目前客户端只有房产后台与服务店铺两种
     * @param <array> $bizInfo
     * return <array> $formatData
     */
    private function _formateBizType($bizInfo) {
        $formatData = array (
            'housing_back' => 0,//房产后台
            'fuwu_dian'    => 0,//服务店铺
        );
        if(!empty($bizInfo)) {
            foreach ($bizInfo as $v) {
                switch ($v) {
                    case UserNamespace::BIZ_TYPE_HOUSING :
                        $formatData['housing_back'] = 1;
                        break;
                    case UserNamespace::BIZ_TYPE_FUWU_DIAN :
                        $formatData['fuwu_dian'] = 1;
                        break;
                }
            }
        }
        return $formatData;
    }

    /**
    * @brief: 获取用户收到的面试邀请数量
    *
    * @param @user_id 用户id
    * @return 用户收到的面试邀请数量
    */
    private function _getInterviewUnreadCount($user_id){/*{{{*/
        //初始化返回总数
        $unread_total = 0;
        //获得面试邀请的总数已经未读数
        $data = ReceiveInterviewInterface::getUserReceiveInterviewCount($user_id,-1,-1,-1,false,true);
        //出现错误,直接返回0
        if(empty($data['errMsg'])){
            $unread_total = $data['ret'];
        }
        //返回结果
        return (int)$unread_total;
    }/*}}}*/

    public static function getUserAccount($user_id, $channelId = 14) {
        $key = 'mklloytr4wvzxaqrfnb8352fcnxlkfof';
        $params = new stdClass();
        $params->op = "get_account";
        $params->user_id = $user_id;
        $params->channel  = $channelId;
        $sign = md5(json_encode($params) .$key);
        return PayCenterInterface::getAccount($params, $sign);
    }
}
