<?php
class Dr_Vote extends Dr_Abstract
{
    public function detail($cuid, $pollId, $ptype = 0, $sh = 0, $dataJson = 0)
    {
        try
        {
            $comm_api = Comm_Weibo_Api_WeiboVote::detail();
            $comm_api->cuid = $cuid;
            $comm_api->poll_id = $pollId;
            $comm_api->ptype = $ptype;
            $comm_api->sh = $sh;
            $comm_api->from = "a.weibo.com";
            $comm_api->dataJson = $dataJson;
            return $comm_api->getResult();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw $e;
        }
    }
}