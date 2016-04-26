<?php
class Comm_Weibo_Api_Comments
{
    const RESOURCE = 'comments';

    /**
     * 根据微博消息ID返回某条微博消息的评论列表的。
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportCursor();
        $request->supportPagination();
        $request->addRule('id', 'int64', true);
        $request->addRule('filter_by_author', 'int', false);
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 我发出的评论列表
     */
    public static function byMe()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'by_me');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('filter_by_source', 'int', false);
        $request->supportCursor();
        $request->supportPagination();

        return $request;
    }

    /**
     *   我收到的评论列表
     */
    public static function toMe()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'to_me');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('filter_by_source', 'int', false);
        $request->supportCursor();
        $request->supportPagination();
        $request->addRule('filter_by_author', 'int', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 获取当前用户发送及收到的评论列表
     */
    public static function timeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportCursor();
        $request->supportPagination();
        $request->supportTrimUser();

        return $request;
    }

    /**
     * 返回最新n条提到登录用户的评论
     */
    public static function mentions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'mentions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportCursor();
        $request->supportPagination();
        $request->addRule('filter_by_author', 'int', false);
        $request->addRule('filter_by_source', 'int', false);

        return $request;
    }

    /**
     * 根据批量评论ID返回评论信息
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('cids', 'string', true);
        $request->addSetCallback('cids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 50));

        return $request;
    }

    /**
     * 评论一条微博
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('comment', 'string', true);
        $request->addRule('id', 'int64', true);
        $request->addRule('comment_ori', 'int', false);
        $request->addRule('skip_check', 'int');

        //由于saas检测会有超时情况，临时调整接口超时时间为2s
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 删除一条评论
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('cid', 'int64', true);
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 批量删除微博
     */
    public static function destroyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('cids', 'string', true);
        $request->addSetCallback('cids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 20));

        return $request;
    }

    /**
     * 回复一条微博
     */
    public static function reply()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'reply');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('cid', 'int64', true);
        $request->addRule('id', 'int64', true);
        $request->addRule('comment', 'string', true);
        $request->addRule('without_mention', 'int', false);
        $request->addRule('comment_ori', 'int', false);
        $request->addRule('skip_check', 'int');
        
        //由于saas检测会有超时情况，临时调整接口超时时间为2s
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 获取热门评论列表
     */
    public static function hotTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'hot/timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'int64', true);
        $request->supportCursor();
        $request->supportPagination();
        $request->supportTrimUser();

        return $request;
    }
}
