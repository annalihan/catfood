<?php
//TODO 专业版
class Comm_Weibo_OfficialApp
{
    const GET_APP_INFO_URL = 'http://api.apps.sina.cn/interface/api_showapp_info.php?id=%s&ua=%s';
    
    public static function getInfo($appid, $ua)
    {
        if (empty($appid) || empty($ua))
        {
            return array();
        }

        $url = sprintf(self::GET_APP_INFO_URL, $appid, $ua);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result)
        {
            return $result;
        }
        else
        {
            return array();
        }
    }
}
