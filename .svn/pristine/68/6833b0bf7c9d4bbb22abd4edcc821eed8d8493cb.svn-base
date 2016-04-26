<?php
class Comm_Weibo_Api_Unread
{
    const RESOURCE = 'unread';

    /**
     * 获取用户具体未读消息
     */
    public static function unread()
    {
        $url = "http://rm.api.weibo.com/2/remind/unread_count.json";
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 轮询用户未读消息
     */
    public static function pushCount()
    {
        $url = "http://rm.api.weibo.com/2/remind/push_count.json";
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 小黄签点击关闭
     */
    public static function ignoreCount()
    {
        $url = "http://rm.api.weibo.com/2/remind/ignore_count.json";
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        
        return $request;
    }
}
