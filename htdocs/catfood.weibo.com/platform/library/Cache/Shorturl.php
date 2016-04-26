<?php

class Cache_Shorturl extends Cache_Abstract
{
    protected $configs = array(
        'short_url' => array('%s_1_%s', 3600),
    );

    protected $cachePool = 'MAIN';
    protected $keyPrefix = 'short_url';
    
    public function getShortUrlInfos($shortUrls, &$keys)
    {
        $keys = array();
        foreach ($shortUrls as $shortUrl)
        {
            $keys[$shortUrl] = $this->key('short_url', $shortUrl);
        }

        return $this->mget($keys);
    }

    public function createShortUrlInfos($shortUrlInfos)
    {
        $longValues = array();
        $shortValues = array();
        foreach ($shortUrlInfos as $key => $value)
        {
            $key = $this->key('short_url', $key);

            if ($value['type'] == Tool_Analyze_Link::SHORTURL_TYPE_VOTE)
            {
                $shortValues[$key] = $value;
            }
            else
            {
                $longValues[$key] = $value;
            }
        }

        if (count($longValues) > 0)
        {
            $this->mset($longValues, 86400);
        }

        if (count($shortValues) > 0)
        {
            $this->mset($shortValues, 3600);
        }

        return true;
    }

    /**
     * 批量清楚短链缓存
     * @param array $shortUrls = array()
     */
    public static function clearShortUrlInfos(Array $shortUrls)
    {
        foreach ($shortUrls as $shortUrl)
        {
            $keys[] = $this->key('short_url', $shortUrl);
        }

        return $this->mdel($keys);
    }
}
