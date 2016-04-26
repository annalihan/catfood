<?php

/**
 * Cache_Main
 * @author wbzeqing@staff.sina.com.cn
 * @version 2014.04.11
 */
class Cache_Main extends Cache_Abstract
{

    protected $configs = array(
        'clearance' => array(
            '%s_1_%s',
            3600
        )
    );

    protected $cachePool = 'CLEARANCE';

    protected $keyPrefix = 'CLEARANCE';
    
    protected $isUseCache = false;//是否取缓存
    
    
    public function setClearance($token,$uid){
        $key = $this->key('clearance', $uid);
        return $this->set($key, $token, $this->livetime('clearance'));
    }
    public function  getClearance($uid){
        $key = $this->key('clearance', $uid);
        return $this->get($key);
    }
    public function  delClearance($uid){
        $key = $this->key('clearance', $uid);
        return $this->del($key);
    }
}
