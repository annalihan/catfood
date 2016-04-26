<?php
//TODO 专业版
class Comm_Weibo_Subscribe
{
    // 查询是否订阅
    const GET_SUBSCRIBE = "http://smsmarket.intra.route.sina.com.cn/?m=interface&c=eweibo&a=status&fromuid=%s&touid=%s";
    // 查询是否安装
    const IS_INSTALL = "http://smsmarket.intra.route.sina.com.cn/?m=interface&c=eweibo&a=isappuser&euid=%s";

    /**
     * 查询是否订阅
     *
     * @param $uid int
     *            当前登录用户
     * @param $touid 对应企业用户
     */
    public static function getSubscribe($uid, $touid)
    {
        if (empty($uid) || !is_numeric($uid) || empty($touid) || !is_numeric($touid))
        {
            throw new Comm_Exception_Program('argument $uid and $touid must be not empty and is numeric');
        }

        $url = sprintf(self::GET_SUBSCRIBE, $uid, $touid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if (isset($result ['errno']) && $result ['errno'] != 1)
        {
            Tool_Log::error($url . '|' . $response);
            return array();
        }
        return $result;
    }

    /**
     * 查询是否企业是否安装
     *
     * @param unknown_type $euid
     */
    public static function isInstaller($euid)
    {
        if (empty($euid) || !is_numeric($euid))
        {
            throw new Comm_Exception_Program('argument $euid must be not empty and is numeric');
        }

        $url = sprintf(self::IS_INSTALL, $euid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        return $result;
    }
}
