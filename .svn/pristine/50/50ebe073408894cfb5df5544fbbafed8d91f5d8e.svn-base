<?php
/**
 * 用户相关缓存处理
 */
class Cache_User extends Cache_Abstract
{
    //TODO
    protected $configs = array(
        'user_info' => array('%s_1_0_%s', 10), //用户信息
        'may_interested' => array('%s_2_%s_%s', 100),
        'screen_name_to_uid' => array('%s_3_%s', 86400), //昵称转uid
        'domain_to_uid' => array('%s_4_%s', 172800), //域名转uid
        'user_csr_info' => array('%s_6_%s', 100),
        'user_version' => array('%s_11_%s', 1296000), //用户版本获得
        'lite_user_info' => array('%s_12_0_%s', 10), //简版用户信息
        'user_extend_info' => array('%s_13_0_%s', 86400), //用户扩展信息设置
        'user_friends_100_zt' => array('%s_7_1_%s', 600),  //临时，存100好友的uid，for专题
        'user_fans_100_zt' => array('%s_7_2_%s', 600),  //临时，存100粉丝的uid，for专题
        'user_publish_bubble' => array('%s_7_3_%s', 86400), //发微博引导气泡提醒
        'publish_bubble_trend' => array('%s_7_4', 600), //发微博引导话题数据
        'hot_user' => array('%s_9_%s', 100),
        'user_not_interested' => array('%s_10_%s', 604800),
        'newuser_interested' => array('%s_11_%s_%s', 604800),
        'newuser_guide_status' => array('%s_14_0_%s', 604800), //新用户引导状态值,缓存时间为7天
        'bubble_tips_status' => array('%s_15_1_%s_1_%s', 604800), //用户各种提醒气泡标示
        'guide_security_data' => array('%s_16_2_%s', 300), //账户安全数据缓存
        'user_rank' => array('%s_17_0_%s', 21600), //用户等级 缓存6小时
        'user_rank_detail' => array('%s_18_1_%s', 1800), //用户等级详细信息 缓存30分钟
    );

    protected $cachePool = 'USER';
    protected $keyPrefix = 'user';

    const MAYBE_REASON_USER_TOP6 = 6;
    const MAYBE_REASON_USER_COUNT = 30;

    public function getUserRank($uid)
    {
        $key = $this->key('user_rank', $uid);
        return $this->get($key);
    }
    
    public function createUserRank($uid, $value)
    {
        $key = $this->key('user_rank', $uid);
        return $this->set($key, $value, $this->livetime('user_rank'));
    }
    
    public function detroyUserRank($uid)
    {
        $key = $this->key('user_rank', $uid);
        return $this->del($key);
    }
    
    public function getUserRankDetail($uid)
    {
        $key = $this->key('user_rank_detail', $uid);
        return $this->get($key);
    }
    
    public function createUserRankDetail($uid, $value)
    {
        $key = $this->key('user_rank_detail', $uid);
        return $this->set($key, $value, $this->livetime('user_rank_detail'));
    }
    
    public function destroyUserRankDetail($uid)
    {
        $key = $this->key('user_rank_detail', $uid);
        return $this->del($key);
    }
    
    public function getUserFriends100Zt($uid)
    {
        $key = $this->key('user_friends_100_zt', $uid);
        return $this->get($key);
    }

    public function createUserFriends100Zt($uid, $value)
    {
        $key = $this->key('user_friends_100_zt', $uid);
        return $this->set($key, $value, $this->livetime('user_friends_100_zt'));
    }

    public function getUserFans100Zt($uid)
    {
        $key = $this->key('user_fans_100_zt', $uid);
        return $this->get($key);
    }
    public function createUserFans100Zt($uid, $value)
    {
        $key = $this->key('user_fans_100_zt', $uid);
        return $this->set($key, $value, $this->livetime('user_fans_100_zt'));
    }

    public function getLiteUserInfos(array $uids, &$keys = array())
    {
        foreach ($uids as $uid)
        {
            $keys[(string)$uid] = $this->key('lite_user_info', $uid);
        }

        return $this->mget($keys);
    }

    public function getUserInfos(array $uids, &$keys = array(), &$liteKeys = array())
    {
        foreach ($uids as $uid)
        {
            $keys[(string)$uid] = $this->key('user_info', $uid);
            $liteKeys[(string)$uid] = $this->key('lite_user_info', $uid);
        }

        return $this->mget($keys);
    }

    public function createUserInfos(array $items, $rzLiveTime = 0)
    {
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('user_info');
        return $this->mset($items, $liveTime);
    }
    
    /**
     * 单个创建用户信息缓存，有包括简版
     * @param $uid
     * @param $user_info
    * @param $rzLiveTime 
     */
    public function createUserInfo($uid, array $user_info, $rzLiveTime = 0)
    {
        $key = $this->key('user_info', $uid);
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('user_info');
        return $this->set($key, $user_info, $liveTime);
    }
    
    /**
     * 通过screen_name获取uid信息
     * @param $screenName
     * @param $keys
     */
    public function screenNameToUid($screenName, &$keys = '')
    {
        $keys = $this->key('screen_name_to_uid', $screenName);
        return $this->get($keys);
    }
    
    /**
     * 创建screen_name至uid的映射缓存
     * @param $screenName
     * @param $uid
     * @param $rzLiveTime 
     */
    public function createScreenNameToUid($screenName, $uid, $rzLiveTime = 0)
    {
        $key = $this->key('screen_name_to_uid', $screenName);
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('screen_name_to_uid');
        return $this->set($key, $uid, $liveTime);
    }
    
    /**
     * 通过domain获取uid信息
     * @param $screenName
     * @param $keys
     */
    public function domainToUid($domain, &$keys = '')
    {
        $keys = $this->key('domain_to_uid', $domain);
        return $this->get($keys);
    }
    
    /**
     * 创建screen_name至uid的映射缓存
     * @param $screenName
     * @param $uid
     * @param $rzLiveTime 
     */
    public function createDomainToUid($screenName, $uid, $rzLiveTime = 0)
    {
        $key = $this->key('domain_to_uid', $screenName);
        $liveTime = $rzLiveTime ? $rzLiveTime : $this->livetime('domain_to_uid');
        return $this->set($key, $uid, $liveTime);
    }
    
    /**
     * 删除用户相关缓存,包括全信息和简版信息
     * @param $uid
     */
    public function clearUserInfo($uid)
    {
        $key = $this->key('user_info', $uid);
        $liteKey = $this->key('lite_user_info', $uid);
        $re = $this->del($key);
        if ($re === false)
        {
            return false;
        }

        $re = $this->del($liteKey);
        if ($re === false)
        {
            return false;
        }

        return true;
    }
    
    public function clearScreenNameToUid($screenName)
    {
        $key = $this->key('screen_name_to_uid', $screenName);
        return $this->del($key);
    }
    
    public function clearDomainToUid($domain)
    {
        $key = $this->key('domain_to_uid', $domain);
        return $this->del($key);
    }
    
    public function clearUserExtendInfo($uid)
    {
        $key = $this->key('user_extend_info', $uid);
        return $this->del($key);
    }
    
    /**
     * 获得用户的版本号
     *
     * @param $uid 用户ID
     * @return string 用户的版本号
     */    
    public function getUserVersion($uid)
    {
        $key = $this->key('user_version', $uid);
        return $this->get($key);
    }
    
    public function getUserVersionBatch($uids, &$keys = array())
    {
        if (!is_array($uids) || empty($uids))
        {
            return array();
        }

        foreach ($uids as $uid)
        {
            $keys[(string)$uid] = $this->key('user_version', $uid);
        }

        return $this->mget($keys);
    }

    /**
     * 设置用户的版本号
     *
     * @param $uid 用户ID
     * @param $version 用户的版本号
     * @return string 是否成功
     */        
    public function createUserVersion($uid, $version)
    {
        $key = $this->key('user_version', $uid);
        return $this->set($key, $version, $this->livetime('user_version'));
    }   
    
    public function createUserVersionBatch($versions)
    {
        return $this->mset($versions, $this->livetime('user_version')); 
    }
    
    /**
     * 获得用户的扩展信息设置
     *
     * @param $uid 用户ID
     * @return string 用户的扩展信息
     */      
    public function getUserExtendInfo($uid)
    {
        $key = $this->key('user_extend_info', $uid);
        return $this->get($key);
    }
    
    /**
     * 设置用户的扩展信息
     *
     * @param $uid 用户ID
     * @param $extendInfo 用户的扩展信息
     * @return bool 是否成功
     */        
    public function createUserExtendInfo($uid, $extendInfo)
    {
        $key = $this->key('user_extend_info', $uid);
        return $this->set($key, $extendInfo, $this->livetime('user_extend_info'));
    }
    
/**
     * 用户发微博引导气泡缓存
     */
    public function createPublishBubbleFlag($uid, $time)
    {
        $key = $this->key('user_publish_bubble', $uid);
        return $this->set($key, '1', $time);
    }
    
    public function getPublishBubbleFlag($uid)
    {
        $key = $this->key('user_publish_bubble', $uid);
        return $this->get($key);
    }
    
    /**
     * 发微博引导话题数据
     * Enter description here ...
     */
    public function createPublishBubbleTrend($data)
    {
        $key = $this->key('publish_bubble_trend');
        return $this->set($key, $data, $this->livetime('publish_bubble_trend'));
    }
    
    public function getPublishBubbleTrend()
    {
        $key = $this->key('publish_bubble_trend');
        return $this->get($key);
    }
    
    /**
     * 设置新用户的状态值
     * 
     * @param $uid  用户uid
     * @param $status   状态值
     * @return bool 是否成功
     */
    public function createNewUserGuideStatus($uid, $status)
    {
        $key = $this->key('newuser_guide_status', $uid);
        return $this->set($key, $status, $this->livetime('newuser_guide_status'));
    }
    
    /**
     * 
     * 获取新用户的引导的状态值
     * @param  $uid   用户uid
     * @return string 状态值
     */
    public function getNewUserGuideStatus($uid)
    {
        $key = $this->key('newuser_guide_status', $uid);
        return $this->get($key);
    }
    
    /**
     * 设置气泡提醒标示
     * Enter description here ...
     * @param unknown_type $uid
     * @param unknown_type $type
     * @param unknown_type $time
     */
    public function createBubbleTipsStatus($uid, $type, $time = 0)
    {
        $key = $this->key('bubble_tips_status', $uid, $type);
        $time = $time > 0 ? $time : $this->livetime('bubble_tips_status');
        return $this->set($key, '1', $time);
    }
    
    /**
     * 获取气泡提醒标示
     * Enter description here ...
     * @param unknown_type $uid
     * @param unknown_type $type
     */
    public function getBubbleTipsStatus($uid, $type)
    {
        $key = $this->key('bubble_tips_status', $uid, $type);
        return $this->get($key);
    }
    
    /**
     * 设置 账户安全数据缓存
     * @param int $uid
     */
    public function getGuideSecurityData($uid)
    {
        $key = $this->key('guide_security_data', $uid);
        return $this->get($key);
    }
    
    /**
     * 获取 账户安全数据缓存
     * @param int $uid
     * @param array $data
     */
    public function createGuideSecurityData($uid, $data)
    {
        $key = $this->key('guide_security_data', $uid);
        return $this->set($key, $data, $this->livetime('guide_security_data'));
    }

    public function getMayInterested($uid, $page, $count)
    {
        if ($page != 1)
        {
            return false;
        }

        $key = $this->key('may_interested', $uid, $count);
        return $this->get($key);
    }
    
    public function createMayInterested($uid, $page, $count, $value)
    {
        if ($page != 1)
        {
            return false;
        }

        $key = $this->key('may_interested', $uid, $count);
        return $this->set($key, $value, $this->livetime('may_interested'));
    }

    public function delMayInterested($uid)
    {
        $keys = array();
        $keys[] = $this->key('may_interested', $uid, self::MAYBE_REASON_USER_TOP6);
        $keys[] = $this->key('may_interested', $uid, self::MAYBE_REASON_USER_COUNT);
        $keys[] = $this->key('hot_user', $uid);

        return $this->mdel($keys);
    }

    public function getCsrByUid($uid)
    {
        $key = $this->key('user_csr_info', $uid);
        return $this->get($key);
    }
    
    public function createCsrByUid($uid, $data)
    {
        $key = $this->key('user_csr_info', $uid);
        return $this->set($key, $data, $this->livetime('user_csr_info'));
    }

    public function getUsersHot($uid)
    {
        $key = $this->key('hot_user', $uid);
        return $this->get($key);
    }
    
    public function createUsersHot($uid, $data)
    {
        $key = $this->key('hot_user', $uid);
        return $this->set($key, $data, $this->livetime('hot_user'));
    }

    public function getUsersNotInterested($uid)
    {
        $key = $this->key('user_not_interested', $uid);
        return $this->get($key);
    }
    
    public function createUsersNotInterested($uid, $data)
    {
        $key = $this->key('user_not_interested', $uid);
        return $this->set($key, $data, $this->livetime('user_not_interested'));
    }

    public function createNewUserInterested($uid, $data)
    {
        $key = $this->key('newuser_interested', $uid);
        return $this->set($key, $data, $this->livetime('newuser_interested'));
    }
    
    public function getNewUserInterested($uid)
    {
        $key = $this->key('newuser_interested', $uid);
        return $this->get($key);
    }

    /**
     * 批量删除用户缓存
     * @param  array $uids 待删除的uid数组
     * @return bool
     */
    public function delUserInfos($uids)
    {
        $keys = array();
        foreach ($uids as $uid)
        {
            $keys[$uid] = $this->key('user_info', $uid);
        }

        return $this->mdel($keys);
    }


}
