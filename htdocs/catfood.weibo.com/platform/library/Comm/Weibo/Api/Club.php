<?php
class Comm_Weibo_Api_Club
{
    public static function interest()
    {
        $url = "http://i.service.t.sina.com.cn/club/getDarenInterest.php";
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");
        
        return $platform;
    }
}
