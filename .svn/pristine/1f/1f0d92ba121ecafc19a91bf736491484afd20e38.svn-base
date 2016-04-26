<?php
class Comm_Weibo_Api_Friendships
{
    const RESOURCE = 'friendships';

    /**
     * 获取用户关注列表及每个关注用户的最新一条微博
     *
     * 返回结果按关注时间倒序排列，最新关注的用户排在最前面。
     */
    public static function friends()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination('cursor');
        $request->supportTrimStatus();

        return $request;
    }

    /**
     * 获取共同关注人列表接口
     */
    public static function friendsInCommon()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends/in_common');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('suid', 'int64', true);
        $request->supportPagination();
        $request->supportTrimStatus();

        return $request;
    }

    /**
     * 获取双向关注列表
     */
    public static function friendsBilateral()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends/bilateral');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->supportPagination();
        $request->addRule('sort', 'int', false);

        return $request;
    }

    /**
     * 获取双向关注ID列表
     */
    public static function friendsBilateralIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends/bilateral/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->supportPagination();
        $request->addRule('sort', 'int', false);

        return $request;
    }

    /**
     * 获取用户关注对象uid列表
     */
    public static function friendsIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination('cursor');

        return $request;
    }

    /**
     * 批量获取当前登录关注人的备注信息
     */
    public static function friendsRemarkBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends/remark_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 50));

        return $request;
    }

    /**
     * 获取用户粉丝列表及每个粉丝的最新一条微博
     */
    public static function followers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'followers');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination('cursor');
        $request->supportTrimStatus();

        return $request;
    }

    /**
     * 返回用户的粉丝用户ID列表
     */
    public static function followersIds()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'followers/ids');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->uidOrScreenName();
        $request->supportPagination('cursor');

        return $request;
    }

    /**
     * 获取用户活跃粉丝列表。每次最多返回20条，包括用户的最新的微博
     */
    public static function followersActive()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'followers/active');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('count', 'int', false);

        return $request;
    }

    /**
     * 获取当前登录用户的关注人中，关注了指定用户的用户列表
     */
    public static function friendsChainFollowers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'friends_chain/followers');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->supportPagination();

        return $request;
    }

    /**
     * 获取用户所创建的分组列表
     */
    public static function groups()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'groups');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('show_detail', 'int', false);

        return $request;
    }

    /**
     * 获取某个分组的详细信息
     */
    public static function groupsShow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'groups/show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('list_id', 'int64', true);

        return $request;
    }

    /**
     * 获取两个用户关系的详细情况
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'show');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $namesOfUidScreen = array(
            array('source_id', 'source_screen_name'),
            array('target_id', 'target_screen_name')
        );
        Comm_Weibo_Api_Util::oneOrOtherMulti($request, $namesOfUidScreen);

        return $request;
    }

    /**
     * 获取某用户（无需登录）与一组用户的关注关系
     */
    public static function existsBatchInternal()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'exists_batch_internal');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));

        return $request;
    }

    /**
     * 关注一个用户
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('skip_check', 'int');
        $request->uidOrScreenName();
        $request->setRequestTimeout(5000, 5000);

        return $request;
    }

    /**
     * 当前登录用户批量关注指定ID的用户
     */
    public static function createBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'create_batch');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 20));
        $request->setRequestTimeout(10000, 10000);

        return $request;
    }

    /**
     * 取消关注某用户
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->uidOrScreenName();
        $request->setRequestTimeout(5000, 5000);

        return $request;
    }

    /**
     * 移除粉丝
     */
    public static function followersDestroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'followers/destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uid', 'int64', true);
        $request->setRequestTimeout(5000, 5000);

        return $request;
    }

    /**
     * 更新当前登录用户所关注的某个好友的备注信息
     */
    public static function remarkUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'remark/update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('uid', 'int64', true);
        $request->addRule('remark', 'string', true);

        return $request;
    }

    /**
     * 调整用户的分组顺序
     */
    public static function groupsOrder()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'groups/order');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('list_ids', 'string', true);
        $request->addSetCallback('list_ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $request->addRule('count', 'int', true);

        return $request;
    }

    /**
     * 按权重获取当前登录用户关注人中的公司、学校和标签
     */
    public static function followersCommonInfo()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'common_info');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 已废弃
     *  按条件过滤我的关注人 
     */
    public static function searchFriendsFilter()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "friends/filter");
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule('page', 'int', false);
        $request->addRule('count', 'int', false);
        $request->addRule('tag', 'string', false);
        $request->addRule('scho', 'string', false);
        $request->addRule('comp', 'string', false);
        $request->addRule('is_encoded', 'int', false);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 批量判断密友关系
     */
    public static function friendshipsCloseFriendsExists()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'close_friends/exists');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uids', 'string', true);
        
        return $request;
    }

    /**
     * 批量获取当前登录用户指定用户的备注信息
     * @doc http://wiki.intra.weibo.com/2/friendships/encompassing/remarks
     */
    public static function friendshipsEncompassingRemarks()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'encompassing/remarks');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('followings', 'string', false);
        $request->addRule('followers', 'string', false);
        $request->addRule('nones', 'string', false);
        $request->addRule('is_encoded', 'int', false);
        $request->setRequestTimeout(5000, 5000);
        
        return $request;
    }

    /**
     * 更新当前登录用户对某个用户的备注信息
     * @doc http://wiki.intra.weibo.com/2/friendships/encompassing/remark/update
     */
    public static function friendshipsEncompassingRemarkUpdate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'encompassing/remark/update');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('f_uid', 'int64', true);
        $request->addRule('type', 'int64', true);
        $request->addRule('remark', 'string', true);
        $request->setRequestTimeout(5000, 5000);
        
        return $request;
    }
}
