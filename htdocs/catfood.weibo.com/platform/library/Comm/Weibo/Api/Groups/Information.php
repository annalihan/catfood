<?php
class Comm_Weibo_Api_Groups_Information
{
    const RESOURCE = "groups";

    /**
     * 获取群信息
     * @param int $groupId
     */
    public static function info($groupId)
    {
        Comm_Weibo_Api_Util::checkInt($groupId);
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $groupId, "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->supportBaseApp();

        return $request;
    }

}
