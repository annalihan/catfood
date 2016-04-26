<?php
class Cache_Photos extends Cache_Abstract
{
    protected $configs = array(
        'photos_info' => array('%s_1_1_%s', 3600),
        'weibo_photos_info' => array('%s_1_1_%s_%s', 600),
    );

    protected $cachePool = 'APP';
    protected $keyPrefix = 'platform_photos';
    
    /**
     * 获取照片列表.
     * 
     * @param int $uid            
     */
    public function getPhotos($uid)
    {
        $key = $this->key('photos_info', $uid);
        return $this->cache_obj->get($key);
    }
    
    /**
     * 设置照片列表信息 ...
     * 
     * @param $uid
     * @param $url_index
     * @param $value
     */
    public function setPhotos($uid, $value)
    {
        $key = $this->key('photos_info', $uid);
        return $this->cache_obj->set($key, $value, $this->livetime('photos_info'));
    }
    
    /**
     * 获取微博照片列表.
     * 
     * @param int $uid            
     * @param int $count
     *            一次取多少张
     */
    public function getWeiboPhotos($uid, $count)
    {
        $key = $this->key('weibo_photos_info', $uid, $count);
        return $this->cache_obj->get($key);
    }
    
    /**
     * 设置微博照片列表信息 ...
     * 
     * @param $uid
     * @param $url_index
     * @param $value
     */
    public function setWeiboPhotos($uid, $count, $value)
    {
        $key = $this->key('weibo_photos_info', $uid, $count);
        return $this->cache_obj->set($key, $value, $this->livetime('weibo_photos_info'));
    }
    
    /**
     * 清除照片列表信息...
     * 
     * @param unknown_type $uid            
     * @param unknown_type $url_index            
     */
    public function clearPhotos($uid)
    {
        $key = $this->key('photos_info', $uid);
        return $this->cache_obj->del($key);
    }
}
