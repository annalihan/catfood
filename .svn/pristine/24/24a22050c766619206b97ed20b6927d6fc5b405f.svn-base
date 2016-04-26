<?php
class Cache_HourlyTopic extends Cache_Abstract
{
    protected $configs = array(
        'hourly_topic' => array('%s_2_2', 1800),  
    );

    protected $cachePool = 'HOURLYTOPIC';
    protected $keyPrefix = 'hourlytopic';
    
    public function createHourlyTopic($data)
    {
        $key = $this->key('hourly_topic');
        return $this->set($key, $data, $this->livetime('hourly_topic'));
    }

    public function getHourlyTopic()
    {
        $key = $this->key('hourly_topic');
        return $this->get($key);
    }
}
