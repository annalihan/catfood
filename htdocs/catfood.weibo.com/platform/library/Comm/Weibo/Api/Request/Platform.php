<?php
class Comm_Weibo_Api_Request_Platform extends Comm_Weibo_Api_Request_Abstract
{
    public static $platformApiServerName = "http://i2.api.weibo.com";
    public static $platformApiServerNameV5 = "http://i2.api.weibo.com/2";
    public static $platformApiVersionLeast = "1";
    public static $platformApiDefaultFormat = "json";
    public static $platformApiServerNameV3 = "http://api.t.sina.com.cn";
    public static $platformApiServerNameInternal = "http://i.api.t.sina.com.cn";

    private static $_internalApis = array(
        "iremind" => true, 
        "remind" => true, 
        "groups" => true, 
        "short_url" => true, 
        'nav' => true, 
        'notice' => true, 
        'im' => true
    );

    protected $usePassword = false;
    protected $useTAuth2 = false;

    const REQUEST_IP_KEY = 'API-RemoteIP';

    public function __construct($url, $method = false)
    {
        parent::__construct($url, $method);

        $appkeyPost = Comm_Context::form('appsource', "");
        $appkeyGet = Comm_Context::param('appsource', "");

        if (isset($appkeyPost) && ctype_digit($appkeyPost))
        {
            $source = $appkeyPost;
        }
        elseif (isset($appkeyGet) && ctype_digit($appkeyGet))
        {
            $source = $appkeyGet;
        }
        elseif (Comm_Context::get('appsource', false) !== false)
        {
            $source = Comm_Context::get('appsource', false);
        }
        else
        {
            $source = Comm_Config::get("env.platform_api_source");
        }

        if (strtoupper($method) == "POST")
        {
            $this->httpRequest->addPostField("source", $source);
        }
        else
        {
            $this->httpRequest->addQueryField("source", $source);
        }
        $ip = Comm_Context::getClientIp();
        $this->httpRequest->addHeader(self::REQUEST_IP_KEY, $ip);
    }

    /**
     * 接口请求方法
     * @see Comm_Weibo_Api_Request_Abstract::getResult()
     * @return 接口无异常时的正常返回值
     */
    public function getResult($throwException = true, $default = array())
    {
        if ($this->useTAuth2)
        {
            $tauthUid = Comm_Context::get('tauth2_uid', "");
            $tauthAuthString = Comm_Context::get('tauth2_auth_string', "");
        }
        else
        {
            $tauthUid = Comm_Context::get('tauth_uid', "");
            $tauthAuthString = Comm_Context::get('tauth_auth_string', "");
        }

        if (!empty($tauthUid) && !empty($tauthAuthString) && $this->usePassword == false)
        {
            $this->httpRequest->addHeader("Authorization", $tauthAuthString, false, true);
        }
        elseif ($this->usePassword == false)
        {
            $this->httpRequest->addCookie("SUE", Comm_Context::cookie('SUE', ''), true);
            $this->httpRequest->addCookie("SUP", Comm_Context::cookie('SUP', ''), true);
        }

        return parent::getResult($throwException, $default);
    }

    /**
     * 添加翻页参数的统一方法
     * @param string $pageName
     * @param string $offsetName
     */
    public function supportPagination($pageName = "page", $offsetName = "count")
    {
        parent::addRule($pageName, "int", false);
        parent::addRule($offsetName, "int", false);
    }

    /**
     * 添加base_app参数的统一方法
     */
    public function supportBaseApp()
    {
        parent::addRule("base_app", "string", false);
    }

    /**
     * 添加trimUser参数的统一方法
     */
    public function supportTrimUser()
    {
        parent::addRule("trim_user", "int", false);
    }

    /**
     * 添加trim_status参数的统一方法
     */
    public function supportTrimStatus()
    {
        parent::addRule("trim_status", "int", false);
    }

    /**
     * 添加支持gzip的统一方法
     */
    public function supportGzip()
    {
        $this->httpRequest->gzip = true;
    }

    /**
     * 添加游标参数的统一方法
     */
    public function supportCursor($sinceIdName = "since_id", $maxIdName = "max_id")
    {
        parent::addRule($sinceIdName, "int64");
        parent::addRule($maxIdName, "int64");
    }

    /**
     * 返回结果是否转义
     */
    public function supportEncode()
    {
        parent::addRule('is_encoded', 'int', false);
    }

    /**
     * 当uid和screenName互斥时的添加方法
     * Enter description here ...
     * @param string $uidName uid参数名
     * @param string $screen screen_name参数名
     * @param boolean $isBatch 是否为批量值
     */
    public function uidOrScreenName($uidName = 'uid', $screen = 'screen_name', $isBatch = false)
    {
        parent::addRule($uidName, "string");
        parent::addRule($screen, "string");

        if ($isBatch)
        {
            parent::addSetCallback($uidName, 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 20));
            parent::addSetCallback($screen, 'Comm_Weibo_Api_Util', 'checkBatchValues', array('string', ',', 20));
        }

        parent::addBeforeSendCallback('Comm_Weibo_Api_Util', "checkAlternative", array($uidName, $screen));
    }

    /**
     * 生成接口url
     * @param string $resource
     * @param string $interface
     * @param string $format
     * @param string $version
     * @param bool $isV4
     * @param $preQuery
     * @throws Comm_Exception_Program
     */
    public static function assembleUrl($resource, $interface, $format = "json", $version = null, $isV4 = true, $preQuery = '', $isV5 = false)
    {
        if (isset($version) && $version < self::$platformApiVersionLeast)
        {
            throw new Comm_Exception_Program("api least version: ".self::$platformApiVersionLeast);
        }

        if (empty($format))
        {
            $format = self::$platformApiDefaultFormat;
        }

        if (true === $isV4)
        {
            $url = self::$platformApiServerName . '/' . $resource;
        }
        elseif (true == $isV5)
        {
            $url = self::$platformApiServerNameV5 . '/' . $resource;
        }
        else
        {
            if (isset(self::$_internalApis[$resource]))
            {
                $url = self::$platformApiServerNameInternal . '/' . $resource;
            }
            else
            {
                $url = self::$platformApiServerNameV3 . '/' . $resource;
            }
        }

        $url .= (isset($version) ? '/' . $version : "");
        
        if (!empty($interface))
        {
            $url .= '/' . $interface;
        }

        $url .= '.' . $format;

        if (!empty($preQuery))
        {
            $url .= "?$preQuery";
        }

        return $url;
    }

    /**
     * 用户名、密码方式访问时，设定相关信息
     * @param string $defaultUser
     * @param string $defaultPassword
     */
    public function addUserPassword($defaultUser = null, $defaultPassword = null)
    {
        $this->usePassword = true;
        $user = empty($defaultUser) ? Comm_Config::get("env.unlogin_reg_user") : $defaultUser;
        $pwd = empty($defaultPassword) ? Comm_Config::get("env.unlogin_reg_psw") : $defaultPassword;

        $this->httpRequest->addUserPassword($user, $pwd);
    }

    /**
     * 使用tauth2方式认证
     * @param string $defaultUser
     * @author 罗圆 <luoyuan@staff.sina.com.cn>
     * @version 2.0 2014-04-29
     */
    public function useTAuth2Authorize($defaultUser = null)
    {
        if (empty($defaultUser))
        {
            return false;
        }

        $tauth2Header = Tool_TAuth2::getTAuth2Header($defaultUser);
        if ($tauth2Header === false)
        {
            return false;
        }

        Comm_Context::set('tauth2_uid', $defaultUser, array(), true);
        Comm_Context::set('tauth2_auth_string', $tauth2Header, array(), true); 

        $this->useTAuth2 = true;
        return true;
    }

    /**
     * 返回http请求对象
     */
    public  function getHttpRequest()
    {
        return $this->httpRequest;
    }
}
