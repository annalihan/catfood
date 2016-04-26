<?php
class Cache_Tips extends Cache_Abstract
{
    protected $configs = array(
        'home_tips' => array('%s_1_%s', 300), //推荐位管理-首页tips 
        'home_tips_location' => array('%s_2_%s', 300), //推荐位管理-首页tips每帧显示顺序缓存
    );

    protected $cachePool = 'TIPS';
    protected $keyPrefix = 'tips';
     
    /**
     * 获得用户首页tips
     *
     * @param $uid 用户ID
     */  
    public function getHomeTips($uid)
    {
        $key = $this->key('home_tips', $uid);
        return $this->get($key);
    }
    
    /**
     * 创建用户首页tips缓存
     *
     * @param $data
     */ 
    public function createHomeTips($uid, $data)
    {
        $key = $this->key('home_tips', $uid);
        return $this->set($key, $data, $this->livetime('home_tips'));
    }
    
    /**
     * 创建用户首页tips位置缓存
     * @param $uid 用户ID
     * @param $location tips位置缓存
     */
    public function createHomeTipsLocation($uid, $location)
    {
        $key = $this->key('home_tips_location', $uid);
        return $this->set($key, $location, $this->livetime('home_tips_location'));
    }
    
    /**
     * 获取用户首页tips位置缓存
     * @param $uid 用户ID
     */
    public function getHomeTipsLocation($uid)
    {
        $key = $this->key('home_tips_location', $uid);
        return $this->get($key);
    }


}