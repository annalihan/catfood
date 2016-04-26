<?php
class Comm_Weibo_Api_Quota
{
    const RESOURCE = "messages/quota";

    /**
     * 增加商业配额接口
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function increaseQuotaAmount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "increase");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("amount", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 减少商业配额接口
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function descreaseQuotaAmount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "descrease");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("amount", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 查询剩余的商业配额
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getLeftBusinessQuota()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "amount");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);
        $platform->addRule('fansuid', 'int64');
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }
}
