<?php
class Comm_Weibo_Api_Report
{
    const RESOURCE = "report";

    /**
     * 举报某条信息
     */
    public static function spam()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "spam");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("url", "string", true);
        $platform->addRule("content", "string", true);
        $platform->addRule("ip", "string", true);
        $platform->addRule("type", "int", false);
        $platform->addRule("rid", "int64", false);
        $platform->addRule("class_id", "int", false);

        return $platform;
    }
}
