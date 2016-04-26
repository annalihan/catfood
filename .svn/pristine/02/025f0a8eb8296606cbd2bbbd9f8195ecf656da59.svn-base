<?php
//TODO 专业版
class Comm_Weibo_Guidance
{
    // 添加用户到白名单
    const ADD_EPS_WHITE_LIST_URL = 'http://ls.mps.weibo.com/admin/white/adduser/?vid=%u&uid=%s';
    // 检测用户是否存在于白名单中
    const GET_NEW_WHITE_LIST_URL = 'http://ls.mps.weibo.com/interface/checkstat/?pid=%u&vid=%u&uid=%s&type=%u';
    // 从白名单中删除用户
    const DEL_EPS_WHITE_LIST_URL = 'http://ls.mps.weibo.com/admin/white/deluser/?vid=%u&uid=%s';

    /**
     * 添加用户至白名单
     * --- 用户完成必经步骤后，将该用户加入v2版本白名单
     *
     * @param int $uid
     * @return boolean
     */
    public function addEpsv2WhiteList($uid)
    {
        if (!is_numeric($uid))
        {
            throw new Comm_Exception_Program('argument $uid must not empty');
        }

        $url = sprintf(self::ADD_EPS_WHITE_LIST_URL, 87, $uid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        
        if ($result ['code'] == 'A00000')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * 检测用户是否存在于白名单中
     *
     * @param int $uid
     * @param int $type
     *            : 1检测白名单，0检测黑名单
     * @return boolean
     */
    public function checkEpsIsWhite($uid, $type = 1)
    {
        $url = sprintf(self::GET_NEW_WHITE_LIST_URL, 100, 87, $uid, 1);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);

        if ($result['code'] == 'A00000' && $result['data']['user'] == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * 检查用户是否切换到V5
     *
     * @param unknown_type $uid
     * @return boolean
     */
    public function checkEpsIsV5($uid)
    {
        $url = 'http://ls.mps.weibo.com/interface/checkstat/?pid=100&vid=129&type=1&uid=' . $uid;
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['code'] == 'A00000' && $result['data']['user'] == 1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
