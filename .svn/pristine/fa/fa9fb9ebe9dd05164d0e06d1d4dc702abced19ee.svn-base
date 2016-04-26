<?php
class Comm_Weibo_Api_Suggestions_Statuses
{
    const RESOURCE = "suggestions/statuses";

    /**
     * 对当前用户的friend_timeline根据兴趣进行重排。支持对前500条微博进行重排。
     */
    public static function statusesReorder()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "reorder");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('section', 'int', true);
        $platform->supportPagination();
        $platform->supportCursor();

        return $platform;
    }

    /**
     * 对当前用户的friend_timeline根据兴趣进行重排，仅返回id。
     *
     * @see statuses_reorder
     */
    public static function statusesReorderIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "reorder/ids");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('section', 'int', true);
        $platform->supportPagination();
        
        return $platform;
    }
}
