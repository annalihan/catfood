<?php
class Comm_Weibo_Api_Statuses
{
    const RESOURCE = 'statuses';

    /**
     * 获取最新的公共微博消息
     */
    public static function publicTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'public_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportBaseApp();

        return $request;
    }

    /**
     * 获取当前登录用户及其所关注用户的最新微博消息
     */
    public static function friendsTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportTrimUser();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);
        $request->addRule('visible', 'int');
        $request->supportGzip();

        return $request;
    }

    /**
     * 获取当前登录用户及其所关注用户的最新微博消息ids
     */
    public static function friendsTimelineIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends_timeline/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportTrimUser();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);
        $request->addRule('visible', 'int');
        //$request->supportGzip();

        return $request;
    }

    /**
     * 获取双向关注用户的最新微博消息
     */
    public static function bilateralTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'bilateral_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);

        return $request;
    }

    /**
     * 获取当前登录用户及其所关注用户的最新微博消息
     */
    public static function homeTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'home_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportTrimUser();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);

        return $request;
    }

    /**
     * 返回用户最新发表的微博消息列表
     */
    public static function userTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);
        $request->addRule('trim_user', 'int', false);
        $request->supportGzip();

        return $request;
    }

    /**
     *返回用户最新发表的微博消息列表ids
     */
    public static function userTimelineIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user_timeline/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination();
        $request->supportBaseApp();
        $request->supportCursor();
        $request->addRule('feature', 'int', false);
        $request->addRule('visible', 'int');
        $request->setRequestTimeout(2000, 2000);
        //$request->supportGzip();

        return $request;
    }

    /**
     * 批量获取指定的一批用户timeline
     */
    public static function timelineBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'timeline_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName('uids', 'screen_name', true);
        $request->supportPagination();
        $request->supportBaseApp();
        $request->addRule('feature', 'int', false);

        return $request;
    }

    /**
     * 返回一条原创微博的最新n条转发微博信息
     */
    public static function repostTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'repost_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'int64', true);
        $request->addRule('filter_by_author', 'int');
        $request->supportPagination();
        $request->supportCursor();

        return $request;
    }

    /**
     * 用户的最新转发微博。获取当前用户最新转发的n条微博消息
     */
    public static function repostByMe()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'repost_by_me');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportCursor();

        return $request;
    }

    /**
     * 获取@当前用户的最新微博
     */
    public static function mentions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'mentions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportTrimUser();
        $request->supportCursor();
        $request->addRule('filter_by_author', 'int', false);
        $request->addRule('filter_by_type', 'int', false);
        $request->addRule('filter_by_source', 'int', false);

        return $request;
    }

    /**
     * 获取@当前用户的最新微博id死钱有
     *
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function mentionsIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'mentions/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->supportTrimUser();
        $request->supportCursor();
        $request->addRule('filter_by_author', 'int', false);
        $request->addRule('filter_by_type', 'int', false);
        $request->addRule('filter_by_source', 'int', false);

        return $request;
    }

    /**
     * 根据ID获取单条微博信息信息
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'int64', true);

        return $request;
    }

    /**
     * 根据提供的ID批量获取一组微博的信息
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('ids', 'string', true);
        $request->addSetCallback('ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 50));
        $request->addRule('trim_user', 'int', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 返回新浪微博官方所有表情、魔法表情的相关信息。包括短语、表情类型、表情分类，是否热门等。
     */
    public static function emotions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('emotions', '');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('type', 'string');
        $request->addRule('language', 'string');

        return $request;
    }

    /**
     * 通过id获取mid。
     */
    public static function queryMid()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'querymid');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'string', true);
        $request->addRule('type', 'int', true);
        $request->addRule('is_batch', 'int');

        return $request;
    }

    /**
     * 通过mid获取id。通过mid获取id。其中id为该条微博/评论/私信在API系统中的id；mid为该条微博/评论/私信在web系统中的id值。
     */
    public static function queryId()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'queryid');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('mid', 'string', true);
        $request->addRule('type', 'int', true);
        $request->addRule('is_batch', 'int');
        $request->addRule('inbox', 'string');
        $request->addRule('isBase62', 'string');

        return $request;
    }

    /**
     * 返回热门转发榜
     * @param string $type weekly(按天)、 daily(按周)
     */
    public static function hotRepost($type = 'weekly')
    {
        $type = $type == 'daily' ? $type : 'weekly';
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'hot/repost_'.$type);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportBaseApp();
        $request->addRule('count', 'int');

        return $request;
    }

    /**
     * 返回热门评论榜
     * @param string $type weekly(按天)、 daily(按周)
     */
    public static function hotComments($type = 'weekly')
    {
        $type = $type == 'daily' ? $type : 'weekly';
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'hot/comments_'.$type);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportBaseApp();
        $request->addRule('count', 'int');

        return $request;
    }

    /**
     * 转发一条微博信息
     */
    public static function repost()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'repost');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('mid', 'string');
        $request->addRule('source', 'string');
        $request->addRule('status', 'string');
        $request->addRule('id', 'int64', true);
        $request->addRule('is_comment', 'int');
        $request->addRule('skip_check', 'int');
        $request->addRule('visible', 'int');

        //由于saas检测会有超时情况，临时调整接口超时时间为3s
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 根据ID删除微博消息。注意：只能删除自己发布的微博消息。
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64', true);
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 发布一条微博信息。
     */
    public static function update()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('mid', 'string');
        $request->addRule('status', 'string', true);
        $request->addRule('source', 'string');
        $request->addRule('lat', 'float');
        $request->addRule('long', 'float');
        $request->addRule('annotations', 'string');
        $request->addRule('skip_check', 'int');
        $request->addRule('visible', 'int');
        $request->addRule('list_id', 'string');

        //由于saas检测会有超时情况，临时调整接口超时时间为2s
        $request->setRequestTimeout(5000, 5000);
        $request->setWarningTimeout(2000);

        return $request;
    }

    /**
     * 发布一条微博，同时指定已经上传的图片picid或internet上的图片url.
     */
    public static function uploadUrlText()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'upload_url_text');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        $request->addRule('mid', 'string');
        $request->addRule('status', 'string', true);
        $request->addRule('source', 'string');
        $request->addRule('pic_id', 'string');
        $request->addRule('url', 'string');
        $request->addRule('skip_check', 'int');
        $request->addRule('visible', 'int');

        //由于saas检测会有超时情况，临时调整接口超时时间为5s
        $request->setRequestTimeout(5000, 5000);

        return $request;
    }

    /**
     * 上传图片并发布一条微博信息
     */
    public static function upload()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'upload');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('status', 'string', true);
        $request->addRule('pic', 'filepath', true);
        $request->addRule('source', 'string');
        $request->addRule('lat', 'float');
        $request->addRule('long', 'float');
        $request->addRule('visible', 'int');
        $request->getHttpRequest()->hasUpload = true;

        return $request;
    }

    /**
     * 上传图片
     */
    public static function uploadPic()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'upload_pic');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('pic', 'filepath', true);

        return $request;
    }

    /**
     * 屏蔽某个@提到我的微博，以及后续对其转发而引起的@提到我
     */
    public static function mentionsShield()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'mentions/shield');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64', true);
        $request->addRule('follow_up', 'int', false);

        return $request;
    }

    /**
     * 批量获取指定微博的转发数评论数
     *
     */
    public static function statusesCount()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'count');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('ids', 'string', true);

        return $request;
    }

    /**
     * 创建微博标签
     *
     */
    public static function tagsCreate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('tag', 'string', true);

        return $request;
    }

    /**
     * 删除微博标签
     *
     */
    public static function tagsDestroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('tag', 'string', true);

        return $request;
    }

    /**
     * 修改微博标签
     *
     */
    public static function tagsUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('old_tag', 'string', true);
        $request->addRule('new_tag', 'string', true);

        return $request;
    }

    /**
     * 获取指定用户的微博标签列表
     *
     */
    public static function tags()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('count', 'int', false);
        $request->addRule('page', 'int', false);
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 获取当前用户某个标签的微博ID列表
     *
     */
    public static function tagTimelineIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tag_timeline/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('tag', 'string', true);
        $request->addRule('uid', 'int64', true);
        $request->addRule('since_id', 'int64', false);
        $request->addRule('max_id', 'int64', false);
        $request->addRule('count', 'int', false);
        $request->addRule('page', 'int', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 根据提供的ID批量获取微博标签的信息
     *
     */
    public static function tagsShowBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/show_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('ids', 'string', true);
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 更新某个微博的标签
     *
     */
    public static function updateTags()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'update_tags');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int', true);
        $request->addRule('tags', 'string', false);

        return $request;
    }

    /**
     * 获取登录用品屏蔽的mid列表
     */
    public static function getFilteredIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'get_filtered/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('count', 'int', false);

        return $request;
    }

    /**
     * 屏蔽某条微博
     */
    public static function filterCreate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'filter/create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64', true);

        return $request;
    }

    /**
     * 获取当前登录用户所悄悄关注用户的微博的ids
     */
    public static function statusesPrivateTimelineIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'private_timeline/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('count', 'int64', false);
        $request->addRule('page', 'int64', false);
        $request->addRule('feature', 'int64', false);
        $request->addRule('since_id', 'int64', false);
        $request->addRule('max_id', 'int64', false);

        return $request;
    }

    /**
     * 获取当前登录用户所悄悄关注用户的微博
     */
    public static function statusesPrivateTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'private_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('count', 'int64', false);
        $request->addRule('page', 'int64', false);

        return $request;
    }
}