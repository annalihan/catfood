<?php
/**
 * Dr_User
 * @todo 待处理转换格式
 * @not_check 先不检查内容
 */
class Dr_User extends Dr_Abstract
{
    const MC_USER_RZ_LIVETIME = 10;
    private static $_userHotTypes = array(
        'region_uids' => 1, 
        'v1_uids' => 1, 
        'searched_24_uids' => 1, 
        'original_7_uids' => 1, 
        'my_searched_uids' => 1, 
        'interest_uids' => 1, 
    );

    /**
     * 根据用户ID获取用户信息
     * 
     * @param int $uid 
     * @return Do_User
     */
    public static function getUserInfo($uid)
    {
        $userInfo = self::getUserInfos(array($uid));
        $userInfo = isset($userInfo[$uid]) ? $userInfo[$uid] : array();

        if (empty($userInfo))
        {
            throw new Comm_Exception_Program('20003: user '. $uid . ' dose not exist');
        }

        return new Do_User($userInfo, Do_Abstract::MODE_OUTPUT);
    }
    
    /**
     * 批量获取用户信息，不返回Do_User对象
     * 
     * @param array $uids
     * @param bool $realCount
     * @return array
     */
    public static function getUserInfos(array $uids, $realCount = true)
    {
        if (empty($uids))
        {
            throw new Dr_Exception('uids does not empty');
        }

        $keys = array();
        $liteKeys = array(); //简版用户信息
        $userInfos = array();
        $cacheUser = new Cache_User();

        //从缓存获取用户信息
        $result = $cacheUser->getUserInfos($uids, $keys, $liteKeys);

        //筛选出未命中的uid
        $queryUids = self::filterCachedItems($result, $keys, $userInfos);
        if (empty($queryUids))
        {
            return $realCount ? self::combineUserCounts($uids, $userInfos) : $userInfos;
        }

        //批量取用户信息接口每次只支持返回20条，所以需要批量来取
        $chunkUids = array_chunk($queryUids, 20);
        $multi = array();
        $setRongzai = false;

        foreach ($chunkUids as $uids)
        {
            $api = Comm_Weibo_Api_Users::showBatch();
            
            if (!empty($uids))
            {
                $api->setValue('uids', join(',', $uids));
            }
            $api->setValue('trim_status', 1);
            $api->setValue('has_extend', 1);
            $api->setValue('simplify', '8,5');

            if (Tool_Misc::isUseUnloginAuth())
            {
                $api->addUserPassword(); //指定用户名、密码方式访问
            }

            $rst = $api->getResult();
            $users = isset($rst['users']) ? $rst['users'] : array();
            foreach ($users as $user)
            {
                $multi[$keys[$user['id']]] = $user;
                $multi[$liteKeys[$user['id']]] = self::formatLiteUserInfo($user); //种一下简版用户信息缓存
                $userInfos[$user['id']] = $user;
                $userInfos[$user['id']]['description'] = htmlspecialchars($userInfos[$user['id']]['description']);
            }

            if (!$setRongzai && isset($rst['rongzai']))
            {
                $setRongzai = true;
            }
        }

        //设定缓存
        $rzLiveTime = $setRongzai ? self::MC_USER_RZ_LIVETIME : 0;
        $cacheUser->createUserInfos($multi, $rzLiveTime);
        if ($realCount)
        {
            $diffUids = array_diff($uids, $queryUids);
            
            if (!empty($diffUids))
            {
                return self::combineUserCounts($diffUids, $userInfos);
            }
        }

        return $userInfos;
    }

    /**
     * 格式化用户信息至精简版
     * 
     * @param array $userInfo 用户对象为openapi返回的标准格式
     * @see http://wiki.intra.weibo.com/1/user
     * @return array
     */
    public static function formatLiteUserInfo(array $userInfo)
    {
        $data = array();
        $data['id'] = $userInfo['id'];
        $data['screen_name'] = ($userInfo['screen_name'] != '' ? $userInfo['screen_name'] : $userInfo['id']);
        $data['name'] = $userInfo['name'] != '' ? $userInfo['name'] : $userInfo['id'];
        $data['profile_image_url'] = $userInfo['profile_image_url'];
        $data['domain'] = $userInfo['domain'] != '' ? $userInfo['domain'] : $userInfo['id'];
        $data['gender'] = $userInfo['gender'];
        $data['verified'] = $userInfo['verified'];
        $data['verified_type'] = $userInfo['verified_type'];
        $data['level'] = isset($userInfo['level']) ? $userInfo['level'] : 0;
        $data['badge'] = isset($userInfo['badge']) ? $userInfo['badge'] : null;
        $data['type'] = isset($userInfo['type']) ? $userInfo['type'] : null;
        
        return $data;
    }
    
    /**
     * 批量获取用户的关注数、转发数、微博数
     * 
     * @param array $uids
     * @return array
     */
    public static function getUserCounts(array $uids)
    {
        $userCounts = array();

        try
        {
            $uids = array_unique($uids);
            if (empty($uids))
            {
                return $userCounts;
            }

            $apiUser = Comm_Weibo_Api_Users::counts();
            $apiUser->uids = join(',', $uids);
            if (Tool_Misc::isUseUnloginAuth())
            {
                $apiUser->addUserPassword(); //指定用户名、密码方式访问
            }

            $rst = $apiUser->getResult();
            foreach ($rst as $user)
            {
                $userCounts[$user['id']]['followers_count'] = $user['followers_count'];
                $userCounts[$user['id']]['friends_count'] = $user['friends_count'];
                $userCounts[$user['id']]['statuses_count'] = $user['statuses_count'];
                $userCounts[$user['id']]['private_friends_count'] = $user['private_friends_count'];
            }
        }
        catch (Comm_Exception_Program $e)
        {
            //接口出现异常，直接显示用户信息里的旧数
        }

        return $userCounts;
    }
    
    /**
     * 合并用户粉丝数、评论数和微博数，接口获取失败，返回缓存旧数据
     * 
     * @param array $uids
     * @param array $userInfos
     * @return array
     */
    public static function combineUserCounts(array $uids, array $userInfos)
    {
        $userCounts = self::getUserCounts($uids);
        
        foreach ($userInfos as $key => $userInfo)
        {
            $uid = $userInfo['id'];
            if (!isset($userCounts[$uid]))
            {
                continue;
            }

            $userInfos[$key]['followers_count'] = $userCounts[$uid]['followers_count'];
            $userInfos[$key]['friends_count'] = $userCounts[$uid]['friends_count'];
            $userInfos[$key]['statuses_count'] = $userCounts[$uid]['statuses_count'];
            $userInfos[$key]['private_friends_count'] = $userCounts[$uid]['private_friends_count'];            
        }
        
        return $userInfos;
    }

    /**
     * 获得用户的版本号（不区分V3.6和V4），只用于V3用户的校验
     *
     * @return string 用户的版本号
     */
    public static function getUserVersion()
    {
        //由于5之前的旧版本全都下线，所以只返回5
        return '5';
    }

    /**
     * 根据uid获得用户的版本号（不区分V3.6和V4），只用于V3用户的校验
     *
     * @return string 用户的版本号
     */
    public static function getUserVersionLogin($uid = null)
    {
        /*
         * 同上，只返回5
         */
        return '5';
    }
    
    /**
     * 获取用户的类型
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public static function getUserType($uid)
    {
        if (empty($uid))
        {
            return;
        }

        $api = Comm_Weibo_Api_Users::getUserType();
        $api->setValue('uid', $uid);
        return $api->getResult();
    }

    /**
     * 判断$user->id是人还是物
     * @param int uid
     * @return 人: true 物: false
     */
    public static function checkUserPtype($uid)
    {
        return strlen($uid) < 16;
    }

    /**
     * 根据uid或者screen_name获取用户信息(直接从接口取)
     * 
     * @param int $uid
     * @param string $screenName
     * @return mixed
     */
    public static function getUserInfoNoCache($uid = null, $screenName = null)
    {
        $apiUser = Comm_Weibo_Api_Users::show();
        $apiUser->setValue('uid', $uid);
        $apiUser->setValue('screen_name', $screenName); 
        $apiUser->setValue('has_extend', 1); 
        $rst = $apiUser->getResult();

        if (isset($rst['status']))
        {
            $rst['status_id'] = $rst['status']['id'];
            unset($rst['status']);
        }

        return $rst;
    }

    /**
     * 根据昵称获取用户信息
     * 
     * @param string $screenName
     * @return Do_User
     */
    public static function getUserInfoByScreenName($screenName)
    {
        Comm_ArgChecker::string($screenName, 'widthMin,1;widthMax,20;re,/^[\x{4e00}-\x{9fa5}a-zA-Z0-9_-]+$/u');
        
        if (empty($screenName))
        {
            throw new Comm_Exception_Program('screen_name does not empty');
        }
        
        $cacheUser = new Cache_User('MIDCV4USER');
        $uid = $cacheUser->screenNameToUid($screenName);
        if (!empty($uid))
        {
            return self::getUserInfo($uid);
        }
        
        $userInfo = self::getUserInfoNoCache(null, $screenName);
        if (isset($userInfo['status']))
        {
            $userInfo['status_id'] = $userInfo['status']['id'];
            unset($userInfo['status']);
        }

        if (!isset($userInfo['rongzai']))
        {
            $cacheUser->createScreenNameToUid($screenName, $userInfo['id']);
        }
        else
        {
            $cacheUser->createScreenNameToUid($screenName, $userInfo['id'], self::MC_USER_RZ_LIVETIME);
        }

        return new Do_User($userInfo, Do_Abstract::MODE_OUTPUT);
    }

    public static function getUrlVisiable($uid)
    {
        //TODO
        $basicInfo = Dr_Account::getProfileBasic($uid);
    }

    /**
     * 根据域名获取用户信息
     * 
     * @param string $domain
     * @return Do_User
     */
    public static function getUserInfoByDomain($domain)
    {
        if (empty($domain))
        {
            throw new Dr_Exception('domain does not empty');
        }

        $cacheUser = new Cache_User('MIDCV4USER');
        $uid = $cacheUser->domainToUid($domain);
        if (!empty($uid))
        {
            return self::getUserInfo($uid);
        }
        
        $api = Comm_Weibo_Api_Users::domainShow();
        $api->domain = $domain;
        $api->setValue('has_extend', 1); 

        if (Tool_Misc::isUseUnloginAuth())
        {
            $api->addUserPassword(); //指定用户名、密码方式访问
        }

        $userInfo = $api->getResult();
        if (isset($userInfo['status']))
        {
            $userInfo['status_id'] = $userInfo['status']['id'];
            unset($userInfo['status']);
        }

        if (!isset($userInfo['rongzai']))
        {
            $cacheUser->createDomainToUid($domain, $userInfo['id']);
        }
        else
        {
            $cacheUser->createDomainToUid($domain, $userInfo['id'], self::MC_USER_RZ_LIVETIME);
        }

        return new Do_User($userInfo, Do_Abstract::MODE_OUTPUT);
    }

    /**
     * 密友推荐
     *
     * @param  $uid  务必为当前登录者uid，否则过滤黑名单时会出错
     * @return unknown
     */
    public static function getMayCloseFriend($uid)
    {
        $resMc = array();
        try
        {
            $api = Comm_Weibo_Api_Proxy_Suggestions::usersCloseFriends();
            $api->setValue('uid', $uid);
            $res_api = $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            $res_api = false;
        }

        if ($res_api !== false)
        {
            $resMc = $res_api;
        }
        else
        {
            $resMc = array();
        }

        //提取uid
        if (!isset($resMc['result']))
        {
            return array();
        }

        $uids = array();
        foreach ($resMc['result'] as $value)
        {
            $uids[$value['id']] = $value['id'];
        }

        //过滤我没有关注的(接口有延迟)
        $relation = Dr_Relation::checkRelationBatch($uids, $uid);

        foreach ($uids as $key => $value)
        {
            $find = false;

            foreach ($relation as $v)
            {
                if ($value == $v['id'])
                {
                    $find = true;
                    break;
                }
            }

            if (!$find)
            {
                unset($uids[$key]);
            }
        }
        
        //过滤我已经添加过的密友
        try
        {
            //TODO
            //$closeFriendUids = Dr_Relation::getCloseFriendsIds(140, 1);
            foreach ($closeFriendUids['ids'] as $uid)
            {
                if (isset($uids[$uid]))
                {
                    unset($uids[$uid]);
                }
            }
        }
        catch (Comm_Exception_Program $e)
        {
        
        }

        //获取用户的备注名
        $remarkName = Dr_Relation::friendsRemarkBatch($uids);
        
        //提取用户信息，并过滤封杀、冻结用户
        try
        {
            $userList = Dr_User::getUserInfos($uids);
        } 
        catch (Comm_Exception_Program $e)
        {
            $userList = array();
        }

        foreach ($userList as $key => $userInfo)
        {
            if (empty($userInfo['screen_name']) || $userInfo['type'] == Do_User::STATE_BLOCK || $userInfo['type'] == Do_User::STATE_FREEZE)
            {
                unset($userList[$key]);
                continue;
            }

            //推荐模块过滤蓝V用户
            if ($userInfo['verified'] && $userInfo['verified_type'] != 0)
            {
                unset($userList[$key]);
                continue;
            }
             
            //设置备注名
            $userList[$key]['remark'] = isset($remarkName[$key]) ? $remarkName[$key] : '';

            //设置截取后的备注名（密友推荐模块用,加起来最多14个英文字符）
            $screenNameLen = mb_strwidth($userInfo['screen_name'], 'UTF-8');
            if ($screenNameLen > 10)
            {
                $userList[$key]['remark_cut'] = '';
            }
            else
            {
                $userList[$key]['remark_cut'] = Tool_Formatter_String::substrCn($userList[$key]['remark'], 14 - $screenNameLen);
            }        
        }

        //按照推荐返回的uids顺序对user_list重新排序(因为getUserInfos的返回可能会打乱输入的uids顺序)
        $result = array();
        if (is_array($uids) && !empty($uids) && is_array($userList) && !empty($userList))
        {
            foreach ($uids as $uid)
            {
                if (isset($userList[$uid]))
                {
                    $result[$uid] = $userList[$uid];
                }
            }
        }

        return $result;
    }

    /**
     * 可能感兴趣的人
     * 
     * @see http://doc.api.weibo.com/index.php/1/suggestions/users/may_interested
     * @param unknown_type $page
     * @param unknown_type $count
     * @return unknown|multitype:
     */
    public static function getMayInterested($page = 1, $count = 30)
    {
        $userId = Comm_Context::get('viewer')->id;
        $cacheUser = new Cache_User();
        $data = $cacheUser->getMayInterested($userId, $page, $count);
        if ($data)
        {
            return $data;
        }

        $api = Comm_Weibo_Api_Suggestions_Users::mayInterested();
        $api->setValue('uid', $userId);
        $api->setValue('num', $count);
        $resNew = $api->getResult();
        
        foreach ($resNew['data'] as $k => $v)
        {
            $format['uid'] = $k;
            if (!empty($v['following_bridge']))
            {
                $format['reason']['h']['uid'] = $v['following_bridge'];
                $format['reason']['h']['n'] = count($v['following_bridge']);
            }
            elseif (!empty($v['friend_bridge']))
            {
                $format['reason']['f']['n'] = count($v['friend_bridge']);
                $format['reason']['f']['uid'] = $v['friend_bridge'];
            }

            if (empty($v['friend_bridge']) && empty($v['following_bridge']))
            {
                continue;
            }

            $result[] = $format;
            unset($format);
        }

        shuffle($result);

        //提取uids列表
        $uids = array();

        foreach ($result as $value)
        {
            $uids[] = $value['uid'];
            if (isset($value['reason']['f']['uid']))
            {
                $value['reason']['f']['uid'] = array_slice($value['reason']['f']['uid'], 0, 3);
                $uids = array_merge($uids, $value['reason']['f']['uid']);
            }
            
            if (isset($value['reason']['h']['uid']))
            {
                $value['reason']['h']['uid'] = array_slice($value['reason']['h']['uid'], 0, 3);
                $uids = array_merge($uids, $value['reason']['h']['uid']);
            }
        }

        //批量获取推荐用户信息
        $userInfos = array();
        //过滤不感兴趣的uid
        $uids = self::uidsFilter($uids);
        $uids = array_unique($uids);
        if (!empty($uids))
        {
            $userInfos = Dr_User::getLiteUserInfos($uids);
        }

        //TODO
        $data = array();
        $education = Dr_School::getSchools($userId);
        foreach ($education as $v)
        {
            $school[$v['school_id']] = array('name' => $v['school'], 'type' => $v['type']);
        }

        foreach ($result as $value)
        {
            if (!isset($userInfos[$value['uid']]))
            {
                continue;
            }

            $data[$value['uid']]['info'] = $userInfos[$value['uid']];
            $keys = array_keys($value['reason']);

            switch ($keys[0])
            {
                case 'c':
                    $value['reason']['c']['n'] = 0;
                    if (!empty($value['reason']['h']['uid']))
                    {
                        foreach ($value['reason']['h']['uid'] as $uid)
                        {
                            if (!is_array($value['reason']['c']))
                            {
                                $value['reason']['c'] = array();
                            }

                            if (isset($userInfos[$uid]) && is_array($userInfos[$uid]))
                            {
                                $value['reason']['c']['users'][] = new Do_User($userInfos[$uid]);
                            }
                        }

                        $value['reason']['c']['n'] = $value['reason']['h']['n'];
                        unset($value['reason']['h']);
                    }

                    $data[$value['uid']]['reason'] = $value['reason'];
                    break;

                case 's':
                    if (empty($value['reason']['h']['uid']) && empty($education))
                    {
                        unset($data[$value['uid']]);
                        break;
                    }

                    $value['reason']['s']['n'] = 0;
                    if (!empty($value['reason']['h']['uid']))
                    {
                        foreach ($value['reason']['h']['uid'] as $uid)
                        {
                            if (!is_array($value['reason']['s']))
                            {
                                $value['reason']['s'] = array();
                            }
                            if (isset($userInfos[$uid]) && is_array($userInfos[$uid]))
                            {
                                $value['reason']['s']['users'][] = new Do_User($userInfos[$uid]);
                            }
                        }

                        $value['reason']['s']['n'] = $value['reason']['h']['n'];
                        unset($value['reason']['h']);
                    }

                    if (!empty($education) && isset($value['reason']['s']['id']))
                    {
                        $value['reason']['s']['name'] = isset($school[$value['reason']['s']['id']]['name']) ? $school[$value['reason']['s']['id']]['name'] : '';
                        $value['reason']['s']['type'] = isset($school[$value['reason']['s']['id']]['type']) ? $school[$value['reason']['s']['id']]['type'] : '';
                    }

                    $data[$value['uid']]['reason'] = $value['reason'];
                    break;

                case 'f':
                    if (isset($value['reason']['f']['uid']))
                    {
                        foreach ($value['reason']['f']['uid'] as $uid)
                        {
                            if (isset($userInfos[$uid]))
                            {
                                $value['reason']['f']['users'][] = new Do_User($userInfos[$uid]);
                            }
                        }

                        unset($value['reason']['f']['uid']);
                        $data[$value['uid']]['reason'] = $value['reason'];
                    }

                    break;

                case 'h':
                    if (isset($value['reason']['h']['uid']))
                    {
                        foreach ($value['reason']['h']['uid'] as $uid)
                        {
                            if (isset($userInfos[$uid]))
                            {
                                $value['reason']['h']['users'][] = new Do_User($userInfos[$uid]);
                            }
                        }

                        unset($value['reason']['h']['uid']);
                        $data[$value['uid']]['reason'] = $value['reason'];
                    }

                    break;

                default:
                    $data[$value['uid']]['reason'][$keys[0]] = $value['reason'][$keys[0]];
                    break;
            }
        }

        $cacheUser->createMayInterested($userId, $page, $count, $data);
        return $data;
    }

    /**
     * 
     * 取单条微博页的相关用户
     * @param string $content
     * @param int $num
     * @param string $url
     */
    public static function usersByStatus($content, $num = 10, $url = "")
    {
        try
        {
            $commApi = Comm_Weibo_Api_Suggestions_Users::usersByStatus();
            $commApi->setValue('content', $content);
            $commApi->setValue('num', $num);

            if ($url)
            {
                $commApi->setValue('url', $url);
            }
            return $commApi->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array('users' => array(), 'total_number' => 0);
        }
    }

    /**
     * 获取推荐我关注的用户
     * @param int $uid
     * @param int $page
     * @param int $count
     * @param int $cuid 当前登录用户
     */
    public static function usersWorthFollow($uid, $page = 1, $count = 20, $cuid = '', $realCount = true)
    {
        $doUsers = array();
        try
        {
            $commApi = Comm_Weibo_Api_Suggestions_Users::worthFollow();
            $commApi->setValue('uid', $uid);
            $commApi->setValue('page', $page);
            $commApi->setValue('count', $count);
            $result = $commApi->getResult();

            if (is_array($result) && count($result) > 0)
            {
                $uids = array();
                $viewerId = ($cuid != '') ? $cuid : Comm_Context::get('viewer', '')->id;

                foreach ($result as $k => $user)
                {
                    if ($viewerId != $user['id'])
                    {
                        $uids[] = $user['id'];
                    }
                }

                $doUsers = self::getUserInfos($uids, $realCount);
                foreach ($doUsers as $key => $val)
                { 
                    if (isset($val['description']))
                    {
                        $val['description'] = htmlspecialchars($val['description']);
                    }

                    $doUsers[$key] = $val;
                }
            }

            return $doUsers;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 根据V用户UID返回用户的客服代表信息
     * @param int $uid
     */
    public static function csrGetCsrByUid($uid)
    {
        $cacheUser = new Cache_User();
        $data = $cacheUser->getCsrByUid($uid);
        if ($data)
        {
            return $result;
        }

        try
        {
            $commApi = Comm_Weibo_Api_Admin::csrGetCsrByUid();
            $commApi->setValue('uid', $uid);
            $result = $commApi->getResult();
            $cacheUser->createCsrByUid($uid, $result);
            return $result;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 人气用户推荐
     * 
     * @see http://doc.api.weibo.com/index.php/1/suggestions/users/hot
     * @param unknown_type $page
     * @param unknown_type $count
     * @return unknown|multitype:
     */
    public static function getUsersHot($attenNum, $regionNum, $v1Num, $searched24, $original7, $mySearched, $interestNum, $manualPushId, $manualNum, $manualIsMix = 1, $isInfoPage = false)
    {
        $userId = Comm_Context::get('viewer')->id;

        $cacheUser = new Cache_User();
        $data = $cacheUser->getUsersHot($userId);
        if ($data && count($data) >= 6)
        {
            return $data;
        }

        try
        {
            $api = Comm_Weibo_Api_Suggestions_Users::hot();
            $api->atten_num = $attenNum;
            $api->region_num = $regionNum;
            $api->v1_num = $v1Num;
            $api->searched_24 = $searched24;
            $api->original_7 = $original7;
            $api->my_searched = $mySearched;
            $api->interest_num = $interestNum;
            $api->manual_push_id = $manualPushId;
            $api->manual_num = $manualNum;
            $api->manual_is_mix = $manualIsMix;
            $result = $api->getResult();

            //提取uids列表
            $uids = array();
            foreach ($result as $k => $v)
            {
                if ($k == 'atten_uids')
                {
                    if (count($result[$k]) > 0)
                    {
                        foreach ($v as $key => $item)
                        {
                            $uids[] = $key;
                            foreach ($item as $uid)
                            {
                                $uids[] = $uid;
                            }
                        }
                    }
                }

                if ($k == 'original_7_uids')
                {
                    if (count($result[$k]) > 0)
                    {
                        foreach ($v as $key => $item)
                        {
                            $uids[] = $key;
                        }
                    }
                }

                if ($k == 'manual_uids')
                {
                    if (count($result[$k]) > 0)
                    {
                        foreach ($v as $key => $item)
                        {
                            $uids[] = $item['uid'];
                        }
                    }
                }

                if ($k == 'manual2_uids')
                {
                    if (isset($v['recomlist']) && count($v['recomlist']) > 0)
                    {
                        foreach ($v['recomlist'] as $sk => $sv)
                        {
                            $uids[] = $sv['uid'];
                        }
                    }
                }

                if (isset(self::$_userHotTypes[$k]))
                {
                    if (count($result[$k]) > 0)
                    {
                        foreach ($v as $uid)
                        {
                            $uids[] = $uid;
                        }
                    }
                }
            }
            
            //过滤不感兴趣的uid
            $uids = self::uidsFilter($uids);

            //批量获取推荐用户信息
            $userInfos = array();
            $uids = array_unique($uids);
            if (!empty($uids))
            {
                $userInfos = Dr_User::getUserInfos($uids, false);
            }

            $data = array();
            foreach ($result as $k => $item)
            {
                if ($k == 'atten_uids')
                {
                    foreach ($item as $key => $v)
                    {
                        if (isset($userInfos[$key]))
                        {
                            $data[$key] = $userInfos[$key];
                            $reasonShortF = array_slice($v, 0, 1);
                            $reasonShortInfo = $userInfos[$reasonShortF[0]]['screen_name'];
                            $reasonLongF = array_slice($v, 0, 2);
                            if ($isInfoPage)
                            {
                                $reasonLongInfo = "<a suda-data='key=interest_info&value=reason:{$key}' href='/" . $userInfos[$reasonLongF[0]]['id'] . "'>" . $userInfos[$reasonLongF[0]]['screen_name'] . "</a>";
                            }
                            else
                            {
                                $reasonLongInfo = "<a suda-data='key=tblog_hotuser_v4&value=same_frieds_v4:0:{$key}' href='/" . $userInfos[$reasonLongF[0]]['id'] . "'>" . $userInfos[$reasonLongF[0]]['screen_name'] . "</a>";
                            }

                            if (isset($reasonLongF[1]['id']))
                            {
                                if ($isInfoPage)
                                {
                                    $reasonLongInfo .= "、" . "<a suda-data='key=interest_info&value=reason:{$key}' href='/" . $userInfos[$reasonLongF[1]]['id'] . "'>" . $userInfos[$reasonLongF[1]]['screen_name'] . "</a>";
                                }
                                else
                                {
                                    $reasonLongInfo .= "、" . "<a suda-data='key=tblog_hotuser_v4&value=same_frieds_v4:0:{$key}' href='/" . $userInfos[$reasonLongF[1]]['id'] . "'>" . $userInfos[$reasonLongF[1]]['screen_name'] . "</a>";
                                }
                            }

                            $data[$key]['reason_short'] = $reasonShortInfo;
                            $data[$key]['reason_short_show'] = $reasonShortInfo;
                            $data[$key]['reason_long'] = $reasonLongInfo;
                            $data[$key]['data_type'] = $k;
                        }
                    }
                }

                if ($k == 'original_7_uids')
                {
                    foreach ($item as $key => $v)
                    {
                        if (isset($userInfos[$key]))
                        {
                            $data[$key] = $userInfos[$key];
                            $data[$key]['reason_short'] = count($v);
                            $data[$key]['reason_long'] = $userInfos[$v]['verified_reason'];
                            $data[$key]['data_type'] = $k;
                        }
                    }
                }

                if ($k == 'manual_uids')
                {
                    foreach ($item as $key => $v)
                    {
                        if (isset($userInfos[$key]))
                        {
                            $data[$key] = $userInfos[$key];
                            $data[$key]['reason_short'] = $v['showreason'];

                            if ($v['sreasonlink'] != '')
                            {
                                if ($isInfoPage)
                                {
                                    $data[$key]['reason_long'] = "<a suda-data='key=interest_info&value=reason:{$key}' href='" . $v['sreasonlink'] . "' target='_blank'>" . $v['recreason'] . "</a>";
                                }
                                else
                                {
                                    $data[$key]['reason_long'] = "<a suda-data='key=tblog_hotuser_v4&value=same_frieds_v4:0:{$key}' href='" . $v['sreasonlink'] . "' target='_blank'>" . $v['recreason'] . "</a>";
                                }
                            }
                            else
                            {
                                $data[$key]['reason_long'] = $v['recreason'];
                            }

                            $data[$key]['data_type'] = $k;
                            $data[$key]['source'] = $v['source'];
                        }
                    }
                }

                if ($k == 'manual2_uids')
                {   
                    $manualData = $tmpData = array();

                    if (!empty($item['recomlist']) && $item['probability'] > 0)
                    {
                        $manualData['probability'] = $item['probability'];

                        foreach ($item['recomlist'] as $mkey => $mval)
                        {
                            $tmpData = $userInfos[$mval['uid']];
                            $tmpData['reason_short'] = $mval['short_reason'];
                            $tmpData['probability'] = isset($mval['probability']) ? $mval['probability'] : 0;
                            $tmpData['reason_long'] = $mval['long_reason'];
                            $tmpData['data_type'] = $k;
                            $tmpData['from'] = $mval['fr'];
                            $manualData['recomlist'][] = $tmpData;
                        }

                        $data['extra'] = $manualData;
                    }
                }

                if (isset(self::$_userHotTypes[$k]))
                {
                    foreach ($item as $key => $v)
                    {
                        if ($k == 'my_searched_uids')
                        {
                            if (isset($userInfos[$v]))
                            {
                                $data[$v] = $userInfos[$v];
                                $data[$v]['reason_long'] = $userInfos[$v]['verified_reason'];
                                $data[$v]['data_type'] = $k;

                                if (strtotime($userInfos[$v]['created_at']) + 2592000 < time())
                                {
                                    $data[$v]['reason_short'] = 'old';
                                }
                                else
                                {
                                    $data[$v]['reason_short'] = 'new';
                                }
                            }
                        }

                        if ($k == 'region_uids' || $k == 'interest_uids')
                        {
                            if (isset($userInfos[$v]))
                            {
                                $data[$v] = $userInfos[$v];
                                $data[$v]['reason_short'] = $data[$v]['reason_long'] = $userInfos[$v]['verified_reason'];
                                $data[$v]['data_type'] = $k;
                            }
                        }

                        if ($k == 'v1_uids')
                        {
                            if (isset($userInfos[$v]))
                            {
                                $data[$v] = $userInfos[$v];
                                $data[$v]['reason_short'] = 'new';
                                $data[$v]['reason_long'] = $userInfos[$v]['verified_reason'];
                                $data[$v]['data_type'] = $k;
                            }
                        }

                        if ($k == 'searched_24_uids')
                        {
                            if (isset($userInfos[$v]))
                            {
                                $data[$v] = $userInfos[$v];
                                $data[$v]['reason_short'] = 'searched_24';
                                $data[$v]['reason_long'] = $userInfos[$v]['verified_reason'];
                                $data[$v]['data_type'] = $k;
                            }
                        }

                        if ($k == 'original_7_uids')
                        {
                            if (isset($userInfos[$v]))
                            {
                                $data[$v] = $userInfos[$v];
                                $data[$v]['reason_short'] = '';
                                $data[$v]['reason_long'] = $userInfos[$v]['verified_reason'];
                                $data[$v]['data_type'] = $k;
                            }
                        }
                    }
                }
            }

            $cacheUser->createUsersHot($userId, $data);
            
            return $data;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 过滤uids里的不感兴趣uid 
     * @param array $uids
     */
    public static function uidsFilter($uids = array())
    {
        $userId = Comm_Context::get('viewer')->id;
        $cacheUser = new Cache_User();
        $data = $cacheUser->getUsersNotInterested($userId);
        
        if (false !== $data && count($data) >= 1)
        {
            foreach ($uids as $uid)
            {
                if (!in_array($uid, $data))
                {
                    $res[] = $uid;
                }
            }

            return $res;
        }

        return $uids;
    }

    /**
     * 冻结用户解冻,修改用户状态
     */
    public static function getUserActiveCode($s, $uid, $m)
    {
        return false;
        //TODO
        $api = Comm_Weibo_Api_Users::getUserActiveCode();
        $api->s = $s;
        $api->setValue('uid', $uid);
        $api->m = $m;

        try
        {
            return $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return false;
        }
    }

    /**
     * 获取新注册用户的引导的状态值
     * @param $uid
     */
    public function getNewUserGuideStatus($uid)
    {
        $cacheUser = new Cache_User();
        $newuserStatus = $cacheUser->getNewUserGuideStatus($uid);
        
        if ($newuserStatus === false || empty($newuserStatus))
        {
            $newuserStatus = 1;
        }

        return $newuserStatus;
    }

    /**
     * 获取新注册用户可能感兴趣的人
     * @param  $count 获取的数量
     */
    public function getNewUserInterested($count = 50)
    {
        $userId = Comm_Context::get('viewer')->id;
        $cacheUser = new Cache_User();
        $data = $cacheUser->getNewUserInterested($uid);

        if ($data)
        {
            return $data;
        }

        $attesinUid = Dr_Relation::friendsIds($userId, $cur = -1, $count);
        $attesinUid = is_array($attesinUid['ids']) ? $attesinUid['ids'] : array();
        $attesinUid = implode(',', $attesinUid);
        if (empty($attesinUid))
        {
            $attesinUid = $userId;
        }

        $api = Comm_Weibo_Api_Suggestions_Users::newUserMayInterested();
        $api->count = $count;
        $api->uids = $attesinUid;
        $rst = $api->getResult();

        //取不到数据再重新获取
        if (count($rst) < 1)
        {
            $api = Comm_Weibo_Api_Suggestions_Users::newUserMayInterested();
            $api->count = $count;
            $api->uids = $attesinUid;
            $rst = $api->getResult();
        }

        $result = $re = array();
        $result = is_array($rst) ? $rst : array();
        shuffle($result);
        foreach ($result as $k => $v)
        {
            $re[$k]['id'] = $v['id'];
            $re[$k]['screen_name'] = $v['screen_name'];
            $re[$k]['profile_image_url'] = $v['profile_image_url'];
            if ($v['verified'])
            {
                $re[$k]['verified'] = TRUE;
                $re[$k]['location'] = $v['verified_reason'];
            }
            else
            {
                $re[$k]['location'] = $v['description'];
            }
        }

        unset($result, $rst);
        $cacheUser->createNewUserInterested($uid, $re);
        return $re;
    }

    /**
     * 判断是否为屏蔽用户
     */
    public static function isBlockUser($uids)
    {
        if (is_array($uids))
        {
            $uids = implode(',', $uids);
        }

        try
        {
            $api = Comm_Weibo_Api_Users::isBlockUser();
            $api->uids = $uids;
            return $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 获取草根人气用户（最多返回100个id）
     * @param unknown_type $class1
     * @param unknown_type $class2
     * @param unknown_type $num
     */
    public static function getGrassUser($class1 = 6, $class2 = 1)
    {
        $cache = new Cache_Main();
        $grassUid = $cache->getGrassUser($class1, $class2);
        if (!empty($grassUid))
        {
            return $grassUid;
        }

        $pl = Comm_Weibo_Api_Admin::getGrassUser();
        $pl->class1 = $class1;
        $pl->class2 = $class2;
        $pl->num = 100;
        $result = $pl->getResult();
        $uids = $result['result'];
        $cache->createGrassUser($uids, $class1, $class2);

        return $uids;
    }

    /**
     * 人工推荐用户
     * 10101新用户注册人气关注,10701 最新加入
     * @param unknown_type $type
     * @param unknown_type $count
     * @param unknown_type $isMixed
     */
    public static function getManualRecommend($type = 10101, $isMixed = 1)
    {
        $cache = new Cache_Main();
        $uids = $cache->getManualRecommendUser($type, $isMixed);

        if (!empty($uids))
        {
            return $uids;
        }

        $pl = Comm_Weibo_Api_Suggestions::userManualRecommend();
        $pl->type = $type;
        $pl->count = 100;
        $pl->setValue('is_mixed', $isMixed);
        $users = $pl->getResult();

        $uids = array_map(create_function('$a', 'return $a["id"];'), $users['recommend_users']);
        $cache->createManualRecommendUser($uids, $type, $isMixed);
        return $uids;
    }

    /**
     * 调用接口批量获取用户信息
     * @param array $uids
     * @param int $hasExtend
     * @param int $trimStatus
     * @throws Dr_Exception
     */
    public static function getUserInfoBatchApi($uids, $hasExtend = 0, $trimStatus = 0)
    {
        $userInfos = $users = $rtnList = array();
        if (is_array($uids) && count($uids))
        {
            $uidsSplit = array_chunk($uids, 20);
            foreach ($uidsSplit as $key => $uidsChunk)
            {
                $request = Comm_Weibo_Api_Users::showBatch();
                $request->uids = implode(',', $uidsChunk);
                $request->has_extend = $hasExtend;
                $request->trim_status = $trimStatus;
                $rst = $request->getResult();
                $users = isset($rst['users']) ? $rst['users']: array();
                foreach ($users as $user)
                {
                    $rtnList['users'][] = $user;
                }
            }

            //DO 数据校验
            if (isset($rtnList['users']))
            {
                foreach ($rtnList['users'] as $k => $userInfo)
                {
                    $temp_data = new Do_User($userInfo, Do_User::MODE_OUTPUT);
                    $userInfos['users'][$k] = $temp_data->to_array();
                }
                unset($rtnList);
            }
        }
        else
        {
            throw new Dr_Exception('paramter uids error:' . $uids);
        }
       
        return $userInfos;
    }

    /**
     * 获取联系搜索用户
     * @param unknown_type $key
     * @param unknown_type $count
     */
    public static function getLenovoUser($key, $count = 10, $sid = 't_find')
    {
        $request = Comm_Weibo_Api_Search::suggestionsUsers();
        $request->count = $count;
        $request->q = $key;
        $request->sid = $sid;
        return $request->getResult();
    }

    /**
     * 获取屏蔽的用户列表
     * @param int $page 页码
     * @param int $count 每页显示数 最大200
     * @param int $trimStatus 是否显示完整微博信息
     */
    public static function getFilteredUsers($page, $count = 50, $trimStatus = 1)
    {
        if ($page <= 0 || $count > 200)
        {
            return array();
        }

        try
        {
            $api = Comm_Weibo_Api_Users::getFilteredUsers();
            $api->page = $page;
            $api->count = $count;
            $api->trim_status = $trimStatus;
            $rst = $api->getResult();
            
            if (!isset($rst['filter_list']) || !is_array($rst['filter_list']))
            {
                return array();
            }

            return $rst['filter_list'];
        } 
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 判断是否需要显示新用户引导气泡
     * @param unknown_type $viewer
     */
    public static function showUserGuiderBubble($viewer)
    {
        $userGuider = Dr_UserExtend::getExtend($viewer->id, Dr_Userextend::TYPE_BUBBLE_ID);
        $regTime = strtotime($viewer->created_at); //注册时间
        $minTime = strtotime("2011-10-18");
        $maxTime = 30 * 24 * 60 * 60;
        $hadReg = time() - $regTime; //已经注册了多久
        if ($userGuider == 6 || $regTime < $minTime || $maxTime < $hadReg)
        {
            return false;
        }

        return true;
    }

    /**
     * 判断是否需要显示用户指南气泡 (规则为新注册用户，注册6周(43天)内显示, 气泡状态由气泡系统判断)
     * @param object     $viewer    用户对象
     * @return bool     显示返回true, 不显示返回false
     */
    public static function showUserHandbookBubble($viewer)
    {
        //TODO
        $bubbleId = Comm_Config::get("handbook.bubble_id");
        
        //判断气泡是否已经关闭
        $close_bubbles = Dr_Newbubble::get_user_close_bubble($viewer['id']);
        $inCloseBubble = in_array($bubbleId, $close_bubbles['all']);
        
        //判断用户状态是新手
        $time = 43 * 24 * 60 * 60;
        $hadReg = time() - strtotime($viewer->created_at); //已经注册了多久
        $showHandbookbubble = ($hadReg <= $time) ? true : false;
        
        return ($inCloseBubble == false && $showHandbookbubble == true);
    }

    /**
     * 获取用户微博等级
     * @param int64 $uid
     */
    public static function getUserRank($uid)
    {
        $cacheUser = new Cache_User();
        $userRank = $cacheUser->getUserRank($uid);
        if ($userRank === false)
        {
            $request = Comm_Weibo_Api_Users::showRank();
            $request->setValue('uid', $uid);
            $rst = $request->getResult();
            
            //数据校验
            $doUserRank = new Do_UserRank($rst);
            $userRank = $doUserRank['rank'];
            unset($rst);

            //缓存
            $cacheUser->createUserRank($uid, $userRank);
            unset($cacheUser);
        }
        
        return $userRank;
    }

    /**
     * 获取用户等级详情
     * @param int64 $uid
     */
    public static function getUserRankDetail($uid)
    {
        $cacheUser = new Cache_User();
        $rankDetail = $cacheUser->getUserRankDetail($uid);
        if ($rankDetail === false)
        {
            $request = Comm_Weibo_Api_Users::showRankDetail();
            $request->setValue('uid', $uid);
            $rst = $request->getResult();
            
            //数据校验
            $doUserRank = new Do_UserRank($rst);
            $rankDetail = $doUserRank->toArray();
            unset($rst);

            //缓存
            $cacheUser->createUserRankDetail($uid, $rankDetail);
            unset($cacheUser);
        }
        
        return $rankDetail;
    }

    /**
     * 获取等级范围信息
     * @param int $rank
     */
    public static function getRankRange($rank)
    {
        $rankRange = Comm_Context::get('user_rank_range', false);
        if ($rankRange === false)
        {
            $rankConfig = Comm_Config::get('userrank.rank_range');
            foreach ($rankConfig as $range)
            {
                if ($rank >= $range['min'] && $rank <= $range['max'])
                {
                    $rankRange = $range;
                    break;
                }
            }
            
            Comm_Context::set('user_rank_range', $rankRange);
        }
        
        return $rankRange;
    }

    /**
     * 检查用户登录用户是否是可信用户 （创建公开分组，编辑公开分组  关注别人创建的公开分组）
     * 检查条件 v用户 达人用户 绑定手机用户
     *
     */
    public static function isTrustedUser()
    {
        $viewer = Comm_Context::get("viewer");
        if ($viewer->verified == true || $viewer->verified_type == 220)
        {
            return true;
        }

        $moblie = Dr_Mobile::getMobile();
        return $moblie['binding'] == 'true' || $moblie['binding'] === true;
    }

    /**
     * 检查用户登录用户是否是内网用户
     * 返回值 true/false
     */
    public static function isInternalUser()
    {
        try
        {
            $ssoInfo = Comm_Weibo_SinaSSO::getUserInfo();
            $uid = isset($ssoInfo['uid']) ? $ssoInfo['uid'] : 0;
        }
        catch (Comm_Weibo_Exception_SinaSSO $e)
        {
            $uid = 0;
        }
        catch (Comm_Exception_Program $e)
        {
            $uid = 0;
        }

        try
        {
            $end = substr($uid, -1);
            $whitelist = Comm_Config::get("uid_$end.public_group");
        }
        catch (Comm_Exception_Program $e)
        {
            return false;
        }

        return in_array($uid, $whitelist);
    }

    /**
     * 快速找人
     * 
     * @param string $q            搜索的关键字。必须进行URLencode，utf-8编码。 
     * @param string $nick        搜索的昵称，只搜索昵称，必须进行URLencode，utf-8编码。 
     * @param int    $qq            搜索的QQ号，对外部接口不公布该参数
     * @param string $tags        搜索的标签，只搜索标签，参数q为空表示搜索这个标签的所有人，utf-8编码。
     * @param int $province        搜索的省份ID，参考省份城市编码表，参数q为空表示搜索这个省份的所有人。 
     * @param int $city            搜索的城市ID，参考省份城市编码表，参数q为空表示搜索这个城市的所有人。 
     * @param string $gender    搜索的性别，m：男、f：女。 
     * @param int $isv            是否搜索V用户，0：非V用户、1：V用户、2：个人V用户、3：机构V用户，默认为空，表示搜索全部用户。 
     * @param string $ip        搜索坐标附近的用户，参数如：218.24.89.100。 
     * @param int $sbirth        搜索的起始出生年份，比如：1980。
     * @param int $ebirth        搜索的结束出生年份，比如：2062。
     * @param string $comp        搜索的公司名称，必须进行URLencode，utf-8编码，参数q为空表示搜索这个公司的所有人。
     * @param string $scho        搜索的学校名称，必须进行URLencode，utf-8编码，参数q为空表示搜索这个学校的所有人。 
     * @param int $sort            排序方式，0：按粉丝数倒序、108：综合排序，默认为108。 
     * @param int $page            页码，默认为1。 
     * @param int $count        每页返回的数量，默认10，最大50。（默认返回10条） 
     */
    public static function getSearchUsers($q, $nick, $qq, $tags, $province, $city, $gender, $isv, $ip, $sbirth, $ebirth, $comp, $scho, $sort, $page, $count, $sid = 't_find', $single = '0', $sexual = '0', $blood = '0')
    {
        try
        {
            $result = array();
            $api = Comm_Weibo_Api_Search::users();
            $api->q = $q;
            $api->nick = $nick;
            $api->qq = $qq;
            $api->tags = $tags;
            $api->province = $province;
            $api->city = $city;
            $api->gender = $gender;
            $api->isv = $isv;
            $api->ip = $ip;
            $api->sbirth = $sbirth;
            $api->ebirth = $ebirth;
            $api->comp = $comp;
            $api->scho = $scho;
            $api->sort = $sort;
            $api->page = $page;
            $api->count = $count;
            $api->sid = $sid;
            $api->single = $single;
            $api->sexual = $sexual;
            $api->blood = $blood;
            return $api->getResult();
            return $result;
        } 
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 搜索标签时的即时搜索建议 
     * 
     * @param string $q        搜索的关键字，必须进行URL_encoding，UTF-8编码。 
     * @param int $count
     * @param unknown_type $sid
     */
    public static function getSuggestionsTags($q, $count, $sid = 't_find')
    {
        try
        {
            $result = array();
            $request = Comm_Weibo_Api_Search::suggestionsTags();
            $request->q = $q;
            $request->count = $count;
            $request->sid = $sid;
            return $request->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 获取根据来源推荐的用户列表
     * @param int $province 用户所在的省
     * @param int $category 推荐用户的分类
     */
    public static function getLocationUsers($province = 1000, $category = 100)
    {
        try
        {
            //TODO
            $cacheUser = new Cache_User();
            $users = $cacheUser->getLoginLocUsers();

            if (false === $users)
            {
                $api = Comm_Weibo_Api_Suggestions_Users::usersByLocation();
                $api->province = $province;
                $api->category = $category;
                $rst = $api->getResult();
                $users = $rst[0]['users'];
                $cacheUser->setLoginLocUsers($users);
            }

            return $users;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 获取未登录首页微博
     */
    public static function getLoginMblog($type = 20)
    {
        try
        {
            //TODO
            $cacheUser = new Cache_User();
            $mblog = $cacheUser->getLoginMblog();
            if (false === $mblog)
            {
                $db = Comm_Db::pool('top');
                $sql = "SELECT conf FROM `config` WHERE `type`=?";
                $res = $db->fetchAll($sql, array($type));
                $mblog = !empty($res[0]['conf']) ? $res[0]['conf'] : array();
                $cacheUser->setLoginMblog($mblog);
            }

            $mblog = explode(",", $mblog);
            return $mblog;
        } 
        catch (Comm_Exception_Program $e)
        {
            return array();
        }
    }

    /**
     * 获取人气榜
     */
    public static function getRanksHotlist($depart, $class)
    {
        try
        {
            //TODO
            $cacheUser = new Cache_User();
            $hotlist = $cacheUser->getLoginHotlist();
            if (false === $hotlist)
            {
                //TODO
                $api = Comm_Weibo_Api_Proxy_Ranks::hotlist();
                $api->depart = $depart;
                $api->class = $class;
                $rst = $api->getResult();
                $hotlist = $rst['list'];
                $cacheUser->setLoginHotlist($hotlist);
            }

            return $hotlist;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 获取影响力榜
     */
    public static function getInfluenceList($depart, $class, $type)
    {
        try
        {
            $api = Comm_Weibo_Api_Proxy_Ranks::influenceList();
            $api->depart = $depart;
            $api->class = $class;
            $api->type = $type;
            return $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }
    
    /**
     * 获取用户活跃频次标签 ,智能排序用的
     */
    public static function getActiveTag($uid)
    {
        try
        {
            $api = Comm_Weibo_Api_Users::activeTag();
            $api->setValue('uid', $uid);
            return $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return false;
        }
    }

    /**
     * 获取用户的等级信息
     * @param unknown_type $uid
     */
    public static function showRankDetail($uid)
    {
        try
        {
            $api = Comm_Weibo_Api_Proxy_Rank::showDetail();
            $api->setValue('uid', $uid);
            return $api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }

    /**
     * 根据手机号获取对应的用户信息
     * 
     * @param $mobilenum 手机号
     * @return array     手机号绑定与待绑定对应的用户信息
     */
    public static function getUserInfoByMobile($mobilenum)
    {
        //TODO
        try
        {
            $uid_result = Comm_Weibo_Api_Mps_Mobile::get_uid_by_phone_new($mobilenum);
        }
        catch (Exception $e)
        {
            $uid_result = array();
        }
        if (empty($uid_result) || !is_array($uid_result))
        {
            return array();
        }

        $uids = $uids_info = array();
        if (isset($uid_result['status']) && $uid_result['status'] == 'suc' && isset($uid_result['uid']))
        {
            $uids = $uid_result['uid'];
        }
        $uids_info = (count($uids) ? self::getUserInfos($uids) : array());
        return $uids_info;
    }

    /**
     * 手机号获取对应的格式化后的用户信息
     * 
     * @param string $mobile 手机号
     * @return array();
     */
    public static function getInfoByMobileformat($mobile)
    {
        //TODO
        $userinfo = $new_userinfo = $userInfoByMobile = array();
        try
        {
            $userinfo = self::getUserinfoByMobile($mobile);
        }
        catch (Exception $e)
        {
            $userinfo = array();
        }

        if (is_array($userinfo) && count($userinfo) == '1')
        {
            foreach ($userinfo as $k => $v)
            {
                $new_userinfo[] = $v;
            }

            //TODO
            $obj_formatter_userlist = new Tool_Formatter_UserList();
            $obj_formatter_userlist->append_remark_info = true;
            $obj_formatter_userlist->append_relation_info = true;
            $obj_formatter_userlist->append_status_info = true;
            $obj_formatter_userlist->append_relation_origin = Dr_Relation::RELATION_ORIGIN_FOLLOWING;
            $obj_formatter_userlist->appender_showdata_to_userlists($new_userinfo, Comm_Context::get("viewer")->id);
            $userInfoByMobile = $new_userinfo;
            unset($userinfo, $new_userinfo);
        }

        return $userInfoByMobile;
    }

    /**
     * 判断用户是否为要引导的老用户
     * 注册42天以上关注数小于120,互粉数小于25
     * @return boolean [description]
     */
    public static function isOldUser()
    {
        $viewer = Comm_Context::get('viewer');
        
        //加V用户
        if ($viewer->verified == true)
        {
            return false;
        }

        //TODO
        $uid = Comm_Context::get('viewer')->id;
        $loginDays = 0;
        $cacheUser = new Cache_User();
        $data = $cacheUser->getOldUser($uid);
        if ($data == false)
        {
            $bi_followers_count = $viewer->bi_followers_count; //互粉
            $friends_count = $viewer->friends_count; //关注数
            $reg_days = Tool_Misc::get_registerd_days($viewer);
            if ($reg_days == false || $reg_days < 31 || $friends_count > 120 || $bi_followers_count > 25)
            {
                return false;
            }
            elseif ($friends_count < 120 && $bi_followers_count < 25)
            {
                $data = self::get_user_active_tag($uid);
                //最初的状态,保存1个月
                $cacheUser->create_old_user($uid, $data);
            }
        }
        else
        {
            $active_tag = self::get_user_active_tag($uid);
            $loginDays = $active_tag['login_days']; //当前已登陆的天数,不能从最初缓存一个月的缓存中取
        }

        if (empty($data))
        {
            return false;
        }
        elseif ($data['frequency'] <= 3 && $data['login_days'] <= 10)
        {
            //中低频,登陆不超过10次
            $loginDays = $data['login_days'];
        }
        else
        {
            return false;
        }

        Comm_Context::set('login_days', $loginDays); //记录用户登陆天数
        return true;
    }

    /**
     * 判断是否显示用户tips广告轮播
     */
    public static function isGuideTips()
    {
        $viewer = Comm_Context::get('viewer', false);
        
        //判读用户是否关闭tips广告轮播   
        $key = 'tips_' . $viewer['id'];
        $cookie = Comm_Context::cookie($key);
        if ($cookie != null)
        {
            return false;
        }
        
        $regTime = strtotime($viewer['created_at']);
        $endTime = time() - 60 * 60 * 24 * 8;
        return $regTime >= $endTime;
    }

    /**
     * 获取用户活跃频次标签
     * http://wiki.intra.weibo.com/2/users/active_tag
     */
    public static function getUserActiveTag($uid)
    {
        //TODO
        $cacheUser = new Cache_User();
        $data = $cacheUser->getUserActiveTag($uid);
        if ($data == false)
        {
            try
            {
                //TODO
                $request = Comm_Weibo_Api_Users::activeTag();
                $request->setValue('uid', $uid);
                $active_tag = $request->getResult();
                $data = $active_tag['active_tag'];
                //缓存
                if ($data['frequency'] > 0)
                {
                    $cacheUser->createUserActiveTag($uid, $data);
                }

                return $data;
            }
            catch (Comm_Weibo_Exception_Api $e)
            {
                return array();
            }
        }
    }

    /**
     * 取老用户引导暂存用户的教育及职业信息
     * @param int $type 用户搜索的类型
     * @param int $uid     用户的uid
     */
    public static function getGuideData($uid, $type)
    {
        //TODO
        $cacheUser = new Cache_User();
        return $cacheUser->getGuideData($uid, $type);
    }

    /**
     * 存老用户引导暂存用户的教育及职业信息
     * @param int $type 用户搜索的类型
     * @param int $uid     用户的uid
     */
    public function setGuideData($uid, $type, $data)
    {
        $cacheUser = new Cache_User();
        return $cacheUser->createGuideData($uid, $type, $data);
    }

    /**
     * 
     * @param int $uid     用户的uid
     */
    public static function get_guide_jx($gender)
    {
        $cacheUser = new Cache_User();
        return $cacheUser->get_guide_jx($gender);
    }

    /**
     * 
     * @param int $uid     用户的uid
     */
    public function set_guide_jx($gender, $data)
    {
        $cacheUser = new Cache_User();
        $cacheUser->create_guide_jx($gender, $data);
        return;
    }

    /**
     *
     * @param int $uid     用户的uid
     */
    public static function get_guide_index($uid, $tab)
    {
        $cacheUser = new Cache_User();
        return $cacheUser->get_guide_index($uid, $tab);
    }

    /**
     *
     * @param int $uid     用户的uid
     */
    public function set_guide_index($uid, $tab, $data)
    {
        $cacheUser = new Cache_User();
        $cacheUser->create_guide_index($uid, $tab, $data);
        return;
    }

    public static function get_guide_event_conf()
    {
        return array('zid' => 12, 'eid' => 10002188, 'key' => 'sfesfessfsdeww');
    }

    /*
     * 检测用户是否身份验证
     * @param int $uid     用户的uid
     */
    public static function check_user_identified($uid)
    {
        try
        {
            return true;
            //return Comm_Weibo_Identify::is_identified($uid);
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return false;
        }
    }

    /**
     * 获取用户会员状态
     * 
     * @param  $uid  用户uid
     * @return array
     * {
     "uid": 1404376560,
     "type": 12,//0:非会员, 11:月付, 12:年付, 13:包月付, 2:过期会员
     "rank": 5  //取值范围是0-6。0：非会员；1-6:代表会员的六个等级
     }
     */
    public static function get_members_show($uid)
    {
        try {
            $api = Comm_Weibo_Api_Members::show();
            $api->setValue('uid', $uid);
            $data = $api->getResult();
        } catch (Exception $e)
    {
            $data = array();
        }
        return $data;
    }

    /**
     *
     * @param int $uid  用户的uid
     */
    public static function get_hotcommend_weibo($uid)
    {
        $cacheUser = new Cache_User();
        return $cacheUser->get_hotcommend_weibo($uid);
    }

    /**
     *
     * @param int $uid  用户的uid
     */
    public function set_hotcommend_weibo($uid, $data)
    {
        $cacheUser = new Cache_User();
        $cacheUser->create_hotcommend_weibo($uid, $data);
        return;
    }
    
    /**
     * 
     * 获取用户发给我的隐私设定接口
     * @author kelizhi <lizhi@staff.sina.com.cn>
     */
    public static function get_user_sendme()
    {
        try{
            $platform = Comm_Weibo_Api_Users::get_user_sendme();
            $res = $platform->getResult();
            return $res;
        }catch (Comm_Exception_Program $e){
            return array();
        }
    }

    /***
     * @static
    * add by chenggang3 2012-10-10
    * 判断用户是否为要引导的老用户 二期
    * 注册30天以上关注数小于150,互粉数小于45
    * 中低频,登陆不超过10次
    */
    public static function olduser_guide($uid)
    {
        try{
            $viewer = Dr_User::getUserInfo($uid);
            //加V用户
            if ($viewer->verified == true)
    {
                return false;
            }
            
            $uid = $viewer->id;
            $loginDays = 0;
            $bi_followers_count = $viewer->bi_followers_count; //互粉
            $friends_count = $viewer->friends_count; //关注数
            $reg_days = Tool_Misc::get_registerd_days($viewer);
            if ($reg_days == false || $reg_days < 31 || $friends_count > 150 || $bi_followers_count > 45)
    {
                return false;
            } elseif ($friends_count < 150 && $bi_followers_count < 45)
    {
                $active_tag = self::get_user_active_tag($uid);
                if (empty($active_tag))
    {
                    return false;
                } elseif ($active_tag['frequency'] <= 3 && $active_tag['login_days'] <= 10 && Dr_Ugrowth::get_olduser_guide($uid)<13)
    {
                    return true;
                } else {
                    return false;
                }
            }
            
            return false;
        }catch (Exception $e){
            return false;
        }
    }

    /**
     * 判断当前用户是否为新注册用户，40天内注册的用户都属于新用户
     * 注：建此函数主要为了首页分组引导取数据时使用
     * @param unknown_type $user
     * @author wujianqiang(jianqiang5@staff.sina.com.cn)
     * @version 2012-10-21
     */
    public static function is_new_user($user)
    {
        $registerd_days = Tool_Misc::get_registerd_days($user);
        if ($registerd_days && $registerd_days < 40)
    {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 获取当前用户是否登陆行为
     *
     * @return boolean TRUE为新登陆行为，false为未知项目
     *
     * @tutorial 返回为TRUE时，应主动调用 Dw_User::set_login_action();
     */
    public static function is_login_action()
    {
        try {
            $viewer = Comm_Context::get('viewer');
            $cookie_sue = Comm_Context::cookie('SUE');
            if ($cookie_sue === false || $cookie_sue === null)
    {
                return false;
            }
            
            $cacheUser = new Cache_User();
            $timestamps = $cacheUser->get_login_timestamps($viewer->id);
            $cur_timestamp = time();
            if (empty($timestamps))
    {
                return true;
            }
            $md5_16 = substr(md5($cookie_sue), 8, 16);
            if (!isset($timestamps[$md5_16]) || $timestamps[$md5_16] <= 5)
    {
                // 未记录的COOKIE信息，被视作新的登陆行为
                return true;
            }
            return false;
        } catch (Exception $e)
    {
            return false;
        }
    }

    /**
     * 获取用户当天最近一次登陆时间
     *
     * @param int64 $uid 当前用户uid
     *            
     * @return int boolean timestamps时间戳，null信息为空
     */
    public static function get_last_login_time($uid)
    {
        try {
            $cacheUser = new Cache_User();
            $cache_timestamps = $cacheUser->get_login_timestamps($uid);
            if (is_array($cache_timestamps) && !empty($cache_timestamps))
    {
                return intval(end($cache_timestamps));
            }
            return null;
        } catch (Exception $e)
    {
            return null;
        }
    }
    
    /**
     * 获取某用户无向元距离数据
     * 
     * @param int64 uid 用户uid
     * 
     * @return ArrayObject
     */
    public static function qinmiduGetDistanceNofontv2($uid)
    {
        try {
            $commApi = Comm_Weibo_Api_Proxy_Sdata::qinmiduGetDistanceNofontv2();
            $commApi->setValue('uid', $uid);
            $rst = $commApi->getResult();

            if (isset($rst['auidlist']) && !empty($rst['auidlist']))
            {
                return $rst['auidlist'];
            }
            else 
            {
                return false;
            }
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return false;
        }        
    }
    
    /**
     * 获取联想搜索
     * Enter description here ...
     * @param unknown_type $uid
     * @param unknown_type $keyword
     * @param unknown_type $type
     * @param unknown_type $page
     * @param unknown_type $pagesize
     * @param unknown_type $sid
     */
    public static function getLenovoSearch($queryStr, $uid, $offset, $length, $type, $sid = '')
    {
        $queryStr = trim ( $queryStr );
        if ($queryStr == '' or $uid <= 0 or $offset < 0 or $length <= 0)
    {
            return false;
        }
        $queryStr = rawurlencode ( $queryStr );
        try {
            $comm = Comm_Weibo_Api_Search::userqueryex() ;
            $comm->query = $queryStr;
            $comm->uid = $uid ;
            $comm->start = $offset ;
            $comm->num= $length;
            $comm->type = $type;
            $comm->sid = $sid;
            $comm->lang = 'utf-8';
            $comm->source = 0; //这个结果传source没数据
            $re = $comm->getResult() ;
        } catch (Comm_Weibo_Exception_Api $e)
    {
            return false; 
        }

        if ($re === false or ! is_array ( $re ))
    {
            return false;
        }
        $total = $re ['num'];
        $record = array ();
        foreach ($re ['users'] as $v)
    {
            $record [] = array ('uid' => $v [0], 'name' => $v [1], 'desc' => $v [2] );
        }
        return array ('total' => $total, 'record' => $record );
    }
}
