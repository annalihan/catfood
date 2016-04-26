<?php
/**
 * 推荐相关的透明代理接口
 */
class Comm_Weibo_Api_Proxy_Suggestions
{
    const RESOURCE = "proxy/suggestions";

    /**
     * 密友推荐
     * wiki: http://wiki.intra.weibo.com/2/proxy/suggestions/users/close_friends
     */
    public static function usersCloseFriends()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "users/close_friend/list");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int", true);
        
        return $platform;
    }

    /**
     * 批量添加用户不感兴趣的密友。 
     * wiki: http://wiki.intra.weibo.com/2/proxy/suggestions/users/close_friend/add_uninterested_batch
     */
    public static function usersCloseFriendsAddUninterestedBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "users/close_friend/add_uninterested_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int", true);
        $platform->addRule("uninterested_uids", "string", true);

        return $platform;
    }

    /**
     * 把某人标志为不感兴趣的人
     */
    public static function usersNotInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "users/not_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("fuid", "int64", true);

        return $platform;
    }
}