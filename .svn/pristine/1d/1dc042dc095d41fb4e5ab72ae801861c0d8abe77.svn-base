<?php
//TODO 专业版
class Comm_Weibo_GovEvent
{

    // 获取活动信息
    const GET_EVENT_INFO_V4 = 'http://i.event.weibo.com/getactivityinfo?uid=%s&eid=%s';
    // 获取活动分类
    const SHOW_EVENT_TYPE = 'http://i.event.weibo.com/gettype';
    // 获取活动的计数信息
    const GET_EVENT_COUNT = 'http://i.event.weibo.com/getcounterbyaid?aid=%s';
    // 获取活动的状态
    const GET_EVENT_JOIN_STATUS = 'http://i.event.weibo.com/getapply?uid=%s&eid=%s';
    // 获取有奖活动的信息
    const GET_EVENT_PRIZEINFO = 'http://i.event.weibo.com/getprizeinfo?eid=%s';
    // 加入活动
    const JOIN_EVENT = 'http://i.event.weibo.com/applyevent';
    const CREATE_EVENT = 'http://i.event.weibo.com/createevent';
    // 获取用户管理的活动
    const GET_MAMAGED_EVENT = 'http://i2.api.weibo.com/2/proxy/events/managed.json?source=%s&uid=%s';

    /**
     * 获取GET|POST请求的响应内容
     *
     * @param string $url
     * @param BOOL $isRawUrl
     *            是否直接使用原始url，此参数解决GET参数传递数组的情况，如id[]=xxx&id[]=yyy
     * @return mixed
     */
    public static function getResponseResult($url, $isRawUrl = FALSE, $method = 'get', $postFiled = array())
    {
        $request = new Comm_HttpRequest();
        if ($isRawUrl === FALSE)
        {
            $request->setUrl($url);
        }
        else
        {
            $request->url = $url;
        }
        if (strtoupper($method) == "POST")
        {
            $request->setMethod("POST");
            if (is_array($postFiled))
            {
                foreach ($postFiled as $key => $value)
                {
                    $request->addPostField($key, $value);
                }
            }
        }
        $request->send();
        return $request->getResponseContent();
    }

    /*
     * 添加活动数据 @param int $uid @param string $startdate @param string $enddate
     */
    public function createEvent($uid, $data)
    {
        $result = array();
        $url = sprintf(self::CREATE_EVENT);
        $resonpse = Tool_Http::post($url, $data);
        if (!$result)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $resonpse);
            return array();
        }
        $result = json_decode($result, true);
        return $result;
    }

    /**
     * 获得获得活动数据
     *
     * @param int $uid
     * @param int $eid
     */
    public static function getEventData($uid, $eid)
    {
        $result = array();
        $url = sprintf(self::GET_EVENT_INFO_V4, $uid, $eid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1 || !$result['result']['status'])
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 加入活动接口
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public static function joinEvent($uid, $postData)
    {
        $result = array();
        $url = sprintf(self::JOIN_EVENT);
        $resonpse = Tool_Http::post($url, $postData);
        if (!$result)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $resonpse);
            return array();
        }
        $result = json_decode($result, true);
        return $result['result'];
    }

    /**
     * 获取活动分类
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public static function getEventType()
    {
        $result = array();
        $url = self::SHOW_EVENT_TYPE;
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 获取活动的计数信息
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public static function getEventCount($uid, $eid)
    {
        $result = array();
        $url = sprintf(self::GET_EVENT_COUNT, $eid);
        $response =  Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 获取活动的状态
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public static function getEventStatus($uid, $eid)
    {
        $result = array();
        $url = sprintf(self::GET_EVENT_JOIN_STATUS, $uid, $eid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result ['errno'] != 1)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 获取有奖活动的信息
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public function getEventPrizeInfo($uid, $eid)
    {
        $result = array();
        $url = sprintf(self::GET_EVENT_PRIZEINFO, $eid);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);
        if ($result['errno'] != 1)
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        return $result['result'];
    }

    /**
     * 获取用户管理的活动列表
     *
     * @param int $uid
     * @param string $startdate
     * @param string $enddate
     */
    public static function getManagedEvent($uid)
    {
        $result = $eventId = array();
        $source = Comm_Config::get('env.platform_api_source');
        $url = sprintf(self::GET_MAMAGED_EVENT, $source, $uid);
        $response = self::getResponseResult2($url);
        $result = json_decode($response, true);
        if (isset ($result['errno']))
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        elseif (count($result['events']))
        {
            foreach ($result['events'] as $v)
            {
                array_push($eventId, $v['id']);
            }
        }
        return $eventId;
    }

    /**
     * 获取GET|POST请求的响应内容
     * 请求URL要求身份验证：cookie传入
     *
     * @param string $url
     * @return mixed
     */
    public static function getResponseResult2($url)
    {
        $request = new Comm_HttpRequest();
        $request->url = $url;
        $request->addCookie("SUE", isset ($_COOKIE ["SUE"]) ? $_COOKIE ["SUE"] : '');
        $request->addCookie("SUP", isset ($_COOKIE ["SUP"]) ? $_COOKIE ["SUP"] : '');
        $request->send();
        return $request->getResponseContent();
    }
}
