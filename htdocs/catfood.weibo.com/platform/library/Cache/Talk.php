<?php

class Cache_Talk extends Cache_Abstract
{
    protected $configs = array(
        'governmenttalk' => array('%s_2_%s_%s_%s_%s_%s_%s', 1600), // myfeed容灾列表
    );
    
    protected $cachePool = 'MYFEED';
    protected $keyPrefix = 'talk';
    
    public function getList($cuid, $urlShort, $urlLong, $shortInfo, $type, $lang)
    {
        return $this->get($this->key('governmenttalk', $cuid, $urlShort, $urlLong, $shortInfo, $type, $lang));
    }
    
    public function createList($cuid, $urlShort, $urlLong, $shortInfo, $type, $lang, $data)
    {
        return $this->set($this->key('governmenttalk', $cuid, $urlShort, $urlLong, $shortInfo, $type, $lang), $data, $this->livetime('governmenttalk'));
    }
}
