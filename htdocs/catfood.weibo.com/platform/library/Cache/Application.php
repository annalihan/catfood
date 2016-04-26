<?php
class Cache_Application extends Cache_Abstract
{
    protected $configs = array(
        'user_app_data' => array('%s_1_%s_%s', 172800), // 用户app data 2天
        'user_app_list' => array('%s_2_%s', 81600), // 用户app list 2天
        'user_recommend_app_list' => array('%s_3_%s', 3600), // 用户推荐app list 1小时
        'user_hot_app_list' => array('%s_4_%s', 3600), // 用户热门app list 1小时
    );
    
    protected $cachePool = 'APP';
    protected $keyPrefix = 'app';
    
    /**
     * 获取用户应用数据
     *
     * @param int $uid            
     */
    public function getUserAppdata($uid, $appid)
    {
        $key = $this->key('user_app_data', $uid, $appid);
        return $this->get($key);
    }
    
    /**
     * 获取我的应用列表cache信息
     *
     * @param int $uid            
     */
    public function getUserApplist($uid)
    {
        $key = $this->key('user_app_list', $uid);
        return $this->get($key);
    }

    /**
     * 获取推荐应用列表cache信息
     *
     * @param int $id
     *            缓存标识
     */
    public function getUserRecommendApplist($id)
    {
        $key = $this->key('user_recommend_app_list', $id);
        return $this->get($key);
    }

    /**
     * 获取热门应用列表cache信息
     *
     * @param int $id
     *            缓存标识
     */
    public function getUserHotApplist($id)
    {
        $key = $this->key('user_hot_app_list', $id);
        return $this->get($key);
    }
    
    /**
     * 设置用户应用数据
     *
     * @param int $uid            
     */
    public function setUserAppdata($uid, $appid, $value)
    {
        $key = $this->key('user_app_data', $uid, $appid);
        return $this->set($key, $value, $this->livetime('user_app_data'));
    }
    
    /**
     * 设置我的应用列表cache信息
     *
     * @param int $uid            
     */
    public function setUserApplist($uid, $value)
    {
        $key = $this->key('user_app_list', $uid);
        return $this->set($key, $value, $this->livetime('user_app_data'));
    }

    /**
     * 设置推荐应用数据
     *
     * @param int $id
     *            缓存标识
     * @param
     *            $value
     */
    public function setUserRecommendApplist($id, $value)
    {
        $key = $this->key('user_recommend_app_list', $id);
        return $this->set($key, $value, $this->livetime('user_recommend_app_list'));
    }

    /**
     * 设置热门应用数据
     *
     * @param int $id
     *            缓存标识
     * @param
     *            $value
     */
    public function setUserHotApplist($id, $value)
    {
        $key = $this->key('user_hot_app_list', $id);
        return $this->set($key, $value, $this->livetime('user_hot_app_list'));
    }
    
    /**
     * 删除用户应用数据
     * @param  [type] $uid   [description]
     * @param  [type] $appid [description]
     * @return [type]        [description]
     */
    public function delUserAppdata($uid, $appid)
    {
        $key = $this->key('user_app_data', $uid, $appid);
        return $this->del($key);
    }
    
    /**
     * 删除我的应用列表cache信息
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function delUserApplist($uid)
    {
        $key = $this->key('user_app_list', $uid);
        return $this->del($key);
    }
    
    /**
     * 删除推荐应用列表cache信息
     *
     * @param $id 缓存标识            
     */
    public function delUserRecommendApplist($id)
    {
        $key = $this->key('user_recommend_app_list', $id);
        return $this->del($key);
    }
    
    /**
     * 删除热门应用列表cache信息
     *
     * @param $id 缓存标识            
     */
    public function delUserHotApplist($id)
    {
        $key = $this->key('user_hot_app_list', $id);
        return $this->del($key);
    }
}
