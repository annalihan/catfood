<?php
class Comm_Weibo_Api_Iremind
{
    const RESOURCE = "iremind";

    /**
     * 清除指定提醒条数
     */
    public static function setCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_count", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("user_id", "int64", true);
        $platform->addRule("type", "string", true);
        $platform->addRule("value", "int", true);

        return $platform;
    }

    /**
     * 清楚所有提醒条数
     */
    public static function clearCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "clear_count", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("user_id", "int64", true);

        return $platform;
    }

    /**
     * 获取未读数
     */
    public static function unreadCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl("remind", "unread_count", "json", null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("user_id", "int64", true);
        $platform->addRule("target", "string");
        
        return $platform;
    }
}
