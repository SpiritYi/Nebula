<?php
/**
 * 路由类
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/15
 * @copyright nebula.com
 */

class ClientRouter {
    //拆分uri 数据信息
    public static function match($uri) {
        preg_match('/^([^\?]*)(\?.*)?$/', $uri, $tempArr);
        $uri = $tempArr[1] . '/';
        $uri = str_replace('//', '/', $uri);
        $root = '/';
        //匹配location
        $locPath = self::_analyseLocation($uri, $root);
        if (empty($locPath)) {
            return array();
        }
        $resourcePreg = '/^' . str_replace("/", "\/", str_replace('//', '/', $root . $locPath)) . "([\w\-\/]*)\/" . '/';

        preg_match($resourcePreg, $uri, $uriArr);
        //切分resource
        $section = explode('/', $uriArr[1]);
        $locPathArr = explode('/', $locPath);
        $result = array(
            'root' => $root,
            'method' => strtoupper($_SERVER['REQUEST_METHOD']),
            'location' => $locPath,
            // 'group_root' => $locPathArr[1],
            'resource' => $section[0],
            'resource_uri' => '/' . strtolower($uriArr[1]) . '/',
        );
        return $result;
    }

    //分析uri 中路径部分
    private static function _analyseLocation($uri, $root) {
        //切分路径部分root 到 ？ 之前
        $preg = '/^' . str_replace('/', '\/', $root) . '([\w\-\/]*)\/' . '/';
        // preg_match('/^\/([^\?]+)\/(.*)?/', $uri, $uriArr);
        preg_match($preg, $uri, $uriArr);
        $dirItem = explode('/', $uriArr[1]);
        $path = '';
        //找到最长路径
        foreach ($dirItem as $dirName) {
            $realPath = API . $root . $path . '/' . $dirName;   //根据url 找到目录路径
            if (is_dir($realPath)) {
                $path .= '/' . $dirName;
            } else {
                $path .= empty($path) ? '' : '/';
                break;
            }
        }
        return $path;
    }

    //映射当前uri 到方法配置
    public static function mapUri($resourceUri, $config) {
        $uriData = array();
        //匹配符合格式的uri 规则和对应的方法配置
        foreach (array_keys($config) as $matchUri) {
            //解析配置的uri
            $uriReg = self::_parse($matchUri);
            $preg = '/^' . $uriReg['regex'] . '$/';
            if (preg_match($preg, $resourceUri, $data)) {
                //提取参数
                if (!empty($uriReg['keys'])) {
                    foreach ($uriReg['keys'] as $index => $keyName) {
                        $uriData[$keyName] = $data[$index + 1];
                    }
                }
                return array(
                    'method_config' => $config[$matchUri],
                    'uri_data' => $uriData,
                );
            }
        }
        return array();
    }

    //把配置的uri 规则转化为正则规则
    private static function _parse($matchUri) {
        $sectionArr = explode('/', $matchUri);
        $regRes = array();
        $keyRes = array();
        foreach ($sectionArr as $index => $item) {
            if (empty($item)) {
                continue;
            }
            switch ($item[0]) {
                case ':':
                    $keyRes[] = RouterKey::parse($item);
                    $regRes[] = RouterKey::$REGEX;
                    break;

                default:
                    $regRes[] = $item;
                    break;
            }
        }
        $regex = str_replace('//', '/', sprintf('/%s/', implode('/', $regRes)));
        return array(
            'regex' => str_replace('/', '\/', $regex),
            'keys' => $keyRes,
        );
    }
}

//提取uri 中字段类
class RouterKey {
    private static $MATCH_REGEX = '/:([a-zA-Z_][\w\-]*)/';

    public static $REGEX = '/([\w\-]*)/';

    //提取key name
    public static function parse($str) {
        if (preg_match(self::$MATCH_REGEX, $str, $res)) {
            return $res[1];
        }
    }
}
