<?php
//TODO 专业版
class Comm_Weibo_Vote
{
    // 获取活动信息
    const GET_VOTE_INFO = 'http://i2.api.weibo.com/2/votes/show.json?source=%s&poll_id=%s';
    
    /**
     * 获得获得投票数据
     *
     * @param int $uid            
     * @param int $eid            
     */
    public static function getVoteData($pollId)
    {
        $result = array();
        $source = Comm_Config::get('env.platform_api_source');
        $url = sprintf(self::GET_VOTE_INFO, $source, $pollId);
        $res = Tool_Http::get($url);
        return $result;
    }
}
