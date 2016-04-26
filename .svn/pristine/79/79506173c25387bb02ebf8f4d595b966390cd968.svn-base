<?php
class Comm_Weibo_Api_Sass
{
    public static function checkMblog()
    {
        $url = "http://safe.i.t.sina.com.cn/v4/common.php";
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('s', 'string', true);
        $request->addRule('t', 'int', true);
        $request->addRule('a', 'string', true);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }

    /**
     * 获取用户是否通过身份验证
     */
    public static function retrieveVerify()
    {
        $url = "http://safe.i.t.sina.com.cn/api/retrieveRealVerify_JSON.php";
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('u', 'int', true);
        $request->setRequestTimeout(2000, 2000);

        return $request;
    }
}
