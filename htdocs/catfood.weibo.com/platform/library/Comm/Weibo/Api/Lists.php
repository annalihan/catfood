<?php
class Comm_Weibo_Api_Lists
{
    const RESOURCE = 'lists';

    /**
     * 获取指定用户的LIST列表
     */
    public static function userOwnLists()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user/own_lists');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('list_type', 'int', false);
        $request->addRule('cursor', 'int', false);
        $request->supportEncode();

        return $request;
    }

    /**
     * 获取用户所创建的分组的名称
     */
    public static function userOwnListsName()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user/own_lists_name');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('list_type', 'int', false);
        $request->addRule('cursor', 'int', false);
        $request->supportEncode();

        return $request;
    }

    /**
     * 批量获取指定用户在当前登录用户的私有组中的分组信息
     */
    public static function userListedBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user/listed_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 50));
        $request->supportEncode();

        return $request;
    }

    /**
     * 列出用户作为成员的所有list列表
     */
    public static function userListed()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user/listed');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('cursor', 'int64', false);

        return $request;
    }

    /**
     * 列出用户订阅的所有list列表
     */
    public static function userSubscriptions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'user/subscriptions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('cursor', 'int64', false);

        return $request;
    }

    /**
     * 获取LIST成员的最新微博 ，私有list的列表只能自己可以访问
     */
    public static function membersTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'members/timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('list_id', 'int64', true);
        $request->supportCursor();
        $request->supportPagination();
        $request->supportBaseApp();
        $request->addRule('feature', 'int', false);

        return $request;
    }

    /**
     * 返回list中所有的成员
     */
    public static function showMembers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show/members');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('list_id', 'int64', true);
        $request->addRule('cursor', 'int64', false);

        return $request;
    }

    /**
     * 返回list中所有的订阅者
     */
    public static function showSubscribers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show/subscribers');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('list_id', 'int64', true);
        $request->addRule('cursor', 'int64', false);

        return $request;
    }

    /**
     * 创建一个新的list，每个用户最多能够创建20个。
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('name', 'string', true);
        $request->addRule('mode', 'string', false);
        $request->addRule('description', 'string', false);

        return $request;
    }

    /**
     * 更新分组
     */
    public static function update()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('name', 'string', true);
        $request->addRule('list_id', 'int64', true);
        $request->addRule('description', 'string', false);

        return $request;
    }

    /**
     * 删除分组
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('list_id', 'int64', true);

        return $request;
    }

    /**
     * 添加多个关注人到分组
     *
     * 每个list最多拥有500个用户。私有列表只能添加自己关注的人
     */
    public static function memberAddUsers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'member/add_users');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $request->addRule('list_id', 'int64', true);

        return $request;
    }

    /**
     * 添加关注人到多个分组
     */
    public static function memberAddLists()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'member/add_lists');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uid', 'int64', true);
        $request->addRule('list_ids', 'string', true);
        $request->addSetCallback('list_ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));

        return $request;
    }

    /**
     * 添加用户到分组
     */
    public static function memberAdd()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'member/add');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uid', 'int64', true);
        $request->addRule('list_id', 'int64', true);

        return $request;
    }

    /**
     * 将用户从分组中删除
     */
    public static function memberDestroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'member/destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uid', 'int64', true);
        $request->addRule('list_id', 'int64', true);

        return $request;
    }
}
