<?php
class Dr_Remind extends Dr_Abstract
{
    const INVITE    = 'invite';
    const FEED      = 'status';
    const BADGE     = 'badge';
    const GROUP     = 'group';
    const SWARM     = 'swarm';
    const COMMENT   = 'cmt';
    const ATTENTION = 'follower';
    const MSG       = 'dm';
    const PHOTO     = 'photo';
    const ATME      = 'mention_status';
    const ATCMT     = 'mention_cmt';
    const NOTICE    = 'notice';
    
    private static $_types = array(
        self::INVITE => true,
        self::FEED => true,
        self::BADGE => true,
        self::GROUP => true,
        self::SWARM => true,
        self::COMMENT => true,
        self::ATTENTION => true,
        self::MSG => true,
        self::PHOTO => true,
        self::ATME => true,
        self::ATCMT => true,
        self::NOTICE => true
    );

    /**
     * 获取提醒未计数
     * 
     * @param int $userId
     * @param string $type 缺少获取所有的提醒
     * @return mixed
     */
    public static function unread($userId, $type = null)
    {
        static $rst; //防止多次调用api
        
        if (null == $rst)
        {
            $api = Comm_Weibo_Api_Iremind::unreadCount();
            $api->user_id = $userId;
            $api->target = "api";
            $rst = $api->getResult();
        }

        if ($type === null)
        {
            return $rst;
        }
        
        if (isset(self::$_types[$type]) === false)
        {
            throw new Dr_Exception('type ' . $type . ' invalid');
        }

        return $rst[$type];
    }
}
