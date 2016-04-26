<?php
require_once T3P_PATH . '/sinasso/SSOWeiboClient.php';
require_once T3P_PATH . '/sinasso/SSOWeiboCookie.php';
require_once PLATFORM_PATH . '/config/SSOConfig.php';
class Comm_Weibo_SinaSSO
{
    /**
     *
     * @var object SSOWeiboClient 实例
     */
    private static $_ssoClient = null;

    /**
     *
     * @var bool 是否登陆
     */
    private static $_isLogon = null;

    /**
     * 从cookie中获取信息判断用户是否登陆
     */
    public static function isLogon($mustCheckSession = false)
    {
        $ssoClient = self::_getSSOClient();

        if (self::$_isLogon === null)
        {
            // 关键行为验证用户的session
            if ($mustCheckSession == true)
            {
                $ssoClient->setConfig('use_session', true);
            }

            self::$_isLogon = $ssoClient->isLogined();
        }

        return self::$_isLogon;
    }

    /**
     * 从cookie中获取基本用户信息，如uid, gender, screenname等
     */
    public static function getUserInfo($mustCheckSession = false)
    {
        if (!self::isLogon($mustCheckSession))
        {
            throw new Comm_Weibo_Exception_SinaSSO("need login");
        }

        $ssoUserInfo = self::_getSSOClient()->getUserInfo();
        if (!isset($ssoUserInfo ['uniqueid']) || !isset($ssoUserInfo ['uid']))
        {
            throw new Comm_Weibo_Exception_SinaSSO("need relogin");
        }

        if (isset($ssoUserInfo ['uniqueid']))
        {
            $ssoUserInfo ['uid'] = $ssoUserInfo ['uniqueid'];
        }

        return $ssoUserInfo;
    }

    /**
     * 销毁cookie
     */
    public static function logout()
    {
        $ssoClient = self::_getSSOClient();
        $ssoClient->logout();
    }

    /**
     * 确保sso只被实例化一次
     */
    private static function _getSSOClient()
    {
        if (self::$_ssoClient === null)
        {
            self::$_ssoClient = new SSOClient();
        }

        return self::$_ssoClient;
    }

    /**
     * 获取用户详细信息,必须保证用户已登录或指定$uid 参数
     */
    public static function getUserInfoBySSO($uid)
    {
        $ssoClient = self::_getSSOClient();
        return $ssoClient->getUserInfoByUniqueid($uid);
    }
}
