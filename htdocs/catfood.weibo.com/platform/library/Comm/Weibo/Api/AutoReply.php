<?php
class Comm_Weibo_Api_AutoReply
{
    const RESOURCE = "messages/user";

    /**
     * 取消周期限制
     * @param unknown $uid
     */
    public static function deleteAccess()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "logout");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("is_deleted_all", "int");
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 设置用户回复类型
     * @param string $interface
     */
    public static function setReplyType()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_reply_type");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("reply_type", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 设置自动回复开关是否开启
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function setUserEnable()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('messages/auto_reply', "set_user_enable");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("is_enable", "int", true);
        $platform->setRequestTimeout(2000, 2000);

        return  $platform;
    }

    /**
     * 设置“+关注”和私信自动回复内容接口
     * @return string
     */
    public static function addReplayRule($interface = 'messages/auto_reply/set_auto_reply')
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('messages/auto_reply', "set_auto_reply");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule('uid', "int64", true);
        $platform->addRule('reason', 'int', true);
        $platform->addRule('type', 'string');
        $platform->addRule('text', 'string', true);
        $platform->addRule('data', 'string', false);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }

    /**
     * 设置关键词自动回复内容的规则
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function setAutoReplyRule()
    {
        //TODO
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('messages/auto_reply', "set_rule");
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $platform->addRule('uid', "int64", true);
        $platform->addRule('is_send_all', 'int', true);
        $platform->addRule('rule', 'string', true);
        $platform->addRule('keywords', 'string', true);
        $platform->addRule('whole_match', 'string', true);
        $platform->addRule('types', 'string', true);
        $platform->addRule('texts', 'string', true);
        $platform->addRule('datas', 'string', false);
        $platform->setRequestTimeout(2000, 2000);

        return $platform;
    }
}
