<?php
class Dr_Check extends Dr_Abstract
{
    /**
     * 检测文本是否包含敏感词
     *
     * @param string $keyword            
     */
    public static function checkKeyWord($keyword)
    {
        try
        {
            if (!is_string($keyword) || $keyword == '')
            {
                return false;
            }
    
            $api = Comm_Weibo_Api_Admin::checkContent();
            $api->content = $keyword;
            
            return $api->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}
