<?php

class Tool_Misc
{
    const GET_JS_VERSION_URL = "http://i2.api.weibo.com/2/proxy/admin/content/version.json?source=908033280&type=14";

    /**
     * 检测owner是不是viewer
     * @return [type] [description]
     */
    public static function checkOwnerIsViewer()
    {
        $owner = Comm_Context::get('owner', false);
        $viewer = Comm_Context::get('viewer', false);
        
        if (false === $owner || false === $viewer)
        {
            return false;
        }
        
        return $owner->id == $viewer->id;
    }
    
    /**
     * 判断用户是否登录
     */
    public static function isLogin()
    {
        return Comm_Context::get('viewer', false) ? true : false;
    }
    
    /**
     * 判断是否使用未登录认证
     */
    public static function isUseUnloginAuth()
    {
        return Comm_Context::get('UNLOGIN_ACCESS', false) ? true : false;
    }

    /**
     * 判断php宿主环境是否是64bit
     * 
     * ps: 在64bit下，php有诸多行为与32bit不一致，诸如mod、integer、json_encode/decode等，具体请自行google。
     * 
     * @return bool
     */
    public static function is64bit()
    {
        return (int)0xFFFFFFFF !== -1;
    }
    
    /**
     * 修正过的ip2long
     * 
     * 可去除ip地址中的前导0。32位php兼容，若超出127.255.255.255，则会返回一个float
     * 
     * for example: 02.168.010.010 => 2.168.10.10
     * 
     * 处理方法有很多种，目前先采用这种分段取绝对值取整的方法吧……
     * @param string $ip
     * @return float 使用unsigned int表示的ip。如果ip地址转换失败，则会返回0
     */
    public static function ip2long($ip)
    {
        $ipChunks = explode('.', $ip, 4);
        foreach ($ipChunks as $i => $v)
        {
            $ipChunks[$i] = abs(intval($v)); 
        }

        return sprintf('%u', ip2long(implode('.', $ipChunks)));
    }
    
    /**
     * 判断是否是内网ip
     * @param string $ip
     * @return boolean 
     */
    public static function isPrivateIp($ip)
    {
        $ipValue = self::ip2long($ip);

        //10.0.0.0-10.255.255.255
        //172.16.0.0-172.31.255.255
        //192.168.0.0-192.168.255.255
        return ($ipValue & 0xFF000000) === 0x0A000000 || ($ipValue & 0xFFF00000) === 0xAC100000 || ($ipValue & 0xFFFF0000) === 0xC0A80000;
    }
    
    /**
     * 使json_decode能处理32bit机器上溢出的数值类型
     * 
     * @param string $response
     * @param string $field_name
     * @param boolean $assoc
     * @return array|object
     */
    public static function jsonDecode($value, $assoc = true)
    {
        //PHP5.3以下版本不支持
        //TODO 获取机器CPU位数
        if (version_compare(PHP_VERSION, '5.3.0', '>') && defined('JSON_BIGINT_AS_STRING'))
        {
            return json_decode($value, $assoc, 512, JSON_BIGINT_AS_STRING);
        }
        else
        {
            $value = preg_replace("/\"(\w+)\":(\d+[\.\d+[e\+\d+]*]*)/", "\"\$1\":\"\$2\"", $value);
            return json_decode($value, $assoc);
        }
    }
    
    /**
     * To get ip belonged region according to ip
     * @param <string> $ip ip address, heard that can be ip strings, split by "," ,but i found it not used
     * @param <int> $type 地域名及ISP的显示格式  0 默认文本格式；
     *                                           1 regions.xml中的id；
     *                                           2 regions.xml中的code，即ISO-3166的地区代码；
     *                                           3 regions.xml中的fips，即FIPS的地区代码。
     * @param <string> $encoding  编码类, gbk或utf-8, 默认为gbk
     * @return <int or array>
     */
    public static function getIpSource($ip, $type = 1, $encoding = 'utf-8')
    {
        if (!function_exists('lookup_ip_source'))
        {
            return 0;
        }

        $code = lookup_ip_source($ip, $type, $encoding);
        switch ($code)
        {
            case "-1" :
                return 0;

            case "-2" :
                return 0;

            case "-3" :
                return 0;

            default :
                return $code;
        }
    }

    /**
     * 命名转换，从screen_name转成ScreenName
     * @param  [type]  $underScore [description]
     * @param  string  $glue       是否需要添加分隔符
     * @param  boolean $firstUpper 第一个单词是否也首字母大写
     * @return [type]              [description]
     */
    public static function underScoreToCamel($underScore, $glue = '', $firstUpper = true)
    {
        $paths = explode('_', $underScore);
        $newPaths = array();
        foreach ($paths as $path)
        {
            $newPaths[] = ucfirst($path);
        }

        return implode($glue, $newPaths);
    }

    /**
     * 采用和v5.weibo.com一样的版本策略
     * @param  string $key [description]
     * @return [type]      [description]
     */
    public static function getVersion($key = 'homesite_js')
    {
        static $version = array();
        $currentTime = time();

        if (empty($version))
        {
            try
            {
                //文件格式
                //{"global_top":"e1d6209be3b315e5","homesite_js":"2387cebda7033be5","homesite_css":"3b88e1fcec58c669"}
                $versionFile = Comm_Context::getServer('SINASRV_PRIVDATA_DIR') . '/version/js_css_version.txt';

                if (file_exists($versionFile))
                {
                    $json = file_get_contents($versionFile);
                    $version = json_decode($json, true);
                }

                //如果文件不存在或者过期或者无效
                if (isset($version['homesite_js']) === false || isset($version['homesite_css']) === false || isset($version['global_top']) === false || (isset($version['expire']) && $version['expire'] < time()))
                {
                    $jsVersion = Tool_Http::get(self::GET_JS_VERSION_URL);
                    $jsVersion = json_decode($jsVersion, true);
                    
                    if ($jsVersion)
                    {
                        $version = array(
                            'homesite_js' => $jsVersion['result'],
                            'homesite_css' => $jsVersion['result'],
                            'global_top' => $jsVersion['result'],
                            'expire' => time() + 300, //本地五分钟有效期
                        );

                        //存在保存到文件
                        if (is_dir(dirname($versionFile)) === false)
                        {
                            mkdir(dirname($versionFile), 0777, true);
                        }

                        file_put_contents($versionFile, json_encode($version));
                    }
                }
            }
            catch (Comm_Exception_Program $e) 
            {

            }
        }

        if (!isset($version[$key]))
        {
            $defaultVer = Comm_Config::get('project.default_js_css_ver');
            $version[$key] = $defaultVer ? $defaultVer : date('YmdHi');
        }

        return $version[$key];
    }

    /**
     * 第三方顶导版本号 
     * 
     * @return string
     */
    public static function globalTopVersion()
    {
        static $globalTopVersion = '';
        if ($globalTopVersion)
        {
            return $globalTopVersion;
        }

        return $globalTopVersion = self::getVersion('global_top');
    }

    /**
     * 主站js版本号 
     * 
     * @return string
     */
    public static function homesiteJsVersion()
    {
        static $homesiteJsVersion = '';
        if ($homesiteJsVersion)
        {
            return $homesiteJsVersion;
        }

        return $homesiteJsVersion = self::getVersion('homesite_js');
    }

    /**
     * 主站css版本号  
     * 
     * @return string
     */
    public static function homesiteCssVersion()
    {
        static $homesiteCssVersion = '';
        if ($homesiteCssVersion)
        {
            return $homesiteCssVersion;
        }

        return $homesiteCssVersion = self::getVersion('homesite_css');
    }
}
