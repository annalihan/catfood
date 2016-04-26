<?php
/**
 * 头文件，包括常量和include
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
    include dirname(__FILE__) . '/library/Core/Loader.php';
    
    define('ENV_TYPE_PRODUCT', 'production');
    define('ENV_TYPE_DEVELOP', 'development');
    define('ENV_TYPE_TEST', 'test');
    define('PLATFORM_VERSION', '1.0.0');

    //脚本启动时间
    define('PLATFORM_START_TIME', microtime(true));

    define('YAF_LOADER_CONTROLLER', 'Controller');
    define('YAF_LOADER_LEN_CONTROLLER', 10);
    define('YAF_LOADER_MODEL', 'Model');
    define('YAF_LOADER_LEN_MODEL', 5);
    define('YAF_LOADER_PLUGIN', 'Plugin');
    define('YAF_LOADER_LEN_PLUGIN', 6);
    define('YAF_LIBRARY_DIRECTORY_NAME', "library");
    define('YAF_CONTROLLER_DIRECTORY_NAME', "controllers");
    define('YAF_PLUGIN_DIRECTORY_NAME', "plugins");
    define('YAF_MODEL_DIRECTORY_NAME', "models");
    define('YAF_CONFIG_DIRECTORY_NAME', "config");
        
    define('LOG_LEVEL_ALL', 0);
    define('LOG_LEVEL_FATAL', 1);
    define('LOG_LEVEL_ERROR', 2);
    define('LOG_LEVEL_WARN', 3);
    define('LOG_LEVEL_INFO', 4);
    define('LOG_LEVEL_DEBUG', 5);
    define('LOG_LEVEL_TRACE', 6);
    define('LOG_LEVEL_OTHER', 7);

    define('CACHE_ENGINE_REDIS', 'Redis');
    define('CACHE_ENGINE_MEMCACHED', 'Memcached');

    //TODO 定义接口返回错误码
    define('API_CODE_SUCCESS', 0);
    define('API_CODE_FAILED', 100000);
    define('API_CODE_FIELD_MISSING', 100001);
    define('API_CODE_FIELD_ILLEGAL', 100002);
    define('API_CODE_FIELD_INVALID', 100003);

    /**
     * 包含段落
     * @param  string $view_id  段落视图的ID，格式如footer, xxx/xxx, xx/xx/xx
     */
    function include_section($tpl = '', $path = false)
    {
        if ($path == false)
        {
            $traces = debug_backtrace(1);
            $path = isset($traces[0]['file']) ? $traces[0]['file'] : false;
        }

        Core_Template::includeTemplate($tpl, $path);
    }

    function include_pagelet($data = '{}')
    {
        Comm_Bigpipe_Pagelet::addPagelet($data);
    }