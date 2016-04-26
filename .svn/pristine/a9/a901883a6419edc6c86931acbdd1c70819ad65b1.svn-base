<?php
class Dr_Account extends Dr_Abstract
{
    /**
     * 获取用户基本信息
     *
     * @param unknownType $uid            
     */
    public static function getProfileBasic($uid)
    {
        try
        {
            $comm = Comm_Weibo_Api_Account::getProfileBasic();
            $comm->uid = $uid;
            $result = $comm->getResult();
        
            return new Do_Account($result, Do_Abstract::MODE_OUTPUT);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 获取隐私信息
     */
    public static function getPrivacy()
    {
        try
        {
            $comm = Comm_Weibo_Api_Account::getPrivacy();
            $result = $comm->getResult();
        
            return new Do_Privacy($result);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 批量获取隐私设置
     *
     * @param array $uids            
     */
    public static function getPrivacyBatch(array $uids)
    {
        try
        {
            $uidsStr = implode(',', $uids);
        
            $comm = Comm_Weibo_Api_Account::getPrivacyBatch();
            $comm->uids = $uidsStr;
            $result = $comm->getResult();
            $usersPrivacy = array();
    
            foreach ($result as $info)
            {
                $usersPrivacy[$info['uid']] = new Do_Privacy($info['privacy']);
            }
    
            return $usersPrivacy;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    public static function watermark()
    {
        try
        {
            $api = Comm_Weibo_Api_Account::watermark();
            return $api->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    public static function activationCode($mobile)
    {
        try
        {
            $commApi = Comm_Weibo_Api_Account::activationCode();
            $commApi->mobile = $mobile;

            return $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 获取微号信息批量
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public static function weihaoBatch($list)
    {
        if (!is_array($list))
        {
            return array();
        }

        $uids = array_keys($list);
        $numbers = array_values($list);
        
        $cache = new Cache_Account();
        $infos = $cache->getWeihaoInfos($uids);
        $weihaoInfos = $uidsForApi = $numbersForApi = array();
        if (false !== $infos)
        {
            foreach ($uids as $k => $v)
            {
                $key = $cache->key('weihao_info', $v);
                if (!isset($infos[$key]) || $infos[$key] === false || !isset($infos[$key]['type']))
                {
                    $uidsForApi[] = $v;
                    $numbersForApi[] = $list[$v];
                }
                else
                {
                    $weihaoInfos[$v] = $infos[$key];
                }
            }
        }

        if (!$numbersForApi)
        {
            return $weihaoInfos;
        }

        $numbersApi = implode(",", $numbersForApi);
        
        try
        {
            $commApi = Comm_Weibo_Api_Account::weihaoBatch();
            $commApi->numbers = $numbersApi;
            $result = $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        if (is_array($result) && isset($result['weihao']))
        {
            $cacheWeihaoInfos = array();

            foreach ($result['weihao'] as $key => $value)
            {
                if ($value)
                {
                    $weihaoInfos[$value['uid']] = $value;
                }

                if (isset($value['type']))
                {
                    $key = $cache->key('weihao_info', $value['uid']);
                    $cacheWeihaoInfos[$key] = $value;
                }
            }

            if ($cacheWeihaoInfos)
            {
                $cache->createWeihaoInfos($cacheWeihaoInfos);
            }
        }

        return $weihaoInfos;
    }

    /**
     * 判断用户是否绑定手机
     *
     * @throws Comm_Weibo_Exception_Api
     */
    public static function getMobile($uid = "")
    {
        try
        {
            //TODO +cache
            $apiObj = Comm_Weibo_Api_Account::mobile();
            $apiObj->uid = $uid;
        
            return $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     * 批量判断用户是否绑定手机
     *
     * @param array() $uids            
     */
    public static function getMobileBatch($uids)
    {
        if (!is_array($uids))
        {
            $uids = array($uids);
        }

        try
        {
            $uids = array_unique($uids);
            $uids = join(",", $uids);
            $apiObj = Comm_Weibo_Api_Account::mobileMulti();
            $apiObj->uids = $uids;
            $mobileInfo = $apiObj->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        $bindInfo = array();

        foreach ($mobileInfo as $info)
        {
            $bindInfo[$info['id']]['mobile'] = $info['number'];
            $bindInfo[$info['id']]['binding'] = $info['binding'];
        }

        return $bindInfo;
    }
}
