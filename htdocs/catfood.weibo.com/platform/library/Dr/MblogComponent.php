<?php
class Dr_MblogComponent extends Dr_Abstract
{
    /**
     * 获取商品详细信息
     * 
     * @param unknown_type $pid            
     * @param unknown_type $cuid            
     * @param unknown_type $mid            
     * @param unknown_type $isforward            
     */
    public static function showGoodsFeed($pid, $cuid, $mid, $isforward)
    {
        try
        {
            $widgetApi = Comm_Weibo_Api_Admin::showGoodsFeed();
            $widgetApi->pid = $pid;
            $widgetApi->cuid = $cuid;
            $widgetApi->type = 'v4';
            $widgetApi->mid = $mid;
            $widgetApi->isforward = $isforward;
            $rtn = $widgetApi->getResult();

            return isset($rtn['data']) ? $rtn['data'] : array();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 喜欢商品post
     * 
     * @param unknownType $pid            
     * @param unknownType $cuid            
     * @param unknownType $mid            
     * @param unknownType $isforward            
     * @param unknownType $prefer            
     * @param unknownType $ufrom            
     * @param unknownType $cip            
     */
    public static function goodsLike($pid, $cuid, $mid, $isforward, $prefer, $ufrom, $cip)
    {
        try
        {
            $widgetApi = Comm_Weibo_Api_Admin::showGoodsFeed();
            $widgetApi->pid = $pid;
            $widgetApi->cuid = $cuid;
            $widgetApi->type = 'v4';
            $widgetApi->mid = $mid;
            $widgetApi->isforward = $isforward;
            $widgetApi->prefer = $prefer;
            $widgetApi->ufrom = $ufrom;
            $widgetApi->cip = $cip;
            
            $rtn = $widgetApi->getResult();
            return isset($rtn['data']) ? $rtn['data'] : array();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 根据ID获取单个活动的feed
     * 
     * @param unknownType $eid            
     */
    public static function showEventFeed($eid)
    {
        try
        {
            $widgetApi = Comm_Weibo_Api_Admin::showEventFeed();
            $widgetApi->eid = $eid;
            
            return $widgetApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 转发某条有奖微博
     * 
     * @param unknownType $eid            
     */
    public static function repostEventFeed($eid)
    {
        try
        {
            $widgetApi = Comm_Weibo_Api_Admin::repostEventFeed();
            $widgetApi->eid = $eid;

            return $widgetApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}
