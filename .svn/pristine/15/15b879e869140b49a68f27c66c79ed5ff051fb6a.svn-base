<?php
class Comm_Weibo_Api_Tabs
{
    const RESOURCE = 'tabs';

    /**
     * 获取安装的tab_id等信息
     */
    public static function getInstallList()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'install_list', 'json', null, false, '', true);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule("uid", "int64", true);

        return $request;
    }
}