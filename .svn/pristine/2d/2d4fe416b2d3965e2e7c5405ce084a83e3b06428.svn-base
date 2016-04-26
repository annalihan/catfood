<?php
class Comm_Weibo_Api_Topic
{
    const RESOURCE = "trends";

    /**
     * 获取某人关注的话题
     */
    public static function getTrends()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, null);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("has_num", "int64", false);
        $platform->supportPagination();

        return $platform;
    }

    /**
     * 判断是否关注某话题
     */
    public static function isFollow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "is_follow");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("trend_name", "string", true);

        return $platform;
    }

    /**
     * 返回最近一小时内的热门话题
     */
    public static function hourly()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "hourly");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportBaseApp();

        return $platform;
    }

    /**
     * 返回最近一天内的热门话题
     */
    public static function daily()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "daily");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportBaseApp();

        return $platform;
    }

    /**
     * 返回最近一周内的热门话题
     */
    public static function weekly()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "weekly");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportBaseApp();

        return $platform;
    }

    /**
     * 关注某话题
     */
    public static function follow()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "follow");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("trend_name", "string", true);

        return $platform;
    }

    /**
     * 取消关注的某一个话题
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "destroy");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "DELETE");
        $platform->addRule("trend_id", "string", true);

        return $platform;
    }

    /**
     * 首页右侧热门话题(主站专用,带推荐位)
     */
    public static function hotSp()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "hot_sp");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->addRule("pid", "int", true);
        $platform->addRule("cid", "int", true);

        return $platform;
    }

    /**
     * 发布框推荐话题
     * 
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function publisherTopic()
    {
        $url = "http://interface.recom.i.t.sina.com.cn/guide_topic/get_guide_topic.py";
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);
        $platform->addRule("pid", "int", true);
        $platform->addRule("cid", "int", true);
        $platform->addRule("num", "int", true);
        $platform->addRule("app", "int", true);

        return $platform;
    }

    public static function sharpSuggestions()
    {
        $url = 'http://i2.api.weibo.com/2/proxy/huati/suggestions/list.json';
        $platform = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $platform->addRule("uid", "int64", true);

        return $platform;
    }
}
