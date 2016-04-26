<?php
class Dr_Thirdapi extends Dr_Abstract
{
    /**
     *
     *
     * 获取微博展开信息数据
     * 
     * @return string json_encode 的数据
     */
    public static function getRenderfeedInfo($cuid, $urlShort, $urlLong, $type, $metadata, $lang)
    {
        try
        {
            $renderfeedurl = Comm_Config::get('thirdapiconf.' . $type);
            $objThird = Comm_Weibo_Api_ThirdModule::getApiRenderfeed($renderfeedurl ['rendurl']);
            $objThird->cuid = $cuid;
            $objThird->url_short = $urlShort;
            $objThird->url_long = $urlLong;
            $objThird->type = $type;
            $objThird->metadata = $metadata;
            $objThird->lang = $lang;
            $objThird->cip = Comm_Context::getClientIp();
            $objThird->source = Comm_Config::get("env.platform_api_source");
            $result = $objThird->getResult();
            if (!empty($result['error_code']))
            {
                return false;
            }

            return $result;
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
    
    /**
     *
     *
     * 获取微博短链接展开后内容交互、发布器插件展开及展开交互
     * 
     * @return string json_encode 的数据
     */
    public static function getInteractiveInfo($cuid, $type, $action, $cip, $lang, $actiondata = '')
    {
        $interactiveurl = Comm_Config::get('thirdapiconf.' . $type);
        $objThird = Comm_Weibo_Api_ThirdModule::getApiInteractive($interactiveurl ['interactiveurl']);
        if ($type == 25)
        {
            // 广告特性feed
            $objThird->getHttpRequest()->addHeader("REFERER", Comm_Context::getServer('HTTP_REFERER'));
            $objThird->getHttpRequest()->addHeader("USER-AGENT", Comm_Context::getServer('HTTP_USER_AGENT'));
            
            foreach ($_COOKIE as $key => $value)
            {
                $objThird->getHttpRequest()->addCookie($key, $value, true);
            }
        }
        
        $objThird->cuid = $cuid;
        $objThird->type = $type;
        $objThird->action = $action;
        $objThird->cip = $cip;
        $objThird->lang = $lang;
        $objThird->source = Comm_Config::get("env.platform_api_source");
        $objThird->actiondata = $actiondata;
        $objThird->setRequestTimeout(3000, 3000);

        try
        {
            $result = $objThird->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $result;
    }
    
    /**
     *
     *
     * 政府微访谈项目内部接口，单独进行处理
     * 获取微博展开信息数据
     * 
     * @return string json_encode 的数据
     */
    public static function getGovernmenttalkRenderfeedInfo($cuid, $urlShort, $urlLong, $shortInfo, $type, $lang)
    {
        $md5Long = md5($urlLong);
        $md5Info = md5($shortInfo);
        
        $cache = new Cache_Talk();
        $cacheResult = $cache->getList($cuid, $urlShort, $md5Long, $md5Info, $type, $lang);
        
        if ($cacheResult === false)
        {
            $renderfeedurl = 'http://i.service.t.sina.com.cn/talk/getspecialfeed.php';
            $objThird = Comm_Weibo_Api_ThirdModule::getGovernmenttalkRenderfeed($renderfeedurl);
            $objThird->cuid = $cuid;
            $objThird->url_short = $urlShort;
            $objThird->url_long = $urlLong;
            $objThird->type = $type;
            $objThird->lang = $lang;
            $objThird->short_info = $shortInfo;
            $objThird->source = Comm_Config::get("env.platform_api_source");
            $result = $objThird->getResult();

            if (!empty($result ['error_code']))
            {
                $cache->createList($cuid, $urlShort, $md5Long, $md5Info, $type, $lang, array());
                return false;
            }
            
            $cache->createList($cuid, $urlShort, $md5Long, $md5Info, $type, $lang, $result);
        }
        else
        {
            $result = $cacheResult;
        }

        return $result;
    }
    
    /**
     * 政府微访谈项目内部接口，单独进行处理
     * 
     * @param bigint $cuid 当前登录用户的id
     * @param int $type 短链的类型
     * @param string $cip 当前登录用户的id
     * @param int $lang 所拥有的语言
     * @param string $mblogcount 用户发的微博内容
     */
    public static function getGovernmenttalkInteractiveInfo($cuid, $type, $cip, $lang, $actiondata)
    {
        $interactiveurl = 'http://i.service.t.sina.com.cn/talk/addspecialfeed.php';
        $objThird = Comm_Weibo_Api_ThirdModule::getGovernmenttalkInteractive($interactiveurl);
        $objThird->cuid = $cuid;
        $objThird->type = $type;
        $objThird->cip = $cip;
        $objThird->lang = $lang;
        $objThird->actiondata = $actiondata;
        $objThird->source = Comm_Config::get("env.platform_api_source");
        
        try
        {
            $result = $objThird->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $result;
    }
    
    /**
     * 获取广告特型feed html
     *
     * @param int $uid            
     * @param string $slstr            
     * @param int $type            
     */
    public static function getBpfeedInfo($slstr)
    {
        $url = 'http://sl.biz.weibo.com/slfront/getSLData';
        $objThird = Comm_Weibo_Api_ThirdModule::getBpfeed($url);
        $objThird->getHttpRequest()->addHeader("REFERER", Comm_Context::getServer('HTTP_REFERER'));
        $objThird->getHttpRequest()->addHeader("USER-AGENT", Comm_Context::getServer('HTTP_USER_AGENT'));
        $viewer = Comm_Context::get('viewer', FALSE); // 当前登录用户
        $uid = $viewer->id;
        
        // 此处uid不可为空
        if (!isset($uid) || empty($uid))
        {
            $uid = '2355672605'; // cuitest404的uid
        }

        $objThird->uid = $uid;
        $objThird->slstr = $slstr;
        $objThird->ip = Comm_Context::getClientIp();
        $objThird->appid = 78; // 企业微博对应的appid
        $objThird->wbVersion = 'v5';

        try
        {
            $result = $objThird->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        if ($result[0]['status'] != 1)
        {
            return false;
        }

        return $result[0]['data'];
    }
}
