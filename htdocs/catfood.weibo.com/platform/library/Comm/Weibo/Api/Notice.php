<?php
class Comm_Weibo_Api_Notice
{
    const RESOURCE = "notice";

    /**
     * api不存在
     * 获取用户收到通知列表
     * @param int64 $uid
     * @param int $page
     * @param int $count
     * @return obj
     */
    public static function receiveList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'receive_list', "json", null, false, '', true);
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule('uid', 'int64', true);
        $request->addRule('page', 'int', false);
        $request->addRule('count', 'int', false);
        $request->supportPagination();

        return $request;
    }

    /**
     * api 不存在 ？？？ wiki上存在
     * 发送一条新通知
     * @param string $uids
     * @param string $title
     * @param string $content
     */
    public static function send()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "app_send", "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->addRule("uids", "string", true);
        $request->addRule("title", "string", true);
        $request->addRule("content", "string", true);
        $request->addSetCallback("uids", "Comm_Weibo_Api_Util", "checkBatchValues", array("string", ", ", 1000, 1));
        $request->addSetCallback("title", "Comm_Weibo_Api_Notice", "checkTitle");
        $request->addSetCallback("content", "Comm_Weibo_Api_Notice", "checkContent");
        
        return $request;
    }

    /**
     * 不存在了？
     * 发送一条已经存在的通知
     * @param string $uids
     * @param string $noticeId
     */
    public static function sendId($noticeId)
    {
        $interface = "send/:{$noticeId}";
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $interface, "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->addRule("uids", "string", true);
        $request->addSetCallback("uids", "Comm_Weibo_Api_Util", "checkBatchValues", array("string", ", ", 1000, 1));
        return $request;
    }

    /**
     * 给微博所有用户广播群发一条通知
     * @param unknown_type $title
     * @param unknown_type $content
     */
    public static function sendAll()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "send_all", "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->addRule("title", "string", true);
        $request->addRule("content", "string", true);
        $request->addSetCallback("title", "Comm_Weibo_Api_Notice", "checkTitle");
        $request->addSetCallback("content", "Comm_Weibo_Api_Notice", "checkContent");
        return $request;
    }

    /**
     * 删除一条当前应用发出的通知
     * @param string $noticeId
     */
    public static function deleteNotice($noticeId)
    {
        $interface = ":{$noticeId}";
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $interface, "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "DELETE");
        $request->addRule("_method", "string");
        return $request;
    }

    /**
     * 更新一条当前应用发出的通知
     * @param string $title
     * @param string $content
     * @param string $noticeId
     */
    public static function updateNotice($noticeId)
    {
        $interface = ":{$noticeId}";
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, $interface, "json", null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $request->addRule("title", "string");
        $request->addRule("content", "string");
        $request->addSetCallback("title", "Comm_Weibo_Api_Notice", "checkTitle");
        $request->addSetCallback("content", "Comm_Weibo_Api_Notice", "checkContent");
        return $request;
    }

    /**
     * 检查通知标题长度
     * @param string $title
     */
    public static function checkTitle($title)
    {
        $titleWidth = mb_strwidth($title, "utf-8");
        if ($titleWidth <= 60 && $titleWidth > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 检查通知内容长度
     * @param string $content
     */
    public static function checkContent($content)
    {
        $contentWidth = mb_strwidth($content, "utf-8");
        if ($contentWidth <= 600 && $contentWidth > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
