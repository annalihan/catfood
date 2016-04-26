<?php
class Comm_Weibo_Api_Photos_Photo
{
    const RESOURCE = 'photos/photo';

    /**
     * 获取用户的照片
     */
    public static function recentPhoto()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, '');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64');
        $request->addRule('page', 'int');
        $request->addRule('count', 'int');
        $request->addRule('feature', 'int');

        return $request;
    }

    /**
     * 获取照片信息
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('picid', 'string');
        $request->addRule('skip_question', 'int');

        return $request;
    }

    /**
     * 喜欢一张照片
     */
    public static function like()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'like');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('picid', 'string');

        return $request;
    }
}
