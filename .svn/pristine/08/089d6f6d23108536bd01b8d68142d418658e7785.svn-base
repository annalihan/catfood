<?php
class Comm_Weibo_Api_Place
{
    const RESOURCE = 'place';

    /**
     * 根据经纬度解析地理信息，区分国内外
     */
    public static function poisShow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'pois/show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('poiid', 'string', true);

        return $request;
    }

    /**
     * 批量获取POI点的信息
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function poisShowBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'pois/show_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('poiids', 'string', true);
        
        return $request;
    }
}
