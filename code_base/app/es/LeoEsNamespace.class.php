<?php
/**
 * Leo 项目es 操作namespace
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/24
 * @copyright nebula-fund.com
 */

require_once CODE_BASE . '/util/http/EsUtil.class.php';

class LeoEsNamespace {

    //获取es 数据存储库
    private static function _getEsUrl() {
        $index = DBConfig::IS_DEV_ENV ? 'leonebula-test' : 'leonebula';
        $esUrl = sprintf('http://es.nebula-fund.com/%s/backend/', $index);
        return $esUrl;
    }

    public static function addRecord($id, $data) {
        if (empty($data) || !is_array($data)) {
            return false;
        }
        $url = self::_getEsUrl() . "{$id}";
        $res = EsUtil::curlRequest($url, 'POST', $data);
        return !empty($res['_shards']['successful']) ? true : false;
    }

    public static function delRecord($id) {
        if (empty($id)) {
            return false;
        }
        $url = self::_getEsUrl() . "{$id}";
        $res = EsUtil::curlRequest($url, 'DELETE');
        return !empty($res['_shards']['successful']) ? true : false;
    }

    /**
     * 更新es 数据
     * @param $id
     * @param array $newData        //key -> value, 要更新的字段名, 新数值
     * @return array
     */
    public static function updateRecord($id, $newData) {
        if (empty($newData) || !is_array($newData)) {
            return false;
        }
        $newData['update_t'] = time();
        $url = self::_getEsUrl() . "{$id}/_update";
        $res = EsUtil::curlRequest($url, 'POST', ['doc' => $newData]);
        return !empty($res['_shards']['successful']) ? true : false;
    }
}