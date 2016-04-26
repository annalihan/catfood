<?php
class Dr_Weihao extends Dr_Abstract
{
    public function searchWeihaoAtUser($q, $count = 10)
    {
        if (!preg_match("/^[0-9]{3,9}$/", $q))
        {
            return array();
        }

        try
        {
            $api = Comm_Weibo_Api_Search::suggestionsWeihao();
            $api->q = $q;
            $api->count = $count;
            
            $result = array();
            $rst = $api->getResult();

            if (!is_array($rst))
            {
                return array();
            }

            foreach ($rst as &$v)
            {
                if ($v['nickname'] != "")
                {
                    if (!is_numeric($v['weihao']))
                    {
                        continue;
                    }

                    $v['screen_name'] = $v['nickname'] . "(" . $v['weihao'] . ")";
                    $v['remark'] = "";
                    unset($v['nickname'], $v['weihao'], $v['uid']);
                    $result[] = $v;
                }
            }

            return $result;
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            return array();
        }
    }
}