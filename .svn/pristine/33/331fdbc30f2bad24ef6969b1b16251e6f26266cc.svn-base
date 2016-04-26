<?php
class Comm_Weibo_Api_Photos
{
    const RESOURCE = "photos";

    /**
     * 获取用户基本信息
     */
    public static function getUserPhotos()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "photo");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("page", "int");
        $platform->addRule("count", "int");
        $platform->addRule("feature", "int");
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 获取微博配图
     */
    public static function getWeiboPhotos()
    {
        //V5 API
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "user_timeline/ids", 'json',  null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("page", "int");
        $platform->addRule("count", "int");
        $platform->setRequestTimeout(2000, 2000);
        
        return $platform;
    }

}
