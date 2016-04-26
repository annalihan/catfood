<?php
class Dr_Privacy extends Dr_Abstract
{
    const VISIABLE_SELF = 0; //0-自己可见；
    const VISIABLE_FOLLOWED = 1; //1-我关注人可见
    const VISIABLE_ALL = 2; //2-所有人可见
        
    public static function getPrivacy()
    {
        static $currentPrivacy = null;
        
        if ($currentPrivacy === null)
        {
            try
            {
                $comm = Comm_Weibo_Api_Account::getPrivacy();
                $currentPrivacy = $comm->getResult();
            }
            catch (Exception $e)
            {
                throw new Dr_Exception($e);
            }           
        }

        return $currentPrivacy;        
    }
    
    /**
     * 获取url可见权限
     * 
     * @param int $uid
     * @return int
     */
    public static function getUrlVisiable($uid)
    {
        $cacheObject = new Cache_Privacy();
        $urlVisiable = $cacheObject->getUrlVisiable($uid);
        
        if (false === $urlVisiable)
        {
            $basicInfo = Dr_Account::getProfileBasic($uid);
            $urlVisiable = $basicInfo->getValue('url_visible');
            $cacheObject->setUrlVisiable($uid, $urlVisiable);
        }
        
        return $urlVisiable;
    }
    
    /**
     * 批量获取用户隐私
     * @param  array  $uids [description]
     * @return [type]       [description]
     */
    public static function getPrivacyBatch(array $uids)
    {
        try
        {
            $comm = Comm_Weibo_Api_Account::getPrivacyBatch();
            $comm->uids = join(',', $uids);
            $result = $comm->getResult();
            $privacyInfos = array();

            foreach ($result as $privacy)
            {
                $privacyInfos[$privacy['uid']] = $privacy['privacy'];
            }

            return $privacyInfos;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 根据隐私及关系获取显示状态
     * @param unknown_type $privacy
     * @param unknown_type $relation
     */
    public static function isShow($privacy, $relation)
    {        
        $privacy = intval($privacy);

        switch (true)
        {
            case $privacy === Dr_Privacy::VISIABLE_ALL:
                return true;

            case $privacy === Dr_Privacy::VISIABLE_FOLLOWED:
                return $relation == Dr_Relation::RELATION_BILATERAL || $relation == Dr_Relation::RELATION_FOLLOWED;

            case $privacy === Dr_Privacy::VISIABLE_SELF:
                return false;
        }
    }
}
