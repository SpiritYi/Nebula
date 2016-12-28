<?php
/**
 * 网站目录常量定义
 * @author chenyihong <jinglingyueyue@gmail.com>
 * @version 2015/03/07
 * @copyright nebula.com
 */

define('WWWROOT', dirname(__FILE__) . '/../');

define('API', WWWROOT . '/api');
define('CONFIG', WWWROOT . '/config');
define('CODE_BASE', WWWROOT . '/code_base');
define('WEBSITE', WWWROOT . '/website');
define('CRON', WWWROOT . '/cron');

define('BACKEND', WWWROOT . '/backend');
define('LEO_BACKEND', WWWROOT . '/leo_backend');
define('STOCK', WWWROOT . '/stock');

date_default_timezone_set('PRC');
