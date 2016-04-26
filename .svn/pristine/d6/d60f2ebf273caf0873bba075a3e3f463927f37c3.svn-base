<?php
class Comm_Weibo_Api_Search
{
    const RESOURCE = 'search';
    const TIMEOUT = 3000;
    const CONNECT_TIMEOUT = 3000;

    /**
     * 搜索用户时的即时搜索建议
     */
    public static function suggestionsUsers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/users');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('sid', 'string', true);
        self::_supportQCount($request, true);

        return $request;
    }

    /**
     * 搜索微博时的即时搜索建议
     */
    public static function suggestionsStatuses()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/statuses');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('sid', 'string', true);
        self::_supportQCount($request, true);

        return $request;
    }

    /**
     * 搜索学校时的即时搜索建议
     */
    public static function suggestionsSchools()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/schools');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('sid', 'string', true);
        self::_supportQCount($request, true);
        $request->addRule('type', 'int', false);
        $request->addRule('dup', 'int');

        return $request;
    }

    /**
     * 搜索公司时的即时搜索建议
     */
    public static function suggestionsCompanies()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/companies');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('sid', 'string', true);
        self::_supportQCount($request, true);

        return $request;
    }

    /**
     * 搜索应用时的即时搜索建议
     */
    public static function suggestionsApps()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/apps');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule("sid", 'string', true);
        self::_supportQCount($request);

        return $request;
    }

    /**
     *  TODO:: request 404?
     * 在@某人时，实时获取用户名建议
     */
    public static function suggestionsAtUsers()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/at_users');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        self::_supportQCount($request, true);
        $request->addRule('type', 'int', true);
        $request->addRule('range', 'int', false);
        $request->addRule('sid', 'string', true);

        return $request;
    }

    /**
     * 综合联想搜索，给出符合的用户、微群以及应用搜索建议
     */
    public static function suggestionsIntegrate()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'suggestions/integrate');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', true);
        $request->addRule('sort_user', 'int', false);
        $request->addRule('sort_app', 'int', false);
        $request->addRule('sort_grp', 'int', false);
        $request->addRule('user_count', 'int', false);
        $request->addRule('app_count', 'int', false);
        $request->addRule('grp_count', 'int', false);

        return $request;
    }

    /**
     * user_timeline的高级搜索接口
     */
    public static function statusesUserTimelineSp()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/user_timeline_sp');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', false);
        $request->supportPagination('start', 'num');

        return $request;
    }

    /**
     * user_timeline的高级搜索接口 (新方式，简化query串的内容分解为参数传递)
     */
    public static function statusesUserTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/user_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('sid', 'string', true);
        $request->addRule('uid', 'int64', false);
        self::_addTimelineRules($request);
        $request->addRule('ids', 'string', false);
        $request->addSetCallback('ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', '~', 2000));
        $request->addRuleMethod('ids', 'POST');
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * friends_timeline的高级搜索接口 (旧接口方式)
     */
    public static function statusesFriendsTimelineSp()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/friends_timeline_sp');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        // $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', false);
        $request->addRule('gid', 'int64', false);
        $request->supportPagination('start', 'num');

        return $request;
    }

    /**
     * friends_timeline的高级搜索接口 (新方式，简化query串的内容分解为参数传递)
     */
    public static function statusesFriendsTimeline()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/friends_timeline');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        self::_addTimelineRules($request);
        $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', false);
        $request->addRule('gid', 'int64', false);
        $request->addBeforeSendCallback('Comm_Weibo_Api_Util', "checkAlternative", array('uid', 'gid'));
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 评论搜索 范围是人名与评论内容 (旧接口方式)
     */
    public static function statusesComments()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/comments');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', false);
        $request->supportPagination('start', 'num');

        return $request;
    }

    /**
     * 评论搜索范围是人名与评论内容(新方式，简化query串的内容分解为参数传递)
     */
    public static function comments()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'comments');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        self::_addCommentsRules($request);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 搜索评论人 即在评论箱（发出以及接收到的）中的联系人的搜索
     */
    public static function usersCommentsUsersSp()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'users/comments_users_sp');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('query', 'string', true);
        $request->addRule('uid', 'int64', false);
        $request->supportPagination('start', 'num');
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 搜索用户
     */
    public static function users()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'users');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('q', 'string', false);
        $request->addRule('snick', 'int', false);
        $request->addRule('sdomain', 'int', false);
        $request->addRule('sintro', 'int', false);
        $request->addRule('stag', 'int', false);
        $request->addRule('province', 'int', false);
        $request->addRule('city', 'int', false);
        $request->addRule('gender', 'string', false);
        $request->addRule('comorsch', 'string', false);
        $request->addRule('sort', 'int', false);
        $request->supportPagination();
        $request->supportBaseApp();
        $request->addRule('callback', 'string', false);

        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 提到我的微博的高级搜索接口
     */
    public static function statusesMentions ()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses/mentions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        self::_addTimelineRules($request);
        $request->addRule('atme', 'int', true);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 提到我的评论的高级搜索接口
     */
    public static function commentsMentions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'comments/mentions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        self::_addCommentsRules($request);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 私信搜索
     */
    public static function directMessages()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'directMessages');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('key', 'string', true);
        $request->addRule('cuid', 'int64', true);
        self::_addSidRule($request);
        $request->supportPagination('start', 'num');
        $request->addRule('isred', 'int', false);
        $request->addRule('startime', 'int64', false);
        $request->addRule('endtime', 'int64', false);
        $request->addRule('type', 'int', false);
        $request->addRule('contact', 'int', false);

        $request->setRequestTimeout(4000, 4000);

        return $request;
    }

    /**
     * 收藏搜索
     */
    public static function statusesFavorites()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "statuses/favorites");
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule("key", "string", true);
        $request->addRule("cuid", "int64", true);
        $request->addRule("sid", "string", true);
        $request->supportPagination("start", "num");
        $request->addRule("isred", "int");
        $request->addRule("istag", "int");
        $request->addRule("onlytotal", "int");
        $request->addRule("onlyid", "int");
        $request->addRule("contact", "int");
        $request->addRule("uid", "int64");

        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 微号搜索
     * Enter description here ...
     */
    public static function suggestionsWeihao()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "suggestions/weihao");
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule("q", 'string', true);
        $request->addRule('count', 'int', false);
        return $request;
    }

    /**
     * 添加关键字和搜索结果数规则
     * @param Comm_Weibo_Api_Request_Platform $request
     * @param boolean $q
     * @param boolean $count
     */
    private static function _supportQCount(Comm_Weibo_Api_Request_Platform $request, $q = false, $count = false)
    {
        $request->addRule('q', 'string', $q);
        $request->addRule('count', 'int', $count);
    }

    /**
     * 添加搜索标识sid
     * @param Comm_Weibo_Api_Request_Platform $request
     */
    private static function _addSidRule(Comm_Weibo_Api_Request_Platform $request)
    {
        $request->addRule('sid', 'string', true);
    }

    /**
     * 添加搜索timeline通用参数规则
     * @param Comm_Weibo_Api_Request_Platform $request
     */
    private static function _addTimelineRules(Comm_Weibo_Api_Request_Platform $request)
    {
        $request->addRule('key', 'string', false);
        $request->addRule('cuid', 'int64', false);
        self::_addSidRule($request);
        $request->supportPagination('start', 'num');
        $request->addRule('isred', 'int', false);
        $request->addRule('xsort', 'int', false);
        $request->addRule('zone', 'string', false);
        $request->addRule('starttime', 'int64', false);
        $request->addRule('endtime', 'int64', false);
        $request->addRule('haspic', 'int', false);
        $request->addRule('haslink', 'int', false);
        $request->addRule('hasori', 'int', false);
        $request->addRule('hasret', 'int', false);
        $request->addRule('hasat', 'int', false);
        $request->addRule('hasvideo', 'int', false);
        $request->addRule('hasmusic', 'int', false);
        $request->addRule('hastext', 'int', false);
        $request->addRule('appid', 'int', false);
        $request->addRule('nofilter', 'int', false);
        $request->addRule('istag', 'int', false);
        $request->addRule('status', 'int', false);
        $request->addRule('onlytotal', 'int', false);
        $request->addRule('onlymid', 'int', false);
    }

    /**
     * 添加搜索comments通用参数规则
     * @param Comm_Weibo_Api_Request_Platform $request
     */
    private static function _addCommentsRules(Comm_Weibo_Api_Request_Platform $request)
    {
        $request->addRule('key', 'string', true);
        $request->addRule('cuid', 'int64', true);
        self::_addSidRule($request);
        $request->addRule('uid', 'int64', false);
        $request->supportPagination('start', 'num');
        $request->addRule('isred', 'int', false);
        $request->addRule('atme', 'int', false);
        $request->addRule('startime', 'int64', false);
        $request->addRule('endtime', 'int64', false);
        $request->addRule('type', 'int', false);
        $request->addRule('contact', 'int', false);
    }

    public static function statusesSearch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'statuses');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('q', 'string', false);
        $request->addRule('page', 'int', false);
        $request->addRule('sort', 'string', false);
        $request->addRule('atten', 'int', false);
        $request->addRule('atme', 'int', false);
        $request->addRule('count', 'int', false);
        self::_addTimelineRules($request);
        //$request->addRule('ids', 'string', false);
        //$request->addSetCallback('ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', '~', 2000));
        //$request->addRuleMethod('ids', 'POST');

        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }
}
