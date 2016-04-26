<?php
class Cache_School extends Cache_Abstract
{
    protected $configs = array(
        'schools' => array('%s_1_%s', 300),
        'schools_viewer' => array('%s_2_%s', 300),
        'schools_follow' => array('%s_3_%s', 300),
    );

    protected $keyPrefix = 'school';
    protected $cachePool = 'MAIN';
    
    public function getSchools($userId)
    {
        $key = $this->key("schools", $userId);
        return $this->get($key);
    }
    
    public function setSchools($userId, $schools)
    {
        $key = $this->key("schools", $userId);
        return $this->set($key, $schools, $this->livetime("schools"));
    }
    
    public function getSchoolsViewer($userId)
    {
        $key = $this->key("schools_viewer", $userId);
        return $this->get($key);
    }
    
    public function setSchoolsViewer($userId, $schools)
    {
        $key = $this->key("schools_viewer", $userId);
        return $this->set($key, $schools, $this->livetime("schools_viewer"));
    }

    public function getSchoolsFollow($userId)
    {
        $key = $this->key("schools_follow", $userId);
        return $this->get($key);
    }
    
    public function setSchoolsFollow($userId, $schools)
    {
        $key = $this->key("schools_follow", $userId);
        return $this->set($key, $schools, $this->livetime("schools_follow"));
    }
}