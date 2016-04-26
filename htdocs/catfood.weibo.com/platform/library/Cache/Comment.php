<?php
class Cache_Comment extends Cache_Abstract
{
    protected $configs = array(
        'inbox' => array('%s_11_%s', 86400),
        'topmblog' => array('%s_12_%s'),
        'hot' => array('%s_2_%s_%s', 300),
        'list' => array('%s_3_%s_%s', 300),
        'mention' => array('%s_6_%s', 300),
    );

    protected $keyPrefix = 'comment';
    protected $cachePool = 'COMMENT';
    
    public function getCommentInbox($uid)
    {
        $key = $this->key("inbox", $uid);
        return $this->get($key);
    }
    
    public function createCommentInbox($uid, $list)
    {
        $key = $this->key("inbox", $uid);
        return $this->set($key, $list, $this->livetime("inbox"));
    }
    
    public function getTopmblog($key)
    {
        $key = $this->key("topmblog", $key);
        return $this->get($key);
    }
    
    public function createTopmblog($key, $commentIds, $time)
    {
        $key = $this->key("topmblog", $key);
        return $this->set($key, $commentIds, $time);
    }

    public function getHotComment($count, $type)
    {
        $key = $this->key("hot", $count, $type);
        return $this->get($key);
    }

    public function createHotComment($count, $type, $commentHot)
    {
        $key = $this->key("hot", $count, $type);
        return $this->set($key, $commentHot, $this->livetime("hot"));
    }
}