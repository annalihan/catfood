<?php
class Comm_Weibo_Api_Im
{
    const RESOURCE = "im";

    /**
     * 当前登录用户设置自己的隐身状态
     */
    public static function setPrivacy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "account/set_privacy", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("privacy", "int");

        return $platform;
    }

    /**
     * 当前用户查询自己隐身设置状态
     */
    public static function queryPrivacy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "account/query_privacy", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);

        return $platform;
    }

    /**
     * 查询用户在线状态
     */
    public static function statusQuery()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "status/query", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("is_sample", "int");

        return $platform;
    }

    /**
     * 批量查询用户的在线状态
     */
    public static function statusQueryBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "status/query_batch", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        //$platform->addRule("uid", "int64", true);
        $platform->addRule("uids", "string", true); //这个参数是逗号分隔的string，文档上标为int64是错的
        $platform->addRule("is_sample", "int");

        return $platform;
    }

    /**
     * 查询当前用户的最近联系人
     */
    public static function rosterRecentContacts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "roster/recent_contacts", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

        return $platform;
    }

    /**
     * 查询当前用户好友列表
     */
    public static function rosterFriends()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "roster/friends", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("cursor", "int");
        $platform->supportPagination();

        return $platform;
    }

    /**
     * 搜索好友
     */
    public static function rosterSearch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "roster/roster_search", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("words", "string", true);
        
        return $platform;
    }
}
