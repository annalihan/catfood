<?php
class Comm_Weibo_Api_Groups_Suggestions
{
    const RESOURCE = "groups";

    /**
     * 获取当前登录用户可能感兴趣的微群列表
     */
    public static function mayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "suggestions/may_interested", 'json', null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('count', 'int', true);

        return $platform;
    }
}
