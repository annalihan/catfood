<?php
class Comm_Weibo_Api_Groups_Users
{
    const RESOURCE = "groups";

    /**
     * 获取用户加入的所有群列表
     */
    public static function joined()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "joined", "json", null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportBaseApp();
        $platform->supportPagination();
        $platform->addRule("uid", "int64", true);
        $platform->addRule("sort", "int64", true);

        return $platform;
    }

    /**
     * 批量判断用户是否在某个群中
     * @param int $groupId
     */
    public static function exists($groupId)
    {
        Comm_Weibo_Api_Util::checkInt($groupId);
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $groupId . '/users/exists', "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule('nuid', 'string', true);
        $request->addSetCallback('nuid', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int', ','));
        $request->supportBaseApp();
        return $request;
    }

}
