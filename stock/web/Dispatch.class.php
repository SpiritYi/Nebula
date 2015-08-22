<?php
/**
 * 页面分发入口
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/08/20
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../config/StockDirConfig.inc.php';

require_once CONFIG . '/DBConfig.class.php';
require_once CODE_BASE . '/util/logger/Logger.class.php';

require_once CODE_BASE . '/util/http/HttpUtil.class.php';

class Dispatch {

    public static function Page_404() {
        require_once WEBSITE . '/404.html';
        exit;
    }

    public static function run() {
        //获取需要访问的页面文件
        $requestUri = $_SERVER['REQUEST_URI'];
        $requestUri = str_replace(array('///', '//'), '/', strtolower($requestUri));
        if (($queryPoint = strpos($requestUri, '?')) > 0)   //带? 参数的uri 截取
            $requestUri = substr($requestUri, 0, $queryPoint);
        if ($requestUri == '/')     //首页默认落地页
            $requestUri = '/default';

        $router = WebRouter::parseUri($requestUri);
        if (empty($router))
            self::Page_404();

        //启动需要访问的文件
        require_once CODE_BASE . '/app/page/PageBase.class.php';
        PageBase::init(STOCK . '/web/template');

        require_once STOCK_WEB . '/app/StockMaster.class.php';     //主页面模板

        require_once $router['path'] . '/' . $router['code_file'] . '.class.php';
        $className = $router['code_file'] . 'Page';
        new $className();
    }
}

Dispatch::run();

class WebRouter {
    /**
     * 分析uri， 找到路径，页面文件
     */
    public static function parseUri($uri) {
        $uriArr = explode('/', $uri);
        if (empty($uriArr))
            return array();
        //根据uri 找文件路径
        $pathStr = STOCK_WEB . '/app';
        //保留url数据有效文件系统节点
        foreach ($uriArr as $k => $v) {
            if (empty($v))
                unset($uriArr[$k]);
        }
        $uriArr = array_values($uriArr);
        foreach ($uriArr as $index => $uriItem) {
            if (empty($uriItem))
                continue;
            $dirStr = $pathStr . '/' . $uriItem;
            if (is_dir($dirStr)) {      //当前路径存在，则继续往下找
                $pathStr = $dirStr;
            } else if (($codeFile = self::_findClassFile($pathStr, $uriItem)) !== false) {   //下一级文件夹查找失败，查找请求的落地页面文件
                if ($index != (count($uriArr) - 1)) {   //找到文件但不是最后节点，404
                    return array();
                }
                $router = array(
                    'path' => $pathStr,
                    'code_file' => $codeFile,
                );
                return $router;
            } else {
                return array();
            }
        }
        return array();
    }

    /**
     * 在路径下找到请求(小写) 是否有对应的类文件(大写驼峰)
     */
    private static function _findClassFile($path, $nodeName) {
        $subHandle = opendir($path);
        while (($subName = readdir($subHandle)) !== false) {
            if (!strpos($subName, '.class.php')) {
                continue;
            }
            preg_match('/^([\w-\.]{1,})\.class\.php$/', $subName, $nameArr);
            if (empty($nameArr))
                return false;
            if (strtolower($nameArr[1]) == $nodeName)
                return $nameArr[1];
        }
        return false;
    }
}
