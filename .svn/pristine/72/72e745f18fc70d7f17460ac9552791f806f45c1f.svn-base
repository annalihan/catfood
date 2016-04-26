<?php

class Cache_Tags extends Cache_Abstract
{
    protected $configs = array(
        'tags' => array('%s_1_%s', 300),
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'tag';
    
    public function getTags($userId)
    {
        $key = $this->key('tags', $userId);
        return $this->get($key);
    }

    public function setTags($userId, $data)
    {
        $key = $this->key('tags', $userId);
        return $this->set($key, $data, $this->livetime('tags'));
    }
}
