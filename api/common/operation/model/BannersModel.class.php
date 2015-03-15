<?php

/**
 * @file BannersModel.class.php
 * @brief 获取运营banner操作类
 * @author lirui(lirui1@ganji.com)
 * @version 1.0
 * @date 2015-01-22
 */
include_once CODE_BASE2 . '/app/mobile_client/model/console/ClientActivityNoticeModel.class.php';
require_once CODE_BASE2 . '/app/mobile_client/model/baseConsoleModel.class.php';
require_once CLIENT_ROOT . '/api/assembly/util/AppDataBaseHelper.class.php';
require_once CLIENT_API . '/common/operation/config/OperationConfig.php';
require_once CLIENT_ROOT . '/tools/Util.php';

class BannersModel extends baseConsoleModel {

    private static $TABLE_NAME = 'client_activity_notice';

    /**
     * 缓存key前缀
     */
    private static $KEY_PREFIX = 'client_operate_banner_cache_';

    /**
     * 缓存过期时间
     * @var type 
     */
    private static $CLIENT_OPERATE_CACHE_TIME = 600;

    /**
     * 最大条数
     */
    private static $MAX_NUM = 10;

    /**
     * args 参数列表：
     * customerId 客户id
     * city_id     城市id
     * page_type   页面类型
     * category_id 大类id
     * major_category_id 小类id
     * versionId  客户端软件版本
     * post       帖子属性
     * installId  安装id
     * @param type $args
     */
    public function __construct($args = array()) {
        $this->args = $args;
    }

    public function getBannerList() {
//        if (empty($this->args['customerId'])) {
//            return false;
//        }
//        $key = self::$KEY_PREFIX . $this->args['customerId'];
//        $mc = AppDataBaseHelper::getMcHandler();
//        if ($mc) {
//            $result = $mc->read($key);
//        }
//        if (!is_array($result) || empty($result)) {
        $result = $this->getList();
//            if (false === $result) {
//                $result = array();
//            } else {
//                if ($mc && !empty($result)) {
//                    $mc->write($key, $result, self::$CLIENT_OPERATE_CACHE_TIME);
//                }
//            }
//        }
        $result = $this->availOperate($result); //筛选
        $result = $this->formatField($result);
        return $result;
    }

    /**
     * 获取banner 列表
     * @return type
     */
    private function getList() {
        $time = time();
        $sql = "SELECT * FROM " . self::$TABLE_NAME . "
                    WHERE begin_time < '{$time}'
                        AND end_time > '{$time}'
                        AND customer_id IN (-1, {$this->args['customerId']})
                        AND status = 1
                        AND show_mode = 1
                    ORDER BY id DESC";
        return DBMysqlNamespace::query($this->getDbHandler(true), $sql);
    }

    /**
     * 筛选出当前条件下（城市、页面位置、版本、渠道）的有效广告
     * @return <array>
     */
    private function availOperate($operateList) {
        if (!is_array($operateList) || empty($operateList)) {
            return array();
        }
        $operateList = $this->checkCity($operateList);
        $operateList = $this->checkPositionId($operateList);
        $operateList = $this->checkVersion($operateList);
        $operateList = $this->checkPostProperties($operateList);
        return $operateList;
    }

    /**
     * 筛选该城市下的有效广告
     * @return <array>
     */
    private function checkCity($operateList) {
        $cityId = $this->args['city_id'];
        $tempData = array();
        if (!is_array($operateList) || empty($operateList)) {
            return $tempData = array();
        }

        foreach ($operateList as $value) {
            if ($value['city_ids'] == -1) {
                $tempData[] = $value;
                continue;
            }
            $citys = explode(',', $value['city_ids']);
            if (is_array($citys) && in_array($cityId, $citys)) {
                $tempData[] = $value;
            }
        }
        return $tempData;
    }

    /**
     * 筛选
     * @return <type>
     */
    private function checkPositionId($operateList) {
        $tempData = array();
        if (!is_array($operateList) || empty($operateList)) {
            return $tempData;
        }
        foreach ($operateList as $value) {
            $positions = explode(',', $value['position_id']);
            foreach ($positions as $position) {
                $positionArr = ClientActivityNoticeModel::parsePositionId($position);
                $pageType = $positionArr['pageType'];
                $category = $positionArr['category_id'];
                $majorCategory = $positionArr['major_category_id'];
                $pagePosition = $positionArr['pagePosition'];
                if ($pageType == $this->args['page_type'] && ($category == 0 || $category == $this->args['category_id']) && ($majorCategory == 0 || $majorCategory == $this->args['major_category_id'])) {
                    $positionId = ClientActivityNoticeModel::mergePositionId(intval($this->args['page_type']), $this->args['category_id'], $this->args['major_category_id'], $pagePosition);
                    $value['position_id'] = $positionId; //当前位置的实际位置
                    $tempData[] = $value;
                    break;
                }
            }
        }
        return $tempData;
    }

    /**
     * 筛选目标版本下的有效广告
     * @return <type>
     */
    private function checkVersion($operateList) {
        $versionId = $this->args['versionId'];
        $tempData = array();
        if (!is_array($operateList) || empty($operateList)) {
            return $tempData;
        }
        foreach ($operateList as $value) {
            if ($value['client_version'] == '' || $value['client_version'] == -1) {
                $tempData[] = $value;
                continue;
            }
            $clientVersions = explode(',', $value['client_version']);
            if (clientPara::versionCompare($versionId, $clientVersions[0]) >= 0 && clientPara::versionCompare($versionId, $clientVersions[1]) <= 0) {
                $tempData[] = $value;
            }
        }
        return $tempData;
    }

    /**
     * 通过检查帖子属性来判断banner是否显示
     * @param type $operateList
     * @return type
     */
    private function checkPostProperties($operateList) {
        if (!isset($this->args['post']) || empty($this->args['post'])) {
            return $operateList;
        }
        $result = array();
        if (!is_array($operateList) || empty($operateList)) {
            return $result;
        }
        $post = $this->args['post'];
        $dbhandle = $this->getDbHandler(self::$MODEL, self::$DB_NAME, true);
        foreach ($operateList as $operate) {
            if ($operate['post_type'] == 0) {
                $result[] = $operate;
                break;
            }
            //查询帖子属性过滤
            $postType = $operate['post_type'];
            $isDisplay = true;
            $sql = SqlBuilderNamespace::buildSelectSql('client_operate_post_info', 'name,post_info', array(array('id', '=', $postType)));
            $postInfo = DBMysqlNamespace::getRow($dbhandle, $sql);
            $properys = json_decode($postInfo['post_info'], true);
            if (is_array($properys)) {
                foreach ($properys as $properyK => $properyV) {
                    if (!isset($post[$properyK]) //
                            || $post[$properyK] != $properyV) {
                        $isDisplay = false;
                        break;
                    }
                }
            }
            if ($isDisplay) {
                $result[] = $operate;
            }
        }
        return $result;
    }

    /**
     * 格式化需要下发的字段
     * @param type $operateList
     */
    private function formatField($operateList) {
        $result = array();
        foreach ($operateList as $operate) {
            $tmp['id'] = $operate['id'];
            $tmp['banner_type'] = $operate['banner_type'];
            //设置基础数据
            switch ($tmp['banner_type']) {
                case OperationConfig::BANNER_TYPE_IMAGE :
                    $tmp['base']['img_url'] = Util::formatActivityImageUrl($operate['img_url']);
                    break;
                case OperationConfig::BANNER_TYPE_IMAGE_TXT:
                    $tmp['base']['img_url'] = Util::formatActivityImageUrl($operate['img_url']);
                    $tmp['base']['title'] = $operate['title'];
                    break;
                case OperationConfig::BANNER_TYPE_TXT:
                    $tmp['base']['title'] = $operate['title'];
                    $tmp['base']['content'] = $operate['content'];
                    break;
            }
            $tmp['jump_url'] = $operate['jump_url'];
            $tmp['jump_data'] = $operate['jump_data'];
            $tmp['icon_url'] = $operate['icon_url'];
            $tmp['loop_time'] = $operate['loop_time'];
            $tmp['begin_time'] = $operate['begin_time'];
            $tmp['end_time'] = $operate['end_time'];
            $tmp['open_mode'] = $operate['open_mode'];
            $tmp['is_login'] = $operate['is_login'];
            $positionArr = ClientActivityNoticeModel::parsePositionId($operate['position_id']);
            $pagePosition = $positionArr['pagePosition'];
            $tmp['position'] = $pagePosition;
            $result[] = $tmp;
        }
        return $result;
    }

}
