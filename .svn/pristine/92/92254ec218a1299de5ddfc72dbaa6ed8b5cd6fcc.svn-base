<?php
class Cache_Face extends Cache_Abstract
{
    //TODO
    protected $configs = array(
        'list' => array('%s_list_%s_%s', 25200),
        'lite' => array('%s_lite_%s_%s', 25200),
    );

    protected $keyPrefix = 'face';
    protected $cachePool = 'MAIN';
    
    public function getList($faceType, $language)
    {
        $key = $this->key("list", $faceType, $language);
        return $this->get($key);
    }
    
    public function createList($faceType, $language, $list)
    {
        $key = $this->key("list", $faceType, $language);
        return $this->set($key, $list, $this->livetime("list"));
    }
    
    public function getLite($faceType, $language)
    {
        $key = $this->key("lite", $faceType, $language);
        return $this->get($key);
    }
    
    public function createLite($faceType, $language, $list)
    {
        $key = $this->key("lite", $faceType, $language);
        return $this->set($key, $list, $this->livetime("lite"));
    }
}