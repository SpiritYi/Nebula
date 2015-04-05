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

    public static function setUserInfo($id, $data) {
        $userInfo = UserInfoModel::selectUserInfoById($id);
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
        return UserInfoModel::updateUserInfo($id, $updateData);
    }

    //根据cookie 明文获取对应用户信息
    public function getCookieUser($cookieP) {
        $cookieData = self::_splitVerifyCookie($cookieP);
        if (!empty($cookieData) && $cookieData['expire'] > time()) {
            $userinfo = UserNamespace::getUserInfo($cookieData['username']);
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
            'username' => $dataArr[1],
            'expire' => $dataArr[2],
        );
    }
}
