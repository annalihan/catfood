<?php
class Tool_Version
{
    //版本标识字符
    const VERSION_V36 = 'v36';
    const VERSION_V4 = 'v4';
    const VERSION_V5 = 'v5';
    private static $_isV5Page = null;

    private static $_notV5Path = array(
        'whisper' => true, 
        'history' => true, 
        'upgrade' => true, 
        'f' => true, 
        's' => true, 
        'i' => true, 
        'recv' => true, 
        'list' => true, 
        'record' => true, 
        'handbook' => true, 
        'logo' => true, 
        'accessdeny' => true, 
    );

    private static $_notV5Path1 = array(
        'message' => true, 
        'messages' => true, 
        'sorry' => true, 
        'unfreeze' => true, 
        'reportspam' => true, 
        'login' => true
    );

    /**
     * 获取当前版本信息
     */
    public static function getVersion()
    {
        return Dr_User::getUserVersion();
    }
    
    /**
     * 根据当前版本信息获取对应系统版本标识
     */
    public static function getVersionMark()
    {
        try
        {
            $version = self::getVersion();

            switch ($version)
            {
                case "3.6" :
                    return self::VERSION_V36;
                case "5" :
                    return self::VERSION_V5;
                default :
                    return self::VERSION_V4;
            }
        }
        catch (Comm_Exception_Program $e)
        {
            return self::VERSION_V5;
        }
    }
    
    public static function isV5Page()
    {
        if (!is_null(self::$_isV5Page))
        {
            return self::$_isV5Page;
        }

        $uri = Comm_Context::get_server('SCRIPT_URL');
        $re = preg_match("/^\/([a-z0-9A-Z_\-]+)\/([a-z0-9A-Z_]+)$/", $uri, $match);

        if (($re && isset(self::$_notV5Path[$match[2]]) === false) || $uri == '/aj/user/cardv5')
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        //新手指南需要判断用户版本
        if ($re && $match[2] == 'handbook')
        {
            if (Dr_User::getUserVersion() == 5)
            {
                self::$_isV5Page = true;
                return self::$_isV5Page;
            }
            else
            {
                self::$_isV5Page = false;
                return self::$_isV5Page;
            }
        }

        if ($re && $match[2] == 'history' && Tool_Version::isV5Msg())
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        if ($re && $match[2] == 'message' && Tool_Version::isV5Msg())
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        $xinqing = preg_match("/^\/u\/([0-9]+)\/([a-z0-9A-Z_]+)\/([a-z0-9A-Z_]*)$/", $uri, $match2);
        if ($xinqing && $match2 [2] == 'xinqing')
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }
        
        //旺铺后台iframe页面
        if (false !== strpos($uri, '/taobao/') || false !== strpos($uri, '/aj/'))
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        if (preg_match('/^\/u\/(\d{5,10})($|\/([a-z0-9A-Z]+)?$)/', $uri, $match))
        {
            if ($match[3] == 'home')
            {
                self::$_isV5Page = true;
                return self::$_isV5Page;
            }
        }
        
        $re = preg_match("/^\/([a-z0-9A-Z_\-]+)$/", $uri, $match);
        if ($re && isset(self::$_notV5Path1[$match[1]]) === false)
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        if ($re && $match[1] == 'messages' && Tool_Version::isV5Msg())
        {
            self::$_isV5Page = true;
            return self::$_isV5Page;
        }

        self::$_isV5Page = false;
        return self::$_isV5Page;
    }

    /**
     * 
     * 监测是否调用V5版私信
     *
     */
    public static function isV5Msg()
    {
        return Dr_User::getUserVersion() == 5;
    }
}
