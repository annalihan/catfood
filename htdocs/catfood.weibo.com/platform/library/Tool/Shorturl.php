<?php
class Tool_Shorturl
{
    private static $_shortUrlDomains = array("t.cn" => 1, "sinaurl.cn" => 1);
    private static $_httpSchemes = array("http" => 1, "https" => 1);
    
    /**
     * 判断长链接格式是否合法
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function checkLongUrl($url)
    {
        $urlInfo = parse_url($url);
        return isset($urlInfo['scheme']) && isset(self::$_httpSchemes[strtolower($urlInfo['scheme'])]);
    }
    
    /**
     * 判断短链接格式是否合法
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function checkShortUrl($url)
    {
        $urlInfo = parse_url($url);
        return isset($urlInfo['host']) && isset(self::$_shortUrlDomains[strtolower($urlInfo['host'])]);
    }
}