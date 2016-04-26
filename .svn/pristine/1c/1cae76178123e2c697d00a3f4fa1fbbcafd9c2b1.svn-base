<?php
//TODO 专业版
class Comm_Weibo_AreaHot
{
    const GET_PUB_SEND_HOT = "http://i.admin.weibo.com/admin/content/getpubsenthotnum.json?tids=%s&start=%s&end=%s&type=%s";
    const GET_PUB_SEND_TIDS = "http://i.admin.weibo.com/admin/content/getpubsentid.json?pid=%s&cid=%s&time=%s";
    const GET_PUB_SEND_MIDS = "http://i.admin.weibo.com/admin/content/getpubsentmids.json?tids=%s&time=%s";

    public function getAreahotPspie($tids, $start, $end, $type)
    {
        $url = sprintf(self::GET_PUB_SEND_HOT, $tids, $start, $end, $type);
        
        return $this->_getResult($url);
    }

    public function getAreahotTids($pid, $cid, $time)
    {
        $url = sprintf(self::GET_PUB_SEND_TIDS, $pid, $cid, $time);
        
        return $this->_getResult($url);
    }

    public function getAreahotRelablog($tids, $time)
    {
        $url = sprintf(self::GET_PUB_SEND_MIDS, $tids, $time);

        return $this->_getResult($url);
    }

    private function _getResult($url, $key = 'result')
    {
        $response = Tool_Http::get($url);
        $response = json_decode($response, true);

        if (!$response)
        {
            return false;
        }

        return isset($response[$key]) ? $response : false;
    }

}
