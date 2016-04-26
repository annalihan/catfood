<?php
class Comm_Weibo_Api_Pub
{
    const RESOURCE = 'pub';

    /**
     * 首页发布器右上角话题
     */
    public static function recommendIssueTopic()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'recommend/issue_topic');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        return $request;
    }

    /**
     * 首页右侧热点话题
     */
    public static function recommendTopTopics()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'recommend/top_topics');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('pid', 'int', true);
        $request->addRule('cid', 'int', true);
        $request->addRule('num', 'int', true);

        return $request;
    }

    /**
     * 获取发微博引导气泡话题
     */
    public static function getPublishBubbleTrend()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'recommend/topic_popup');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        return $request;
    }
}
