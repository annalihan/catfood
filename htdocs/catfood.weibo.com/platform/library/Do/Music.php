<?php
class Do_Music extends Do_Abstract
{
    protected $props = array(
        'artist'        => '', 
        'track'         => '',  
        'title'         => '',
        'album'         => '',
        'year'          => '',
        'gender'        => '',
        'mp3_url'       => '',
        'mp3_short_url' => '',
        'mp3_id'        => '',
    );

    private static $_musicTypes = array(
        'audio/mpeg' => true, 
        'audio/x-mpeg' => true, 
        'application/octet-stream' => true
    );

    private static $_shortUrlDomains = array(
        "t.cn" => 1, 
        "sinaurl.cn" => 1
    );
    
    /**
     * 根据url取乐库的mid
     */
    public static function getSinMusicId($url)
    {
        if (empty($url))
        {
            return 0;
        }

        preg_match('|http://music.sina.com.cn/yueku/[mi]{1}/(\d+).htm|i', $url, $match);
        return ($match[1] > 0 && $match[0]) ? $match[1] : 0;
    }
    
    protected function setMp3Id($value)
    {
        $this->setData('mp3_id', $value);
        $this->mp3_url = sprintf('http://music.sina.com.cn/yueku/i/%s.phtml', $value);
    }
    
    /**
     * 验证url的是否为mp3类型
     */
    public static function isValid($url)
    {
        if (empty($url))
        {
            return false;
        }

        $httpRequest = new Comm_HttpRequest($url);
        $httpRequest->noBody = true;
        $httpRequest->send();
        $type = $httpRequest->getResponseInfo('content_type');

        return isset(self::$_musicTypes[$type]);
    }
    
    /**
     * 判断是否是短连接
     * 
     * 为音乐短链接时，返回音乐信息信息
     * @param string $url
     * @throws 判断是否是短连接
     * @return mixed
     */
    public static function isShortUrl($url)
    {
        $urlInfo = parse_url($url);

        if (isset($urlInfo['host']))
        {
            $urlHost = $urlInfo['host'];
        }
        else
        {
            throw new Do_Exception('url_invalid');   
        }

        $urlId = trim($urlInfo['path'], "/");
        
        if (isset(self::$_shortUrlDomains[$urlHost]) === false)
        {
            return false;
        }

        if ($urlId)
        {
            $shortUrlInfo = Dr_Shorturl::batchInfo(array($urlId));
        }

        if (isset($shortUrlInfo[0]['type']) && $shortUrlInfo[0]['type'] == 2)
        {
            return $shortUrlInfo[0];
        }
        else
        {
            throw new Do_Exception("not_mp3_short_url");
        }
    }
}
