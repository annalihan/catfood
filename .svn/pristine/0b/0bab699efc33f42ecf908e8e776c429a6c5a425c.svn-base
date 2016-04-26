<?php
class Dr_Poi extends Dr_Abstract
{
    /**
     * 批量获取POI信息
     * @param array $poiArray
     * @return unknown
     */
    public static function batchPoi($poiArray)
    {
        try
        {
            if (count($poiArray) > 1)
            {
                $ids = implode($poiArray, ',');
            }
            else
            {
                $ids = $poiArray[0];
            }

            $commApi = Comm_Weibo_Api_Place::poisShowBatch();
            $commApi->poiids = $ids;
            
            return $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 根据UID获取用户所有的POI信息
     * @param unknownType $uid
     * @param unknownType $ip
     * @param unknownType $count
     * @param unknownType $page
     * @return multitype:|unknown
     */
    public static function getUserAllPoi($uid, $ip, $count = 1, $page = 1)
    {
        try
        {
            if (empty($ip) || strlen($ip) < 9)
            {
                $ip = !empty($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '127.0.0.1';
            }

            // http://api.promotion.gypsii.cn/api/userpois.php?uid=1834134725&num=10&offset=0&ip=218.159.230.230&state=0
            $offset = $count * ($page - 1);
            $rst = Comm_Weibo_POI::getPoiByUid($uid, $ip, $count, $offset);
            $ids = '';
            $totalNum = $rst['total'];

            if (count($rst['list']) == 0)
                return array ();

            if (count($rst['list']) > 1)
            {
                $ids = implode($rst['list'], ',');
            }
            else
            {
                $ids = $rst['list'][0];
            }

            $commApi = Comm_Weibo_Api_Place::poisShowBatch();
            $commApi->poiids = $ids;
            $result = $commApi->getResult();
            $result['total_number'] = $totalNum;
            return $result;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 根据uid获取poi详细信息
     * @param type $uid
     * @return type
     * 
     */
    public static function getPoiByUid($uid, $count = 0)
    {
        if (empty($uid))
        {
            return array();
        }

        $poiids = Comm_Weibo_POI::getPoiidByUid($uid, $count);
        $poiInfos = array();
        $cachePoi = new Cache_Poi();
        $poiInfos = $cachePoi->getPoiInfos($poiids);
        foreach ($poiids as $poiid)
        {
            $cacheKey = $cachePoi->key('poi_info', $poiid);
            if (empty($poiInfos[$cacheKey]))
            {
                $poiInfos[$cacheKey] = Comm_Weibo_POI::getPoiByPoiid($poiid);
                $cachePoi->setPoiInfo($poiid, $poiInfos[$cacheKey]);
            }
        }
        
        return $poiInfos;
    }
}
