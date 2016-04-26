<?php
/**
 * 服务上下文数据
 * @package Base
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class Comm_Context
{
    const GET_JS_VERSION_URL = "http://i2.api.weibo.com/2/proxy/admin/content/version.json?source=908033280&type=14"; 

    private static $_contextData = array();
    private static $_hasInited = false;
    private static $_isProduction = null;
    protected static $server = array();
    
    /**
     * 是否保持$_SERVER变量。默认为false，不保持。
     * 
     * 注：Comm_Context::init()默认行为会在得到$_SERVER的内容后删除。为了保持某些lib的兼容性，添加此开关。
     * 
     * @var bool
     */
    public static $keepServerCopy = false;

    /**
     * 获取客户端IP地址
     * @return string
     */
    public static function getClientIp($toLong = false)
    {
        $clientIp = '';

        $forwarded = self::getServer('HTTP_X_FORWARDED_FOR');
        if ($forwarded)
        {
            $ipChains = explode(',', $forwarded);
            $proxiedClientIp = $ipChains ? trim(array_pop($ipChains)) : '';
        }
        
        if (Tool_Misc::isPrivateIp(self::getServer('REMOTE_ADDR')) && isset($proxiedClientIp))
        {
            $realIp = $proxiedClientIp;
        }
        else
        {
            $realIp = self::getServer('REMOTE_ADDR');
        }
        
        return $toLong ? Tool_Misc::ip2long($realIp) : $realIp;
    }

    /**
     * [getRemoteIp description]
     * @return string
     */
    public static function getRemoteIp()
    {
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1';
    }

    public static function getServerIp()
    {
        $cmdStatus = 0;
        $cmdOutput = array();
        $cmd = 'ipmaddr show link | grep eth';

        exec($cmd, $cmdOutput, $cmdStatus);
    
        if ($cmdStatus == 0)
        {
            $line = $cmdOutput[0];
            $ethString = explode('eth', $line);
            $eth = $ethString[1];
            $ethConfigFile = '/etc/sysconfig/network-scripts/ifcfg-eth' . $eth;

            if (file_exists($ethConfigFile))
            {
                $ethInfo = parse_ini_file($ethConfigFile);
                return isset($ethInfo['IPADDR']) ? trim($ethInfo['IPADDR']) : false;
            }
        }
    
        return false;
    }

    /**
     * get value from $_SERVER
     * @param  string $name         KEY值
     * @param  string $defaultValue 默认值
     * @return string
     */
    public static function getServer($name, $defaultValue = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $defaultValue;
    }
    
    /**
     * 初始化context。
     */
    public static function init()
    {
        if (self::$_hasInited)
        {
            return;
        }

        self::$_hasInited = true;
        self::$server = $_SERVER;
        if (!self::$keepServerCopy)
        {
            unset($_SERVER);
        }
    }

    /**
     * 转换过滤字符串
     *
     * @param string $string
     * @return string
     */
    public static function filterString($string)
    {
        if ($string === null)
        {
            return false;
        }

        return htmlspecialchars($string, ENT_QUOTES);
    }

    /**
     * 从$_GET中获取指定参数的值。
     * 如果指定参数未找到，则会返回默认值$ifNotExist的值。
     * 
     * @param string $name 参数名。
     * @param mixed $ifNotExist 若指定的$name的值不存在的情况下返回的默认值。可选，采用null作为默认值。
     * @return string 
     */
    public static function param($name, $ifNotExist = null, $isFilter = false)
    {
        if ($isFilter)
        {
            return isset($_GET[$name]) ?  (self::filterString($_GET[$name])) : $ifNotExist;
        } 

        return isset($_GET[$name]) ?  $_GET[$name] : $ifNotExist;
    }
    
    /**
     * 从$_Cookie中获取指定参数的值。
     * 如果指定参数未找到，则会返回默认值$ifNotExist的值。
     * @param string $name 参数名称
     * @param mixed $ifNotExist 若指定的$name的值不存在的情况下返回的默认值。可选，采用null作为默认值。
     * @return string
     */
    public static function cookie($name, $ifNotExist = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $ifNotExist;
    }
    
    /**
     * POST参数
     * @param  [type] $name         [description]
     * @param  [type] $defaultValue [description]
     * @return [type]               [description]
     */
    public static function form($name, $defaultValue = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    /**
     * POST参数
     * @param  [type] $name         [description]
     * @param  [type] $defaultValue [description]
     * @return [type]               [description]
     */
    public static function post($name, $defaultValue = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    /**
     * 从POST/GET获取参数
     * @param  [type] $name         [description]
     * @param  [type] $defaultValue [description]
     * @return [type]               [description]
     */
    public static function request($name, $defaultValue = null)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $defaultValue;
    }

    /**
     * 根据指定的上下文键名获取一个已经设置过的上下文键值
     * 
     * @param string|int|float $key 键名
     * @param mixed $ifNotExist 当键值未设置的时候的默认返回值。可选，默认是Null。如果该值是Null,当键值未设置则会抛出一个异常；否则，返回该值。
     * @return mixed 如果指定的$key不存在，根据 $ifNotExist 的值，会抛出一个异常或者 $ifNotExist 本身。
     */
    public static function get($key, $ifNotExist = null)
    {
        if (isset(self::$_contextData[$key]))
        {
            return self::$_contextData[$key];
        }

        if ($ifNotExist === null)
        {
            throw new Comm_Exception_Program('context has no "' . $key . '" in it');
        }
        else
        {
            return $ifNotExist;
        }
    }
    
    /**
     * 往一个指定的上下文键名中设置键值。如果该键值已经被设置，则会抛出异常。
     * 
     * @param string|int|float $key
     * @param mixed $value
     * @param array $rule
     * @param boolen $force
     * @throws Comm_Exception_Program
     */
    public static function set($key, $value, array $rule = array(), $force = false)
    {
        if (isset(self::$_contextData[$key]) && $force === false)
        {
            throw new Comm_Exception_Program('context has been already setted');
        }
        
        if ($rule)
        {
            $type = $rule[0];
            $rule[0] = $value;
            $value = call_user_func_array(array('Comm_ArgChecker', $type), $rule);
        }

        self::$_contextData[$key] = $value;
    }
    
    /**
     * 获取当前域名
     * 
     * @return string
     */
    public static function getDomain()
    {
        return self::getServer('SERVER_NAME');
    }
        
    /**
     * 
     * 获取http请求方法。
     * @return string GET/POST/PUT/DELETE/HEAD等
     */
    public static function getHttpMethod()
    {
        return self::getServer('REQUEST_METHOD');
    }
    
    /**
     * 判断当前请求是否是XMLHttpRequest(AJAX)发起
     * @return boolean
     */
    public static function isXmlHttpRequest()
    {
        return self::getServer('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest';
    }
    
    /**
     * 返回当前url
     * 
     * @param bool $urlEncode 是否urlencode后返回，默认true
     */
    public static function getCurrentUrl($urlEncode = true)
    {
        $requestUri = self::getServer('REQUEST_URI');
        if (null === $requestUri)
        {
            $requestUri = self::getServer('PHP_SELF');
        }

        $https = self::getServer('HTTPS');
        $s = $https == 'on' ? 's' : '';

        $protocol = self::getServer('SERVER_PROTOCOL');
        $protocol =  strtolower(substr($protocol, 0, strpos($protocol, '/'))) . $s;
        
        $host = self::getServer('HTTP_HOST');
        if (!$host)
        {
            $serverName = self::getServer('SERVER_NAME');
            $port = self::getServer('SERVER_PORT');
            $port = ($port == '80') ? '' : (':' . $port);

            $host = $serverName . $port;
        }
        
        $currentUrl = $protocol . '://' . $host . $requestUri;
        
        return $urlEncode ? rawurlencode($currentUrl) : $currentUrl;
    }

    public static function getRootUrl($urlEncode = false)
    {
        $https = self::getServer('HTTPS');
        $s = $https == 'on' ? 's' : '';

        $protocol = self::getServer('SERVER_PROTOCOL');
        $protocol =  strtolower(substr($protocol, 0, strpos($protocol, '/'))) . $s;
        
        $host = self::getServer('HTTP_HOST');
        if (!$host)
        {
            $serverName = self::getServer('SERVER_NAME');
            $port = self::getServer('SERVER_PORT');
            $port = ($port == '80') ? '' : (':' . $port);

            $host = $serverName . $port;
        }
        
        $currentUrl = $protocol . '://' . $host;
        
        return $urlEncode ? rawurlencode($currentUrl) : $currentUrl;
    }

    /**
     * 获取当前访问的URL PATH，不带参数
     * @param  boolean $urlEncode [description]
     * @return [type]             [description]
     */
    public static function getCurrentUrlPath($urlEncode = false)
    {
        $rootUrl = self::getRootUrl(false);
        $path = self::getServer('SCRIPT_URL');
        $path = !$path ? '/' : $path;

        $curPath = $rootUrl . $path;

        return $urlEncode ? rawurlencode($curPath) : $curPath;
    }
    
    /**
     * 清除context中的所有内容
     * @return [type] [description]
     */
    public static function clear()
    {
        self::$_contextData = array();
    }

    /**
     * 当前环境是否为线上环境
     * @return boolean [description]
     */
    public static function isProduction()
    {
        if (self::$_isProduction === null)
        {
            $projectEnv = Comm_Context::getServer('PROJECT_ENV', Comm_Config::get('project.env', ENV_TYPE_PRODUCT));
            self::$_isProduction = (strtolower($projectEnv) == ENV_TYPE_PRODUCT ? true : false);
        }

        return self::$_isProduction;
    }

    /**
     * [isCli description]
     * @return boolean [description]
     */
    public static function isCli()
    {
        return PHP_SAPI === 'cli';
    }
}
