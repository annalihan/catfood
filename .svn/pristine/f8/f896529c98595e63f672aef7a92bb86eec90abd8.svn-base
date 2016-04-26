<?php

class Cache_Privacy extends Cache_Abstract
{
    protected $configs = array(
        'url_visiable' => array('%s_1_%s', 300),
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'privacy';

    public function getUrlVisiable($uid)
    {
        $key = $this->key('url_visiable', $uid);
        return $this->get($key);
    }

    public function setUrlVisiable($uid, $data)
    {
        $key = $this->key('url_visiable', $uid);
        return $this->set($key, $data, $this->livetime('url_visiable'));
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
