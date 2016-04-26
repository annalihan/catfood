<?php
class Comm_Weibo_Api_Invite
{
    const RESOURCE = 'invite';
    const CONNECT_TIMEOUT = 3000;
    const TIMEOUT = 3000;

    /**
     * 发送邀请
     */
    public static function send()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'send', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        $request->addRule('uid', 'int64', true);
        $request->addRule('to_uids', 'string', true);
        $request->addSetCallback('to_uid', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ',', 5));
        $request->addRule('type', 'string', true);
        $request->addRule('value', 'int', false);
        $request->addBeforeSendCallback('Comm_Weibo_Api_Invite', "checkSendValue", array($request));
        $request->addRule('content', 'string', false);
        $request->addRule('question', 'int', false);
        $request->addRule('answer', 'string', false);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 获取某个用户邀请隐私
     */
    public static function privacyGet()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'privacy/get', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');

        $request->addRule('target_id', 'int64', false);
        $request->addRule('type', 'string', false);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    /**
     * 检查邀请隐私
     */
    public static function check()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'check', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');

        $request->addRule('uid', 'int64', true);
        $request->addRule('to_uids', 'string', true);
        $request->addRule('type', 'string', true);
        $request->addRule('value', 'int', false);
        $request->addBeforeSendCallback('Comm_Weibo_Api_Invite', "checkSendValue", array($request));
        $request->setRequestTimeout(self::CONNECT_TIMEOUT, self::TIMEOUT);

        return $request;
    }

    public static function checkSendValue($request)
    {
        if (!is_null($request->type) && $request->type == 'game')
        {
            if (is_null($request->value))
            {
                throw new Comm_Exception_Program('the parameter value must be set!');
            }
        }
    }
}
