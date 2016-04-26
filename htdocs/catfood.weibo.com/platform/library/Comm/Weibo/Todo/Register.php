<?php
//TODO 
class Comm_Weibo_Register
{

    const VERIFY_V4_URL = "http://i.verified.weibo.com/verify";
    const EDIT_USER_LEVEL = "http://i2.api.weibo.com/2/account/verified_info/update.json"; // 修改用level
    const EPS_INFO_GET_URL = "/apply/get?uid=%s&appid=%d&cip=%s&appkey=908033280";
    const EPS_INFO_SET_URL = "/ent/setnew?uid=%s&appid=%d&cip=%s&appkey=908033280";
    const SUBMIT_2_VERIFY = "/ent/regnew";
    const EDIT_ULEVEL_OLD = "http://i.t.sina.com.cn/person/edituserlevel.php?uid=%s&level=%s&viptitle=%s&appid=%s&cip=%s";
    const CHECK_USER = "http://i2.api.weibo.com/2/register/verify_nickname.json?source=908033280&nickname=%s"; // 验证昵称是否占用
    const EDIT_NICKNAME = "http://i2.api.weibo.com/2/account/profile/basic_update.json";

    /**
     * 注册企业微博
     *
     * @param int $uid
     *            array $datas 修改的企业基本资料项
     * @return array
     */
    public static function regUser($uid, $datas)
    {
        $url = self::VERIFY_V4_URL . self::SUBMIT_2_VERIFY;
        $cip = Comm_Context::getClientIp();
        $appid = Comm_Config::get('env.idott_api_appid');
        $appkey = Comm_Config::get('env.platform_api_source');
        // 希望的昵称
        $url .= '?uid=' . $uid . '&appid=' . $appid . '&cip=' . $cip . '&appkey=' . $appkey;

        $response = Tool_Http::post($url, $datas);
        $result = json_decode($response, true);

        if ($result ['errno'] != 1)
        {
            // Tool_Log_Commlog::write_log("API_DATA_ERROR", $url.'|'.$response);
            return $result;
        }
        return $result;
    }

    /**
     * 修改注册用户 Level 值
     *
     * @param int $uid
     *            int $level 修改的level级别
     * @return bool
     */
    public static function editLevel($uid, $level)
    {
        if (empty($uid) || empty($level))
            return false;
        $ip = Comm_Context::getClientIp();
        $appkey = "908033280";

        $data = array(
            'source' => $appkey,
            'uid' => $uid,
            'level' => $level,
            'cuid' => $uid,
            'ip' => $ip,
            'signature' => '934da34a0bea7e486e55c4f50fc04e23'
        );

        $url = self::EDIT_USER_LEVEL;
        $result = '';
        $response = Tool_Http::post($url, $data);
        $result = json_decode($response, true);
        if (empty($result) || isset($result ['error_code']))
        {
            return false;
        }
        return array(
            'errno' => 1,
            'errmsg' => '用户level信息修改成功'
        );
        // return true;
    }

    /**
     * 根据uid 获取用户信息
     *
     * @param int $uid
     * @return array
     */
    public static function getEpsInfo($uid)
    {
        if (empty($uid) || !is_numeric($uid))
            return false;
        $res = '';
        $cip = Comm_Context::getClientIp();
        $appid = Comm_Config::get('env.idott_api_appid');
        $appkey = Comm_Config::get('env.platform_api_source');

        $postData = array(
            'uid' => $uid
        );

        $url = sprintf(self::VERIFY_V4_URL . self::EPS_INFO_GET_URL, $uid, $appid, $cip);
        // $bRet = $this->requestUrlByPost($url,$postData, $res);
        $bRet = Tool_Http::post($url, $postData);
        // echo '<pre>';print_r($bRet);exit;
        if ($bRet)
        {
            $result = json_decode($bRet, true);
            if ($result ['errno'] != 1)
            {
                return false;
            }
            $result = $result ['result'];
            // $mc->set($key, $result, MEMCACHE_COMPRESSED, 300);
        }
        else
        {
            $result = false;
        }
        // echo '<pre>';print_r($result);exit;
        return $result;
    }

    /**
     * 验证昵称是否被占用
     *
     * @param
     *            string nickname
     * @return array
     */
    public static function checkNickname($nickname)
    {
        if (empty($nickname))
            return false;

        $url = sprintf(self::CHECK_USER, $nickname);
        $bRet = Tool_Http::GET($url);

        if ($bRet)
        {
            $result = json_decode($bRet, true);
            if ($result ['error_code'])
            {
                return false;
            }
        }
        else
        {
            $result = false;
        }
        return $result;
    }

    public static function editNickname($nickname)
    {
        if (empty($nickname))
            return false;

        $postData = array(
            'source' => '908033280',
            'screen_name' => $nickname
        );

        $bRet = self::fetchPostResponseResult(self::EDIT_NICKNAME, $postData);
    }
}
