<?php
//TODO 专业版
class Comm_Weibo_Event
{
    const STATUS_NEW = 1; // 新创建
    const STATUS_COMPLETE = 2; // 已完善
    const STATUS_AUDITING = 3; // 审核中
    const STATUS_NORMAL = 4; // 审核通过
    const STATUS_ONLINE = 5; // 已发布上线
    const STATUS_AUDIT_FAIL = 6; // 审核失败
    const STATUS_OFFLINE = 7; // 已被下线
    const STATUS_END = 8; // 已结束，该状态不存在于数据库，由上线状态和活动时间与当前时间确定
    const STATUS_DELETE = 0; // 已删除
    const STATUS_EXCEPTION = - 1; // 主feed被删除,活动异常

    /*
     * packeid（活动套餐类型）： 3 => '[图文征集+评选]'^M 4 => '[投票+抽奖]'^M 1 => '[试用+分享体验]'^M 2 => '[优惠券+分享体验]'^M 5 => '[转发+抽奖]'^M 6 => '[文字征集+发奖] modify by haiqiang *packid 提供默认值0，并去掉empty(packid) data 2013/06/03*
     */
    const GETLISTCOUNT_URL = 'http://api.e.weibo.com/eweibo/getlistcount?uid=%s&page=%s&limit=%s&packid=%s&get_all=%s';
    
    public static function getlistcount($uid, $packid = 0, $page = 1, $limit = 5, $getAll = 0)
    {
        if (empty($uid))
        {
            throw new Comm_Exception_Program('argument appkey must be not empty and is numeric');
        }

        $url = sprintf(self::GETLISTCOUNT_URL, $uid, $page, $limit, $packid, $getAll);
        $response = Tool_Http::get($url);
        $result = json_decode($response, true);

        if (empty($result))
        {
            Tool_Log::error("API_EVENT_ERROR" . $url . '|' . $response);
            return array();
        }
        
        return $result;
    }
}
