<?php
class Cache_Message extends Cache_Abstract
{
    protected $configs = array(
        'userlist' => array('%s_11_%s', 86400),
        'starstatuslist' => array('%s_22_%s', 3600),
    );
    
    protected $keyPrefix = 'messages';
    protected $cachePool = 'MESSAGE';
    
    public function getUserlist($uid)
    {
        $key = $this->key("userlist", $uid);
        return $this->get($key);
    }
    
    public function createUserlist($uid, $list)
    {
        $key = $this->key("userlist", $uid);
        return $this->set($key, $list, $this->livetime("userlist"));
    }
    
    public function setMessageStarstatuslist($dmid, $data)
    {
        $key = $this->key("starstatuslist", $dmid);
        return $this->set($key, $data, $this->livetime('starstatuslist'));
    }
    
    public function mgetMessageStarstatuslist($dmids)
    {
        foreach ($dmids as $dmid)
        {
            $keys[] = $this->key("starstatuslist", $dmid);
        }

        return $this->mget($keys);
    }
    
    public function msetMessageStarstatuslist($data)
    {
        $values = array();
        foreach ($data as $key => $item)
        {
            $cackeKey = $this->key("starstatuslist", $key);
            $values[$cackeKey] = $item;
        }

        return $this->mset($values);
    }
    
    public function delMessageStarstatus($dmid)
    {
        $key = $this->key("starstatuslist", $dmid);
        return $this->del($key);
    }
}
