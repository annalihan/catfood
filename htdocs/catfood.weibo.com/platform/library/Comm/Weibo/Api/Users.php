<?php
class Comm_Weibo_Api_Users
{
    const RESOURCE = "users";

    /**
     * 根据用户ID获取用户资料
     */
    public static function show()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->uidOrScreenName();
        $platform->addRule("has_extend", "int", false);

        return $platform;
    }

    /**
     * 通过个性域名获取用户信息
     */
    public static function domainShow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "domain_show");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("domain", "string", true);
        $platform->addRule("has_extend", "int", false);

        return $platform;
    }

    /**
     * 批量获取用户信息
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->uidOrScreenName("uids", "screen_name", true);
        $platform->addRule("has_extend", "int", false);
        $platform->addRule("trim_status", "int", 0);
        $platform->addRule("simplify", "string", false);

        return $platform;
    }

    /**
     * 获取系统推荐用户
     */
    public static function hot()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "hot");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("category", "string");

        return $platform;
    }

    /**
     * 获取用户可能感兴趣的人
     */
    public static function mayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "may_interested");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("type", "int");

        return $platform;
    }

    /**
     * 通过一批UID获取用户的扩展信息.仅内部使用
     */
    public static function showExtend()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show_extend");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        Comm_Weibo_Api_Util::oneOrOtherMulti($platform, array(array('uids', 'screenName')));

        return $platform;
    }

    /**
     * 获取用户版本信息
     */
    public static function getVersion()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_version");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);

        return $platform;
    }

    /**
     * 批量获取用户的关注数、粉丝数、微博数
     */
    public static function counts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "counts");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "string", true);

        return $platform;
    }

    /**
     * 批量获取用户版本号
     */
    public static function getVersionBatch()
    {
        $cip = Comm_Context::getServer("REMOTE_ADDR");
        $url = Comm_Weibo_Api_Request_IDotT::assembleUrl("", "person", "getversioninfos", $cip);
        $idott = new Comm_Weibo_Api_Request_IDotT($url, "GET");
        $idott->addRule("uids", "string", true);

        return $idott;
    }

    /**
     *
     * 获取用户的类型
     * @param int64 $uid
     */
    public static function getUserType()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "state");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);

        return $platform;
    }

    /**
     * 冻结用户解冻,修改用户状态
     * 先使用 i.t接口，上线后再替换
     */
    public static function getUserActiveCode()
    {
        //TODO
        return false;
    }

    /**
     * 屏蔽某用户的feed
     */
    public static function blockUser()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "filter/create");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("uid", "int64", true);

        return $platform;
    }

    /**
     * 判断是否为屏蔽用户
     */
    public static function isBlockUser()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "is_filtered");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uids', 'string', true);

        return $platform;
    }

    /**
     * 获取屏蔽的用户列表
     */
    public static function getFilteredUsers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_filtered");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('page', 'int', false);
        $platform->addRule('count', 'int', false);
        $platform->addRule('trim_status', 'int', false);

        return $platform;
    }

    /**
     * 获取用户等级信息。
     */
    public static function showRank()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy', "urank/show_rank");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'int64', true);

        return $platform;
    }

    /**
     * 获取用户等级详细信息。
     */
    public static function showRankDetail()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl('proxy', "urank/show_detail");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'int64', true);

        return $platform;
    }

    /**
     * 获取用户置顶微博mid
     */
    public static function getTopStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "get_top_status", 'json', null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule('uid', 'string', true);

        return $platform;       
    }
    
    /**
     * 设置用户置顶微博
     */
    public static function setTopStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "set_top_status", 'json', null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule('id', 'string', true);

        return $platform;       
    }
    
    /**
     * 取消用户置顶微博
     */
    public static function cancelTopStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "cancel_top_status", 'json', null, false, '', true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule('id', 'string', true);
        
        return $platform;       
    }
}
