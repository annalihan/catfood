<?php
class Comm_Weibo_Api_Attitudes
{
    const RESOURCE = 'attitudes';

    /**
     * 根据微博消息ID返回该微博表态信息
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportCursor();
        $request->supportPagination();
        $request->addRule('id', 'int64', true);
        $request->addRule('base_app', 'int', false);
        $request->addRule('filter_by_author', 'int', false);
        $request->addRule('filter_by_source', 'int', false);
        //$request->addRule('type', 'string', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 我收到的喜欢列表，喜欢为维度
     */
    public static function toMe()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'to_me');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('filter_by_source', 'int', false);
        $request->addRule('filter_by_author', 'int', false);
        //$request->addRule('type', 'string', false);
        $request->supportCursor();
        $request->supportPagination();
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 批量获取当前用户对微博是否表态过
     */
    public static function exists()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'exists');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('ids', 'string', true);

        return $request;
    }

    /**
     * 收到的表态列表，以微博为维度做合并
     */
    public static function toMeStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'to_me_status'); //to_me
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('filter_by_source', 'int', false);
        $request->supportCursor();
        $request->supportPagination();
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 对一条微博发表或更新一个喜欢
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');        
        $request->addRule('attitude', 'string', true);
        $request->addRule('id', 'int64', true);
        $request->addRule('mark', 'string', false);
        $request->addRule('spr', 'string', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 删除一条自己的喜欢
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');        
        $request->addRule('id', 'int64', true);
        $request->addRule('attid', 'int64', false);
        $request->addRule('spr', 'string', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 删除喜欢计数
     */
    public static function clearUnread()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'clear_unread');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'int64', true);

        return $request;
    }

    /**
     * 批量获取微博IDS对应的表态计数（支持简版表态计数，type=heart）
     */
    public static function counts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'counts');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('ids', 'string', true);
        //$request->addRule('type', 'string', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }
}