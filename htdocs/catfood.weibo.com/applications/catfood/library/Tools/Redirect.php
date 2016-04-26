<?php
/*
 * @title     页面跳转函数
 * @Author    quanjunw
 * @Date      2014-5-4下午1:33:22
 * @Copyright copyright(2014) weibo.com all rights reserved 
 */
class Tools_Redirect extends Tool_Redirect
{
    const PAGENOTFOUND = 404;
    const SYSTEMBUSY   = 500;
    /**
     * 跳转到未登录首页
     */
    public static function unloginHome()
    {
        setcookie('wvr', "", time() - 3600, "/", ".weibo.com");
        $url = Comm_Config::get('domain.weibo') . '/logout.php?url=' . $_SERVER['SERVER_NAME'];
        self::response($url);
    }
    
    //自定义跳转
    public static function customSkip($code)
    {
        switch ($code)
        {
            case 404:
                $url = Comm_Config::get('domain.weibo') . '/sorry?pagenotfound';
                $code = '100006';
                self::response($url, $code);
                break;
            case 500:
                $url = Comm_Config::get('domain.weibo') . '/sorry?sysbusy';
                $code = '100005';
                self::response($url, $code);
                break;
        }
    }
}