<?php
class Tool_Analyze_Link
{
    const SHORT_URL_DOMAIN = 't.cn'; //短链域名
    private static $_shortUrlDomains = array("t.cn" => 1, "sinaurl.cn" => 1);

    const SHORTURL_TYPE_WEB =   0;  //短url类型，默认0为网页
    const SHORTURL_TYPE_VIDEO = 1;  //视频
    const SHORTURL_TYPE_MP3 = 2; //MP3
    const SHORTURL_TYPE_WEIYUYIN = 6; //微语音
    const SHORTURL_TYPE_EVENT = 3; //活动
    const SHORTURL_TYPE_MAGIC = 4; // flash魔法表情
    const SHORTURL_TYPE_VOTE = 5; //投票
    const SHORTURL_TYPE_MOOD = 13; //心情
    const SHORTURL_TYPE_NEWS = 7; //新闻
    const SHORTURL_TYPE_KANDIAN = 23; //看点
    const SHORTURL_TYPE_GOODS = 11; //商品
    const SHORTURL_TYPE_QING = 8; //轻博客
    const SHORTURL_TYPE_WEIMANHUA = 21;//微漫画
    const SHORTURL_TYPE_GOVERNMENT_WTALK = 10;//政府微访谈
    const SHORTURL_TYPE_FANFAN = 15;//翻翻
    const SHORTURL_TYPE_BP = 25; // 特型feed  added by liuyu6
    const SHORTURL_TYPE_WEIBOOK = 24;//微读书
    const SHORTURL_TYPE_STOCK = 27;//股票
    const SHORTURL_TYPE_SINANEWS = 34;//新浪新闻    
    const SHORTURL_TYPE_WEIPAN = 28;//微盘
    const SHORTURL_TYPE_V5UPGRADE = 32; // v5升级
    const SHORTURL_TYPE_MEDIA = 39; // 媒体类的短链合集 kelizhi
    const SHORTURL_TYPE_CARTPAGE = 36; // page cart的短链type
    const PARSE_LINK_GREP = '!http\:\/\/[a-zA-Z0-9\$\-\_\.\+\!\*\'\,\{\}\\\^\~\`\<\%\>\/\?\:\@\&\=(\&amp\;)\#\|]+!is';

    protected static $shortUrls = array();
    protected static $shortUrlInfos = array();

    /**
     * 解析短链前执行，搜集短链，为批量获取短链信息作准备
     * @param string $content
     * @return void|void
     */
    public static function prepareParseLink($content)
    {
        //短链解析
        preg_match_all(self::PARSE_LINK_GREP, $content, $out);
        if (!isset($out[0]) || count($out[0]) == 0)
        {
            return;
        }

        foreach ($out[0] as $linkTime)
        {
            $urlArray = parse_url($linkTime);
            if (isset(self::$_shortUrlDomains[strtolower(trim($urlArray['host']))]) === false)
            {
                continue;
            }

            $stringShortUrl = trim($urlArray['path'], "/");
            if (empty($stringShortUrl))
            {
                continue;
            }

            self::$shortUrls[] = $stringShortUrl;
        }
    }

    /**
     * 批量获取prepareParseLink搜集的短链的详细信息
     * 
     * @see prepareParseLink
     */
    public static function genShortUrlInfo()
    {
        if (empty(self::$shortUrls))
        {
            return;
        }

        try
        {
            self::$shortUrlInfos = Dr_Shorturl::batchInfo(self::$shortUrls);
        }
        catch (Comm_Exception_Program $e)
        {
            self::$shortUrlInfos = array();
        }
    }

    /**
     * 短链转长短，建议依次先调用prepareParseLink和genShortUrlInfo，如果智链信息命中，直接从静态变量里取，否则直接调用接口取
     * 
     * @see prepareParseLink genShortUrlInfo
     * @param array $shortUrls
     */
    public static function shortToLong(array $shortUrls)
    {
        if (empty($shortUrls))
        {
            return array();
        }

        $longUrls = array();
        $queryShortUrls = array();

        foreach ($shortUrls as $shortUrl)
        {
            if (!isset(self::$shortUrlInfos[$shortUrl]))
            {
                $queryShortUrls[] = $shortUrl;
            }
            else
            {
                $longUrls[$shortUrl] = self::$shortUrlInfos[$shortUrl];
            }
        }

        if (empty($queryShortUrls))
        {
            return $longUrls;
        }

        try
        {
            $longUrls = array_merge($longUrls, Dr_Shorturl::batchInfo($queryShortUrls));
        }
        catch (Comm_Exception_Program $e)
        {
        }

        return $longUrls;
    }

    /**
     * 
     * 转换文本中的链接成标签
     * @param string $content
     */
    public static function parseLinkToHtml($content, $isForward = 0, $hasRoot = 0, $isAction = false, $mid = false)
    {
        //短链解析
        $contentDecode = htmlspecialchars_decode($content);
        preg_match_all(self::PARSE_LINK_GREP, $contentDecode, $out);
        $media = $urlShort = $videoFrom = $videoArray = $faceArray = $musicArray = array();
        $hasLink = false;
        if (count($out[0]) == 0)
        {
            return array('content' => $content, 'media' => $media, 'has_link' => $hasLink, 'short_url' => $urlShort, 'video_from' => $videoFrom, 'video_arr' => $videoArray, 'music_arr' => $musicArray, 'face_arr' => $faceArray);
        }

        $hasLink = true;
        $content = preg_replace_callback(self::PARSE_LINK_GREP, array("Tool_Analyze_Link", "_searchs"), $contentDecode);
        $content = htmlspecialchars($content);
        $urlShort = $urlLong = $sourceUrl = $urlReplace = $pregUrl = array();

        foreach ($out[0] as $linkItem)
        {
            $urlArray = parse_url($linkItem);

            // 判断域名就是短URL,否则要转成短URL
            if (isset(self::$_shortUrlDomains[strtolower(trim($urlArray['host']))]))
            {
                $stringShortUrl = trim($urlArray['path'], "/");
                if (empty($stringShortUrl))
                {
                    //修复 http://t.cn/  变成MD5 bug
                    $urlReplace[md5($linkItem)] = '<a href="' . $linkItem . '" target="_blank">' . $linkItem . '</a>';
                    $sourceUrl[] = md5($linkItem); 
                    continue;
                }

                $urlShort[] = $stringShortUrl;
                $pregUrl[md5($stringShortUrl)] = $linkItem;
            }
            else
            {
                $urlReplace[md5($linkItem)] = '<a href="' . $linkItem . '" target="_blank">' . $linkItem . '</a>';
            }

            $sourceUrl[] = md5($linkItem); 
        }

        $urlLong = array();
        if (count($urlShort))
        {
            $urlLong = self::shortToLong($urlShort);
        }

        $urlClick = "";
        if (count($urlShort) && count($urlLong))
        {
            //获取配置文件信息
            $thirdapiConf = Comm_Config::get('thirdapiconf');
            $thirdapiConfKeys = array();
            foreach ($thirdapiConf as $key => $value)
            {
                if ($value['feedshow'] === true)
                {
                    $thirdapiConfKeys[$key] = true;
                }
            }

            foreach ($urlShort as $v)
            {
                $item = $urlLong[$v];
                if ($item)
                {
                    $url = $item['url_short'];
                    $jsHtml = "";

                    if (isset($thirdapiConf[$item['type']]['actiontitle']))
                    {
                        $title = $thirdapiConf[$item['type']]['actiontitle'];
                        $item['annotations'][0]['title'] = $title;
                    } 
                    else 
                    {
                        $title = isset($item['annotations'][0]['title']) ? $item['annotations'][0]['title'] : Comm_I18n::get("查看来源");
                    }

                    $title = rawurlencode(htmlspecialchars($title, ENT_QUOTES));
                    $fullUrl = isset($item['url_long']) ? $item['url_long'] : "";
                    $fullUrlEncode = rawurlencode($fullUrl);
                    if (strpos($fullUrl, '#'))
                    {
                        $fullUrl = ""; //?
                    }

                    if (isset($thirdapiConfKeys[$item['type']]))
                    {
                        $sourceData = '';
                        if (isset($thirdapiConf[$item['type']]['preimgsudakey']) && isset($thirdapiConf[$item['type']]['preimgsudavalue']))
                        {
                            $sourceData = urlencode('key=' . $thirdapiConf[$item['type']]['preimgsudakey'] . '&value=' . $thirdapiConf[$item['type']]['preimgsudavalue']);
                            $item['annotations'][0]['source_suda'] = $sourceData;
                        }

                        $sudadata['key'] = $thirdapiConf[$item['type']]['sudakey'];
                        $sudadata['value'] = $thirdapiConf[$item['type']]['sudavalue'];
                        if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                        {
                            if (!empty($sourceData))
                            {
                                $jsHtml .= 'target="_blank" action-data="suda=' . $sourceData . '&title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;type=' . $item['type'] . '" action-type="feed_list_third_rend"';
                            }
                            else
                            {
                                $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;type=' . $item['type'] . '" action-type="feed_list_third_rend"';
                            }
                        }
                        else
                        {
                            $jsHtml .= 'target="_blank"';
                        }

                        if ($item['type'] == self::SHORTURL_TYPE_SINANEWS)
                        {
                            $jsHtml = 'target="_blank"';
                        }

                        if (!empty($sudadata['key']) && !empty($sudadata['value']))
                        {
                            $jsHtml .= ' suda-data="key=' . $sudadata['key'] . '&value=' . $sudadata['value'] . '"';
                        }

                        $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="' . $thirdapiConf[$item['type']]['feedclass'] . '" title="' . $thirdapiConf[$item['type']]['title'] . '"></span></a>';
                        $faceArray[] = $item['url_short'];
                        
                        if (count($media) == 0)
                        {
                            $media = $item;
                            $media['thirdapiflag'] = true;
                            $media['sudadata'] = ' suda-data="key=' . $sudadata['key'] . '&value=' . $sudadata['value'] . '"';
                            if (!isset($media['annotations'][0]['title']) || $media['annotations'][0]['title'] == '')
                            {
                                $media['annotations'][0]['title'] = Comm_I18n::get('查看来源');
                            }

                            if (isset($media['annotations'][0]['thumbid']) && $media['annotations'][0]['thumbid'])
                            {
                                $picUrl = Tool_Picid2url::get_pic_url($media['annotations'][0]['thumbid'], 'large');
                                $media['annotations'][0]['pic'] = $picUrl[$media['annotations'][0]['thumbid']];
                            }
                            elseif (isset($media['annotations'][0]['pid']) && $media['annotations'][0]['pid'])
                            {
                                $picUrl = Tool_Picid2url::getPicUrl($media['annotations'][0]['pid']);
                                $media['annotations'][0]['pic'] = $picUrl[$media['annotations'][0]['pid']];
                            }
                            else
                            {
                                if (isset($media['annotations'][0]['img_prev']) && $media['annotations'][0]['img_prev'])
                                {
                                    $media['annotations'][0]['pic'] = $media['annotations'][0]['img_prev'];
                                }
                                else
                                {
                                    $media['annotations'][0]['pic'] = "";
                                }
                            }
                        }
                    }
                    else
                    {
                        switch ($item['type'])
                        {
                            //@todo 扩展信息的嵌入 ，需要js指出
                            case self::SHORTURL_TYPE_VIDEO:
                                $videoArray[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_vedio" title="' . Comm_I18n::get('视频') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_VIDEO2:        
                                if ($item['annotations'][0]['object_type'] != 'video')
                                {
                                    $stringShortUrlHtml = '<a href="' . $item['url_short'] . '" target="_blank">' . $item['url_short'] . '</a>';
                                    break;
                                }

                                $title = $item['annotations'][0]['object']['display_name'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_vedio" title="' . Comm_I18n::get('视频') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['type'] = self::SHORTURL_TYPE_VIDEO;
                                    $item['annotations'][0] = array(
                                        'title' => $item['annotations'][0]['object']['display_name'],
                                        'pic' => $item['annotations'][0]['object']['image']['url'],
                                        'type' => self::SHORTURL_TYPE_VIDEO,
                                        'url' => $item['annotations'][0]['object']['embed_code']
                                    );
                                    $videoArray[] = $item['url_short'];
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_WEIMANHUA:
                                $manhua_arr[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_cartoon" title="' . Comm_I18n::get('漫画') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = '';
                                    if ($item['annotations'][0]['image_prev'])
                                    {
                                        $item['annotations'][0]['pic'] = $item['annotations'][0]['image_prev'];
                                    }

                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_WEIBOOK:
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_read" title="' . Comm_I18n::get('未读数') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = '';
                                    if ($item['annotations'][0]['image_prev'])
                                    {
                                        $item['annotations'][0]['pic'] = $item['annotations'][0]['image_prev'];
                                    }

                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_STOCK:
                                $videoArray[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="W_ico16 icon_sw_stock" title="股票"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = '';
                                    if ($item['annotations'][0]['img_prev'])
                                    {
                                        $item['annotations'][0]['pic'] = $item['annotations'][0]['img_prev'];
                                    }
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_FANFAN:
                                $manhua_arr[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_fanfan" title="' . Comm_I18n::get('翻翻') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = '';
                                    if ($item['annotations'][0]['img_prev'])
                                    {
                                        $item['annotations'][0]['pic'] = $item['annotations'][0]['img_prev'];
                                    }
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_WEIYUYIN:
                            case self::SHORTURL_TYPE_MP3:
                                $musicArray[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '" action-type="feed_list_media_music"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . '" ' . $jsHtml . '  >' . $url . '<span class="feedico_music" title="' . Comm_I18n::get('音乐') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = 'http://img.t.sinajs.cn/t3/style/images/index/music_s.gif';
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_EVENT:
                                if (is_array($item['annotations']) && is_array($item['annotations'][0]) && isset($item['annotations'][0]['img_prev']) && $item['annotations'][0]['img_prev'] != '')
                                {
                                    if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                    {
                                        $sourceData = $suda = '';
                                        $title = Comm_I18n::get('查看来源');
                                        $sourceData = urlencode('key=feedevent&value=linkjump');
                                        $item['annotations'][0]['source_suda'] = $sourceData;
                                        $suda = 'suda-data=key=feedevent&value=feedshow';
                                        $jsHtml .= 'target="_blank" ' . $suda . ' action-data="suda=' . $sourceData . '&amp;title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '" action-type="feed_list_media_widget"';
                                    }
                                    else
                                    {
                                        $jsHtml .= 'target="_blank"';
                                    }

                                    $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_active"></span></a>';
                                    if (count($media) == 0)
                                    {
                                        $media = $item;
                                    }
                                } 
                                else 
                                {
                                    $stringShortUrlHtml = '<a title="' . $fullUrl . '" target="_blank" href="' . $url . $urlClick . '" title="' . $item['url_long'] . '" target="_blank" mt="event" >' . $url . '<span class="feedico_active"></span></a>';
                                }
                                break;

                            case self::SHORTURL_TYPE_MAGIC:
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'title="' . $fullUrl . '" target="_blank" href="' . $item['url_short'] . '" action-data="swf=' . $item['url_long'] . '" action-type="feed_list_media_magic"';
                                } 
                                else
                                {
                                    $jsHtml .= 'title="' . $url . '" target="_blank" href="' . $item['url_short'] . '" ';
                                }

                                $stringShortUrlHtml = '<a ' . $jsHtml . ' >' . $url . '<span class="feedico_magic" title="' . Comm_I18n::get('魔法表情') . '"></span></a>';
                                $faceArray[] = $item['url_short'];
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_VOTE:
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;type=' . $item['type'] . '" action-type="feed_list_media_vote"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_vote" title="' . Comm_I18n::get('投票') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                    if (isset($media['annotations'][0]['thumbid']) && $media['annotations'][0]['thumbid'])
                                    {
                                        $picUrl = Tool_Picid2url::getPicUrl($media['annotations'][0]['thumbid'], 'large');
                                        $media['annotations'][0]['pic'] = $picUrl[$media['annotations'][0]['thumbid']];
                                    }
                                    elseif (isset($media['annotations'][0]['pid']) && $media['annotations'][0]['pid'])
                                    {
                                        $picUrl = Tool_Picid2url::getPicUrl($media['annotations'][0]['pid']);
                                        $media['annotations'][0]['pic'] = $picUrl[$media['annotations'][0]['pid']];
                                    }
                                    else
                                    {
                                        $media['annotations'][0]['pic'] = "";
                                    }
                                }
                                break;

                            case self::SHORTURL_TYPE_NEWS:
                                $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . $urlClick . '" target="_blank" mt="news" action-type="feed_list_media_news" >' . $url . '<span class="feedico_news" title="' . Comm_I18n::get('新闻') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_BP:
                                # 即便type = 25，如果status不为1，也即非上线特型feed，也不显示“我要推广”图标
                                foreach ($urlShort as $shorturl)
                                {
                                    if (strpos($item['url_short'], $shorturl) !== false)
                                    {
                                        $slstr = $shorturl;
                                    }
                                }

                                $ret = Dr_Thirdapi::getBpfeedInfo($slstr);
                                if ($ret !== false)
                                {
                                    $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . $urlClick . '" target="_blank" mt="bp" action-type="feed_list_media_bpfeed" >' . $url . '<span class="feedico_business" title="' . Comm_I18n::get('推广') . '"></span></a>';
                                    $item['annotations'][0]['bp_feed_html'] = urldecode($ret);
                                }
                                else 
                                {
                                    $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . $urlClick . '" target="_blank" mt="url" action-type="feed_list_url">' . $url . '</a>';
                                }
                                $media = $item;
                                break;

                            case self::SHORTURL_TYPE_KANDIAN:
                                $videoArray[] = $item['url_short'];
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_expand_focus" title="' . Comm_I18n::get('看点') . '"></span></a>';
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_WEIPAN:
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $jsHtml .= ' target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_media_video"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="icon_sw_wepan" title="微盘"></span></a>';
                                if (count($media) == 0)
                                {
                                    $item['annotations'][0]['pic'] = '';
                                    if ($item['annotations'][0]['image_prev'])
                                    {
                                        $item['annotations'][0]['pic'] = $item['annotations'][0]['image_prev'];
                                    }

                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_QING:  
                                if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                {
                                    $title = Comm_I18n::get('查看来源');
                                    $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&template_name=' . $item ['annotations'] [0]['ctype'] . '" action-type="feed_list_media_qing"';
                                }
                                else
                                {
                                    $jsHtml .= 'target="_blank"';
                                }

                                $stringShortUrlHtml = '<a title="' . $url . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_qing"></span></a>';
                                if (count($media) == 0)
                                {
                                    $media = $item;
                                }
                                break;

                            case self::SHORTURL_TYPE_GOVERNMENT_WTALK: //政府微访谈短链类型
                                if ($item['annotations'][0]['act'] == 2)
                                {
                                    if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                    {
                                        $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata"';
                                    }
                                    else
                                    {
                                        $jsHtml .= 'target="_blank"';
                                    }

                                    $stringShortUrlHtml = '<a title="' . $title . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_interview"  title="' . Comm_I18n::get('微访谈') . '"></span></a>';
                                    $media = $item;
                                }
                                else
                                {
                                    if (!($hasRoot xor $isForward) && $isAction && !empty($fullUrl))
                                    {
                                        $jsHtml .= 'target="_blank" action-data="title=' . $title . '&amp;short_url=' . $item['url_short'] . '&amp;full_url=' . $fullUrlEncode . '&amp;metadata" action-type="feed_list_third_rend"';
                                    }
                                    else
                                    {
                                        $jsHtml .= 'target="_blank"';
                                    }

                                    $stringShortUrlHtml = '<a title="' . $title . '" href="' . $url . '" ' . $jsHtml . ' >' . $url . '<span class="feedico_interview" title="' . Comm_I18n::get('微访谈') . '"></span></a>';
                                    if (count($media) == 0)
                                    {
                                        $item['annotations'][0]['pic'] = '';
                                        if ($item['annotations'][0]['img'])
                                        {
                                            $item['annotations'][0]['pic'] = $item['annotations'][0]['img'];
                                        }
                                        $media = $item;
                                    }
                                }
                                break;

                            default:
                                $stringShortUrlHtml = '<a title="' . $fullUrl . '" href="' . $url . $urlClick . '" target="_blank" mt="url" action-type="feed_list_url">' . $url . '</a>';
                                break;
                        }
                    }
                }
                else
                {
                    $item = "http://" . self::SHORT_URL_DOMAIN . "/" . $v;
                    $url = $item;
                    $stringShortUrlHtml = '<a title="' . $item . '" href="' . $item.$urlClick.'" target="_blank" mt="url" >' . $item . '</a>';
                }

                $url = $pregUrl[md5($v)];
                $urlReplace[md5($url)] = $stringShortUrlHtml;
            }
        }
        else
        {
            foreach ($out[0] as $item)
            {
                $urlReplace[md5($item)] = '<a title="' . $item . '" href="' . $item . '" title="' . $item . '" target="_blank" mt="url" >' . $item . '</a>';
            }
        }

        //解决短链解析顺序不正确的问题
        foreach ($sourceUrl as $url)
        {
            $content = str_replace($url, $urlReplace[$url], $content);
        }

        //转换
        //$content = str_replace($sourceUrl, $urlReplace, $content);    

        return array('content' => $content, 'media' => $media, 'has_link' => $hasLink, 'short_url' => $urlShort, 'video_from' => $videoFrom, 'video_arr' => $videoArray, 'music_arr' => $musicArray, 'face_arr' => $faceArray);
    }

    /**
     * 配合preg_replace_callback做正则替换
     *
     * @param array $matches 正则匹配到的url
     */
    private static function _searchs($matches)
    {
        return md5($matches[0]);
    }

    public static function parseSourceContentLinkForSsm($content)
    {
        //短链解析
        $grep = "!http\:\/\/[a-zA-Z0-9\$\-\_\.\+\!\*\'\,\{\}\\\^\~\]\`\<\%\>\/\?\:\@\&\=(\&amp\;)\#\|]+!is";
        preg_match_all($grep, $content, $out);
        $urlShort = $urlLong = $videoFrom = $videoArray = $faceArray = $musicArray = array();
        $hasLink = false;
        if (count($out[0]) === 0)
        {
            return array('content' => $content, 'has_link' => $hasLink, 'url_short' => $urlShort, 'url_long' => $urlLong, 'video_from' => $videoFrom, 'video_arr' => $videoArray, 'music_arr' => $musicArray, 'face_arr' => $faceArray);
        }

        $hasLink = true;
        $content = preg_replace_callback($grep, array("Tool_Analyze_Link", "_searchs"), $content);
        foreach ($out[0] as $linkItem)
        { 
            $urlArray = parse_url($linkItem);
            
            // 判断域名就是短URL,否则要转成短URL
            if (isset(self::$_shortUrlDomains[strtolower(trim($urlArray['host']))]))
            {
                $stringShortUrl = trim($urlArray['path'], "/");
                if (empty($stringShortUrl))
                {
                    $urlLong[] = $linkItem; //只发http://t.cn 作为长链接处理。 
                    continue;
                }

                $urlShort[] = $stringShortUrl;
            }
            else
            {
                $urlLong[] = $linkItem;
            }
        }

        if (count($urlShort))
        {
            try
            {
                $urlShortInfo = Dr_Shorturl::batchInfo($urlShort);
            }
            catch (Exception $e)
            {
                $urlShortInfo = array();
            }
        }

        if (count($urlShortInfo))
        {
            foreach ($urlShort as $v)
            {
                $item = $urlShortInfo[$v];

                if ($item)
                {
                    switch ($item['type'])
                    {
                        case self::SHORTURL_TYPE_VIDEO:
                            $videoArray[] = $v;
                            break;
                        case self::SHORTURL_TYPE_WEIYUYIN:
                        case self::SHORTURL_TYPE_MP3:
                            $musicArray[] = $v;
                            break;
                        case self::SHORTURL_TYPE_EVENT:
                            break;
                        case self::SHORTURL_TYPE_MAGIC:
                            $faceArray[] = $v;
                            break;
                        case self::SHORTURL_TYPE_VOTE:
                            break;
                        default:
                            break;
                    }
                }
            }
        }

        return array('content' => $content, 'has_link' => $hasLink, 'url_short' => $urlShort, 'url_long' => $urlLong, 'video_from' => $videoFrom, 'video_arr' => $videoArray, 'music_arr' => $musicArray, 'face_arr' => $faceArray);
    }

}
