<?php
class Cache_Group extends Cache_Abstract
{
    protected $configs = array(
        'followers_info' => array('%s_22_%s', 86400), // 关注人权重最高的公司,学习,标签
        'may_interested' => array('%s_1_%s', 3600),
        'user_group' => array('%s_2_%s', 300), //TODO
    );

    protected $cachePool = 'GROUP';
    protected $keyPrefix = 'group';
    
    /**
     * 创建关注人中权重最高的公司，学校，标签
     * Enter description here .
     * ..
     * 
     * @param unknown_type $uid            
     * @param unknown_type $data            
     */
    public function createFollowersInfo($uid, $data)
    {
        $key = $this->key('followers_info', $uid);
        return $this->set($key, $data, $this->livetime('followers_info'));
    }
    
    public function getFollowersInfo($uid)
    {
        $key = $this->key('followers_info', $uid);
        return $this->get($key);
    }

    public static function getUserGroupCache($uid)
    {
        $key = $this->key('user_group', $uid);
        return $this->get($key);
    }

    public static function createUserGroupCache($uid, $data)
    {
        $key = $this->key('user_group', $uid);
        return $this->set($key, $data, $this->livetime("user_group"));
    }

    public static function clearUserGroupCache($uid)
    {
        $key = $this->key('user_group', $uid);
        return $this->del($key);
    }
}