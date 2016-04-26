<?php
class Comm_Weibo_Api_Suggestions
{
    const RESOURCE = "suggestions";

    /**
     * 把某人标志为不感兴趣的人
     */
    public static function usersNotInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "users/not_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("trim_status", "int");

        return $platform;
    }

    /**
     * 返回系统推荐的用户列表
     */
    public static function favoritesHot()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "favorites/hot");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        
        return $platform;
    }

    /**
     * 获取当前登录用户可能感兴趣的活动列表
     */
    public static function eventsMayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "events/may_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('count', 'int', true);
        
        return $platform;
    }

    public static function allInOne()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "all_in_one");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'int64', true);
        $platform->addRule('num', 'int', true);
        $platform->addRule('type', 'string', true);
        
        return $platform;
    }

    /**
     * 推荐位管理-首页tips
     */
    public static function homeTips()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "banner_tips");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $platform;
    }

    /**
     * 获取人工推荐人气关注用户
     */
    public static function userManualRecommend()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "users/manual_recommend");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("type", "int", true);
        $platform->addRule("count", "int", true);
        $platform->addRule("is_mixed", "int", true);
        
        return $platform;
    }
}
