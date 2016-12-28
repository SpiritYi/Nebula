<?php
/**
 * 页面分发入口
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/05/01
 * @copyright nebula-fund.com
 */

require_once dirname(__FILE__) . '/../config/BackendDirConfig.inc.php';

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
            $requestUri = '/backenddefault';

        require_once CODE_BASE . '/util/architecture/WebRouter.class.php';
        $router = WebRouter::parseUri(BACKEND_WEB, $requestUri);
        if (empty($router))
            self::Page_404();

        //启动需要访问的文件
        require_once CODE_BASE . '/app/page/PageBase.class.php';
        PageBase::init(BACKEND . '/web/template');

        require_once BACKEND_WEB . '/app/BackendMaster.class.php';     //主页面模板

        require_once $router['path'] . '/' . $router['code_file'] . '.class.php';
        $className = $router['code_file'] . 'Page';
        new $className();
    }
}

Dispatch::run();