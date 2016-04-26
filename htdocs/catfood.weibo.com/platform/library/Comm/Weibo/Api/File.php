<?php
class Comm_Weibo_Api_File
{
    const RESOURCE = 'file';

    //通过私信转发或者分享私信附件
    const CONNECT_TIMEOUT_ATTACHMENT_REPOST = 10000;
    const TIMEOUT_ATTACHMENT_REPOST = 10000;

    //获取上传文件需要的签名
    const CONNECT_TIMEOUT_ATTACHMENT_UPLOAD_SIGN = 10000;
    const TIMEOUT_ATTACHMENT_UPLOAD_SIGN = 10000;

    //通知微盘文件上传完成
    const CONNECT_TIMEOUT_ATTACHMENT_UPLOAD_BACK = 180000;
    const TIMEOUT_ATTACHMENT_UPLOAD_BACK = 180000;

    //获取一个已上传的附件的信息
    const CONNECT_TIMEOUT_ATTACHMENT_INFO = 10000;
    const TIMEOUT_ATTACHMENT_INFO = 10000;

    /**
     * 获取单个文件信息接口
     */
    public static function info()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'info');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('fid', 'int', true);

        return $request;
    }

    /**
     * 私信中的附件上传接口
     */
    public static function msgUpload()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'msgupload');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('dir_id', 'int', true);
        $request->addRule('force', 'int', false);
        $request->addRule('touid', 'int64', true);
        $request->addRule('file', 'filepath', true);

        return $request;
    }

    /**
     * 获取文件上传TOKEN
     */
    public static function attachmentGetToken()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/get_token');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('file_name', 'string', true);
        $request->addRule('sha1', 'string', false);

        return $request;
    }

    /**
     * 确认文件上传结束
     */
    public static function attachmentStatus()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/status');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('upload_key', 'string', true);

        return $request;
    }

    /**
     * 获取一个已上传的附件的信息
     */
    public static function attachmentInfo()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/info');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('fid', 'int64', true);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT_ATTACHMENT_INFO, self::TIMEOUT_ATTACHMENT_INFO);

        return $request;
    }

    /**
     * 删除附件
     */
    public static function attachmentDestroy()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/destroy');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('fid', 'int64', true);

        return $request;
    }

    /**
     * 通过私信转发或者分享私信附件
     */
    public static function attachmentRepost()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/repost');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('fids', 'string', true);
        $request->addSetCallback('fids', 'Comm_Weibo_Api_Util', 'checkBatchValues', array('int64', ','));
        $request->addRule('uid', 'int64', true);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT_ATTACHMENT_REPOST, self::TIMEOUT_ATTACHMENT_REPOST);

        return $request;
    }

    /**
     * 大文件上传单片签名接口
     */
    public static function getTokenPart()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/get_token_part');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('upload_key', 'string', true);
        $request->addRule('part_number', 'int', true);
        $request->addRule('md5', 'string', true);

        return $request;
    }

    /**
     * 大文件上传片段合并签名接口
     */
    public static function getTokenMerge()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/get_token_merge');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'POST');
        $request->addRule('upload_key', 'string', true);
        $request->addRule('md5s', 'string', true);

        return $request;
    }

    /**
     * 获取上传文件需要的签名
     */
    public static function attachmentUploadSign ()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/upload_sign');
        $request = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $request->addRule("file_name", "string", true);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT_ATTACHMENT_UPLOAD_SIGN, self::TIMEOUT_ATTACHMENT_UPLOAD_SIGN);

        return $request;
    }

    /**
     * 通知微盘文件上传完成
     */
    public static function attachmentUploadBack()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'attachment/upload_back');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule("file_name", "string", true);
        $request->addRule("key", "string", true);
        $request->setRequestTimeout(self::CONNECT_TIMEOUT_ATTACHMENT_UPLOAD_BACK, self::TIMEOUT_ATTACHMENT_UPLOAD_BACK);

        return $request;
    }

    /**
     * 获取V盘使用情况
     */
    public static function fileQuota()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'file/quota');
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('dir_id', 'int', true);

        return $request;
    }
}
