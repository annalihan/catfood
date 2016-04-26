<?php
//TODO 专业版
//Oauth改成OAuth
class Comm_Weibo_OAuth
{
    // 自动授权
    const AUTO_AUTO_URL = "http://i2.api.weibo.com/oauth2/getoken?uid=%s&appkey=%s&auto=%s";

    /**
     * 获取自动授权token
     * 接口返回数据格式 {"oauth_token":"2.00ztpHnB0P2hNu8276e2a0bbH2TjnD","appkey":"830739689","app_secret":"eb75aa3d6330cedc1ca2b27b5f82175d","issued_at":1336967375,"expires":1337572175}
     *
     * @param ing $uid
     *            为哪个用户授权
     * @param int $appkey
     *            待授权的应用的appkey
     * @param string $auto
     *            自动授权标志
     *
     */
    public static function getoken($uid, $appkey, $auto = 'true')
    {
        if (!is_numeric($appkey) || !is_numeric($uid))
        {
            throw new Comm_Exception_Program('parameter error');
        }
        $url = sprintf(self::AUTO_AUTO_URL, $uid, $appkey, $auto);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if (isset($result ['error']))
        {
            return array();
        }
        return $result;
    }
}
