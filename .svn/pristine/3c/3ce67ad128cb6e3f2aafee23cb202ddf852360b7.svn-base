<?php
class Comm_Weibo_Api_Location
{
    const RESOURCE = 'location';

    public static function addressToGeo()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'geo/address_to_geo');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('address', 'string');
        
        return $request;
    }

    /**
     * 根据经纬度解析地理信息，区分国内外
     */
    public static function isDomestic()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'geocode/is_domestic');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('coordinates', 'string', true);

        return $request;
    }

    /**
     * 解析地理信息
     */
    public static function getAddress()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'geocode/is_domestic');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('coordinates', 'string', true);

        return $request;
    }

    /**
     * 解析地理信息。最新 ，支持国内外地址（批量）
     */
    public static function getAddressNew()
    {
        $url = 'http://i.api.place.weibo.cn/place/location/xy_to_geo_for_bulk.json';
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('address_language', 'string', true);
        $request->addRule('coordinates', 'string', true);
        $request->setRequestTimeout(3000, 3000);

        return $request;
    }
}
