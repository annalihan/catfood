<?php

class Dr_Im extends Dr_Abstract
{
    public static function statusQuery($uid)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Im::statusQuery();
            $commApi->setValue('uid', $uid);
            $commApi->setValue('is_sample', 1);
            
            return $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 批量查询在线状态
     * @param  [type] $uids Array最多100个
     * @return [type] Array 如果离线，结果中没有记录
     */
    public static function statusQueryBatch($uids)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Im::statusQueryBatch();
            $commApi->setValue('uids', implode(',', $uids));
            $commApi->setValue('is_sample', 1);
            
            return $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}