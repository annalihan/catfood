<?php
    //加载头文件
    include dirname(__FILE__) . '/header.inc.php';

    define('QUEUE_ENGINE_REDIS', CACHE_ENGINE_REDIS);
    define('QUEUE_ENGINE_MCQ', CACHE_ENGINE_MEMCACHED);
    $g_inited = false;

    function create_daemon($name, $port = 0, $max = 1, $queues = array(), $handlers = array(), $health = array())
    {
        init_daemon();

        $config = array(
            'mainDaemonName' => $name,
            'maxWorker' => $max,

            /*管理端口配置*/
            'host' => Comm_Context::getServerIp(),
            'port' => intval($port),

            /*子进程配置*/
            'CProcess' => array(
                /*队列配置*/
                'queueEngine'    => isset($queues['engine']) ? $queues['engine'] : QUEUE_ENGINE_REDIS, //队列引擎:QUEUE_ENGINE_REDIS, QUEUE_ENGINE_MCQ
                'queueResource'  => isset($queues['resource']) ? $queues['resource'] : '', //config/cache/pool里的配置名
                'queueKey'       => isset($queues['key']) ? $queues['key'] : '', //队列的key

                /*回调函数配置*/
                'queueHandleDo'      => isset($handlers['do']) ? $handlers['do'] : '',
                'queueHandleGetData' => isset($handlers['get']) ? $handlers['get'] : '',
                'queueHandleInit'    => isset($handlers['init']) ? $handlers['init'] : '',

                /*进程重启参数设定*/
                'queueListSleep'     => isset($health['sleep']) ? floatval($health['sleep']) : 0.05, //当没有数据是每次循环等待时间，单位：秒
                'healthCount'        => isset($health['count']) ? intval($health['count']) : 100000, //每个进程处理最大任务数，单位：次
                'healthTime'         => isset($health['time']) ? floatval($health['time']) : 1200, //每个进程运行最大时间，单位：秒
                'idleTime'           => isset($health['idle']) ? floatval($health['idle']) : 60, //每个进程Idle的时间，单位：秒
            ),
        );

        return new Core_Daemon_Main($config);
    }

    function create_firehose_daemon($type, $event, $port, $handler, $max = 1, $refresh = false)
    {
        ini_set('memory_limit', '100M');
        
        init_daemon();

        $firehose = new Comm_Daemon_Firehose($type, $event, $max, $refresh);

        $config = array(
            'mainDaemonName' => "{$type}.{$event}.firehose.daemon",
            'maxWorker'      => intval($max),

            /*管理端口配置*/
            'host' => Comm_Context::getServerIp(),
            'port' => intval($port),

            /*子进程配置*/
            'CProcess'       => array(
                /*回调函数配置*/
                'queueHandleDo'      => $handler,
                'queueHandleGetData' => array($firehose, 'getData'),
                'queueHandleInit'    => array($firehose, 'init'),

                /*进程重启参数设定*/
                'queueListSleep'     => 0.05, //当没有数据是每次循环等待时间，单位：秒
                'healthCount'        => 500000, //每个进程处理最大任务数，单位：次
                'healthTime'         => 1200, //每个进程运行最大时间，单位：秒
            ),
        );

        $daemon = new Core_Daemon_Main($config);
        $daemon->firehose = $firehose;
        return $daemon;
    }

    /**
     * 初始化daemon/crontab环境和动态池环境一致
     * @param  string $domain      域名
     * @param  string $config_file 配置文件
     * @param  string $engine      apache或者nginx
     * @return [type]              [description]
     */
    function init_daemon($domain = '', $config_file = '', $engine = 'apache')
    {
        global $g_inited;
        if ($g_inited)
        {
            return;
        }
        
        $g_inited = true;

        //参数配置，保留部分
        define('DS', DIRECTORY_SEPARATOR);
        define('T3P_PATH', PLATFORM_PATH . '/thirdpart');
        define('LOCALE_PATH', APP_PATH . '/languages/');

        //注册全局变量(从vhost配置)
        init_environment($domain, $config_file, $engine);

        //注册自动加载
        //配置 application.system.use_spl_autoload=true
        spl_autoload_register('load_classes');

        //设定环境
        Core_Loader::getInstance()->initEnvironment();

        //基础资源初始化
        mb_internal_encoding('utf-8');
        setlocale(LC_ALL, 'zh_CN.utf-8');
        date_default_timezone_set('Asia/Chongqing');
        Comm_Context::$keepServerCopy = true;
        Comm_Context::init();
        Comm_Cache::initPool();
        Comm_Db::initPool();

        //开启性能分析
        //Core_Debug::openLog(true);
        Core_Debug::openSummary(true);
    }

    /**
     * 自动加载类
     * @param  [type] $class_name [description]
     */
    function load_classes($class_name)
    {
        $class_name_length = strlen($class_name);

        if (Core_Loader::getInstance()->includeClass($class_name, $class_name_length, '', 0, YAF_LIBRARY_DIRECTORY_NAME))
        {
            return true;
        }

        if (substr($class_name, $class_name_length - YAF_LOADER_LEN_CONTROLLER) == YAF_LOADER_CONTROLLER)
        {
            if (Core_Loader::getInstance()->includeClass($class_name, $class_name_length, YAF_LOADER_CONTROLLER, YAF_LOADER_LEN_CONTROLLER, YAF_CONTROLLER_DIRECTORY_NAME))
            {
                return true;
            }
        }

        if (substr($class_name, $class_name_length - YAF_LOADER_LEN_PLUGIN) == YAF_LOADER_PLUGIN)
        {
            if (Core_Loader::getInstance()->includeClass($class_name, $class_name_length, YAF_LOADER_PLUGIN, YAF_LOADER_LEN_PLUGIN, YAF_PLUGIN_DIRECTORY_NAME))
            {
                return true;
            }
        }
    }

    function init_environment($domain, $config_file, $engine)
    {
        if ($domain == '' || $config_file == '')
        {
            return false;
        }

        $parser = "parse_{$engine}_config";
        if (function_exists($parser) === false)
        {
            throw new Exception("Parser {$engine} is not exist.");
        }

        //解析
        $vhosts = $parser($config_file);

        //全局部分
        if (isset($vhosts[0]['env']))
        {
            set_env($vhosts[0]['env']);
        }

        //域名独立部分
        foreach ($vhosts as $vhost)
        {
            if ($vhost['name'] == $domain || isset($vhost['alias'][$domain]))
            {
                set_env($vhost['env']);
                return true;
            }
        }

        return false;
    }

    function set_env($vhost_env)
    {
        if (is_array($vhost_env) === false)
        {
            return;
        }

        foreach ($vhost_env as $key => $value)
        {
            $_SERVER[$key] = $value;
        }
    }

    function parse_apache_config($config)
    {
        if (!is_readable($config))
        {
            return array();
        }

        $lines = file($config);
        $last_line = '';
        $vhosts = array(array('name' => 'root', 'alias' => array(), 'env' => array()));
        $current_host = &$vhosts[0];

        foreach ($lines as $line)
        {
            if (!preg_match('/^\s*#/', $line) && preg_match('/^\s*(.*)\s+\\\$/', $line, $match))
            {
                // directive on more than one line
                $last_line .= $match[1] . ' ';
                continue;
            }

            if ($last_line != '')
            {
                $line = $last_line . trim($line);
                $last_line = '';
            }

            if (preg_match('/^\s*(\w+)(?:\s+(.*?)|)\s*$/', $line, $match))
            {
                switch ($match[1])
                {
                    case 'ServerName':
                        $current_host['name'] = $match[2];
                        $current_host['alias'][$match[2]] = 1;
                        break;

                    case 'ServerAlias':
                        $current_host['alias'][$match[2]] = 1;
                        break;

                    case 'SetEnv':
                        $values = explode(' ', trim($match[2]));
                        if (count($values) > 1)
                        {
                            $env_key = array_shift($values);
                            $current_host['env'][$env_key] = trim(implode(" ", $values), ' "');
                        }
                        break;
                }
            }
            elseif (preg_match('/^\s*<VirtualHost(?:\s+([^>]*)|\s*)>\s*$/', $line, $match))
            {
                $vhosts[] = array('name' => '', 'alias' => array(), 'env' => array());
                $current_host = &$vhosts[count($vhosts) - 1];
            }
            elseif (preg_match('/^\s*<\/VirtualHost\s*>\s*$/', $line, $match))
            {
                $current_host = &$vhosts[0];
            }
            else
            {
                continue;
            }
        }

        return $vhosts;
    }