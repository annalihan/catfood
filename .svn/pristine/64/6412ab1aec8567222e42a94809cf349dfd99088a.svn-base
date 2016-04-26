<?php
class Dr_Geo
{
    /**
     * 从地址转换成GEO
     * @param  [type] $uid     [description]
     * @param  [type] $address [description]
     * @return [type]          [description]
     */
    public static function getGeoOfAddress($uid, $address)
    {
        $cache = new Cache_Location_Geo();
        $cacheRes = $cache->getAddressGeo($uid);

        if (false !== $cacheRes)
        {
            // 地址相同时，直接返回缓存数据
            if (md5($cacheRes['address']) === md5($address))
            {
                return $cacheRes;
            }
        }

        $res = array();
        $defNodata = array(
            'latitude' => '39.904989',
            'longitude' => '116.405285',
            'zoom' => 3,
            'address' => $address,
        );

        try
        {
            $api = Comm_Weibo_Api_Location::addressToGeo();
            $api->address = $address;
            $res = $api->getResult();
            $info = array(
                'latitude' => $res['geos'][0]['latitude'],
                'longitude' => $res['geos'][0]['longitude'],
                'zoom' => 15,
                'address' => $address,
            );

            $cache->setAddressGeo($uid, $info);
        }
        catch (Exception $e)
        {
            if ($e->getCode() === "21903")
            {
                // 无相关地址的数据，中长缓存
                $cache->setAddressGeo($uid, $defNodata);
            }
            else
            {
                // 系统级错误,中短缓存
                $cache->setAddressGeo($uid, $defNodata, 60);
            }

            $info = $defNodata;
        }
        
        return $info;
    }

    /**
     * 通过坐标获取地址信息
     * 
     * @see getGeoInfos
     * @param string $long 经度
     * @param string $lnt　纬度
     * @return array
     */
    public static function getGeoInfo($long, $lnt)
    {
        return self::getGeoInfos(array('go' => array($long, $lnt)));
    }
    
    /**
     * 批量通过坐标获取地址信息
     * 
     * @param array $points 坐标值，格式如下
     *              $points = array('go' => array('40.07539', '116.592133'),'g1' => array('39.983978', '116.335457'))
     * @return array       
     */
    public static function getGeoInfos(array $points)
    {
        $coordinates = array();

        foreach ($points as $k => $point)
        {
            $coordinates[] = "$point[1],$point[0],$k";
        }

        $chunkCoordinates = array_chunk($coordinates, 50);
        $geoInfos = array();

        foreach ($chunkCoordinates as $coordinates)
        {
            $coordinates = join('|', $coordinates);
            $geoInfo = array();
            
            try
            {
                $api = Comm_Weibo_Api_Location::getAddress();
                $api->coordinates = $coordinates;
                $geoInfo = $api->getResult();
                if (!is_array($geoInfo))
                {
                    continue;
                }
            }
            catch (Comm_Exception_Program $e)
            {
                continue;
            }

            $geoInfos = array_merge($geoInfos, $geoInfo);
        }
        
        return $geoInfos;
    }
}
