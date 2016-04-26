<?php
//TODO 专业版
class Comm_Weibo_POI
{
    const POI_STATE_SUCCESS = 1;
    const POI_STATE_FAIL = - 1;
    const POI_STATE_AUDIT = 0;
    // 通过UID获取其认领的POI ID
    // const GET_POI_URL = 'http://api.promotion.gypsii.cn/api/userpois.php?uid=%s&num=%d&offset=%d&ip=%s&state=%d';
    // 这个urllbs那边说已经废弃
    const GET_POI_URL = 'http://i.api.place.weibo.cn/userpois.php?uid=%s&num=%d&offset=%d&ip=%s&state=%d';
    const GET_POIID_URL = 'http://api.place.weibo.cn/getPoiidByUid.php?uid=%s&orderby=ctime';
    const GET_POIID_OFFSET_URL = 'http://api.place.weibo.cn/getPoiidByUid.php?uid=%s&count=%d&page=%d&orderby=ctime';
    const GET_POI_BY_ID_URL = 'http://i.api.place.weibo.cn/place/pois/show.json?poiid=%s&source=%s';

    /**
     * 根据用户UID获取其对应的POI列表
     *
     * @param unknown_type $uid
     * @param string $ip
     *            用户的IP
     * @param int $num
     *            需要多少个
     * @param int $offset
     *            从x开始
     * @param int $state
     *            0 申请中 1 申请已批准（已认领成功） -1 申请已被拒绝
     * @throws Comm_Weibo_Exception_Api
     * @return multitype: mixed
     */
    public static function getPoiByUid($uid, $ip, $num = 100, $offset = 0, $state = 1)
    {
        if (empty($uid) || empty($ip))
        {
            return array();
        }
        $url = sprintf(self::GET_POI_URL, $uid, $num, $offset, $ip, $state);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result ['rsp'] == 1)
        {
            return $result ['data'];
        }
        else
        {
            $msg = sprintf('get_poi_by_uid error[url:%s][response:%s]', $url, $response);
            Tool_Log::error($msg);
            throw new Comm_Weibo_Exception_Api($msg);
        }
    }

    /**
     * 根据uid获取poiid,按照加入时间倒排
     *
     * @param type $uid
     *            用户的uid
     * @return type
     * @throws Comm_Weibo_Exception_Api
     */
    public static function getPoiidByUid($uid, $count = 0, $page = 1)
    {
        if (empty($uid))
        {
            return array();
        }
        if (empty($count))
        {
            $url = sprintf(self::GET_POIID_URL, $uid);
        }
        else
        {
            $url = sprintf(self::GET_POIID_OFFSET_URL, $uid, $count, $page);
        }
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if (!empty($result))
        {
            $poiids = array();
            // 按时间倒排
            foreach ($result as $key => $val)
            {
                if (isset($val ['poiid']))
                {
                    $poiids [] = $val ['poiid'];
                }
            }
            return $poiids;
        }
        else
        {
            $msg = sprintf('get_poiid_by_uid error[url:%s][response:%s]', $url, $response);
            Tool_Log::error($msg);
            throw new Comm_Weibo_Exception_Api($msg);
        }
    }

    /**
     * 根据poiid获取poi详细信息
     *
     * @param type $poiid
     * @return type
     * @throws Comm_Weibo_Exception_Api
     */
    public static function getPoiByPoiid($poiid)
    {
        if (empty($poiid))
        {
            return array();
        }
        $appkey = Comm_Config::get("env.platform_api_source");
        $url = sprintf(self::GET_POI_BY_ID_URL, $poiid, $appkey);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if (!empty($result))
        {
            return $result;
        }
        else
        {
            $msg = sprintf('get_poi_by_poiid error[url:%s][response:%s]', $url, $response);
            Tool_Log::error($msg);
            throw new Comm_Weibo_Exception_Api($msg);
        }
    }
}
