<?php
class Cache_Relation extends Cache_Abstract
{
    protected $configs = array(
        'friendships_followers' => array('%s_1_%s_%s', 300), // 取得粉丝列表
        'followers_list' => array('%s_2_%s_%s', 300), // 取得关注列表缓存
        'same_follow' => array('%s_3_%s_%s_%s_%s', 3000), // 共同关注列表
        'friends_chain_followers' => array('%s_4_%s_%s_%s_%s', 3000), // 关注人中，关注了指定用户的用户列表
        'followers' => array('%s_5_%s_%s', 300), // 取得关注uid列表
        'friends' => array('%s_6_%s_%s', 300), // 取得关注uid列表
    );
    
    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'relation';

    public function getFollowersList($uid, $count)
    {
        $key = $this->key('followers_list', $uid, $count);
        return $this->get($key);
    }
    
    public function setFollowersList($uid, $count, $followers)
    {
        $key = $this->key('followers_list', $uid, $count);
        return $this->set($key, $followers);
    }
    
    public function getFollowers($uid, $count)
    {
        $key = $this->key('followers', $uid, $count);
        return $this->get($key);
    }
    
    public function setFollowers($uid, $count, $followers)
    {
        $key = $this->key('followers', $uid, $count);
        return $this->set($key, $followers);
    }
    
    public function getFriends($uid, $count)
    {
        $key = $this->key('friends', $uid, $count);
        return $this->get($key);
    }
    
    public function setFriends($uid, $count, $friends)
    {
        $key = $this->key('friends', $uid, $count);
        return $this->set($key, $friends);
    }

    public function getSameFollowers($uid, $fuid, $page, $count)
    {
        $key = $this->key('same_follow', $uid, $fuid, $page, $count);
        return $this->get($key);
    }

    public function setSameFollowers($uid, $fuid, $page, $count, $data)
    {
        $key = $this->key('same_follow', $uid, $fuid, $page, $count);
        return $this->set($key, $data, $this->livetime('same_follow'));
    }

    public function getChainFollowers($uid, $fuid, $page, $count)
    {
        $key = $this->key('friends_chain_followers', $uid, $fuid, $page, $count);
        return $this->get($key);
    }
    
    public function setChainFollowers($uid, $fuid, $page, $count, $data)
    {
        $key = $this->key('friends_chain_followers', $uid, $fuid, $page, $count);
        return $this->set($key, $data, $this->livetime('friends_chain_followers'));
    }
}