<?php
class Dr_Tabs extends Dr_Abstract
{
    public static function getInstallList($uid)
    {
        try
        {
            $commTabs = Comm_Weibo_Api_Tabs::getInstallList();
            $commTabs->uid = $uid;
            return $commTabs->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

}
