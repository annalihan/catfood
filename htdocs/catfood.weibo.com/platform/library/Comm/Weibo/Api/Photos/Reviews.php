<?php
class Comm_Weibo_Api_Photos_Reviews
{
    const RESOURCE = 'photos/reviews';

    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('rid', 'int64');
        
        return $request;
    }
}
