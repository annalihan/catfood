<?php
class Cache_Location_Geo extends Cache_Abstract
{
    protected $configs = array(
        'address_to_geo' => array('%s_1_%s', 3600),
    );

    protected $cachePool = 'APP';
    protected $keyPrefix = 'app_contact';

    public function getAddressGeo($uid)
    {
        $key = $this->key('address_to_geo', $uid);
        return $this->get($key);
    }

    public function setAddressGeo($uid, $value, $livetime = null)
    {
        $key = $this->key('address_to_geo', $uid);
        $livetime = isset($livetime) ? $livetime : $this->livetime('address_to_geo');
        $this->set($key, $value, $livetime);
    }
    
    public function delAddressGeo($uid)
    {
        $key = $this->key('address_to_geo', $uid);
        return $this->del($key);
    }
}