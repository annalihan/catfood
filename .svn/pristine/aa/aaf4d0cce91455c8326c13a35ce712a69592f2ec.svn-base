<?php
class Dr_Setting extends Dr_Abstract
{
    /**
     * 获取用户个性设置，此函数必需登录才能访问
     */
    public static function getSetting($uid = null)
    {
        if (null == $uid)
        {
            $viewer = Comm_Context::get('viewer', false);    
            if (false === $viewer)
            {
                throw new Comm_Exception_Program('viewer does not init');
            }

            $uid = $viewer->id;
        }

        $cacheSetting = new Cache_Setting();
        $settings = $cacheSetting->getSettings($uid);
        if (false === $settings)
        {
            try
            {
                $comm = Comm_Weibo_Api_Account::getSettings();
                $settings = $comm->getResult();
                $cacheSetting->createSettings($uid, $settings);
            }
            catch (Exception $e)
            {
                throw new Dr_Exception($e);
            }
        }
        
        return $settings;
    }
}
