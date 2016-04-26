<?php

class Dr_Members extends Dr_Abstract
{
    //月付会员
    const MEMBER_TYPE_MONTH = 11;
    //季度会员
    const MEMBER_TYPE_QUARTER = 13;
    //年付会员
    const MEMBER_TYPE_YEAR = 12;
    //过期会员
    const MEMBER_TYPE_EXPIRE = 2;
    //准会员
    const MEMBER_TYPE_ZHUN = 14;
    
    //会员类型集
    public static $validateMemberType = array(self::MEMBER_TYPE_MONTH, self::MEMBER_TYPE_QUARTER, self::MEMBER_TYPE_YEAR, self::MEMBER_TYPE_ZHUN);
    public static $zhunMemberType = array(self::MEMBER_TYPE_ZHUN);

    //根据会员等级不同而设置不同的分组成员上限
    const COMMON_LIMIT_GROUP_COUNT = 200; //非会员
    const PRIMARY_LIMIT_GROUP_COUNT = 300;
    const INTERMEDIATE_LIMIT_GROUP_COUNT = 500;
    const SENIOR_LIMIT_GROUP_COUNT = 800;
    const YEAR_SENIOR_LIMIT_GROUP_COUNT = 1000;
    
    public static $memberTypes = array(self::MEMBER_TYPE_MONTH, self::MEMBER_TYPE_YEAR, self::MEMBER_TYPE_QUARTER);
    
    //以前是会员（过期会员）
    const JS_MEMBER_EXPIRE = 3;
    
    //当下是会员（会员）
    const JS_MEMBER_NOW = 2;
    
    //未来是会员（非会员）
    const JS_MEMBER_FUTURE = 1;

    /**
     * 获取用户的会员状态
     * 此接口废弃，改调用此类中get_member_detail方法
     * @deprecated
     * @param int $uid 不传时默认取当前用户的
     * @return ArrayObject
     */
    public static function show($uid)
    {
        $cacheMember = new Cache_Members();
        $result = $cacheMember->getMemberInfo($uid);
        if ($result)
        {
            return $result;
        }

        try
        {
            $memberApi = Comm_Weibo_Api_Members::show();
            $memberApi->uid = $uid;
            $memberApi->flag = 1;
            $result = $memberApi->getResult();

            if (is_array($result) && !empty($result))
            {
                $cacheMember->createMemberInfo($uid, $result);
                return $result;
            }
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return false;
    }

    /**
     * 批量获取用户会员身份信息
     * 
     * @param array $uids uid列表
     * 
     * @return ArrayObject
     */
    public static function showBatch($uids)
    {
        if (!is_array($uids))
        {
            return array();
        }

        foreach ($uids as $key => $value)
        {
            if (!is_numeric($value))
            {
                unset($uids[$key]);
            }
        }

        $uids = array_unique($uids);
        $keys = array();
        $memberInfos = array();
        $cacheMembers = new Cache_Members();
        $result = $cacheMembers->getMemberInfos($uids, $keys);
        $qUids = self::filterCachedItems($result, $keys, $memberInfos);
        if (!$qUids)
        {
            return $memberInfos;
        }

        try
        {
            $memberApi = Comm_Weibo_Api_Members::showBatch();
            $chunkUids = array_chunk($qUids, 20);
            $qInfos = array();
            foreach ($chunkUids as $uids)
            {
                $memberApi->uids = implode(',', $uids);
                $memberApi->flag = 1;
                $rst = $memberApi->getResult();
                
                if (is_array($rst) && isset($rst['uids']) && is_array($rst['uids']))
                {
                    $qInfos = array_merge($qInfos, $rst['uids']);
                }
            }

            $needCacheItem = array();
            if (!empty($qInfos))
            {
                foreach ($qInfos as $info)
                {
                    $memberInfos[$info['uid']] = $info;
                    $needCacheItem[$keys[$info['uid']]] = $memberInfos[$info['uid']];
                }
            }

            if (!empty($needCacheItem))
            {
                $cacheMembers->createMemberInfos($needCacheItem);
            }
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $memberInfos;
    }

    /**
     * 获取微博信息中用户的会员身份信息
     * 
     * @param Do_Status $status 微博列表
     * @return ArrayObject
     */
    public static function getMemberInfoFromStatus($status)
    {
        if (!is_array($status))
        {
            return array();
        }

        $uids = array();
        foreach ($status as $info)
        {
            if (isset($info['user']))
            {
                $uids[] = $info['user']['id'];
            }

            if (isset($info['retweeted_status']) && isset($info['retweeted_status']['user']))
            {
                $uids[] = $info['retweeted_status']['user']['id'];
            }

            //@我的评论页特殊微博结构
            if (isset($info['reply_comment']) && isset($info['reply_comment']['user']))
            {
                $uids[] = $info['reply_comment']['user']['id'];
            }

            if (isset($info['status']) && isset($info['status']['user']))
            {
                $uids[] = $info['status']['user']['id'];
            }

            //收藏微博的特殊结构
            if (isset($info['status']['retweeted_status']) && isset($info['status']['retweeted_status']['user']))
            {
                $uids[] = $info['status']['retweeted_status']['user']['id'];
            }
        }

        return !empty($uids) ? self::showBatch($uids) : array();
    }

    /**
     * 获取会员详细信息
     * 
     * @param int $uid 用户id
     * 
     * @return array();
     */
    public static function getMemberDetail($uid)
    {
        try
        {
            $memberApi = Comm_Weibo_Api_Members::showDetail();
            $memberApi->uid = $uid;
            return $memberApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 获取指定会员用户的分组成员上限
     */
    public static function getGroupMembersUpperLimit($uid)
    {
        $userMemberRank = array();
        $memInfo = Dr_User::getUserInfo($uid);
        
        if (!empty($memInfo))
        {
            $rank = $memInfo['mbrank']; //会员等级
            $type = in_array($memInfo['mbtype'], array(self::MEMBER_TYPE_MONTH, self::MEMBER_TYPE_QUARTER, self::MEMBER_TYPE_YEAR)) ? $memInfo['mbtype'] : false; //会员类型判断
            $isMember = true; //是否会员
            
            if (in_array($rank, array('6')) && $type)
            {
                $groupLimit = ($type == self::MEMBER_TYPE_YEAR ? self::YEAR_SENIOR_LIMIT_GROUP_COUNT : self::SENIOR_LIMIT_GROUP_COUNT);
            }
            elseif (in_array($rank, array('4', '5')) && $type)
            {
                $groupLimit = ($type == self::MEMBER_TYPE_YEAR ? self::SENIOR_LIMIT_GROUP_COUNT : self::INTERMEDIATE_LIMIT_GROUP_COUNT);
            }
            elseif (in_array($rank, array('1', '2', '3')) && $type)
            {
                $groupLimit = ($type == self::MEMBER_TYPE_YEAR ? self::INTERMEDIATE_LIMIT_GROUP_COUNT : self::PRIMARY_LIMIT_GROUP_COUNT);
            } 
            else
            {
                $groupLimit = self::COMMON_LIMIT_GROUP_COUNT;
                $isMember = false;
            }

            $userMemberRank = array('rank' => $rank, 'group_limit' => $groupLimit, 'type' => $memInfo['mbtype'], 'is_member' => $isMember);
            Comm_Context::set('user_member_rank', $userMemberRank);
        }

        return $userMemberRank;
    }

    /**
     * 获取用户登陆用户的悄悄关注数上限
     */
    public static function getUserQuietNumber($uid)
    {
        $userInfo = Comm_Context::get('viewer', false);
        if ($uid && $uid != $userInfo['id'])
        {
            $userInfo = Dr_User::getUserInfo($uid);
        }

        $quietNumber = 10;
        if ($userInfo && $userInfo->id)
        {
            $mbtype = $userInfo['mbtype'];
            $mbrank = $userInfo['mbrank'];

            if (in_array($mbtype, self::$memberTypes))
            {
                if ($mbtype == self::MEMBER_TYPE_YEAR)
                {
                    $quietNumber = ($mbrank == 6 ? 30 : 20);
                } 
                else if (in_array($mbrank, array(1, 2, 3)))
                {
                    $quietNumber = 15;
                } 
                else if (in_array($mbrank, array(4, 5))) 
                {
                    $quietNumber = 20;
                } 
                else if ($mbrank == 6) 
                {
                    $quietNumber = 30;
                }
            }
        }

        return $quietNumber;
    }

    /**
     * 根据UID返回用户会员身份
     * @param $uid
     */
    public static function getMemberType($uid)
    {
        if (empty($uid))
        {
            return self::JS_MEMBER_FUTURE;
        }

        $userInfo = Dr_User::getUserInfo($uid);
        if (isset($userInfo['mbtype']) && in_array($userInfo['mbtype'], self::$memberTypes))
        {
            return self::JS_MEMBER_NOW;
        } 
        else if (isset($userInfo['mbtype']) && $userInfo['mbtype'] == self::MEMBER_TYPE_EXPIRE) 
        {
            return self::JS_MEMBER_EXPIRE;
        } 
        else 
        {
            return self::JS_MEMBER_FUTURE;
        }    
    }
}
