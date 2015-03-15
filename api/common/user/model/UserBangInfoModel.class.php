<?php

/**
 * 获取用户的帮帮属性model，暂时供帮帮赶集叮咚使用
 * @author jiajianming@ganji.com
 * @since 2014-06-11
 * @copyright Copyright (c) 2005-2014 GanJi(http://www.ganji.com)
 */
require_once CODE_BASE2 . '/interface/uc/UserPostInterface.class.php';
require_once CODE_BASE2 . '/interface/uc/UserBizInterface.class.php';
require_once CODE_BASE2 . '/app/user2/UserNamespace.class.php';
require_once CODE_BASE2 . '/app/wanted_findjob/JobCrmNamespace.class.php';
require_once CODE_BASE2 . '/app/bang/BangNamespace.class.php';
require_once CODE_BASE2 . '/app/housing/interface/CustomerAccountNamespace.class.php';

class UserBangInfoModel {

    public static $WANTED_BANG = 'wanted';
    public static $ERSHOUCHE_BANG = 'ershouche';
    public static $SERVICE_STORE_BANG = 'servicestore';
    public static $FANG_BANG = 'fang';

    /**
     * 获得用户的帮帮属性
     * @param <int> $userId 用户的ucId
     * @return <array> ;
     */
    public static function getUserBangList($userId) {
        $bangList[self::$WANTED_BANG] = self::_isWantedBang($userId) ? 1 : 0;
        $bangList[self::$ERSHOUCHE_BANG] = self::_isErshoucheBang($userId) ? 1 : 0;
        $bangList[self::$SERVICE_STORE_BANG] = self::_isServiceStoreBang($userId) ? 1 : 0;
        $bangList[self::$FANG_BANG] = self::_isFangBang($userId) ? 1 : 0;
        return $bangList;
    }

    /**
     * 是否为招聘帮帮
     * @param <int> $userId 登录用户的ucid
     * @return <bool> true：是，false：不是
     */
    private static function _isWantedBang($userId) {
        $res = false;
        $res = JobCrmNamespace::IsBangbangUser($userId);
        return $res;
    }

    /**
     * 是否为二手车辆帮帮
     * @param <int> $userId 登录用户的ucid
     * @return <bool> true：是，false：不是
     */
    private static function _isErShouCheBang($userId) {
        $userId = intval($userId);

        if ($userId < 0) {
            return false;
        }

        require_once CODE_BASE2 . '/app/bang/model/BangCompanyModel.class.php';
        $bangInfo = BangCompanyModel::getRow('*', "user_id={$userId} and category_id=6");

        //存在帮帮店铺且店铺不是停用状态
        return (!empty($bangInfo) && $bangInfo['status'] != 3);
    }

    /**
     * 是否为服务店铺帮帮
     * @param <int> $userId 登录用户的ucid
     * @return <bool> true：是，false：不是
     */
    private static function _isServiceStoreBang($userId) {
        $res = false;
        $res = BangNamespace::isActiveAccountByUserId($userId, 5);
        //todo
        return $res;
    }

    /**
     * 是否为房产帮帮
     * @param <int> $userId 登录用户的ucid
     * @return <bool> true：是，false：不是
     */
    private static function _isFangBang($userId) {
        $userId = intval($userId);
        if ($userId <= 0) {
            return false;
        }
        $res = CustomerAccountNamespace::getAccountIdByUserId($userId);
        return !empty($res);
    }

    /*
     * 通过用户最新的一条帖子，根据帖子类型判断，帖子所属的帮帮类型
     * @param string $userId
     */

    public static function judgePostBang($userId) {
        $result = array();
        $result[self::$WANTED_BANG] = 0;
        $result[self::$ERSHOUCHE_BANG] = 0;
        $result[self::$SERVICE_STORE_BANG] = 0;
        $result[self::$FANG_BANG] = 0;
        $postRes = UserPostInterface::getPostList($userId, 0, 1, 100, 1);
        $postArr = $postRes['item_list'];
        if (!empty($postArr)) {
            $post = $postArr[0];
            $categoryId = $post['category_id'];
            switch ($categoryId) {
                case 2:
                case 3:
                    $result[self::$WANTED_BANG] = 1;
                    break;
                case 6:
                    //二手车
                    if ($post['major_category_id'] == 14) {
                        $result[self::$ERSHOUCHE_BANG] = 1;
                    }
                    break;
                case 4:
                case 5:
                    $result[self::$SERVICE_STORE_BANG] = 1;
                    break;
            }
        }
        return $result;
    }
}