<?php
class Comm_Weibo_Api_Widget
{
    const RESOURCE = 'widget';

    /**
     * 根据短链获取媒体的播放HTML
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show', 'json', null, true);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('short_url', 'string', true);
        $request->addRule('lang', 'string', false);
        $request->addRule('jsonp', 'string', false);
        $request->addRule('template_name', 'string', false);
        $request->setRequestTimeout(2000, 2000);
        
        return $request;
    }
}
