<?php
/**
 * 用户信息namespace
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/27
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/app/user/model/UserInfoModel.class.php';

class UserNamespace {
    const USER_VERIFY_COOKIE_KEY = 'verify_user';

    public static function getUserInfo($username) {
        $userInfo = UserInfoModel::selectUserInfoByName($username);
        if (empty($userInfo))
            return false;
        return $userInfo[0];
    }

    public static function setUserInfo($data) {
        return UserInfoModel::updateUserInfo($data['id'], $data);
    }

    //解密后的cookie 明文
    public static function splitVerifyCookie($cookieValue) {
        preg_match('/^(.*)_([\d]{8,})$/', $cookieValue, $dataArr);     //cookie 数据本身加到期时间戳，防止抓取伪造, 格式为admintest_1428422400
        if (empty($dataArr) || empty($dataArr[1])) {
            return false;
        }
        return array(
            'username' => $dataArr[1],
            'expire' => $dataArr[2],
        );
    }
}
