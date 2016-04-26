<?php
class Comm_Weibo_Api_Request_Mss extends Comm_Weibo_Api_Request_Platform
{
    private static $_domain = "http://upload.api.weibo.com/";
    
    public static function assembleUrl($resource, $interface, $format = "json", $version = null, $isV4 = true, $preQuery = '', $isV5 = false)
    {
        $url = self::$_domain;

        if (isset($version))
        {
            $url .= $version . "/";
        }
        
        $url .= $resource . "/" . $interface . "." . $format;
        
        return $url; 
    }
}