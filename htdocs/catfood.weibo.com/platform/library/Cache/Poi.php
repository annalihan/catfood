<?php

class Cache_Poi extends Cache_Abstract
{
    protected $configs = array(
        'poi_info' => array('%s_1_%s', 86400), // 位置信息 1d
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'poi';
    
    /**
     * 获取位置信息
     */
    public function getPoiInfo($poiid)
    {
        $key = $this->key('poi_info', $poiid);
        return $this->get($key);
    }
    
    /**
     * 批量获取位置信息
     */
    public function getPoiInfos($poiids)
    {
        $keys = array();

        foreach ($poiids as $poiid)
        {
            $keys[] = $this->key('poi_info', $poiid);
        }

        if (empty($keys))
        {
            return array();
        }
        
        return $this->mget($keys);
    }
    
    /**
     * 设置位置信息
     */
    public function setPoiInfo($poiid, $data)
    {
        $key = $this->key('poi_info', $poiid);
        return $this->set($key, $data, $this->livetime('poi_info'));
    }
    
    /**
     * 删除位置信息
     */
    public function delPoiInfo($poiid)
    {
        $key = $this->key('poi_info', $poiid);
        return $this->del($key);
    }
}
