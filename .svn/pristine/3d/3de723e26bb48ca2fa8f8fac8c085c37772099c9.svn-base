<?php
class Dr_Blocks extends Dr_Abstract
{
    public static function getBlockingIds()
    {
        try
        {
            $comm = Comm_Weibo_Api_Blocks::blockingIds();
            return $comm->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}
