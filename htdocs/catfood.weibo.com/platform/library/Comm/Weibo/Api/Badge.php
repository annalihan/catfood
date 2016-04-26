<?php
class Comm_Weibo_Api_Badge
{
    const RESOURCE = 'proxy/badges';

    public static function badge()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "badge");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");
        $platform->addRule("count", "int");
        $platform->addRule("page", "int");
        $platform->addRule("is_show", "int");
        $platform->addRule("lang", "string");

        return $platform;
    }

    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("bid", "string", true);
        $platform->addRule("lang", "string");
        
        return $platform;
    }

    public static function badgeIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "badge/ids");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64");
        $platform->addRule("count", "int");
        $platform->addRule("page", "int");
        $platform->addRule("is_show", "int");
        
        return $platform;
    }

    /**
     * 给用户颁发勋章
     */
    public static function issue()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "issue",  "json",  null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("source", "string", true);
        $platform->addRule("uids", "int64", true);
        $platform->addRule("badge_id", "string", true);

        return $platform;
    }
}
