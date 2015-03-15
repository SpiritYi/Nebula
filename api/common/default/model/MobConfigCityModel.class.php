<?php
/**
 * 客户端城市数据版本配置
 * db: mob.mobile_app_config.mobile_config_city
 * @author chenyihong <chenyihong@ganji.com>
 * @version 2015/01/05
 * @copyright ganji.com
 */

require_once CLIENT_ROOT . '/api/assembly/util/AppDataBaseHelper.class.php';

class MobConfigCityModel {
    private static $_CITY_VERSION_CONFIG_MCKEY = 'city_version_config_%d';
    //获取城市数据的版本
    public static function getCityVersionConfig($cityId) {
        if (empty($cityId)) {
            return false;
        }
        $mcKey = sprintf(self::$_CITY_VERSION_CONFIG_MCKEY, $cityId);
        //保存到缓存
        $mcHandle = AppDataBaseHelper::getMcHandler();

        if ($mcHandle) {
            $mcRes = $mcHandle->read($mcKey);
            if (!empty($mcRes)) {
                return $mcRes;
            }
        }
        $handle = AppDataBaseHelper::getDbHandler('MOBILE', 'mobile_app_config');
        $sqlString = SqlBuilderNamespace::buildSelectSql('mobile_config_city', array('city_id', 'cityScriptIndex', 'version', 'isxiaoqu', 'cn'),
                array(array('city_id', '=', $cityId)));
        $result = DBMysqlNamespace::query($handle, $sqlString);
        //缓存10分钟
        if (!empty($result)) {
            $mcHandle->write($mcKey, $result, 600);
        }
        return $result;
    }
}
