<?php
class Comm_Weibo_Api_Messages
{
    const RESOURCE = "direct_messages";
    const RESOURCE_SEARCH = "search";

    /**
     * 获取某个用户最新的私信列表
     */
    public static function getNewList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, null);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->supportCursor();
        $plateform->supportPagination();

        return $plateform;
    }

    /**
     * 可以通过时间索引获取私信 的信息
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getNewList2()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, '', 'json', null, false, '', true);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("from", "int64");
        $plateform->addRule("end", "int64");
        $plateform->supportCursor();
        $plateform->supportPagination();

        return $plateform;
    }
    
    /**
     * 获取当前用户收到的所有私信，包括单发私信、单发留言、群发私信
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getReceiveList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'fans_service/receive', 'json', null, false, '', true);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("since", "int64");
        $plateform->addRule("end", "int64");
        $plateform->addRule("cursor", "int64");
        $plateform->addRule("count", "int");
        $plateform->addRule("is_encoded", "int");
        
        return $plateform;
    }
    
    /**
     * 获取当前用户与某用户的所有会话，包括单发私信、单发留言、群发私信
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getConversationList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'fans_service/conversation', 'json', null, false, '', true);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("uid", "int64", true);
        $plateform->addRule("cursor", "int");
        $plateform->addRule("count", "int");
        $plateform->addRule("is_encoded", "int");
        
        return $plateform;
    }
    
    /**
     * 获取当前用户的私信搜索结果
     * @return Comm_Weibo_Api_Request_Platform
     */
    public static function getSearchedMsgList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE_SEARCH, 'direct_messages', 'json', null, false, '', true);
        $plateform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $plateform->addRule("key", "string", true);
        $plateform->addRule("cuid", "int64", true);
        $plateform->addRule("sid", "string", true);
        $plateform->addRule("start", "int");
        $plateform->addRule("num", "int");
        $plateform->addRule("isred", "int");
        $plateform->addRule("startime", "int64");
        $plateform->addRule("endtime", "int64");
        $plateform->addRule("type", "int");
        $plateform->addRule("contact", "int");
        
        return $plateform;
    }
    
    /**
     * 获取当前用户发送的最新私信列表
     */
    public static function sendList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "sent");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->supportCursor();
        $platform->supportPagination();

        return $platform;
    }

    /**
     * 获取与当前登录用户有私信往来的用户列表
     */
    public static function userList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "user_list");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("count", "int");
        $platform->addRule("cursor", "int");

        return $platform;
    }

    /**
     *
     * 获取与指定用户的往来私信列表
     */
    public static function conversation()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "conversation");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);
        $platform->supportCursor();
        $platform->supportPagination();

        return $platform;
    }

    /**
     *
     * 获取与指定用户的往来私信列表(i.t接口)
     */
    public static function getChatMessage($cip)
    {
        $url = Comm_Weibo_Api_Request_Idott::assembleUrl('', 'message', 'getchatmessage', $cip);
        $idott = new Comm_Weibo_Api_Request_Idott($url, "GET");
        $idott->addRule("fuid", "int64", true);
        $idott->addRule("page", "int", false);
        $idott->addRule("pagesize", "int", false);

        return $idott;
    }

    /**
     * 获取与当前登录用户有私信往来的用户列表(i.t接口)
     * @param 当前用户IP $cip
     */
    public static function getMessageList($cip)
    {
        $url = Comm_Weibo_Api_Request_Idott::assembleUrl('', 'message', 'getmessagelist', $cip);
        $idott = new Comm_Weibo_Api_Request_Idott($url, "GET");
        $idott->addRule("fuid", "int64", true);
        $idott->addRule("page", "int", false);
        $idott->addRule("pagesize", "int", false);

        return $idott;
    }

    /*
     * 发一条私信
     */
    public static function newMessage()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "new");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $callBackObj = new Comm_Weibo_Api_Messages;
        $platform->uidOrScreenName();
        $platform->addRule("text", "string", true);
        $platform->addRule("fids", "string", false);
        $platform->addRule("id", "int64", false);
        $platform->addRule('skip_check', 'int');
        //由于saas检测会有超时情况，临时调整接口超时时间为2s
        $platform->setRequestTimeout(5000, 5000);

        return $platform;
    }

    /*
     * 删除一条私信
     */
    public static function destroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "destroy");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "POST");
        $platform->addRule("id", "int64", true);
        $platform->setRequestTimeout(5000, 5000);
        return $platform;
    }

    /*
     * 批量删除私信
     */
    public static function destroyBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "destroy_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "DELETE");

        $oneOrOther = array(
            array('ids', 'uid'),
        );

        Comm_Weibo_Api_Util::oneOrOtherMulti($platform, $oneOrOther);
        $platform->addSetCallback('ids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $platform->addSetCallback('uid', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $platform->setRequestTimeout(5000, 5000);

        return $platform;
    }

    /**
     * 根据私信ID批量获取私信内容
     */
    public static function showBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "show_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("dmids", "string", true);
        $platform->addSetCallback('dmids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 50));

        return $platform;
    }

    /**
     * 判断当前登录用户是否可以给对方发私信。
     */
    public static function isCapable()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "is_capable");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uid", "int64", true);

        return $platform;
    }

    /**
     * 批量获取对话私信总数和未读数。
     */
    public static function countBatch()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "count_batch");
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("uids", "string", true);
        $platform->addRule("type", "string", true);

        return $platform;
    }
}
