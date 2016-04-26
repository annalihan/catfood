<?php
class Comm_Weibo_Api_Favorites
{
    const RESOURCE = 'favorites';

    /**
     * 获取当前用户的收藏列表
     */
    public static function favorites()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, '');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('page', 'int');
        $request->addRule('count', 'int');

        return $request;
    }

    /**
     * 返回指定收藏的信息
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('id', 'int64', true);

        return $request;
    }

    /**
     * 根据标签返回当前用户该标签下的所有收藏
     */
    public static function byTags()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'by_tags');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('tid', 'int64', true);
        $request->supportPagination();

        return $request;

    }

    /**
     * 当前登录用户的收藏标签列表
     */
    public static function tags()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();

        return $request;
    }

    /**
     * 常用标签列表
     */
    public static function tagsCommon()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/common');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();
        $request->addRule('id', 'int64', true);

        return $request;
    }

    /**
     * 添加收藏
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64', true);
        $request->setRequestTimeout(5000, 5000);
        return $request;
    }

    /**
     * 删除微博收藏。注意：只能删除自己收藏的信息。
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64');
        $request->setRequestTimeout(5000, 5000);
        return $request;
    }

    /**
     * 批量删除收藏
     */
    public static function destroyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('ids', 'string', true);
        $request->addSetCallback('ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));

        return $request;
    }

    /**
     * 更新收藏标签
     */
    public static function tagsUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('id', 'int64', true);
        $request->addRule('tags', 'string', false);
        $request->addSetCallback('tags', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('string', ',', 2));
        $request->setRequestTimeout(5000, 5000);

        return $request;
    }

    /**
     * 更新当前用户所有收藏下的指定标签
     */
    public static function tagsUpdateBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/update_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('tid', 'int64', true);
        $request->addRule('tag', 'string', false);

        return $request;
    }

    /**
     * 删除指定标签（即删除当前用户所有收藏中的此标签）
     */
    public static function tagsDestroyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'tags/destroy_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('tid', 'int64', true);

        return $request;
    }

}
