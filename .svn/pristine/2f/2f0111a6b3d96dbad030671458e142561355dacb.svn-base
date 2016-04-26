<?php
class Cache_Trend extends Cache_Abstract
{
    protected $configs = array(
        'issue_topic'   => array('%s_2', 120),      //首页发布器右上角话题
        'top_topics'    => array('%s_4_%s', 180),   //首页右侧热点话题
        'topics'        => array('%s_5', 86400),    //用户关注的话题
    );
    
    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'trend';
    
    public function getTopTopics($uid)
    {
        $key = $this->key('top_topics', $uid);
        return $this->get($key);
    }

    public function setTopTopics($uid, $data)
    {
        $key = $this->key('top_topics', $uid);
        return $this->set($key, $data, $this->livetime('top_topics'));
    }
    
    public function getIssueTopic()
    {
        $key = $this->key('issue_topic');
        return $this->get($key);
    }
    
    public function createIssueTopic($data)
    {
        $key = $this->key('issue_topic');
        $livetime = $this->livetime('issue_topic');
        return $this->cache_obj->set($key, $data, $livetime);
    }
}
