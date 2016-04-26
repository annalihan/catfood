<?php
/**
 * 发布相关
 *     灰度上线、小流量测试等
 * @package Core
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2014-06-04
 */
class Core_Branch
{
    const EXPIRE_VALUE = 300;
    const DEFAULT_BRANCH_NAME = 'g1';
    const DEFAULT_GRAY_NAME = '_bucket';
    const HEADER_NAME_BRANCH = 'X-EPF-BRANCH';
    const HEADER_NAME_BRANCH_HTTP = 'HTTP_X_EPF_BRANCH';
    const HEADER_NAME_GRAY = 'X-EPF-GRAY';
    const HEADER_NAME_GRAY_HTTP = 'HTTP_X_EPF_GRAY';

    //灰度版本的标识，非灰度时此值为空字符串，灰度时为灰度版本标识
    public static $grayVersion = '';

    //小流量控制标识
    public static $branchName = '';

    private static $_inited = false;

    public static function init()
    {
        if (self::$_inited)
        {
            return;
        }

        self::$_inited = true;
        self::_initConfigs();
        self::_initEnvironment();
    }

    private static function _getConfig()
    {
        $config = array();

        //第一版，使用配置文件
        //return Comm_Config::get('branch');

        //第二版，本地json文件+MC缓存(暂时使用MESSAGE这个池子，访问量小)
        $projectName = Comm_Config::get('project.name', 'epf');
        return self::_getProject($projectName);
    }

    private static function _getProject($projectName)
    {
        $config = array();
        $currentTime = time();
        $privateDir = Comm_Context::getServer('SINASRV_PRIVDATA_DIR', '/tmp/');
        $configFile = $privateDir . DS . 'branch_' . $projectName . '.json';
        if (file_exists($configFile))
        {
            $content = file_get_contents($configFile);
            $config = json_decode($content, true);

            if (isset($config['expire']) && ($config['expire'] > $currentTime - rand(0, 100)))
            {
                return $config;
            }
        }

        $cacheKey = strtoupper('EPF_BRANCH_' . $projectName);
        $cache = Comm_Cache::pool('EPF');
        $content = $cache->get($cacheKey);
        $cacheValue = json_decode($content, true);
        $config = ($cacheValue ? $cacheValue : $config);

        self::_setProject($projectName, $config);

        return $config;
    }

    private static function _setProject($projectName, $config)
    {
        $currentTime = time();
        $privateDir = Comm_Context::getServer('SINASRV_PRIVDATA_DIR', '/tmp/');
        $configFile = $privateDir . DS . 'branch_' . $projectName . '.json';
        $config['expire'] = $currentTime + self::EXPIRE_VALUE;
        $content = json_encode($config);
        file_put_contents($configFile, $content);
        //Tool_Log::info("Branch config is created, project:{$projectName}, content:{$content}");
    }

    public static function saveProject($projectName, $config)
    {
        unset($config['expire']);
        $cacheKey = strtoupper('EPF_BRANCH_' . $projectName);
        $content = json_encode($config);
        
        $cache = Comm_Cache::pool('EPF');
        $result = $cache->set($cacheKey, $content, 0);
        //Tool_Log::info("Branch config is created, project:{$projectName}, content:{$content}, result:{$result}");

        return $result;
    }

    private static function _openGray($grayEmpty)
    {
        if ($grayEmpty == false)
        {
            self::$grayVersion = self::DEFAULT_GRAY_NAME;    
        }
    }

    private static function _openBranch($branchEmpty)
    {
        if ($branchEmpty == false)
        {
            self::$branchName = self::DEFAULT_BRANCH_NAME;    
        }
    }

    private static function _initConfigs()
    {
        //HEADER用于后端HTTP请求
        $branchHeader = Comm_Context::getServer(self::HEADER_NAME_BRANCH_HTTP, '');
        $grayHeader = Comm_Context::getServer(self::HEADER_NAME_GRAY_HTTP, '');
        if ($branchHeader || $grayHeader)
        {
            self::_openGray(!$grayHeader);
            self::_openBranch(!$branchHeader);
            return;
        }

        try
        {
            //获取小流量配置
            //黑名单、白名单，viewer和owner
            //TODO:IP
            //TODO:用户属性
            //TODO:OWNER
            $config = self::_getConfig();
            $branchEmpty = empty($config['enabled']);
            $grayEmpty = empty($config['gray']);

            //同时关闭小流量和灰度时处理
            if ($branchEmpty && $grayEmpty)
            {
                return;
            }

            Core_Authorize::getViewer();
        }
        catch (Exception $e)
        {

        }

        //viewer
        if (Core_Authorize::$viewer)
        {
            //先黑后白
            if (isset($config['black']['viewer']))
            {
                $result = self::_checkUser(Core_Authorize::$viewer->id, $config['black']['viewer']);
                if ($result)
                {
                    return;
                }
            }

            if (isset($config['white']['viewer']))
            {
                $result = self::_checkUser(Core_Authorize::$viewer->id, $config['white']['viewer']);
                if ($result)
                {
                    self::_openBranch($branchEmpty);
                    self::_openGray($grayEmpty);
                    return;
                }
            }
        }
    }

    private static function _initEnvironment()
    {
        //设定环境
        if (self::$branchName)
        {
            //判断小流量目录是否存在
            $checkPath = dirname(dirname(APP_PATH)) . DS . self::$branchName;
            if (is_dir($checkPath) === false)
            {
                self::$branchName = '';
                return;
            }

            Core_Loader::getInstance()->initEnvironment(self::$branchName);
            Core_Template::getInstance();
        }
    }

    private static function _checkUser($userId, $config)
    {
        //ID
        if (isset($config['normal'][$userId]) && $config['normal'][$userId])
        {
            return true;
        }

        //MOD
        foreach ($config['mod'] as $mod)
        {
            if (isset($mod['div']) && isset($mod['value']) && $mod['div'] > 0)
            {
                if (($userId / 10) % $mod['div'] == $mod['value'])
                {
                    return true;
                }
            }
        }

        //TODO ATTR

        return false;
    }

    /**
     * 渲染引擎中的灰度处理
     *     phtml中 <?=$g_bucket?> : <link href="http://img.t.sinajs.cn/t4/appstyle/e/apps/message/css<?=$g_bucket?>/group.css?version=<?=Tool_Misc::homesiteCssVersion()?>" type="text/css" rel="stylesheet" charset="utf-8" />
     * @param  [type] $engine [description]
     * @return [type]         [description]
     */
    public static function assignGrayVersion($engine)
    {
        $engine->assign('g_bucket', self::$grayVersion);
    }

    /**
     * Page配置文件中的js和css版本处理
     *     page_config.json中 {$g_bucket}
     *         "pl_left_nav_common" : {
     *             "fun":"FM.view",
     *             "ns":"pl.leftNav.common",
     *             "domid":"pl_leftnav_pcpagecommon",
     *             "js":"apps\/pro_component\/js\/pl\/leftNav{$g_bucket}\/common.js?version=",
     *             "css":["appstyle\/e\/css\/module\/nav{$g_bucket}\/admin_left_nav.css?version="]
     *         }
     * @param  [type] $uri     [description]
     * @param  [type] $version [description]
     * @return [type]          [description]
     */
    public static function formatJs($uri, $version)
    {
        $suffixPosition = stripos($uri, '?');
        $uri = ($suffixPosition > 0 ? substr($uri, 0, $suffixPosition) : $uri);
        
        //替换{$g_bucket}
        $uri = str_replace('{$g_bucket}', self::$grayVersion, $uri);

        //替换<?=$g_bucket?/>
        //$uri = str_replace('<?=$g_bucket?/>', Core_Branch::$grayVersion, $uri);

        return $uri . $version;
    }
}