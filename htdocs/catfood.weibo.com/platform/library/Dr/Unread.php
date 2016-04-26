<?php
class Dr_Unread extends Dr_Abstract
{
    public static function pushCount()
    {
        try
        {
            $commApi = Comm_Weibo_Api_Unread::pushCount();
            $rst = $commApi->getResult();
            return $rst;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw $e;
        }
    }

    public static function unread()
    {
        try
        {
            $commApi = Comm_Weibo_Api_Unread::unread();
            $rst = $commApi->getResult();
            return $rst;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw $e;
        }
    }
}