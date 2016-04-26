<?php
class Comm_Weibo_Api_Admin
{
    const RESOURCE = 'admin';

    /**
     * 待审批学校名称列表
     */
    public static function accountNewSchoolList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'account/new_school_list');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();

        return $request;
    }

    /**
     * 后台发送私信
     */
    public static function directMessagesNew()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'direct_messages/new');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        $request->addRule('text', 'string', true);
        $request->addRule('fuid', 'int64', true);
        $request->addRule('tuids', 'string', true);
        $request->addSetCallback('tuids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $request->addRule('cip', 'string', true);
        $request->addRule('cname', 'string', true);
        $request->addRule('fids', 'string', false);
        $request->addSetCallback('fids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $request->addRule('id', 'int64', false);

        return $request;
    }

    /**
     * 根据V用户UID返回用户的客服代表信息
     */
    public static function csrGetCsrByUid()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/admin', 'csr/getCsrByUid');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);

        return $request;
    }

    /**
     * 更具谣言微博mid获取辟谣信息
     *
     */
    public static function getRumorInfoByMid()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/admin', 'content/get_mblogext');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('mids', 'string', true);

        return $request;
    }

    /**
     * 获取首页右侧公告
     * */
    public static function contentGetAnnouncement()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/admin', 'content/get_announcement');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->getHttpRequest()->connectTimeout = 1000;
        $request->getHttpRequest()->timeout = 1000;

        return $request;
    }

    /**
     * 显示商品feed详情
     */
    public static function showGoodsFeed()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/commerce', 'internal/mainfeed');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        $request->addRule('pid', 'string', true);
        $request->addRule('cuid', 'int64', true);
        $request->addRule('type', 'string', true);
        $request->addRule('mid', 'string', true);
        $request->addRule('isforward', 'string', true);
        $request->addRule('prefer', 'string', false);
        $request->addRule('ufrom', 'string', false);
        $request->addRule('cip', 'string', false);

        return $request;
    }

    /**
     * 根据ID获取单个活动的feed
     */
    public static function showEventFeed()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/events', "show_feed");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("eid", "int64", true);
        $platform->setRequestTimeout(3000, 3000);

        return $platform;
    }

    /**
     * 转发活动并返回feed
     */
    public static function repostEventFeed()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/events', "statuses/repost_feed");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("eid", "int64", true);
        $platform->setRequestTimeout(3000, 3000);

        return $platform;
    }

    /**
     * 获取草根人气用户接口
     */
    public static function getGrassUser()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl("proxy", "pub/recommend/getgrass_uids");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("class1", "int", true);
        $platform->addRule("class2", "int");
        $platform->addRule("num", "int");

        return $platform;
    }

    /**
     * 检查文本关键字
     *
     */
    public static function checkContent()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/admin', 'content/check_keyword');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('content', 'string', true);
        $request->setRequestTimeout(3000, 3000);
        
        return $request;
    }

    /**
     * 获取认证用户分类信息
     * @param 分类类型ID，1：名人，2：媒体，3：企业，4：网址，5：无线，6：政府，7：校园，9：应用，10：机构，默认为空，表示为获取全部分类类型信息。
     */
    public static function getEnterpriseClass($depart = 3)
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy/admin', 'vuser/class_info');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('depart', 'int', true);

        return $request;
    }

    /**
     *
     * 批量判断用户是否存在
     * @param string $uids 需要查询的用户ID
     * @param string $cuid 模拟登录用户的uid
     * @param string $ip 请求来源ip
     */
    public static function checkUidExistsBatch($uids, $cuid, $ip, $signature)
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('2/register', "exists_batch");
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule("uids", 'string', true);
        $request->addRule('cuid', "string", true);
        $request->addRule("ip", "string", true);
        $request->addRule('signature', 'string', true);

        return $request;
    }
}
