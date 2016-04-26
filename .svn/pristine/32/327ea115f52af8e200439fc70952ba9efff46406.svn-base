<?php
class Comm_Weibo_Api_Limit
{
    const RESOURCE = "messages/limit";

    /**
     * 设置用户流量控制
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function setFlowLimit()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_flow");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("amount", "int", true);
        $platform->addRule("unit", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 获取用户流量控制
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getFlowLimit()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "query_flow");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 设置用户每周期内免费赠送给用户的私信数  也就是设置周期限制
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function setFreeQuotaAmount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_circle");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("amount", "int", true);
        $platform->addRule("unit", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    public static function setCircleLimit()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_circle");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("amount", "int", true);
        $platform->addRule("unit", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 查询用户每周期内免费赠送给用户的私信数  也就是查询周期限制
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function queryFreeQuotaAmount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "query_circle");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    public static function getCircleLimit()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "query_circle");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }
}
