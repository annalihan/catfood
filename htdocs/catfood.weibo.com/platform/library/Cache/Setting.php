<?php

class Cache_Setting extends Cache_Abstract
{
    protected $configs = array(
        'settings' => array('%s_0_%s', 1296000), // 用户个性设置
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'setting';

    public function getSettings($uid)
    {
        $key = $this->key('settings', $uid);
        return $this->get($key);
    }
    
    public function createSettings($uid, $settings)
    {
        $key = $this->key('settings', $uid);
        $livetime = $this->livetime('settings');
        return $this->set($key, $settings, $livetime);
    }
    
    public function clearSettingsBatch(array $uids)
    {
        $uids = array_unique($uids);
        if (empty($uids))
        {
            return true;
        }

        $keys = array();
        foreach ($uids as $uid)
        {
            $keys[$uid] = $this->key('settings', $uid);
        }
        
        return $this->mdel($keys);
    }
}
