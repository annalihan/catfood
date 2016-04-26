<?php
class Comm_Weibo_Api_Request_Idott extends Comm_Weibo_Api_Request_Abstract
{
    public static $idottApiServerName = "http://i.t.sina.com.cn";
    public static $idottApiServerNameIp = "http://127.0.0.1";
    public static $idottApiDefaultFormat = "json";

    const REQUEST_HOST_KEY = 'Host';
    const REQUEST_HOST_VALUE = 'i.t.sina.com.cn';
    protected static $idottApiByIp = false;

    const ERROR_TYPE_API = 1;
    const ERROR_TYPE_SYS = 2;
    const ERROR_TYPE_INFO = 3;

    public function __construct($url, $method, $uid = null)
    {
        if ($uid == null)
        {
            if ($_COOKIE['SUP'])
            {
                if (preg_match('/uid=([0-9]+)/', $_COOKIE['SUP'], $matches))
                {
                    $uid = $matches[1];
                }
            }
        }

        if ($uid)
        {
            $url .= '&uid=' . $uid;
        }
        else
        {
            throw new Comm_Exception_Program("i.t interface need uid");
        }

        parent::__construct($url, $method);
        if (self::$idottApiByIp)
        {
            $this->httpRequest->addHeader(self::REQUEST_HOST_KEY, self::REQUEST_HOST_VALUE);
        }
    }

    public function supportUid()
    {
        parent::addRule("uid", "int64", true);
    }

    public function supportGetPage()
    {
        parent::addRule("page", "int", false);
        parent::addRule("pagesize", "int", false);
        parent::addRule("sort", "int", false);
    }

    /**
     * i.t下接口的拼接方法
     * @param string $resource 请求的资源
     * @param string $source 接口的来源（wap，msn，api）
     * @param string $interface 接口名称
     * @param string $cip 当前用户的ip地址
     * @param bool $byIp 是否使用IP使用（即本地接口）
     */
    public static function assembleUrl($resource, $source, $interface, $cip, $byIp = false)
    {
        if (empty($interface))
        {
            throw new Comm_Exception_Program("interface not exist");
        }
        if (empty($cip))
        {
            throw new Comm_Exception_Program("i.t interface need cip");
        }

        $url = self::$idottApiServerName;
        if ($byIp)
        {
            self::$idottApiByIp = $byIp;
            $url = self::$idottApiServerNameIp;
        }

        $appid = Comm_Config::get("env.idott_api_appid");
        $query['cip'] = $cip;
        $query['appid'] = $appid;
        $url = $url . "/" . ((isset($source) && !empty($source)) ? "{$source}/" : "") . ((isset($resource) && !empty($resource)) ? "{$resource}/" : "") . $interface . ".php" . "?" . http_build_query($query);

        return $url;
    }
}
