<?php
/**
 * Stock 用户操作名空间
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/user/model/StockUserInfoModel.class.php';

class StockUserNamespace {
    const USER_VERIFY_COOKIE_KEY = 'verify_user';   //方便js api ajax 获取cookie跟主站一致

    public static function getUserInfoById($uid) {
        $userInfo = StockUserInfoModel::selectUserInfo($uid, 'uid');
        if (empty($userInfo))
            return false;
        return $userInfo[0];
    }

    public static function getUserInfoByName($username) {
        $userInfo = StockUserInfoModel::selectUserInfo($username);
        if (empty($userInfo))
            return false;
        return $userInfo[0];
    }

    //设置用户数据
    public static function setUserInfo($id, $data) {
        $userInfo = StockUserInfoModel::selectUserInfo($id, 'uid');
        if (empty($userInfo))
            return false;
        $updateData = array();
        //只做变动更新
        foreach ($userInfo[0] as $k => $v) {
            if (isset($data[$k]) && !empty($data[$k]) && $data[$k] != $userInfo[0][$k]) {
                $updateData[$k] = $data[$k];
            }
        }
        if (empty($updateData))
            return true;
        $updateData['active_time'] = time();
        return StockUserInfoModel::updateUserInfo($id, $updateData);
    }

    //根据cookie 明文获取对应用户信息
    public static function getCookieUser($cookieP) {
        $cookieData = self::_splitVerifyCookie($cookieP);
        if (!empty($cookieData) && $cookieData['expire'] > time()) {
            $userinfo = self::getUserInfoById($cookieData['uid']);
            if (!empty($userinfo)) {
                return $userinfo;
            }
        }
        return false;
    }

    //解密后的cookie 明文
    private static function _splitVerifyCookie($cookieValue) {
        preg_match('/^(.*)_([\d]{8,})$/', $cookieValue, $dataArr);     //cookie 数据本身加到期时间戳，防止抓取伪造, 格式为admintest_1428422400
        if (empty($dataArr) || empty($dataArr[1])) {
            return false;
        }
        return array(
            'uid' => $dataArr[1],
            'expire' => $dataArr[2],
        );
    }
}
