<?php
class Cache_Members extends Cache_Abstract
{
    protected $configs = array(
        'member_infos' => array('%s_30_1_%s', 604800),  //用户会员身份信息
    ); 
    
    protected $cachePool = 'MEMBERS';
    protected $keyPrefix = 'members';
    
    /**
     * 创建单个用户会员身份信息缓存
     * @param int $uid
     * @param array $memberInfo 会员信息
     * @param $rzLiveTime 缓存时间
     */
    public function createMemberInfo($uid, $memberInfo, $rzLiveTime = 0)
    {
        $key = $this->key('member_infos', $uid);
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('member_infos');
        return $this->set($key, $memberInfo, $liveTime);
    }
    /**
     * 获取单个用户的会员身份信息
     * @param int $uid
     */
    public function getMemberInfo($uid)
    {
        $key = $this->key('member_infos', $uid);
        return $this->cache_obj->get($key);
    }

    /**
     * 批量创建用户会员身份信息缓存
     * @param int $uid
     */
    public function createMemberInfos($items, $rzLiveTime = 0)
    {
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('member_infos');
        return $this->mset($items, $liveTime);
    }

    /**
     * 批量获取用户会员身份信息
     * @param unknown_type $uids
     * @param unknown_type $keys
     */
    public function getMemberInfos($uids, &$keys)
    {
        foreach ($uids as $uid)
        {
            $keys[(string)$uid] = $this->key('member_infos', $uid);
        }

        return $this->mget($keys);
    }
}