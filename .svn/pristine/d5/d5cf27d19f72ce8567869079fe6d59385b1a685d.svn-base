<?php
class Dr_Music extends Dr_Abstract
{
    /**
     * 
     *@deprecate    根据mp3 url 连接取得相应的mp3的扩展信息 
     *@param        url mp3音乐连接
     *@return    Do_Music 对象
     *@see        定义需要参考的函数、变量，并加入相应的超级连接
     *@throws    音乐连接无效,或者mp3本身没有附带ID3信息 抛出errorMusicUrl 异常
     *@todo        没有对不支持断点下载的服务器处理
     **/
    public static function getMusicId3($url) 
    {
        $musicType = Tool_Analyze_Music::getTypes($url);
        $musicInfo = array();
        if ($musicType == false)
        {
            if (!Do_Music::isValid($url))
            {
                throw new Dr_Exception("error_music_url");
            }

            $info = Tool_Analyze_Music::parseId3($url);
            $musicInfo['artist'] = $info['artist'];
            $musicInfo['album'] = $info['album'];
            $musicInfo['title'] = $info['title'];
            $musicInfo['gender'] = $info['gender'];
            $musicInfo['mp3_url'] = $url;
        } 
        else if ($musicType['from'] == "sina")
        {
            $musicId = Do_Music::getSinaMusicId($url);
            $musicInfo = self::getSinaMusicInfo($musicId, false);
        } 
        else 
        {
            $info = Tool_Analyze_Music::getThdId3($url, $musicType['from']);
            $musicInfo['artist'] = $info['artist'];
            $musicInfo['album'] = $info['album'];
            $musicInfo['title'] = $info['title'];
            $musicInfo['gender'] = $info['gender'];
            $musicInfo['mp3_url'] = $url;
        }

        if ($musicInfo == array())
        {
            throw new Dr_Exception("error_music_url");
        }

        return new Do_Music($musicInfo);
    }
    
    /**
     * 
    *@deprecate    根据音乐短连接取得原始音乐长连接和其他信息
    *@param        url mp3的url地址
    *@return    mp3 url 和 附加的一些扩展信息
    *@todo        指明应该改进或没有实现的地方
    **/
    public function getMusicInfoByShorturl($url)
    {
        return Dr_Shorturl::parseShortUrl($url);
    }
    
    /**
     * 联想搜索的mp3
     * 
     * @param string $keyword 搜索的key
     * @throws Dr_Exception
     * @return array
     */
    public function suggest($keyword)
    {
        $apiMusic = new Comm_Weibo_Music();
        $result = $apiMusic->suggest($keyword);

        if (!$result)
        {
            throw new Dr_Exception('suggest result empty');
        }

        return new Ds_Music($result, Do_Abstract::MODE_OUTPUT);
    }
    
    /**
     * search 
     * 
     * 按照关键字搜索sina乐库的信息
     * @param string $keyword 
     * @access public
     * @return array 
     */
    public function search($keyword, $hasUrl = false)
    {
        $sourceUrl = 'http://music.sina.com.cn/yueku/i/%s.html';
        $apiMusic = new Comm_Weibo_Music();
        $list = $apiMusic->search($keyword);
        if (empty($list))
        {
            throw new Dr_Exception('search result empty');
        }
        
        $result = array();
        if ($hasUrl == false)
        {
            $result = new Do_Music($list, Do_Abstract::MODE_OUTPUT);
        }
        else
        {
            $mp3Ids = array_keys($list);
            $result = $this->getSinaMusicInfos($mp3Ids);
        }
        
        return $result;
    }
    
    /**
     * 根据yueku  id 提供音乐连接
     * 
     * @param int $music_id]
     * @param bool $return_do 是否返回Do对象
     * @return Do_Music
     */
    public static function getSinaMusicInfo($musicId, $returnDo = true)
    {
        $apiMusic = new Comm_Weibo_Music();
        $musicInfo = $apiMusic->getSinaMusicInfo($musicId);
        if (empty($musicInfo))
        {
            throw new Dr_Exception("music_info_empty");
        }
        
        $result = array();
        $result['title'] = $musicInfo['title'];
        $result['artist'] = $musicInfo['artist'];
        $result['mp3_url'] = $musicInfo['mp3_url'];
        $result['mp3_id'] = $musicInfo['mp3_id'];
        
        if ($returnDo)
        {
            return new Do_Music($result);
        }

        return $result;
    }
    
    /**
     * 批量取得音乐信息
     * @param $music_ids
     */
    public function getSinaMusicInfos(Array $musicIds)
    {
        $sourceUrl = 'http://music.sina.com.cn/yueku/i/%s.html';
        $apiMusic = new Comm_Weibo_Music();
        $list = $apiMusic->getSinaMusicInfos($musicIds);
        if (empty($list))
        {
            throw new Dr_Exception("result_empty");
        }
        
        $doResult = array();
        foreach ($list as $v)
        {
            $item = array();
            $item['title'] = $v['title'];
            $item['artist'] = $v['artist'];
            $item['album'] = $v['album'];
            $item['mp3_url'] = sprintf($sourceUrl, $v['mp3_id']);
            $item['mp3_id'] = $v['mp3_id'];
            $doResult[$item['mp3_id']] = new Do_Music($item);
        }

        return $doResult;
    }
}
