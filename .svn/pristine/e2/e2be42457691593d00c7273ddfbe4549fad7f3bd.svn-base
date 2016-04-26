<?php
class Comm_Weibo_Api_Likes
{
    const RESOURCE = 'likes';

    /**
     * 根据ID批量获取对象的总喜欢数
     */
    public static function counts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'counts');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('object_ids', "string", true);

        return $request;
    }

    /**
     * 喜欢某个对象
     */
    public static function update()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('object_id', "string", true);
        $request->addRule('object_type', "string", true);
        $request->addRule('object', "string", false);

        return $request;
    }

    /**
     * 取消喜欢某个对象
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('object_id', "string", true);

        return $request;
    }

    /**
     * 判断某个人是否喜欢过某个对象
     */
    public static function exist()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'exist');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', "string");
        $request->addRule('object_id', "string", true);

        return $request;
    }

    /**
     * 批量判断某个人是否喜欢过某个对象
     */
    public static function exists()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'exists');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', "string");
        $request->addRule('object_ids', "string", true);

        return $request;
    }

    /**
     * list
     */
    public static function likeList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'list');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('object_id', "string", true);
        $request->addRule('friendships_range', "int", true);
        $request->addRule('page', "int", true);
        $request->addRule('count', "int", true);
        
        return $request;
    }
}
