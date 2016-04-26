<?php
class Cache_Favorite extends Cache_Abstract
{
    protected $configs = array(
        'fav_list' => array('%s_1_%s', 3600),
        'tag_list' => array('%s_2_%s', 3600),
        'recomm_fav' => array('%s_3', 86400),
        'total_number' => array('%s_4_%s', 129600),
    );

    protected $keyPrefix = 'favlist';
    protected $cachePool = 'MAIN';
    
    public function getRecommend()
    {
        $key = $this->key("recomm_fav");
        return $this->get($key);
    }

    public function setRecommend($statuses)
    {
        $key = $this->key("recomm_fav");
        return $this->set($key, $statuses, $this->livetime("recomm_fav"));
    }
    
    public function getTotalNumber($viewerId)
    {
        $key = $this->key("total_number", $viewerId);
        return $this->get($key);
    }

    public function setTotalNumber($viewerId, $totalNumber)
    {
        $key = $this->key("total_number", $viewerId);
        return $this->set($key, $totalNumber, $this->livetime("total_number"));
    }
}