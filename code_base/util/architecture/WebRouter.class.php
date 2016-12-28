<?php
/**
 * 架构层网站路径解析,文件定位类
 * @author Yihong Chen <jinglingyueyue@gmail.com>
 * @version 2016/12/25
 * @copyright nebula-fund.com
 */

class WebRouter {
    /**
     * 分析uri， 找到路径，页面文件
     * @param string $rootPath          //网站分项目文件夹目录, index.php 所在目录
     * @param string $uri               //请求路径, 解析到文件
     * @return array
     */
    public static function parseUri($rootPath, $uri) {
        $uriArr = explode('/', $uri);
        if (empty($uriArr))
            return array();
        //根据uri 找文件路径
        $pathStr = $rootPath . '/app';
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