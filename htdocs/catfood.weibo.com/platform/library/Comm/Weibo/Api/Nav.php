<?php
class Comm_Weibo_Api_Nav
{
    const RESOURCE = 'nav';

    /**
     * 获取导航列表
     * @deprecated
     */
    public static function navList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'list', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('category_ids', 'string', false);
        $request->addSetCallback('category_ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int', ','));
        $request->addRule('lang', 'string', false);

        return $request;
    }

    /**
     * 获取左导应用/游戏列表
     */
    public static function leftNavList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'items', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('user_id', "int64", true);
        $request->addRule('category_id', 'string', true);
        $request->addRule('with_time', 'string', false);

        return $request;
    }

    /**
     * 获取顶导应用列表
     */
    public static function topNavList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'list_apps', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('user_id', 'string', true);

        return $request;
    }

    /**
     * 获取首页右侧玩转微博
     * */
    public static function rightFunList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'fun_apps', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 获取游戏左侧导航列表
     */
    public static function listGames()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'list_games', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('user_id', 'int64', true);
        $request->addRule('count', 'int', true);

        return $request;
    }
}
