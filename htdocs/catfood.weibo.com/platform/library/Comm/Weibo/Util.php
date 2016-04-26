<?php
class Comm_Weibo_Util
{    
    const LOWER_IE_BROWER = 1;
    const HANDLE_MAC_EQUIPMENT = 2;

    /**
     * 根据用户浏览器获取特殊的css标识
     * @return int
     */
    public static function detectSpecialCss()
    {
        $showSpecialCss = false;

        if (Comm_ClientProber::isIE())
        {
            $version = Comm_ClientProber::getVersion();
            if ($version >= 7)    
            {
                $showSpecialCss = self::LOWER_IE_BROWER;
            }
        }

        $info = Comm_ClientProber::getAgent();
        if (isset($info['mobile']) && $info['mobile'] && isset($info['platform']) && $info['platform'] && $info['platform'] == 'Mac OS X')
        {
            $showSpecialCss = self::HANDLE_MAC_EQUIPMENT;
        }

        return $showSpecialCss;
    }
}