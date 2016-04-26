<?php
class Dr_Shorturl extends Dr_Abstract
{
    /**
     * 获取短链信息
     * 
     * @param string $shortUrl 短链接地址
     */
    public static function parseShortUrl($shortUrl)
    {
        //TODO
        // 获取短链信息
        $urlArray = parse_url($shortUrl);
        $stringShortUrl = trim($urlArray['path'], "/");

        try
        {
            $api = Comm_Weibo_Api_Shorturl::batchInfo();
            $api->url_short = $stringShortUrl;
            return $api->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        $urlBatchInfo = Dr_Shorturl::batchInfo(array($strinShortUrl));
        $batchInfo = array();
        $info = $urlBatchInfo[$strinShortUrl];
        $batchInfo['type'] = $info['type'];
        
        if (Tool_Analyze_Link::SHORTURL_TYPE_GOODS == $batchInfo['type'])
        {
            // 商品feed
            if (is_array($info['annotations']) && is_array($info['annotations'][0]))
            {
                if (isset($info['annotations'][0]['pid']))
                {
                    $batchInfo['pid'] = $info['annotations'][0]['pid'];
                }
            }
        }
        
        if (Tool_Analyze_Link::SHORTURL_TYPE_EVENT == $batchInfo['type'])
        {
            // 活动feed
            if (is_array($info['annotations']) && is_array($info['annotations'][0]))
            {
                if (isset($info['annotations'][0]['eid']))
                {
                    $batchInfo['eid'] = $info['annotations'][0]['eid'];
                }
            }
        }
        
        /*
         * if (Tool_Analyze_Link::SHORTURL_TYPE_MOOD == $batch_info ['type']) { //微博心情 if (is_array ( $info['annotations'] ) && is_array ( $info['annotations'][0] )) { if (isset ( $info['annotations'][0]['data'] )) { $batch_info ['data'] = $info['annotations'][0]['data']; } } }
         */
        return $batchInfo;
    }
    
    public static function longToShort($longUrl)
    {
        if (Tool_Shorturl::checkLongUrl($longUrl) === false)
        {
            throw new Comm_Exception_Program('error long url');
        }

        try
        {
            $longUrl = urlencode($longUrl);
            $api = Comm_Weibo_Api_Shorturl::shorten(array($longUrl));
            return $api->getResult();
        } 
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    public static function shortToLong($shortUrl)
    {
        if (Tool_Shorturl::checkShortUrl($shortUrl) === false)
        {
            throw new Comm_Exception_Program('error short url');
        }

        try 
        {
            $api = Comm_Weibo_Api_Shorturl::expand(array($shortUrl));
            return $api->getResult();
        } 
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }

    /**
     * 返回结果是key为短链的数组
     * @param  array  $shortUrls [description]
     * @return [type]           [description]
     */
    public static function batchInfo($shortUrls = array())
    {
        //缓存读取
        $keys = array();
        $cacheShortUrl = new Cache_Shorturl();
        $shortUrlInfos = array();
        $cacheResult = $cacheShortUrl->getShortUrlInfos($shortUrls, $keys);
        $nocacheShortUrls = self::filterCachedItems($cacheResult, $keys, $shortUrlInfos);

        if (empty($nocacheShortUrls))
        {
            return $shortUrlInfos;
        }

        try
        {
            $api = Comm_Weibo_Api_Shorturl::batchInfo();
            $api->url_short = join(',', $nocacheShortUrls);
            $nocacheShortUrlInfos = $api->getResult();
            $needCacheItems = array();

            foreach ($nocacheShortUrlInfos as $shortUrlInfo)
            {
                $short = $shortUrlInfo['url_short'];
                $needCacheItems[$short] = $shortUrlInfo;
                $shortUrlInfos[$short] = $shortUrlInfo;
            }

            //Create Cache
            $cacheShortUrl->createShortUrlInfos($needCacheItems);
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }

        return $shortUrlInfos;
    }
    
    /**
     * 批量获取短链的分享数
     * @param array $shortUrls
     * @author hz_sunxuechao@staff.sina.com.cn
     * @date 14-2-13 20:20
     * @return array
     * @throws Dr_Exception
     */
    public static function counts($shortUrls)
    {
        try
        {
            if (!$shortUrls) {
                return array();
            }
            $urlShort = join('&url_short=', $shortUrls);
            $commApi = Comm_Weibo_Api_Shorturl::counts();
            $commApi->setValue('url_short', $urlShort);
            return $commApi->getResult();
        }
        catch (Exception $e)
        {
            throw new Dr_Exception($e);
        }
    }
}

