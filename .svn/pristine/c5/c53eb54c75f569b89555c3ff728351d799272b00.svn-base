<?php
//TODO
class Comm_Weibo_Api_ThirdModule
{
    public static function getHtml($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "GET");
        $platform->addRule("cuid", "int64");
        $platform->addRule("ouid", "int64");
        $platform->addRule("lang", "string");
        $platform->addRule("gender", "string");
        $platform->addRule("version", "string");
        $platform->addRule("province", "int64");
        $platform->addRule("city", "int64");
        $platform->addRule("ip", "string");
        $platform->addRule("mid", "string");
        $platform->addRule("url", "string");

        return $platform;
    }

    /**
     * feed区域对展开的标准接口 请求地址
     * @param string $url 接口的地址
     */
    public static function getApiRenderFeed($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('cuid', 'int64');
        $platform->addRule("url_short", "string", true);
        $platform->addRule("url_long", "string", true);
        $platform->addRule("type", "int", true);
        $platform->addRule("metadata", "string", true);
        $platform->addRule("source", "int", true);
        $platform->addRule("lang", "string", true);
        $platform->addRule("cip", "string", false);

        return $platform;

    }

    /**
     * 短链接展开后内容交互、发布器插件展开及展开交互
     * @param string $url 请求的url地址
     */
    public static function getApiInteractive($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('cuid', 'int64');
        $platform->addRule("type", "int", true);
        $platform->addRule("action", "int", true);
        $platform->addRule("source", "int", true);
        $platform->addRule("lang", "string", true);
        $platform->addRule("cip", "string", false);
        $platform->addRule("actiondata", "string", false);

        return $platform;
    }

    /**
     * 政府项目接口1
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function getGovernmentTalkRenderFeed($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('cuid', 'int64');
        $platform->addRule("url_short", "string", true);
        $platform->addRule("url_long", "string", true);
        $platform->addRule("type", "int", true);
        $platform->addRule("source", "int", true);
        $platform->addRule("lang", "string", true);
        $platform->addRule("short_info", "string", true);
        //$platform->setRequestTimeout(5000, 5000);
        
        return $platform;
    }
    
    /**
     * 政府项目接口
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function getGovernmentTalkInteractive($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('cuid', 'int64');
        $platform->addRule("type", "int", true);
        $platform->addRule("source", "int", true);
        $platform->addRule("lang", "string", true);
        $platform->addRule("cip", "string", false);
        $platform->addRule("actiondata", "string", false);

        return $platform;
    }

    /**
     * 精选微博
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function getEssenceWeibo($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('class_id', 'int64', true);
        $platform->addRule("is_pic", "int64");
        $platform->addRule("count", "int");
        //$platform->setRequestTimeout(5000, 5000);
        
        return $platform;
    }

    /**
     * 设置用户的总抽奖机会
     * @param [type] $url [description]
     */
    public static function setGameTimes($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('zid', 'int64', true);
        $platform->addRule("uid", "string", true);
        $platform->addRule("num", "int", true);
        $platform->addRule("sign", "string", true);
        $platform->setRequestTimeout(5000, 5000);
        
        return $platform;
    }

    /**
     * 获取某用户在某活动中玩游戏的次数及剩余的次数
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function checkPrize($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('eid', 'int64', true);
        $platform->addRule("uid", "int64", true);
        
        return $platform;
    }

    /**
     * 获取某用户在某活动中玩游戏的次数及剩余的次数
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function fetchRewardList($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "GET");
        $platform->addRule('eid', 'int64', true);
        $platform->addRule("page", "int");
        $platform->addRule("count", "int");
        $platform->addRule("user", "string");

        return $platform;
    }

    /**
     * 我的获奖列表
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function myAwardList($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "GET");
        $platform->addRule('eid', 'int64', true);
        $platform->addRule("uid", 'int64', true);
        $platform->addRule("page", "int");
        $platform->addRule("count", "int");

        return $platform;
    }

    /**
     * 抽奖接口
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function prize($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "POST");
        $platform->addRule('eid', 'int64', true);
        $platform->addRule("uid", 'int64', true);
        $platform->addRule("ip", "string", true);

        return $platform;
    }

    /**
     * 热评微博
     * @param  [type] $url [description]
     * @return [type]      [description]
     */
    public static function getHotCommendWeibo($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, "GET");
        $platform->addRule('uid', 'int64', true);
        $platform->addRule("num", "int");
        
        return $platform;
    }

    /**
     * 获取广告特型feed模板
     * @param string $url  api url
     * @return object
     */
    public static function getBpFeed($url)
    {
        $platform = new Comm_Weibo_Api_Request_ThirdModule($url, 'GET');
        $platform->addRule('uid', 'int64', true);
        $platform->addRule('slstr', 'string', true);
        $platform->addRule("ip", "string", true);
        $platform->addRule('appid', 'int64', true);
        $platform->addRule('wb_version', 'string', true);
        $platform->setRequestTimeout(8000, 8000);

        return $platform;
    }
}
