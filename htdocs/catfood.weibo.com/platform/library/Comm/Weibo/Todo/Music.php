<?php
//TODO 专业版
class Comm_Weibo_Music
{
    // sina乐库接口
    // 联想搜索
    const RECOMMEND_URL = "http://i.music.sina.com.cn/yueku/search/getRecommendXml1dot0.php?q=%s";
    // 搜索sina乐库的歌曲
    const SEARCH_URL = "http://i.music.sina.com.cn/yueku/intro/musina_mmi_search.php?key=%s&start=0&limit=5";
    // sina乐库歌曲的详细信息
    const MUSIC_INFO = "http://i.music.sina.com.cn/yueku/intro/musina_mmi_playlist.php?coFlag=200006";
    const MUSIC_LOGO = "http://i.music.sina.com.cn/yueku/port/playlog.php?id=%d&name=%s&playlength=%s&totallength=%s&coFlag=200006&ownerid=%d";

    /**
     * mp3关键字联想
     *
     * @param string $keyword
     * @return string string string multitype:
     */
    public function suggest($keyword)
    {
        if (empty($keyword))
        {
            throw new Comm_Exception_Program('argument $keyword must not empty');
        }

        $url = sprintf(self::RECOMMEND_URL, $keyword);
        $response = $this->getResponseResult($url);
        if (!$response)
        {
            return FALSE;
        }

        preg_match_all('/<search_key><!\[CDATA\[(.*)\]\]>/isU', $response, $matches);

        $musics = isset($matches [1]) ? $matches [1] : array();

        $result = array();
        foreach ($musics as $k => $v)
        {
            $result [] ['title'] = mb_convert_encoding($v, 'UTF-8', 'GBk');
        }
        return $result;
    }

    /**
     * mp3关键词搜索
     *
     * @param string $keyword
     * @return mixed
     */
    public function search($keyword)
    {
        if (empty($keyword))
        {
            throw new Comm_Exception_Program('argument $keyword must be not empty');
        }

        $url = sprintf(self::SEARCH_URL, urlencode($keyword));
        $response = $this->getResponseResult($url);
        if (!$response)
        {
            return FALSE;
        }

        $response = json_decode($response, true);
        if (!$response)
        {
            return FALSE;
        }

        $songList = isset($response ['result'] ['songlist']) ? $response ['result'] ['songlist'] : array();
        $result = array();
        foreach ($songList as $k => $v)
        {
            $item = array();
            $item ['title'] = htmlspecialchars($v ['NAME'], ENT_QUOTES);
            $item ['artist'] = htmlspecialchars($v ['SINGERCNAME'], ENT_QUOTES);
            $item ['album'] = htmlspecialchars($v ['ALBUMCNAME'], ENT_QUOTES);
            $item ['mp3_id'] = $v ['SONGBASEID'];
            $result [$item ['mp3_id']] = $item;
        }
        return $result;
    }

    /**
     * 根据muserId获取音乐详细信息
     *
     * @param int $musicId
     * @return mixed
     */
    public function getSinaMusicInfo($musicId)
    {
        if (empty($musicId))
        {
            throw new Comm_Exception_Program('argument $$musicId must be not empty');
        }

        $url = self::MUSIC_INFO . "&id[]=$musicId";
        $response = Tool_Http::get($url);
        if (!$response)
        {
            return FALSE;
        }
        $response = json_decode($response, true);
        if (!isset($response ['result'] [0] ['NAME']))
        {
            return array();
        }

        $result = array();
        $result ['title'] = $response ['result'] [0] ['NAME'];
        $result ['artist'] = $response ['result'] [0] ['SINGERCNAME'];
        $result ['mp3_url'] = $response ['result'] [0] ['MP3_URL'];
        $result ['mp3_id'] = $response ['result'] [0] ['SONGBASEID'];
        return $result;
    }

    /**
     * 批量取得音乐信息
     *
     * @param
     *            $musicIds
     */
    public function getSinaMusicInfos(Array $musicIds)
    {
        if (empty($musicIds))
        {
            throw new Comm_Exception_Program('argument $music_ids must be not empty');
        }
        $musicIds = array_map(create_function('$a', 'return "id[]=$a";'), $musicIds);
        $url = self::MUSIC_INFO . "&" . implode("&", $musicIds);

        $response = Tool_Http::get($url);
        $response = json_decode($response, true);
        $response = isset($response ['result']) ? $response ['result'] : array();
        $result = array();
        foreach ($response as $v)
        {
            $item = array();
            $item ['title'] = $v ['NAME'];
            $item ['artist'] = $v ['SINGERCNAME'];
            $item ['album'] = $v ['ALBUMCNAME'];
            $item ['mp3_url'] = $v ['MP3_URL'];
            $item ['mp3_id'] = $v ['SONGBASEID'];
            $result [$item ['mp3_id']] = $item;
        }
        return $result;
    }
}
