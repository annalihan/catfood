<?php
class Comm_Weibo_Api_Notification
{
    const RESOURCE = "notification";

    /**
     * 获取当前应用发送的最新通知列表
     *
     * @param int $page
     * @param int $count
     */
    public static function sendList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "send_list");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);
        
        return $request;
    }

    /**
     * 获取用户通知列表
     *
     * @param int $page
     * @param int $count
     */
    public static function appsList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "apps_list");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);

        return $request;
    }

    /**
     * 获取当前用户收到的最新通知列表
     *
     * @param int $page
     * @param int $count
     */
    public static function receiveList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "receive_list");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);

        return $request;
    }

    /**
     * 获取当前用户收到的指定应用的通知列表
     *
     * @param string appkey62
     * @param int $page
     * @param int $count
     */
    public static function receiveListByApp()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "receive_list_by_app");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('appkey62', "string", true);
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);

        return $request;
    }

    /**
     * 给一个或多个用户发送一条新的状态通知
     *
     * @param int $page
     * @param int $count
     * @param string objects1
     * @param int objects1_count
     * @param string objects2
     * @param int objects2_count
     * @param string objects3
     * @param int objects3_count
     * @param string action_url
     */
    public static function send()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "send");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uids', "string", true);
        $request->addRule('tpl_id', "int", true);
        $request->addRule('objects1', "string", false);
        $request->addRule('objects1_count', "int", false);
        $request->addRule('objects2', "string", false);
        $request->addRule('objects2_count', "int", false);
        $request->addRule('objects3', "string", false);
        $request->addRule('objects3_count', "int", false);
        $request->addRule('action_url', "string", false);

        //TODO
        //账号为tongzhifs@sina.com密码为eps_tongzhi
        $request->addUserPassword("tongzhifs@sina.com", "eps_tongzhi");

        return $request;
    }

    /**
     * 屏蔽某个应用
     */
    public static function appBlock()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/block");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('appkey62', "string", true);

        return $request;
    }

    /**
     * 解除屏蔽某个应用
     */
    public static function appUnblock()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/unblock");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('appkey62', "string", true);

        return $request;
    }

    /**
     * 获取用户屏蔽通知应用列表
     */
    public static function appBlockList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/block_list");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', "int64", true);
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);

        return $request;
    }

    /**
     * 获取当前用户未屏蔽了通知的应用列表
     */
    public static function appUnblockList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/unblock_list");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', "int64", true);
        $request->addRule('page', "int", false);
        $request->addRule('count', "int", false);

        return $request;
    }

    /**
     * 批量判断应用是否可以被用户屏蔽其通知
     */
    public static function appCanBlock()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/can_block");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('appkey62s', "string", true);

        return $request;
    }

    /**
     * 批量判断通知是否被当前用户屏蔽
     */
    public static function appCheckBlocked()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app/check_blocked");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('appkey62s', "string", true);

        return $request;
    }
}
