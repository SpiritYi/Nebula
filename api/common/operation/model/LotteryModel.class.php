<?php

/**
 * 运营抽奖活动：群聊推广
 * @author chenwei5 <chenwei5@ganji.com>
 * @version 2015/01/17
 * @copyright ganji.com
 */
require_once CODE_BASE2 . '/../mobile_client/tools/ClientRedisCache.class.php';
require_once CODE_BASE2 . '/app/mobile_client/ClientUserCreditNamespace.class.php';
require_once CODE_BASE2 . '/app/mobile_client/ClientCreditProductNamespace.class.php';
require_once CODE_BASE2 . '/interface/webim/WebImInterface.class.php';

class LotteryModel {

    /**
     * 对应RedisConfig统一配置里，对应业务服务的配置变量名称
     * @var <string>
     */
    private static $configName = 'GROUP_APPCREDIT';

    /**
     * redis链接句柄
     * @var <type>
     */
    private static $redisHandler;
    public static $MAX_TIMES = 30;
    public static $DAY_MAX_TIMES = 6;
    public static $ONE_TIMES = 3;
    private static $_LOTTERY_BENEFITS_REDIS_KEY = 'app_lottery_benefits'; //群主福利报名
    private static $_LOTTERY_TIMES_TOTAL_REDIS_KEY = 'app_lottery_times_total'; //抽奖总次数
    private static $_LOTTERY_TIMES_DAY_TOTAL_REDIS_KEY = 'app_lottery_times_total_%s'; //每天可用抽奖总次数
    private static $_LOTTERY_TIMES_DAY_USED_REDIS_KEY = 'app_lottery_times_used_%s'; //每天已用抽奖次数
    private static $_LOTTERY_REDIS_KEY_EXPIREAT = '2015-03-25 23:59:59'; //redis 缓存过期时间
    private static $_LOTTERY_START = '2015-01-20 00:00:00'; //活动开始时间
    private static $_LOTTERY_END = '2015-02-25 23:59:59'; //活动结束时间
    private static $_WINNING_CONTENT_TEMPLATE = '%s摇中了%s！'; //最近中奖滚动文案模板，xx用户摇中了xx奖品！
    private static $_LOTTERY_RECENT_WINNING_LIST_REDIS_KEY = 'app_lottery_recent_winning_list';

    /**
     * 获取redis 链接句柄
     * @param sting $configName
     * @return object
     */
    public static function getRedisHandler($configName = 'GROUP_APPCREDIT') {
        if (empty(self::$redisHandler[$configName])) {
            ClientRedisCache::setRedisConfig($configName);
            self::$redisHandler[$configName] = ClientRedisCache::getRedisHandler();
        }
        return self::$redisHandler[$configName];
    }

    /**
     * 获取用户可用抽奖次数
     * @param type $userId
     * @return int
     */
    public static function getUserAvailableLotteryTimes($userId) {
        if (!is_numeric($userId) || $userId <= 0) {
            return 0;
        }
        $totalRedisKey = self::$_LOTTERY_TIMES_TOTAL_REDIS_KEY;
        $dayTotalRedisKey = sprintf(self::$_LOTTERY_TIMES_DAY_TOTAL_REDIS_KEY, date('Ymd'));
        $dayUsedlRedisKey = sprintf(self::$_LOTTERY_TIMES_DAY_USED_REDIS_KEY, date('Ymd'));
        
        $redis = self::getRedisHandler();
        $total = (int) $redis->hget($totalRedisKey, $userId);
        $dayTotal = (int) $redis->hget($dayTotalRedisKey, $userId);
        $dayUsed = (int) $redis->hget($dayUsedlRedisKey, $userId);
        self::_expireAt($redis, $totalRedisKey, strtotime(self::$_LOTTERY_REDIS_KEY_EXPIREAT));
        self::_expireAt($redis, $dayTotalRedisKey, strtotime(date('Y-m-d') . ' 23:59:59'));
        self::_expireAt($redis, $dayUsedlRedisKey, strtotime(date('Y-m-d') . ' 23:59:59'));
        if ($dayTotal < self::$DAY_MAX_TIMES) {
            $tmpTimes = self::_getTimes($userId);
            $tmpTimes = $tmpTimes >= self::$DAY_MAX_TIMES ? self::$DAY_MAX_TIMES : ($tmpTimes >= $dayTotal ? $tmpTimes : $dayTotal);
            if ($tmpTimes > $dayTotal) {
                $redis->hSet($dayTotalRedisKey, $userId, $tmpTimes);
                $dayTotal = (int) $redis->hget($dayTotalRedisKey, $userId);
            }
        }
        if ($total < self::$MAX_TIMES && $dayTotal <= self::$DAY_MAX_TIMES && $dayUsed < $dayTotal) {
            return $dayTotal - $dayUsed;
        }
        return 0;
    }
    
    /**
     * 获取用户完成的有效任务数
     * @param int $userId
     * @return int
     * @throws type
     */
    public static function getTaskTimes($userId){
        $result = 0;
        try {
            if (!is_numeric($userId) || $userId <= 0) {
                throw new Exception("用户id错误");
            }
            $totalRedisKey = self::$_LOTTERY_TIMES_TOTAL_REDIS_KEY;
            $dayTotalRedisKey = sprintf(self::$_LOTTERY_TIMES_DAY_TOTAL_REDIS_KEY, date('Ymd'));
            $redis = self::getRedisHandler();
            $total = (int) $redis->hget($totalRedisKey, $userId);
            $dayTotal = (int) $redis->hget($dayTotalRedisKey, $userId);
            
            if ($total < self::$MAX_TIMES && $dayTotal <= self::$DAY_MAX_TIMES) {
                $result = ceil($dayTotal / self::$ONE_TIMES);
            }else{
                $result = 2;
            }
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
        }
        return 2 - $result;
    }

    /**
     * 设置redis key的过期时间
     * @param object $redis
     * @param string $key
     * @param int $time
     * @return int
     */
    private static function _expireAt($redis, $key, $time) {
        if ($redis->ttl($key) == -1) {
            $redis->expireAt($key, $time);
        }
        return $redis->ttl($key);
    }

    /**
     * 获取用户的抽奖次数
     * @param int $userId
     * @return int
     */
    private static function _getTimes($userId) {
        $beginTime = strtotime(date("Y-m-d") . ' 00:00:00');
        $endTime = time();
        $result = WebImInterface::getJoinGroupTimes($userId, $beginTime, $endTime);
        return (int) self::$ONE_TIMES * $result;
    }

    /**
     * 获取用户群主福利活动报名状态
     */
    public static function getUserEnrollStatus($userId) {
        if (!is_numeric($userId) || $userId <= 0) {
            return array(
                '1' => false,
                '2' => false,
            );
        }
        $redis = self::getRedisHandler();
        $jsonString = $redis->hget(self::$_LOTTERY_BENEFITS_REDIS_KEY, $userId);
        $arr = json_decode($jsonString, true);

        $one = false;
        $two = false;

        if (is_array($arr) && !empty($arr)) {
            $one = !empty($arr['1']);
            $two = !empty($arr['2']);
        }

        $result = array(
            '1' => $one,
            '2' => $two,
        );

        return $result;
    }

    /**
     * 获取用户的群组信息
     * @param int $userId
     * @return array
     */
    public static function getUserGroups($userId) {
        $result = WebImInterface::getGroupAndmemberGroupCount($userId);
        return is_array($result) ? $result : array();
    }

    /**
     * 群主福利报名
     * @param int $userId
     * @param int $type
     * @param array $groups
     * @return boolean
     */
    public static function enrollBenefits($userId, $type, $groups) {
        if (!is_numeric($userId) || $userId <= 0) {
            return false;
        }
        $redis = self::getRedisHandler();
        $jsonString = $redis->hget(self::$_LOTTERY_BENEFITS_REDIS_KEY, $userId);
        $jsonArray = json_decode($jsonString, true);
        $jsonArray = is_array($jsonArray) ? $jsonArray : array();
        $jsonArray[$type] = $groups;
        $redis->hSet(self::$_LOTTERY_BENEFITS_REDIS_KEY, $userId, json_encode($jsonArray));
        self::_expireAt($redis, self::$_LOTTERY_BENEFITS_REDIS_KEY, strtotime(self::$_LOTTERY_REDIS_KEY_EXPIREAT));
        return true;
    }

    /**
     * 获取所有奖品列表
     */
    public static function getAllProductList() {
        $productsList = CreditProductModel::getLotteryProductList();
        return $productsList;
    }

    /**
     * 获取用户摇中的奖品列表
     */
    public static function getUserProductList($userId,$page) {
        $size = 5;
        $detailList = ClientUserCreditNamespace::getUserProductList($userId, 1);
        $tmp = array();
        foreach($detailList as $item){
            $tmp[$item['inventory_id']] = $item;
        }
        sort($tmp);
        $start = ($page - 1) * $size;
        $end = $page * $size;
        $result = array();
        foreach($tmp as $key => $item){
            if($start<=$key && $key<$end){
                $result[] = $item;
            }
        }
        
        return array(
            'total_page' =>ceil(count($tmp)/$size),
            'page' => $page,
            'list' => $result,
        );
    }

    /**
     * 获取最近中奖用户列表
     */
    public static function getRecentWinningUserList() {
        $productsList = self::_getRecentWinningList();
        $nameArr = array(
            '2015-02-05' => '清晨有风',
            '2015-02-06' => '清晨有风',
            '2015-02-07' => 'pengz89',
            '2015-02-08' => 'pengz89',
            '2015-02-09' => '徐晗昕',
            '2015-02-10' => '徐晗昕',
            '2015-02-11' => '假设的人生',
            '2015-02-12' => '假设的人生',
            '2015-02-13' => '晨晨',
            '2015-02-14' => '晨晨',
            '2015-02-15' => '吉祥四宝宝',
            '2015-02-16' => '吉祥四宝宝',
            '2015-02-17' => '火锅里挣扎的鱼',
            '2015-02-18' => '火锅里挣扎的鱼',
            '2015-02-19' => '灰太狼的人生',
            '2015-02-20' => '灰太狼的人生',
            '2015-02-21' => '变形花道',
            '2015-02-22' => '变形花道',
            '2015-02-23' => '午饭君是个渣',
            '2015-02-24' => '午饭君是个渣',
            '2015-02-25' => '清晨有风',
            '2015-02-26' => '清晨有风',
            '2015-02-27' => 'pengz89',
        );
        $day = date('Y-m-d');
        $productsList[0] = empty($nameArr[$day]) ? '清晨有风摇中了iphone6！' : $nameArr[$day] . '摇中了iphone6！';
        return $productsList;
    }

    /**
     * 保存用户收货地址信息
     */
    public static function saveUserRecord($data) {
        if ($data['user_id'] <= 0) {
            return false;
        }
        return ClientUserCreditNamespace::setUserExpressInfo($data['user_id'], $data);
    }

    /**
     * 获取用户收货地址信息
     */
    public static function getUserCredit($loginId) {
        $userCredit = ClientUserCreditNamespace::getUserExpressInfo($loginId);
        $result = self::_formatUserCredit($userCredit[0]);
        return $result;
    }

    //格式化用户地址信息
    private static function _formatUserCredit($userCredit) {
        $result = array(
            'express_consignee' => $userCredit['express_consignee'],
            'express_phone' => $userCredit['express_phone'],
            'express_address' => $userCredit['express_address'],
        );
        return $result;
    }

    /**
     * 运行摇奖，记录日志，扣除摇奖次数
     * @param type $userId
     * @return boolean
     */
    public static function runLottery($userId) {
        if (!is_numeric($userId) || $userId <= 0) {
            return false;
        }
        $productId = self::_lotteryOne();
        if(empty($productId)){
            $productId = self::_lotteryTwo();
        }
        $flag = ClientCreditProductNamespace::giveLotteryProduct($userId, $productId);
        if ($flag === true) {
            $redis = self::getRedisHandler();
            $dayUsedlRedisKey = sprintf(self::$_LOTTERY_TIMES_DAY_USED_REDIS_KEY, date('Ymd'));
            $redis->hIncrBy($dayUsedlRedisKey, $userId, 1);
            $totalRedisKey = self::$_LOTTERY_TIMES_TOTAL_REDIS_KEY;
            $redis->hIncrBy($totalRedisKey, $userId, 1);
            self::_addLotteryLogRecord($userId, $productId);
            self::_recordRecentWinning($userId, $productId);
            return ClientCreditProductNamespace::getProductById($productId);
        }
        return false;
    }
    
    /**
     * 获取运营拼车活动页面访问pv
     * @return int
     */
    public static function getPinChePv(){
        try {
            $min = 8394;
            $redisKey = 'app_operation_pinche_pv';
            $redis = self::getRedisHandler();

            if($redis && !$redis->EXISTS($redisKey)){
                $redis->INCRBY($redisKey,$min);
                $redis->EXPIRE($redisKey,100*24*60*60);
            }
            return $redis->INCRBY($redisKey,mt_rand(1, 4));
        } catch (Exception $exc) {
            return $min;
        }
    }

    /**
     * 摇奖算法:返回奖品id
     * @return int 奖品id
     */
    private static function _lotteryOne() {
        $val = mt_rand(1, 150000);
        $conf_arr = array(
            30 => array('min' => 88888, 'max' => 88888),
            31 => array('min' => 101, 'max' => 115),
            33 => array('min' => 10001, 'max' => 11500),
            50 => array('min' => 20001, 'max' => 24500),
            51 => array('min' => 30001, 'max' => 31500),
        );
        foreach ($conf_arr as $key => $value) {
            if ($val >= $value['min'] && $val <= $value['max']) {
                return $key;
            }
        }
        return 0;
    }

    /**
     * 摇奖算法:返回奖品id
     * @return int 奖品id
     */
    private static function _lotteryTwo() {
        $val = mt_rand(1, 1000);
        $conf_arr = array(
            34 => array('min' => 1, 'max' => 115),
            35 => array('min' => 201, 'max' => 500),
            36 => array('min' => 501, 'max' => 820),
            37 => array('min' => 821, 'max' => 1000),
            38 => array('min' => 141, 'max' => 200),
            39 => array('min' => 121, 'max' => 140),
            40 => array('min' => 116, 'max' => 120),
        );
        foreach ($conf_arr as $key => $value) {
            if ($val >= $value['min'] && $val <= $value['max']) {
                return $key;
            }
        }
        return 0;
    }

    /**
     * 记录抽奖日志
     * @param int $userId
     * @param int $productId
     * @return boolean
     */
    private static function _addLotteryLogRecord($userId = 0, $productId = 0) {
        require_once CODE_BASE2 . '/app/mobile_client/model/credit/LotteryUserLogModel.class.php';
        $data = array(
            'user_id' => $userId,
            'product_id' => $productId,
            'create_time' => time(),
        );
        return LotteryUserLogModel::addLogRecord($data);
    }

    /**
     * 记录最近中奖信息
     * @param int $userId
     * @param int $productId
     * @return boolean
     */
    private static function _recordRecentWinning($userId, $productId) {
        try {
            $userInfo = WebImInterface::getUserInfoByUserId($userId);
            $productInfo = ClientCreditProductNamespace::getProductById($productId);
            if (is_array($userInfo) && !empty($userInfo['nickName']) && !empty($productInfo['name'])) {
                //记录最新购买
                $redis = self::getRedisHandler();
                $hotData = array('u' => $userInfo, 'p' => $productInfo);
                $redis->lPush(self::$_LOTTERY_RECENT_WINNING_LIST_REDIS_KEY, json_encode($hotData));
                //保留最近30条
                $redis->lTrim(self::$_LOTTERY_RECENT_WINNING_LIST_REDIS_KEY, 0, 30);
            }
        } catch (Exception $exc) {
//            echo $exc->getTraceAsString();
        }
        return true;
    }

    /**
     * 获取最近中奖用户列表
     * @return array
     */
    private static function _getRecentWinningList() {
        //读取redis 最近购买记录
        $redis = self::getRedisHandler();
        $redisHotList = $redis->lRange(self::$_LOTTERY_RECENT_WINNING_LIST_REDIS_KEY, 0, 19);
        if (empty($redisHotList))
            return array();

        $resultList = array();
        foreach ($redisHotList as $item) {
            $itemObj = json_decode($item, true);
            if (empty($itemObj) || !is_array($itemObj)) {
                continue;
            }
            if (!is_array($itemObj['u']) || !is_array($itemObj['p'])) {
                continue;
            }
            $resultList[] = sprintf(self::$_WINNING_CONTENT_TEMPLATE, $itemObj['u']['nickName'], $itemObj['p']['name']);
        }
        return $resultList;
    }

}
