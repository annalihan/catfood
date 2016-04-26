<?php
class Comm_Weibo_Api_WeiboVote
{
    const RESOURCE = "weibonew";

    /**
     * 获取投票
     */
    public static function detail()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'detail');
        $request->supportCuid();
        $request->addRule("poll_id", "int", true);
        $request->addRule("ptype", "int", false);
        $request->addRule("sh", "int", false);
        $request->addRule("data_json", "int", false);
        $request->supportFrom();
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 获取投票列表
     */
    public static function mylist()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'mylist');
        $request->supportCuid();
        $request->addRule("page", "int", true);
        $request->addRule("count", "int", true);
        $request->supportFrom();

        return $request;
    }

    /**
     * 获取投票创建页面
     */
    public static function create()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'create');
        $request->supportFrom();

        return $request;
    }

    /**
     * 获取投票标题和发起人信息
     */
    public static function summary()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'summay');
        $request->addRule('poll_id', 'int', true);

        return $request;
    }

    /**
     * 创建投票
     */
    public static function submitCreate()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'submit_create');
        //$request->supportCuid();
        $request->addRule('cuid', 'int64', true);
        $request->addRule('title', 'string', true);
        $request->addRule('pid', 'string', false);
        $request->addRule('info', 'string', false);
        $request->addRule('vote_result', 'int', true);
        $request->addRule('num', 'int', true);
        $request->addRule('ip', 'string', true);
        $request->addRule('date', 'string', true);
        $request->addRule('hh', 'string', true);
        $request->addRule('mm', 'string', true);
        $request->addRule('items', 'string', true);
        //$request->addSetCallback('items', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('string', ','));
        $request->addRule('verified', 'int', false);
        $request->addRule('poll_category', 'string', false);
        $request->supportFrom();

        return $request;
    }

    /**
     * 参与投票
     */
    public static function joined()
    {
        $url = Comm_Weibo_Api_Request_Vote::assembleVoteApiUrl(self::RESOURCE);
        $request = new Comm_Weibo_Api_Request_Vote($url, "POST", 'joined');
        $request->supportCuid();
        $request->addRule('poll_id', 'int', true);
        $request->addRule('item_id', 'string', true);
        $request->addRule('ip', 'string', true);
        $request->addSetCallback('item_id', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int', ','));
        $request->addRule('anonymous', 'int', false);
        $request->addRule('share', 'int', false);
        $request->addRule('verified', 'int', false);

        return $request;
    }
}
