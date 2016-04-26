<?php
/**
 * 日志配置文件
 */
return array(
    
    'level' => LOG_LEVEL_DEBUG,
    //'dir' => '/data1/www/logs'
    'dir' => isset($_SERVER['SINASRV_APPLOGS_DIR']) ? $_SERVER['SINASRV_APPLOGS_DIR'] : '/data0/www/applogs/fuwu.weibo.com',
);