<?php
class Dr_UserExtend extends Dr_Abstract
{
    const DB_POOL = 'user';
    
    //业务项定义
    const TYPE_TIPBAR_MESSAGE = 2; //私信页的诈骗小黄签提醒
    const TYPE_TIPBAR_AT = 3; //@页的诈骗小黄签提醒
    const TYPE_TIPBAR_COMMENT = 4; //评论页的诈骗小黄签提醒
    const TYPE_TIPBAR_INVITE_CODE = 5; //邀请码气泡
    const TYPE_TIPBAR_IM = 6; //首页IM气泡提醒
    const TYPE_BUBBLE_ID = 7; //用户小贴士气泡提醒
    const TYPE_TASKS = 11;
    const TYPE_REG_GUIDE = 9; //注册引导任务
    const TYPE_ENTERPRISE_LAYER = 8; //企业微博用户引导层
    const TYPE_TIPBAR_TOP = 10; //用户吸顶引导
    const TYPE_QUIET_FOLLOW = 12; //悄悄关注气泡
    const TYPE_SECURITY_REDIRECT_FLG = 13; //安全引导页跳转判断标识
    const TYPE_USER_VERSION = 16; //用户宽窄版本信息
    const TYPE_IDENTIFY_NOTICE = 22; //用户是否需要弹出身份认证提醒
    const TYPE_IDENTIFY = 24; // 身份认证吸顶提醒23,24都用过
    const TYPE_INVITE_TIPS_CLOSE = 20; //我的邀请页小黄签提醒
    const TYPE_HOMEFEED_SORT = 25; //首页更新模式类型
    const TYPE_HANDBOOK_REDIRECT_FLG = 26; //微博指南页跳转判断标识
    const TYPE_HANDBOOK_BUBBLE_FLG = 27; //微博指南气泡写入判断标识
    const TYPE_ENTERPRISE_UPGRADE = 28; //V1企业微博用户升级弹层
    const TYPE_CLOSEFRIEND_SWITCH = 29; //密友功能开关
    const TYPE_COVER_TIP = 30; //封面图设置提示
    const TYPE_HOME_GUIDE = 31; //V5home页升级引导
    const TYPE_PROFILE_GUIDE = 32; //v5profile升级引导
    const TYPE_GROUP_GUIDE = 33; //分组引导
    const TYPE_COMMENT_GUIDE = 34; //评论引导（已下线）
    const TYPE_APP_GUIDE = 35; //app应用引导
    const TYPE_APP_UNREAD_GUIDE = 36; //app未读数提醒
    const TYPE_USER_VERSION_V5 = 37; //是否是V5用户
    const TYPE_ATTITUDE_GUIDE = 38; //表态（已下线）功能引导
    const TYPE_MIYOU_GUIDE = 39; //密友引导（已下线）
    const TYPE_CLOSEFRIEND_RELATION_GUIDE = 40; //密友分组页引导(已下线)
    const TYPE_COVER_CUSTOMIZE_COUNT = 41; //自定义cover图使用次数
    const TYPE_PROFILE_PR_GUIDE = 43; //profile页PR引导
    const TYPE_LIKE_GUIDE = 44; //喜欢功能引导
    const TYPE_LEFTAPP_TIPS_SHOW = 45; //左导置顶应用列表查看提示
    const TYPE_LEFTAPP_TIPS_FULL = 46; //左导置顶应用列表已满提示
    const TYPE_LAYER_GUIDE = 48; //首页推荐蒙层引导
    const TYPE_GUIDE_THREE = 49; // 是否走过新用户引导 3.0
    const TYPE_GUIDE_SINGLE = 50; //光棍节引导
    const TYPE_RECOMM_CLOSE = 51; // feed未加关注关闭
    const TYPE_MIYOUQUAN_TAGETUSER = 52; //密友圈引导目标用户判断
    const TYPE_MIYOUQUAN_GUIDE = 53; //密友圈引导

    //业务项对应的值定义
    const VALUE_TIPBAR_OPEN = 0; //诈骗小黄签提醒打开
    const VALUE_TIPBAR_CLOSE = 1; //诈骗小黄签提醒关闭
    const VALUE_ENTERPRISE_CLOSE = 1; //企业微博用户引导层关闭
    const VALUE_ENTERPRISE_OPEN = 0; //企业微博用户引导层开启
    const VALUE_SECURITY_REDIRECT_FLG = 1; //不需要跳转到安全引导页
    const VALUE_HANDBOOK_REDIRECT_FLG = 1; //不需要跳转到微博指南页
    const VALUE_HANDBOOK_BUBBLE_FLG = 1; //不需要写入微博指南气泡
    const VALUE_IDENTIFY_NEED_SHOW = 2; //需要提示身份认证
    const VALUE_IDENTIFY_NOT_SHOW = 3; //不需要提示身份认证
    const VALUE_IDENTIFY_CLOSE = 1; //已经关闭身份认证提醒
    const VALUE_SORT_TIME = 2; //首页feed时间排序模式
    const VALUE_SORT_GENIUS = 1; //首页feed智能排序模式
    const VALUE_CLOSE = 0; //通用关闭
    const VALUE_OPEN = 1; //通用开启
    const VALUE_FALSE = 0; //通用false
    const VALUE_TRUE = 1; //通用true
    const CLSOSE_FRIEND_FIG = 1; //是否开启密友功能  0为关闭  1为开启
    const VALUE_RECOMM_CLOSE_FOREVER = 2; // 关闭永久
    
    //有效的业务号定义
    public static $validServiceNum = array(
        self::TYPE_TIPBAR_MESSAGE, 
        self::TYPE_TIPBAR_AT, 
        self::TYPE_TIPBAR_COMMENT, 
        self::TYPE_TIPBAR_INVITE_CODE, 
        self::TYPE_TIPBAR_IM, 
        self::TYPE_BUBBLE_ID, 
        self::TYPE_TASKS, 
        self::TYPE_REG_GUIDE, 
        self::TYPE_ENTERPRISE_LAYER, 
        self::TYPE_TIPBAR_TOP, 
        self::TYPE_QUIET_FOLLOW, 
        self::TYPE_SECURITY_REDIRECT_FLG, 
        self::TYPE_USER_VERSION, 
        self::TYPE_IDENTIFY_NOTICE, 
        self::TYPE_IDENTIFY, 
        self::TYPE_INVITE_TIPS_CLOSE, 
        self::TYPE_HOMEFEED_SORT, 
        self::TYPE_HANDBOOK_REDIRECT_FLG, 
        self::TYPE_HANDBOOK_BUBBLE_FLG, 
        self::TYPE_ENTERPRISE_UPGRADE, 
        self::TYPE_CLOSEFRIEND_SWITCH, 
        self::TYPE_COVER_TIP, 
        self::TYPE_HOME_GUIDE, 
        self::TYPE_PROFILE_GUIDE, 
        self::TYPE_GROUP_GUIDE, 
        self::TYPE_APP_GUIDE, 
        self::TYPE_COMMENT_GUIDE, 
        self::TYPE_APP_UNREAD_GUIDE, 
        self::TYPE_USER_VERSION_V5, 
        self::TYPE_MIYOU_GUIDE, 
        self::TYPE_COVER_CUSTOMIZE_COUNT, 
        self::TYPE_CLOSEFRIEND_RELATION_GUIDE, 
        self::TYPE_PROFILE_PR_GUIDE, 
        self::TYPE_LIKE_GUIDE,
        self::TYPE_LEFTAPP_TIPS_SHOW,
        self::TYPE_LEFTAPP_TIPS_FULL,
        self::TYPE_LAYER_GUIDE,
        self::TYPE_GUIDE_THREE,
        self::TYPE_GUIDE_SINGLE,
        self::TYPE_RECOMM_CLOSE,
        self::TYPE_MIYOUQUAN_TAGETUSER,
        self::TYPE_MIYOUQUAN_GUIDE,
    );   

    /**
     * 获取用户扩展信息设置
     * @param int $uid
     * @throws Comm_Exception_Program
     */
    public static function getUserExtendInfo($uid)
    {
        try
        {
            $extendInfo = Comm_Context::get('user_extend_info', false);
            if ($extendInfo === false) 
            {
                // 从MC中读取数据
                $cacheUser = new Cache_User();
                $extendInfo = $cacheUser->getUserExtendInfo($uid);

                //TODO
                // 从DB中读取数据
                if (false === $extendInfo)
                {
                    $db = Comm_Db::pool(self::DB_POOL);
                    $sql = "SELECT `extend` FROM `user_extend` WHERE uid=?";
                    $result = $db->fetchAll($sql, array($uid));
                    $extendInfo = !empty($result[0]) ? json_decode($result[0]['extend'], true) : array();
                    
                    // 写MC，当数据为空时也写
                    $cacheUser->createUserExtendInfo($uid, $extendInfo);
                }
                
                Comm_Context::set('user_extend_info', $extendInfo);
            }
            
            return $extendInfo;
        }
        catch (Comm_Exception_Program $e)
        {
            throw $e;
        }
    }

    public static function getExtend($uid, $serviceNum)
    {
        // 开启密友功能
        if ($serviceNum == self::TYPE_CLOSEFRIEND_SWITCH && self::CLSOSE_FRIEND_FIG)
        {
            return self::VALUE_OPEN;
        }
        
        if (!in_array($serviceNum, self::$validServiceNum))
        {
            throw new Dr_Exception('service num invalid');
        }
        
        try
        {
            // 对整数数据和字符串数据做单独处理
            $extendInfo = self::getUserExtendInfo($uid);
            if (!isset($extendInfo[$serviceNum]))
            {
                return null;
            }

            return $extendInfo[$serviceNum];
        }
        catch (Comm_Exception_Program $e)
        {
            return null;
        }
    }
}
