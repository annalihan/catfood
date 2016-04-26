<?php
class Cache_Status extends Cache_Abstract
{
    protected $configs = array(
        'mblog_info' => array('%s_4_0_%s', 1200), // 微博信息，20分钟
        'self_mblog' => array('%s_9_%s', 300), // 发微博时防止openapi延迟，这个key目前还没有完全搬过来，只队列在用。
        'total_reorder_ids' => array('%s_10_%s_%s', 300), // 首页猜你喜欢微博mid，一次性存储500个
        'atme_mblog_rz' => array('%s_11_%s', 86400),
        'rumor_mblog_info' => array('%s_12_%s', 120), // 谣言微博缓存
        'raw_mblog_info' => array('%s_13_%s', 1296000), // 原始微博信息，未渲染
    );

    protected $cachePool = 'STATUS';
    protected $keyPrefix = 'status';
    
    /**
     * 从缓存获取指定mid的微博信息
     *
     * @param $mid
     * @param $key
     */
    public function getMblogInfo($mid, &$key = array())
    {
        $key = $this->key('mblog_info', $mid);
        return $this->get($key);
    }
    
    /**
     * 批量从缓存获取微博信息
     *
     * @param array $mids            
     * @param array $keys            
     * @param mixed $rtn
     *            返回值，默认从mc返回
     */
    public function getMblogInfos(array $mids, array &$keys = array(), $rtn = null)
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('mblog_info', $mid);
        }

        return $rtn === null ? $this->mget($keys) : $rtn;
    }
    
    /**
     * 获取原始微博内容
     * 
     * @param $mids
     * @param $keys
     */
    public function getRawMblogInfos(array $mids, array &$keys = array())
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('raw_mblog_info', $mid);
        }

        return $this->mget($keys);
    }
    
    /**
     * 批量创建微博信息相关缓存
     *
     * @param $items
     */
    public function createMblogInfos(array $items)
    {
        return $this->mset($items, $this->livetime('mblog_info'));
    }
    
    /**
     * 批量创建原始微博信息缓存
     * 
     * @param $items
     */
    public function createRawMblogInfos(array $items)
    {
        return $this->mset($items, $this->livetime('raw_mblog_info'));
    }
    
    /**
     * 创建单条微博缓存
     *
     * 将单条微博渲染后的标准结构存入缓存
     * 
     * @param $mblogInfo
     */
    public function createMblogInfo(array $mblogInfo)
    {
        $key = $this->key('mblog_info', $mblogInfo ['id']);
        return $this->set($key, $mblogInfo, $this->livetime('mblog_info'));
    }
    
    /**
     * 创建单条原始微博缓存
     *
     * 将单条微博渲染后的标准结构存入缓存
     * 
     * @param $mblogInfo
     */
    public function createRawMblogInfo(array $mblogInfo)
    {
        $key = $this->key('raw_mblog_info', $mblogInfo ['id']);
        return $this->set($key, $mblogInfo, $this->livetime('raw_mblog_info'));
    }
    
    /**
     * 批量清除微博信息缓存
     * 
     * @param $mids
     */
    public function clearMblogInfos(array $mids)
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('mblog_info', $mid);
        }

        return $this->mdel($keys);
    }
    
    /**
     * 批量清除微博原始信息缓存
     * 
     * @param $mids
     */
    public function clearRawMblogInfos(array $mids)
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('raw_mblog_info', $mid);
        }

        return $this->mdel($keys);
    }
    
    /**
     * 删除单条微博信息缓存
     *
     * @param $mid
     */
    public function clearMblogInfo($mid)
    {
        return $this->del($this->key('mblog_info', $mid));
    }
    
    /**
     * 删除单条微博原始信息缓存
     *
     * @param $mid
     */
    public function clearRawMblogInfo($mid)
    {
        return $this->del($this->key('raw_mblog_info', $mid));
    }
    
    /**
     * 删除自己发的微博信息缓存
     *
     * @param $id 用户id或微博id            
     * @param $isMid 决定id是用户id还是微博id，默认为false,表示用户id            
     */
    public function clearSelfMblog($id, $isMid = false)
    {
        if ($isMid)
        {
            $mblogInfo = $this->getMblogInfo($id);

            if ($mblogInfo && isset($mblogInfo ['uid']))
            {
                $id = $mblogInfo ['uid'];
            }
            else
            {
                return true;
            }
        }

        return $this->del($this->key('self_mblog', $id));
    }
    
    /**
     * 批量删除指定用户自己发的微博信息缓存
     *
     * @param $uids
     */
    public function clearSelfMblogs(array $uids)
    {
        $keys = array();
        foreach ($uids as $uid)
        {
            $keys[$uid] = $this->key('self_mblog', $uid);
        }

        return $this->mdel($keys);
    }
    
    /**
     * 获取首页猜你喜欢微博ids列表，存储500个
     *
     * @param $uid
     * @param $section
     */
    public function getTotalReorderIds($uid, $section)
    {
        $key = $this->key('total_reorder_ids', $uid, $section);
        return $this->get($key);
    }
    
    /**
     * 创建获取首页猜你喜欢微博ids列表缓存
     * 
     * @param $uid
     * @param $section
     * @param $mids
     */
    public function createTotalReorderIds($uid, $section, $mids)
    {
        $key = $this->key('total_reorder_ids', $uid, $section);
        return $this->set($key, $mids, $this->livetime('total_reorder_ids'));
    }
    
    /**
     * 批量创建谣言微博信息相关缓存
     *
     * @param $items
     */
    public function createRumorMblogInfos($items)
    {
        return $this->mset($items, $this->livetime('rumor_mblog_info'));
    }
    
    /**
     * 批量从缓存获取谣言微博信息
     *
     * @param array $mids            
     * @param array $keys            
     */
    public function getRumorMblogInfos(array $mids, array &$keys = array())
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('rumor_mblog_info', $mid);
        }

        return $this->mget($keys);
    }
    
    /**
     * 批量清除谣言微博信息缓存
     * 
     * @param $mids
     */
    public function clearRumorMblogInfos(array $mids)
    {
        foreach ($mids as $mid)
        {
            $keys[$mid] = $this->key('rumor_mblog_info', $mid);
        }
        
        return $this->mdel($keys);
    }
    
    public function getAtmeMblog($uid)
    {
        $key = $this->key("atme_mblog_rz", $uid);
        return $this->get($key);
    }
    
    public function createAtmeMblog($uid, $list)
    {
        $key = $this->key("atme_mblog_rz", $uid);
        return $this->set($key, $list, $this->livetime("atme_mblog_rz"));
    }
}
