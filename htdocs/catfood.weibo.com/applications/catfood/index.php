<?php
header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Pragma: no-cache");

define('DS', DIRECTORY_SEPARATOR);
define('ROOT_PATH', dirname(__FILE__));
define('APP_PATH', ROOT_PATH);
define('PLATFORM_PATH', dirname(dirname(ROOT_PATH)) . '/platform');
define('T3P_PATH', PLATFORM_PATH . '/thirdpart');
define('LOCALE_PATH', ROOT_PATH . '/languages/');

//子项目
define('SUB_APPS', '');

$app = new Yaf_Application(PLATFORM_PATH . "/config/config.ini");
$app->bootstrap()->run();
