<?php
class Comm_Weibo_Api_Shorturl
{
    const RESOURCE = "short_url";

    /**
     * 批量获取短链富内容
     */
    public static function batchInfo()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "batch_info", 'json', null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("url_short", "string", true);
        $platform->addBeforeSendCallback("Comm_Weibo_Api_Shorturl", "checkShorturl", array($platform));
        
        return $platform;
    }

    /**
     * 取得短链接在微博上的微博分享数（包含原创和转发的微博）
     */
    public static function counts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "share/counts", 'json', null, false);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");
        $platform->addRule("url_short", "string", true);

        return $platform;
    }

    /**
     * 长链转短链
     */
    public static function shorten(array $urlLongs)
    {
        $urlLongsQuery = implode("&", array_map(create_function('$a', 'return "url_long=$a";'), $urlLongs));
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "shorten", 'json', null, false, $urlLongsQuery, true);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

        return $platform;
    }

    /**
     * 短链转长链
     */
    public static function expand(array $urlShorts)
    {
        $urlShortsQuery = implode("&", array_map(create_function('$a', 'return "url_short=$a";'), $urlShorts));
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, "expand", 'json', null, false, $urlShortsQuery);
        $platform = new Comm_Weibo_Api_Request_Platform($url, "GET");

        return $platform;
    }

    public static function checkShorturl(Comm_Weibo_Api_Request_Platform $platform)
    {
        try
        {
            $urls = array();
            $urls = explode(", ", $platform->url_short);
            if (count($urls) <= 0)
            {
                throw new Comm_Exception_Program("shorturl error");
            }

            $reUrls = array();
            foreach ($urls as $url)
            {
                $valUrl = Comm_ArgChecker::string($url, "width_min, 5;width_max, 8;re, /^[a-zA-Z0-9]+$/");
                if (!is_null($valUrl))
                {
                    $reUrls[] = $valUrl;
                }
            }

            if (count($reUrls) <= 0)
            {
                throw new Comm_Exception_Program("shorturl error");
            }

            $platform->url_short = implode(", ", $reUrls);
        }
        catch (Comm_Exception_Program $e)
        {
            return;
        }
    }

    public static function commentCounts()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'comment/counts', 'json', null, false);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->addRule('url_short', 'string', true);

        return $request;
    }

    public static function commentComments()
    {
        $url = Comm_Weibo_Api_Request_Platform::assembleUrl(self::RESOURCE, 'comment/comments', 'json', null, false, '', true);
        $request = new Comm_Weibo_Api_Request_Platform($url, 'GET');
        $request->supportCursor();
        $request->supportPagination();
        $request->addRule('url_short', 'string', true);

        return $request;
    }
}
