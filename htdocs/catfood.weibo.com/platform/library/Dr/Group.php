<?php

class Dr_Group extends Dr_Abstract
{
    const MAX_SHOW_INTERESTED_GROUP = 10; // 感兴趣微群接口返回最大数
    
    public static $showInterestedGroupDataMap = array(
        'id' => true, // 群id
        'name' => true, // 群名
        'logo_50' => true, // 群图片
        'member_cnt' => true // 群用户数
    );

    const INTEREST_GROUP_CATEGORY_FRIENDS = 'friend'; // 互相关注用户推荐群
    const INTEREST_GROUP_CATEGORY_VIP = 'vip'; // 加v用户推荐群
    const INTEREST_GROUP_CATEGORY_FOLLOWING = 'follow'; // 关注用户推荐的微群
    
    private static $_interestGroupCategorys = array(
        self::INTEREST_GROUP_CATEGORY_FRIENDS => true,
        self::INTEREST_GROUP_CATEGORY_VIP => true,
        self::INTEREST_GROUP_CATEGORY_FOLLOWING => true
    );
    
    /**
     * 获取用户分组列表
     *
     * @param int $userId 用户数据对象
     * @param int $cursor 将结果分页，每一页包含20个lists。由-1开始分页，定位一个id地址，通过比较id大小实现next_cursor 和previous_cursor向前或向后翻页
     * @param int $list_type 控制返回的LIST，1：返回私有列表； 0：返回共有列表
     * @return array array(
     *         'lists' => array(),
     *         'previous_cursor' => 0,
     *         'next_cursor' => 0,
     *         )
     */
    public static function getUserGroup($userId, $cursor = -1, $listType = 0)
    {
        try
        {
            $cacheObject = new Cache_Group();            
            $list = $cacheObject->get($userId);
            if ($list)
            {
                return $list;
            }

            $apiLists = Comm_Weibo_Api_Lists::userOwnLists();
            $apiLists->uid = $userId;
            $apiLists->list_type = $listType;
            $apiLists->cursor = $cursor;

            // 返回结果转义
            $apiLists->is_encoded = 1;
            $listsInfo = $apiLists->getResult();
            $cacheObject->set($userId, $listsInfo);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
        
        return $listsInfo;
    }
    
    /**
     * 获取分组用户列表
     *
     * @param int $userId 用户数据对象
     * @param int $gid 分组数据对象
     * @param int $cursor 将结果分页，每一页包含20个lists。由-1开始分页，定位一个id地址，通过比较id大小实现next_cursor 和previous_cursor向前或向后翻页
     * @return array array(
     *         'previous_cursor' => 0,
     *         'users' => array(),
     *         'next_cursor' => 0,
     *         )
     */
    public static function getGroupUser($userId, $gid, $cursor = -1)
    {
        try
        {
            $apiLists = Comm_Weibo_Api_Lists::showMembers();
            $apiLists->uid = $userId;
            $apiLists->list_id = $gid;
            $apiLists->cursor = $cursor;

            return $apiLists->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     *
     *
     * 获取用户所创建的分组的名称
     * 
     * @param int $userId
     * @param int $listType            
     * @param int $cursor            
     * @param int $onlyGid只显示某一个分组的名字            
     * @throws Dr_Exception
     */
    public static function userOwnListsName($userId, $listType = 1, $cursor = -1, $onlyGid = -1)
    {
        try
        {
            $apiLists = Comm_Weibo_Api_Lists::userOwnListsName();
            $apiLists->uid = $userId;
            $apiLists->list_type = $listType;
            $apiLists->cursor = $cursor;
            // 返回结果转义
            $apiLists->is_encoded = 1;
            $listsInfo = $apiLists->getResult();
            $lists = array();
            if (is_array($listsInfo['lists']))
            {
                foreach ($listsInfo ['lists'] as $value)
                {
                    $lists[$value ['id']] = $value;
                }
            }

            $listsInfo['lists'] = $lists;
        
            return $listsInfo;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     *
     * 批量获取指定用户在当前登录用户的私有组中的分组信息
     * 
     * @param string $uids            
     * @throws Dr_Exception
     */
    public static function userListedBatch($uids)
    {
        try
        {
            $apiLists = Comm_Weibo_Api_Lists::userListedBatch();
            $apiLists->uids = $uids;
            // 返回结果转义
            $apiLists->is_encoded = 1;

            return $apiLists->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     *
     * 获取当前登录用户可能感兴趣的微群列表
     * 
     * @param string $count            
     * @throws Dr_Exception
     */
    public static function mayInterested($count)
    {
        try
        {
            $listsInfo = array();
            $apiLists = Comm_Weibo_Api_Groups_Suggestions::mayInterested();
            $apiLists->count = $count;
            
            return $apiLists->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 添加显示感兴趣微群数据
     * 
     * @param array $data            
     */
    public static function appendShowMayInterestedData($data, $unum, $category)
    {
        $showData = array();

        if (isset(self::$_interestGroupCategorys[$category]) === false)
        {
            throw new Dr_Exception('invalid group category data!');
        }

        if (intval($unum) != $unum)
        {
            throw new Dr_Exception('invalid group unum data!');
        }

        if (is_array($data) && count($data))
        {
            foreach ($data as $k => $val)
            {
                isset(self::$showInterestedGroupDataMap [$k]) && $showData [$k] = $val;
            }
        }

        if (count(self::$showInterestedGroupDataMap) != count($showData))
        {
            throw new Dr_Exception('Group may_interested invalid data error!');
        }

        $showData['unum'] = $unum; // 关系相关用户数
        $showData['category'] = $category; // 关系类别

        return $showData;
    }
    
    /**
     * 获取感兴趣的微群对应关系类型显示文案
     * 
     * @param unknown_type $category            
     */
    public static function getInterestGroupTextByCategory($category, $num)
    {
        $text = '';
        $num = intval($num);
        if ($num > 0)
        {
            switch ($category)
            {
                case self::INTEREST_GROUP_CATEGORY_FRIENDS: // 互相关注用户推荐群
                    $text = Comm_I18n::dynamicText('tpls.pl.content.interestgroup.text_friends_num', $num);
                    break;

                case self::INTEREST_GROUP_CATEGORY_VIP: // 加v用户推荐群
                    $text = Comm_I18n::dynamicText('tpls.pl.content.interestgroup.text_vip_num', $num);
                    break;

                case self::INTEREST_GROUP_CATEGORY_FOLLOWING: // 关注用户推荐的微群
                    $text = Comm_I18n::dynamicText('tpls.pl.content.interestgroup.text_fans_num', $num);
                    break;
            }
        }
        else
        {
            $text = Comm_I18n::dynamicText('tpls.pl.content.interestgroup.text_recommend', $num);
        }

        return $text;
    }
    
    /**
     * 获取当前关注人中权重最高的5个公司、学校、标签
     * 
     * @param unknown_type $uid            
     * @param unknown_type $type            
     */
    public static function followersCommonInfo()
    {
        try
        {
            $cacheGroup = new Cache_Group();
            $data = $cacheGroup->getFollowersInfo(Comm_Context::get('viewer')->id);
            if ($data)
            {
                return $data;
            }

            $apiLists = Comm_Weibo_Api_Friendships::followersCommonInfo();
            $listsInfo = $apiLists->getResult();
            $cacheGroup->createFollowersInfo(Comm_Context::get('viewer')->id, $listsInfo);
            return $listsInfo;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}
