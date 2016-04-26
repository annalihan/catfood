<?php
// JS版本号
$js_version = Tool_Misc::homesiteJsVersion();

define('JS_VERSION', "?version=$js_version");

return array(
    
    "platform_api_source" => "908033280",
    "platform_api_appsecret" => "00ede82594b98370bf4681cb133eee7f",
    'applogs_dir' => Comm_Context::getServer('SINASRV_APPLOGS_DIR'),
    'cache_dir' => Comm_Context::getServer('SINASRV_CACHE_DIR'),
    'data_dir' => Comm_Context::getServer('SINASRV_DATA_DIR'),
    'privdata_dir' => Comm_Context::getServer('SINASRV_PRIVDATA_DIR'),
    'version_js' => $js_version,
    'version_css' => $js_version,
    'version_img' => $js_version,
    "css_domain" => 'http://img.t.sinajs.cn/t4/',
    "js_domain" => 'http://js.t.sinajs.cn/t4/',
    "skin_domain" => 'http://img.t.sinajs.cn/t4/',
    'img_domain' => 'http://img.t.sinajs.cn/t4/',
    "css_domain_pool" => array(
        
        'http://img.t.sinajs.cn/t4/',
        'http://img1.t.sinajs.cn/t4/',
        'http://img2.t.sinajs.cn/t4/'
    ),
    'debug' => '45b5f4beb440454716a4a5d7d34c6d03',
    'event_account' => array(
        
        'user' => 'wborder@sina.com',
        'pass' => 'Jenson13',
        'appkey' => '3895963958',
        'event_sys_admin_appkey' => '3895963958',
        'event_admin_appkey' => '3895963958',
        'uid' => '3674418517'
    ),
    'mcq' => array(
        //默认连接的mcq
        'default' => Comm_Context::getServer('SINASRV_MEMCACHEQ_APP_6802_SERVERS')
    ),
    'redisq' => array(
        //默认连接的redis队列
        'default' => '10.13.49.223:6379',
        'dev' => '10.13.49.223:6378'
    )
);
