<?php
class Comm_Weibo_Api_Members
{
    const RESOURCE = "members";
    const PROXY_RESOURCE = "proxy/members";

    /**
     * 获取会员信息。
     * 废弃接口，改调此类中show_detail方法
     * @deprecated 
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", false);
        $platform->addRule('flag', "int", false);

        return $platform;
    }

    /**
     * 批量获取用户会员状态
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "string");
        $platform->addRule('flag', "int", false);

        return $platform;
    }

    /**
     * 获取会员详细信息
     */
    public static function showDetail()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::PROXY_RESOURCE, "show_detail");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int", false);
        
        return $platform;
    }
}