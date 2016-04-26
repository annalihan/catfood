<?php
class Comm_Weibo_Api_Groups_Statuses
{
    const RESOURCE = "groups";

    /**
     * 发布微博信息
     * @param int $groupId
     */
    public static function publish($groupId)
    {
        Comm_Weibo_Api_Util::checkInt($groupId);
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $groupId . '/statuses/publish', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");

        $request->addRule('pic_file', 'filepath', false);
        $request->addRule('status', 'string', true);
        $request->addRule('mid', 'string', true);
        $request->addRule('pic_fid', 'string', false);
        $request->addRule('issync', 'int', false);
        $request->addRule('created_at', 'int', false);
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 获取在所有群@当前用户的微博列表
     */
    public static function mentions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/mentions', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->supportPagination();
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 获取群内@当前用户的未读微博个数
     * @return [type] [description]
     */
    public static function mentionsUnread()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/mentions/unread', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 清空群内@当前用户的未读微博个数
     */
    public static function mentionsResetCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/mentions/reset_count', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 获取用户在所有群收到的评论列表
     * @return [type] [description]
     */
    public static function commented()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/commented', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->supportPagination();
        $request->addRule('state', 'string');
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 获取当前用户的未读群评论个数
     */
    public static function commentsUnread()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/comments/unread', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 清空当前用户的未读群评论个数
     */
    public static function commentsResetCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/comments/reset_count', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->supportBaseApp();

        return $request;
    }
}
