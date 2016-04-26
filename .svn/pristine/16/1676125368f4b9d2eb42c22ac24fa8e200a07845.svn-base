<?php
class Comm_Weibo_Api_Photos_Album
{
    const RESOURCE = 'photos/album';

    /**
     * 用户的照片数、相册数。
     */
    public static function counts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('photos', 'counts');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64');
        return $request;
    }

    /**
     * 获取相册信息
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('album_id', 'string');
        return $request;
    }

    /**
     * 输出用户相册列表
     */
    public static function album()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, '');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->supportPagination();
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }

    /**
     * 查看指定用户的微博相册的评论数、相册封面
     */
    public static function albumWeibo()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'weibo');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);

        return $request;
    }
}
