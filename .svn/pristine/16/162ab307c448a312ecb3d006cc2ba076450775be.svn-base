<?php
/**
 * Dr_Relation
 * @todo 待处理转换格式
 * @not_check 先不检查内容
 */
class Dr_Relation extends Dr_Abstract
{
    const RELATION_BILATERAL = 1;
    const RELATION_FOLLOWED = 2;
    const RELATION_FOLLOWING = 3;
    const RELATION_NO = 4;
    
    /**
     * 联想搜索
     * @param q string 查询关键字
     * @param type int 查询种类，0关注人,1粉丝
     * @param count int 查询条数
     * @param range int 查询范围，0关注人，1备注，2都查
    */
    public static function searchSuggestionsAtUsers($q, $type = 0, $count = 10, $range = 2)
    {
        if (strlen($q) <= 0)
        {
            return array();
        }

        $api = Comm_Weibo_Api_Search::suggestionsAtUsers();
        $api->q = $q;
        $api->type = $type;
        $api->count = $count;
        $api->range = $range;
        $api->sid = 't_main';
        
        $arrAtt = $api->getResult();
        $result = array();
        foreach ($arrAtt as &$a)
        {
            $a['screen_name'] = $a['nickname'];
            unset($a['nickname']);
            $result[] = $a;
        }

        return $result;
    }
    
    /**
     * 按条件过滤我的关注人
     * @param int $page
     * @param int $count
     * @param array $param  scho,tag,comp
     * @param int $is_encoded
     */
    public static function searchFriendsFilter($page, $count, $param, $isEncoded = 1)
    {
        $api = Comm_Weibo_Api_Friendships::searchFriendsFilter();
        $api->page = $page;
        $api->count = $count;

        foreach ($param as $k => $v)
        {
            $api->$k = $v;
        }

        $api->is_encoded = $isEncoded;
        $list = $api->getResult();
        $rtn = array();

        if (isset($list['friends']) && is_array($list['friends']))
        {
            foreach ($list['friends'] as $u)
            {
                if (isset($u['user']) && is_array($u['user']))
                {
                    $rtn[] = $u['user'];
                }
            }
        }

        return $rtn;
    }
    
    /**
     * 取得好友列表  目前接口还每实现 等待测试
     * @param unknown_type $uid 需要查询好友列表的用户uid
     * @param unknown_type $page 页数
     * @param unknown_type $count 每页现实的条数
     */
    public static function friendsList($uid, $page = 1, $count = 20)
    {
        $comm = Comm_Weibo_Api_Friendships::friendsBilateral();
        $comm->uid = $uid;
        $comm->page = $page;
        $comm->count = $count;
        $comm->sort = 0;
        $friendList = $comm->getResult();

        $rtnFriendList = array();
        $rtnFriendList['friend_list'] = array();
        if ($friendList['total_number'] > 0)
        {
            foreach ($friendList['users'] as $v)
            {
                $rtnFriendList['friend_list'][] = new Do_User($v);
            }
        }

        $rtnFriendList['total_number'] = $friendList['total_number'];
        return $rtnFriendList;
    }
    
    /**
     * 取得好友列表id
     * @param unknown_type $uid 需要查询好友列表的用户uid
     * @param unknown_type $page 页数
     * @param unknown_type $count 每页现实的条数
     */
    public static function friendsListIds($uid, $page = 1, $count = 20)
    {   
        try
        {  
            $comm = Comm_Weibo_Api_Friendships::friendsBilateralIds();
            $comm->uid = $uid;
            $comm->page = $page;
            $comm->count = $count;
            $comm->sort = 0;
            $friendList = $comm->getResult();
            return isset($friendList['ids']) ? $friendList['ids'] : array();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw new Dr_Exception($e);
        }
    }
     
    /**
     * 取得共同关注的好友
     * @param unknown_type $uid
     * @param unknown_type $fuid
     * @param unknown_type $page
     * @param unknown_type $count
     */
    public static function sameFollow($uid, $fuid, $page, $count)
    {
        //只缓存第一页
        $needCache = (0 === $page ? true : false);
        if ($needCache)
        {
            $cacheObject = new Cache_Relation();
            $sameFollow = $cacheObject->getSameFollowers($uid, $fuid, $page, $count);
        }
        
        if (false === $sameFollow || !isset($sameFollow))
        {
            $comm = Comm_Weibo_Api_Friendships::friendsInCommon();
            $comm->uid = $uid;
            $comm->suid = $fuid;
            $comm->page = $page;
            $comm->count = $count;
            $comm->trim_status = 0;
            $sameFollow = $comm->getResult();
            
            if ($needCache)
            {
                $cacheObject->setSameFollowers($uid, $fuid, $page, $count, $sameFollow);
            }
        }
        
        $rtnSameFollowList = array();
        if ($sameFollow['total_number'] > 0)
        {
            $rtnSameFollowList['friend_list'] = array();
            foreach ($sameFollow['users'] as $v)
            {
                $rtnSameFollowList['friend_list'][] = new Do_User($v, 'output');
            }
        }

        $rtnSameFollowList['total_number'] = $sameFollow['total_number'];
        return $rtnSameFollowList;
    }
    
    /**
     * 获取当前登录用户的关注人中，关注了指定用户的用户列表
     * @param int64 $fuid
     * @param int $page
     * @param int $count
     * @return array()
     */
    public static function friendsChainFollowers($fuid, $page, $count)
    {
        //只缓存第一页
        $needCache = (0 === $page ? true : false);
        if ($needCache)
        {
            $viewer = Comm_Context::get('viewer');
            $cacheObject = new Cache_Relation();
            $chainFollowers = $cacheObject->getChainFollowers($viewer->id, $fuid, $page, $count);
        }
        
        if (!isset($chainFollowers) || false === $chainFollowers)
        {
            $comm = Comm_Weibo_Api_Friendships::friendsChainFollowers();
            $comm->uid = $fuid;
            $comm->page = $page;
            $comm->count = $count;
            $chainFollowers = $comm->getResult();

            if ($needCache)
            {
                $cacheObject->setChainFollowers($viewer->id, $fuid, $page, $count, $chainFollowers);
            }
        }
        
        $rtnChainFollowers = array();
        if ($chainFollowers['total_number'] > 0)
        {
            $rtnChainFollowers['chain_list'] = array();
            foreach ($chainFollowers['users'] as $v)
            {
                $rtnChainFollowers['friend_list'][] = new Do_User($v, 'output');
            }
        }

        $rtnChainFollowers['total_number'] = $chainFollowers['total_number'];
        return $rtnChainFollowers;
    }
    
    /**
     * 
     * 检测任意两个关系
     * @param unknown_type $uid 登录用户uid
     * @param unknown_type $fuid 资源用户uid
     */
    public static function checkRelation($uid, $fuid)
    {
        if ($uid == "")
        {
            return self::RELATION_NO;
        }

        if ($fuid == "")
        {
            return self::RELATION_NO;
        }

        if ($uid == $fuid)
        {
            return self::RELATION_BILATERAL;;
        }
        
        try
        {
            $comm = Comm_Weibo_Api_Friendships::show();
            $comm->source_id = $uid;
            $comm->target_id = $fuid;
            $relationShip = $comm->getResult();
            
            //双向关注
            if ($relationShip['source']['followed_by'] == true && $relationShip['source']['following'] == true)
            {
                $status = self::RELATION_BILATERAL;
            }
            
            //我的粉丝
            if ($relationShip['source']['followed_by'] == true && $relationShip['source']['following'] == false)
            {
                $status = self::RELATION_FOLLOWED;
            }
            
            //我是他的粉丝
            if ($relationShip['source']['followed_by'] == false && $relationShip['source']['following'] == true)
            {
                $status = self::RELATION_FOLLOWING;
            }
            
            //没有关注关系
            if ($relationShip['source']['followed_by'] == false && $relationShip['source']['following'] == false)
            {
                $status = self::RELATION_NO;
            }

        }
        catch (Comm_Exception_Program $e)
        {
            $status = self::RELATION_NO;
        }

        return $status;
    }
    
    /**
     * @deprecated 检测当前用户和指定用户的关注关系
     * @param unknown_type $fuid
     */
    public static function checkRelationForCurrent($fuid)
    {
        try
        {
            $userInfo = Dr_User::getUserInfo($fuid);
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return "0";
        }

        //双向关注
        if ($userInfo['follow_me'] == true && $userInfo['following'] == true)
        {
            return self::RELATION_BILATERAL;
        }
        
        //我的粉丝
        if ($userInfo['follow_me'] == true && $userInfo['following'] == false)
        {
            return self::RELATION_FOLLOWED;
        }
        
        //我是他的粉丝
        if ($userInfo['follow_me'] == false && $userInfo['following'] == true)
        {
            return self::RELATION_FOLLOWING;
        }
        
        //没有关注关系
        if ($userInfo['follow_me'] == false && $userInfo['following'] == false)
        {
            return self::RELATION_NO;
        }
    }
    
    /**
     * 批量检测好友关系
     * @param unknown_type $fuids
     * @param unknown_type $uid
     */
    public static function checkRelationBatch($fuids, $uid)
    {
        try
        {
            if (is_array($fuids))
            {
                foreach ($fuids as $k => $v)
                {
                    if (!$v)
                    {
                        unset($fuids[$k]);
                    }
                }

                if (count($fuids))
                {
                    $fuids = implode(',', $fuids);
                }
            }

            $comm = Comm_Weibo_Api_Friendships::existsBatchInternal();
            $comm->uids = $fuids;
            $comm->uid = $uid;
            return $comm->getResult();
        }
        catch (Comm_Exception_Program $e)
        {
            return array();
        }
    }
    
    /**
     * 取得粉丝列表
     * @param $uid          int64     用户ID
     * @param $cur          int       用户用于分页请求，请求第1页cursor传-1，在返回的结果中会得到nextCursor字段，表示下一页的cursor。nextCursor为0表示已经到记录末尾。
     * @param $count        int       用户每页返回的最大记录数，最大不能超过200，默认为50。默认为50
     * @param $trimStatus   int       user中的status信息开关，打开trim时，user中的status字段仅返回statusId，关闭时返回完整status信息。取值：1：打开trim开关，0：关闭trim开关。默认打开trim开关。
     */
    public static function friendshipsFollowers($uid, $cur, $count, $trimStatus)
    {
        if ($cur == -1)
        {
            $cacheObject = new Cache_Relation();
            $friend = $cacheObject->getFollowers($uid, $count);
            if (false !== $friend)
            {
                return $friend;
            }                
        }

        try
        {
            $comm = Comm_Weibo_Api_Friendships::followers();
            $comm->uid = $uid;

            if ($cur > 1)
            {
                $cur = ($cur - 1) * $count;
            }
            else
            {
                $cur = -1;
            }

            $comm->cursor = $cur;
            $comm->count = $count;
            $comm->getHttpRequest()->connectTimeout = 2000;
            $comm->getHttpRequest()->timeout = 2000;
            $comm->trim_status = $trimStatus;
            $friendList = $comm->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array('total_number' => -1);
        }

        $friend = array();
        foreach ($friendList['users'] as $v)
        {
            $friend['users'][] = new Do_User($v, Do_Abstract::MODE_OUTPUT);
        }

        $friend['total_number'] = $friendList['total_number'];
        
        if ($cur == -1)
        {
            $cacheObject->setFollowers($uid, $count, $friend);
        }

        return $friend;
    }
    
    /**
     * 取得粉丝uid列表
     * 
     * @param int $uid
     * @param $cur int 分页游标、第一页为-1
     * @param $count int 用户每页返回的最大记录数，最大不能超过200，默认为50。默认为50
     * @throws Comm_Exception_Program
     */
    public static function followersIds($uid, $cur, $count)
    {
        $comm = Comm_Weibo_Api_Friendships::followersIds();
        $comm->uid = $uid;
        $comm->cursor = $cur;
        $comm->count = $count;
        return $comm->getResult();
    }
    
    /**
     * 取得关注uid列表
     * 
     * @param int $uid
     * @param $cur int 分页游标、第一页为-1
     * @param $count int 用户每页返回的最大记录数，最大不能超过200，默认为50。默认为50
     * @throws Comm_Exception_Program
     */
    public static function friendsIds($uid, $cur, $count)
    {
        $comm = Comm_Weibo_Api_Friendships::friendsIds();
        $comm->uid = $uid;
        $comm->cursor = $cur;
        $comm->count = $count;

        if (Tool_Misc::isUseUnloginAuth())
        {
            $comm->addUserPassword(); //指定用户名、密码方式访问
        }

        return $comm->getResult();
    }
    
    /**
     * 获取粉丝列表（先取uid列表，再批量取用户信息)
     * 
     * @param int $uid
     * @param int $cur
     * @param int $count
     * @throws Comm_Exception_Program
     * @param bool $isLiteInfo 是否返回简版用户信息
     */
    public static function getFollowers($uid, $cur, $count, $isLiteInfo = true)
    {
        //只有第一页并且只返回简版用户信息时才走缓存
        $isCache = ($cur == -1 && $isLiteInfo == true);
        if ($isCache)
        {
            $cacheRelation = new Cache_Relation();
            $followersInfo = $cacheRelation->getFollowers($uid, $count);
            if (false !== $followersInfo)
            {
                return $followersInfo;
            }
        }

        $followersInfo = array();
        $followersInfo['users'] = array();
        $followersIds = self::followersIds($uid, $cur, $count); 
        $followersInfo['next_cursor'] = $followersIds['next_cursor'];
        $followersInfo['previous_cursor'] = $followersIds['previous_cursor'];
        $followersInfo['total_number'] = $followersIds['total_number'];
        $uids = isset($followersIds['ids']) ? $followersIds['ids'] : array();
        
        if (empty($uids))
        {
            return $followersInfo;
        }

        $followersInfo['users'] = $isLiteInfo ? Dr_User::getLiteUserInfos($uids) : Dr_User::getUserInfos($uids);
        
        if ($isCache)
        {
            $cacheRelation->setFollowers($uid, $count, $followersInfo);
        }

        return $followersInfo;
    }
    
    /**
     * 获取关注列表 (先取uid列表，再批量取用户信息)
     * 
     * @see followerList
     * @param int $uid
     * @param int $cur
     * @param int $count
     * @param bool $isLiteInfo 是否返回简版用户信息
     */
    public static function getFriends($uid, $cur, $count, $isLiteInfo = true)
    {
        //只有第一页并且只返回简版用户信息时才走缓存
        $isCache = ($cur == -1 && $isLiteInfo == true);
        if ($isCache)
        {
            $cacheRelation = new Cache_Relation();
            $friendsInfo = $cacheRelation->getFriends($uid, $count);
            if (false !== $friendsInfo)
            {
                return $friendsInfo;
            }
        }

        $friendsInfo = array();
        $friendsInfo['users'] = array();
        $followersIds = self::friendsIds($uid, $cur, $count); 
        $friendsInfo['next_cursor'] = isset($followersIds['next_cursor']) ? $followersIds['next_cursor'] : '';
        $friendsInfo['previous_cursor'] = isset($followersIds['previous_cursor']) ? $followersIds['previous_cursor'] : '';
        $friendsInfo['total_number'] = isset($followersIds['total_number']) ? $followersIds['total_number'] : 0;
        $uids = isset($followersIds['ids']) ? $followersIds['ids'] : array();
        if (empty($uids))
        {
            return $friendsInfo;
        }

        $friendsInfo['users'] =  $isLiteInfo ? Dr_User::getLiteUserInfos($uids) : Dr_User::getUserInfos($uids);
        
        if ($isCache)
        {
            $cacheRelation->setFriends($uid, $count, $friendsInfo);
        }

        return $friendsInfo;
    }
    
    /**
     * 取得关注列表
     * @param unknownType $uid
     * @param unknownType $hasUserInfo
     */
    public static function followerList($uid, $cur = 0, $count = 20)
    {
        if ($cur == -1)
        {
            $cacheRelation = new Cache_Relation();
            $list = $cacheRelation->getFollowersList($uid, $count);
            if (false !== $list)
            {
                return $list;
            }
        }

        $comm = Comm_Weibo_Api_Friendships::friends();
        $comm->uid = $uid;
        if ($cur > 1)
        {
            $cur = ($cur - 1) * $count;
        }
        else
        {
            $cur = -1;
        }

        $comm->cursor = $cur;
        //$comm->page = $cur;
        $comm->count = $count;
        $friendList = $comm->getResult();
        $friendDoList = array();

        if (is_array($friendList['users']) && count($friendList['users']) > 0)
        {
            foreach ($friendList['users'] as $v)
            {
                $friendDoList[] = new Do_User($v, Do_Abstract::MODE_OUTPUT);
            }
        }

        $list = array(
            'list' => $friendDoList, 
            'total_number' => $friendList['total_number'], 
            'next_cursor' => $friendList['next_cursor'], 
            'previous_cursor' => $friendList['previous_cursor']
        );

        if ($cur == -1)
        {
            $list = $cacheRelation->setFollowersList($uid, $count, $list);
        }

        return $list;
    }
    
    /**
     * 取得黑名单
     * @param unknownType $count
     * @param unknownType $page
     */
    public static function blackList($count = 20, $page = 1)
    {
        $comm = Comm_Weibo_Api_Blocks::blocking();
        $comm->count = $count;
        $comm->page = $page;
        $blockUserList = $comm->getResult();
        $userDoList = array();
        $addTime  = array();

        foreach ($blockUserList['users'] as $v)
        {
            $userDoList[(string)$v['user']['id']] = new Do_User($v['user']);
            $addTime[(string)$v['user']['id']] = $v['created_at'];
        }

        return array(
            "user_list" => $userDoList, 
            "add_time_list" => $addTime, 
            "count" => $blockUserList['total_number']
        );
    }
    
    /**
     * 取得用户的黑名单用户的id列表
     * @param unknownType $count
     * @param unknownType $page
     */
    public static function blackListId($count = 20, $page = 1)
    {
        $comm = Comm_Weibo_Api_Blocks::blockingIds();
        $comm->count = $count;
        $comm->page = $page;
        $blockUserList = $comm->getResult();
        foreach ($blockUserList as $v)
        {
            $userDoList[(string)$v['blocked_user']['user']['id']] = new Do_User($v['blocked_user']['user']);
            $addTime[(string)$v['blocked_user']['user']['id']] = $v['blocked_user']['add_time'];
        }

        return array(
            "user_list" => $userDoList, 
            "add_time_list" => $addTime, 
            "count" => $blockUserList['count']
        );
    }
    
    /**
     * 检测是用户是否在我的黑名单里
     * @param unknown_type $fuid
     */
    public static function blocksExists($fuid, $invert = 0)
    {
        $comm = Comm_Weibo_Api_Blocks::exists();
        $comm->uid = $fuid;
        $comm->invert = $invert;
        $checkRes = $comm->getResult();
        return (boolean)$checkRes['result'];
    }
    
    /**
     * 批量获取用户备注信息
     * 
     * @param array $uids
     * @return array
     */
    public static function friendsRemarkBatch(array $uids)
    {
        $comm = Comm_Weibo_Api_Friendships::friendsRemarkBatch();
        $chunkUids = array_chunk($uids, 50);
        $remarkList = array();
        foreach ($chunkUids as $uids)
        {
            try
            {
                $comm->uids = join(',', $uids);
                $rst = $comm->getResult();
                foreach ($rst as $v)
                {
                    $remarkList[$v['id']] = $v['remark'];
                }
            }
            catch (Comm_Exception_Program $e)
            {
                continue;
            }
        }

        return $remarkList;
    }
    
    /**
     * 检测指定的用户是否已关注了指定的用户列表，只返回已关注的uid
     * 
     * @param array $uids 需要判断是否已经关注的用户id列表
     * @param array $uid 需要判断的uid，缺少取当前登录用户的uid
     * @return array
     */
    public static function checkFollowedBatch(array $uids, $uid = null)
    {
        $uids = array_unique(arrayFilter($uids));
        if (empty($uids))
        {
            return array();
        }

        if ($uid === null)
        {
            $uid = Comm_Context::get('viewer')->id;
        }

        $comm = Comm_Weibo_Api_Friendships::existsBatchInternal();
        $comm->uids = join(',', $uids);
        $comm->uid = $uid;
        $rst = $comm->getResult();
        $followedList = array();

        //以id作key，方便判断
        foreach ($rst as $v)
        {
            $followedList[$v['id']] = $v['id'];
        }

        return $followedList;
    }
    
    /**
     * 批量判断密友关系
     *
     * @param  $uids  uid数组  指定等待判断密友关系的用户id列表，最大20.
     * @return $array  array('uid1' => '1','uid2' => '0',...)" // 对于当前登录用户来说，待判断用户是：0：无密友关系；1：密友关系；2：密友邀请中；
     */
    public static function friendshipsCloseFriendsExists(array $uids)
    {
        if (empty($uids))
        {
            return array();
        }

        $relation = array();
        $uidString = implode(',', $uids);
        
        try
        {
            $comm = Comm_Weibo_Api_Friendships::friendshipsCloseFriendsExists();
            $comm->uids = $uidString;
            $rst = $comm->getResult();
            if (isset($rst['uids']) && is_array($rst['uids']) && count($rst['uids']))
            {
                foreach ($rst['uids'] as $k => $v)
                {
                    $relation[$v['id']] = $v['result'];
                }
            }

            return $relation;
        } 
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }
}
