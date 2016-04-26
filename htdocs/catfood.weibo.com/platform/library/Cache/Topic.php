<?php
class Cache_Topic extends Cache_Abstract
{
    protected $configs = array(
        'publish_topic' => array('%s_1_1', 86400), // 发微博关键字推荐话题
        'user_topic' => array('%s_1_100_%s', 86400), // 用户话题列表
    );
    
    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'topic';
    
    /**
     * 创建发布话题缓存，通过cron脚本的方式每5分钟调用一次
     * @param unknown_type $data            
     */
    public function createPublishTopic($data)
    {
        $key = $this->key('publish_topic');
        return $this->set($key, $data, $this->livetime('publish_topic'));
    }
    
    /**
     * 获取推荐话题
     */
    public function getPublishTopic()
    {
        $key = $this->key('publish_topic');
        return $this->get($key);
    }
    
    /**
     * 创建用户话题列表缓存
     * 
     * @param unknown $uid            
     * @param unknown $data            
     */
    public function createUserTopicList($uid, $data)
    {
        $key = $this->key('user_topic', $uid);
        return $this->set($key, $data, $this->livetime('user_topic'));
    }
    
    /**
     * 获取用户话题列表缓存
     * 
     * @param unknown $uid            
     */
    public function getUserTopicList($uid)
    {
        $key = $this->key('user_topic', $uid);
        return $this->get($key);
    }
    
    /**
     * 删除用户话题列表缓存
     * 
     * @param unknown $uid            
     */
    public function clearUserTopicList($uid)
    {
        $key = $this->key('user_topic', $uid);
        return $this->del($key);
    }
}
