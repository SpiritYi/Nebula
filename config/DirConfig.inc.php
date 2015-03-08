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
define('BACKEND', WWWROOT . '/backend');
define('WEBSITE', WWWROOT . '/website');

date_default_timezone_set('PRC');
