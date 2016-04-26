<?php
class Comm_Weibo_Api_Suggestions_Users
{
    const RESOURCE = "suggestions/users";

    //人气用户推荐
    const CONNECT_TIMEOUT_RECOMMEND = 3000;
    const TIMEOUT_RECOMMEND = 3000;

    /**
     * 把某人标志为不感兴趣的人
     */
    public static function usersNotInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "not_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("trim_status", "int");

        return $platform;
    }

    /**
     * 获取当前登录用户可能感兴趣的人
     */
    public static function mayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "may_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportPagination();

        return $platform;
    }

    /**
     * 获取当前新注册用户可能感兴趣的人
     */
    public static function newUserMayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "worth_follow_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("count", "int", false);
        $platform->addRule('uids', 'string', true);

        return $platform;
    }

    /**
     * 根据一段微博正文推荐相关微博用户
     */
    public static function usersByStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "by_status");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('content', 'string', true);
        $platform->addRule('num', 'int', false);
        $platform->addRule('url', 'string', false);

        return $platform;
    }

    /**
     * 根据当当前登录用户所查看的用户，获取给当前登录用户的推荐关注
     */
    public static function worthFollow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "worth_follow");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'int64', true);
        $platform->supportPagination();

        return $platform;
    }

    /**
     * 获取当前登录用户人气用户推荐列表
     */
    public static function hot()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "recommend");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('atten_num', 'int', false);
        $platform->addRule('region_num', 'int', false);
        $platform->addRule('v1_num', 'int', false);
        $platform->addRule('searched_24', 'int', false);
        $platform->addRule('original_7', 'int', false);
        $platform->addRule('my_searched', 'int', false);
        $platform->addRule('interest_num', 'int', false);
        $platform->addRule('manual_push_id', 'int', true);
        $platform->addRule('manual_num', 'int', true);
        $platform->addRule('manual_is_mix', 'int', true);
        $platform->setRequestTimeout(self::CONNECT_TIMEOUT_RECOMMEND, self::TIMEOUT_RECOMMEND);

        return $platform;
    }

    /**
     * 根据来源推荐用户
     */
    public static function users_by_location()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "by_location/ids");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");        
        $platform->addRule('province', 'int', false);
        $platform->addRule('category', 'int', false);
        
        return $platform;
    }
}
