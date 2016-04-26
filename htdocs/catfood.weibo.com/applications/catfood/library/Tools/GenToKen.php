<?php
/*
 * @title     token生成验证类
 * @Author    quanjunw
 * @Date      2014-5-5下午6:48:04
 * @Copyright copyright(2014) weibo.com all rights reserved 
 */
class Tools_GenToKen
{
    //请求的url
    static  $url;
    //生成一个token
    public static  function getToken($len = 32, $md5 = true)
    {
        $token = '';
        $uid = uniqid("", true);
        
        mt_srand((double)microtime() * 1000000);
        $chars = Comm_Context::getServer('SCRIPT_URI');
        $chars .= Comm_Context::getServer('REQUEST_TIME');
        $chars .= Comm_Context::getServer('SERVER_ADDR');
        $chars .= Comm_Context::getServer('REMOTE_ADDR');

        $hash = strtoupper(hash('ripemd128', $uid . $token . md5($chars)));
        
        $token = substr($hash, 0, 8) . substr($hash, 8, 4) . substr($hash, 12, 4);
        return $token;
    }
    
    //判断请求来源是否正确
    public static function isReferer()
    {
        if (Comm_Context::isXmlHttpRequest())
        {
            //获取当前访问url
            $referer = Comm_Context::getServer('HTTP_REFERER');
            $newStr = parse_url($referer);
                        
            $url =  'http://' . $newStr['host'] .'/';
    
            if (in_array($url, Comm_Config::get('domain')))
            {
                return true;
            }
            else 
            {
                return false;
            }
        }
        return false;
    }
}