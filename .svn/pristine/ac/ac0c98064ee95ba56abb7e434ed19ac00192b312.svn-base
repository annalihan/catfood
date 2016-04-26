<?php

class Tool_Redirect
{
    /**
     * 发送重定向请求
     * @param unknown_type $url
     * @param unknown_type $code
     * @param unknown_type $msg
     */
    public static function response($url, $code = 0, $msg = '')
    {
        if (Comm_Context::isXmlHttpRequest())
        {
            if (Comm_Context::param('ajaxpagelet', 0))
            {
                echo 'parent.windows.location=' . $url;
                exit();
            }
            else
            {
                echo Tool_Jsout::normal($code, $msg, $url);
                exit();
            }
        }
        else
        {
            //防止出现死环境
            if ('/sorry' == $_SERVER['SCRIPT_URL'])
            {
                return;
            }

            header('Location: ' . $url);
            exit();
        }
    }
    
    /**
     * 访问的用户不存在
     */
    public static function userNotExists()
    {
        $url = Comm_Config::get('domain.weibo') . '/sorry?usernotexists';
        $code = '100004';
        self::response($url, $code); 
    }
    
    /**
     * 退出登录
     */
    public static function unlogin()
    {
        $oldCurrentUrl = Comm_Context::getCurrentUrl();
        
        //白名单 退出登录的时候 ajax接口跳转到weibo.com
        $ajaxUrls = array(rawurlencode('/aj/'), rawurlencode('/ajm/'));
        $isAjaxUrl = false;
        foreach ($ajaxUrls as $v)
        {
            if (strstr($oldCurrentUrl, $v))
            {
                $isAjaxUrl = true;
            }
        }

        if ($isAjaxUrl == false)
        {
            $currentUrl = $oldCurrentUrl;
        }
        else
        {
            $currentUrl = rawurlencode(Comm_Context::getServer('HTTP_REFERER'));
        }

        $url = Comm_Config::get('domain.weibo') . '/login.php?url=' . $currentUrl;
        $code = '100002';
        setcookie('wvr', "", time() - 3600, "/", ".weibo.com");
        setcookie('SUE', "", time() - 3600, "/", ".weibo.com");
        setcookie('SUP', "", time() - 3600, "/", ".weibo.com");
        self::response($url, $code);
    }

    /**
     * 跳转到未登录首页
     */
    public static function unloginHome()
    {
        setcookie('wvr', "", time() - 3600, "/", ".weibo.com");
        $url = Comm_Config::get('domain.weibo') . '/logout.php';
        self::response($url);
    }
    
    public static function reg($invitecode = '', $type = '', $c = '')
    {
        setcookie('wvr', "", time() - 3600, "/", ".weibo.com");
        setcookie('SUE', "", time() - 3600, "/", ".weibo.com");
        setcookie('SUP', "", time() - 3600, "/", ".weibo.com");
        
        //拼装要跳转的url
        $url = Comm_Config::get('domain.weibo') . '/signup/signup.php?inviteCode=' . $invitecode;
        
        if ($type)
        {
            $url .= '&type=' . $type;
        }

        if (!empty($c))
        {
            $url .= '&c=' . $c;
        }
        
        //用户无登录状态，并且retcode!=0，将retcode参数带到注册页 (MINIBLOGBUG-27060)
        $retcode = Comm_Context::param('retcode', null);
        if (!is_null($retcode) && $retcode != 0)
        {
            $url .= '&retcode=' . $retcode; 
        }
        
        //跳转
        self::response($url);
        exit;
    }

    /**
     * 拉黑用户
     */
    public static function userBlock($isViewer = false)
    {
        //status = 7
        $url = Comm_Config::get('domain.weibo') . '/sorry?userblock';
        $isViewer && $url .= '&is_viewer';
        $code = '100003';
        self::response($url, $code);        
    }    
    
    /**
     * 冻结用户
     */
    public static function userFreeze()
    {
        //status = 8
        $url = Comm_Config::get('domain.weibo') . '/sorry?from=' . Comm_Context::getCurrentUrl();
        $code = '100004';
        self::response($url, $code);        
    }
    
    /**
     * 未开通微博处理情况
     */
    public static function needOpenMblog()
    {
        $url = Comm_Config::get('domain.weibo') . '/reg.php?url=' . Comm_Context::getCurrentUrl();
        $code = '100002';
        self::response($url, $code);
    }
    
    /**
     * 系统繁忙处理
     */
    public static function systemBusy()
    {
        $url = Comm_Config::get('domain.weibo') . '/sorry?sysbusy';
        $code = '100005';
        self::response($url, $code); 
    }
    
    /**
     * 页面未找到处理
     */
    public static function pageNotFound()
    {
        $url = Comm_Config::get('domain.weibo') . '/sorry?pagenotfound';
        $code = '100006';
        self::response($url, $code); 
    }
    
    /**
     * 用户信息不完整
     * Enter description here ...
     */
    public static function fullInfo()
    {   
        $url = Comm_Config::get('domain.weibo') . '/signup/full_info.php?nonick=1&lang=' . Comm_I18n::getCurrentLang() . '&callback=' . urlencode(Comm_Config::get('domain.weibo'));
        $code = '100007';
        self::response($url, $code); 
    }
    
    /**
     * 页面未找到处理
     */
    public static function pageUnfreeze()
    {
        $url = Comm_Config::get('domain.weibo') . '/unfreeze';
        $code = '100006';
        self::response($url, $code); 
    }
}
