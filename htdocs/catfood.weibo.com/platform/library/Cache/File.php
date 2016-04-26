<?php
class Cache_File extends Cache_Abstract
{
    protected $configs = array(
        'info' => array('%s_1_%s', 3600),
        'vdisk_usage' => array('%s_6_%s', 300),
    );

    protected $keyPrefix = 'messages';
    protected $cachePool = 'MESSAGE';
    
    public function getInfo($fid)
    {
        $key = $this->key("info", $fid);
        return $this->get($key);
    }

    public function setInfo($fid, $info)
    {
        $key = $this->key("info", $fid);
        $liveTime = $this->livetime("info");
        $liveTime = $liveTime * (empty($info['thumbnail']) ? 720 : 36);
        return $this->set($key, $info, $liveTime);
    }

    public function getVdiskUsage($userId)
    {
        $key = $this->key("vdisk_usage", $userId);
        return $this->get($key);
    }

    public function setVdiskUsage($userId, $usage)
    {
        $key = $this->key("vdisk_usage", $userId);
        return $this->set($key, $usage, $this->livetime("vdisk_usage"));
    }
}