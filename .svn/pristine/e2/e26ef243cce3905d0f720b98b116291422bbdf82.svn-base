<?php
class Comm_Weibo_Api_Game
{
    const RESOURCE = 'proxy/game';

    /**
     * 获取用户的游戏列表
     */
    public static function games()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', false);
        $request->addRule('trim_achv', 'boolean', false);
        $request->supportPagination();
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 获取指定用户共有的游戏列表
     */
    public static function gamesInCommon()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games/in_common');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uid', 'int64', true);
        $request->supportPagination();

        return $request;
    }

    /**
     * 获取推荐游戏
     */
    public static function gamesSuggestions()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games/suggestions');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 获取热门游戏
     */
    public static function gamesHot()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games/hot');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 获取当前登录用户可能感兴趣的游戏列表
     */
    public static function gamesMayInterested()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games/may_interested');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportPagination();

        return $request;
    }

    /**
     * 查询用户剩余微币数量
     */
    public static function gamesSurplusCoins()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'games/surplus_coins');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('uids', 'string', true);
        $request->addSetCallback('uids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 100));

        return $request;
    }
}
