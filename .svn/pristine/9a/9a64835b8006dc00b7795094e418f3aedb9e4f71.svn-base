<?php
class Comm_Weibo_Api_Apps
{
    const RESOURCE = "apps";

    /**
     * 获取应用相关信息
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("appkey62s", "string", true);

        return $platform;
    }

    /**
     * 通过appkey获取appkey62
     */
    public static function getAppkey62()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_appkey62");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("ids", "string", true);

        return $platform;
    }
}

