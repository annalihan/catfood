<?php
//TODO 专业版
class Comm_Weibo_Keywords
{
    // 关键词最近24小时type:1,24小时热门博文，type:2,发博量，type:3发博人数
    const KEYWORDS24H_URL = "http://miniblog.match.sina.com.cn/enterprise/get_enterprise_keyword.php?word=%s&type=%s";
    // 7天，30天发博数，发博量
    const KEYWORDSTREND_URL = "http://data.i.t.sina.com.cn/enterprisev2/getkeyword.php?word=%s&day=%s";
    // 发博者分析
    const KEYWORDSPOSTER_URL = "http://data.i.t.sina.com.cn/enterprisev2/getpublisher.php?word=%s&day=%s";
    // 监控词上个月出现数量
    const FORECASTNUM_URL = "http://data.i.t.sina.com.cn/enterprisev2/getestimate.php?word=%s";
    // 博文筛选搜索接口
    const KEYWORDSSELECT_URL = "http://miniblog.match.sina.com.cn/openapi/rpcMblogyuqing.php?key=%s&pagesize=5&sid=121113106107";

    /*
     * 获取24小时热门博文
     */
    public function getKeywordsHot($keywords)
    {
        $url = sprintf(self::KEYWORDS24H_URL, urlencode($keywords), 1);
        $response = Tool_Http::get($url);
        $result = $this->_checkResponse($response);
        return $result;
    }
    /*
     * 获取博文趋势 type:mblog,poster
     */
    public function getKeywordsTrend24h($keywords, $interType)
    {
        $url = sprintf(self::KEYWORDS24H_URL, urlencode($keywords), $interType);
        $response = Tool_Http::get($url);
        $result = $this->_checkResponse($response);
        return $result;
    }
    /*
     * 获取7天，30天趋势数据包括博文和发博者 day:7d,30d
     */
    public function getKeywordsTrendOther($keywords, $day = '7')
    {
        $url = sprintf(self::KEYWORDSTREND_URL, urlencode($keywords), $day);
        $response = Tool_Http::get($url);
        $result = $this->_checkResponse($response);
        return $result;
    }
    /*
     * 发博者分析数据 day：1,7,30
     */
    public function getKeywordsPosterAnalysis($keywords, $day = '1')
    {
        $url = sprintf(self::KEYWORDSPOSTER_URL, urlencode($keywords), $day);
        $response = Tool_Http::get($url);
        $result = $this->_checkResponse($response);
        return $result;
    }
    public function getKeywordsForecastNum($keywords)
    {
        $url = sprintf(self::FORECASTNUM_URL, urlencode($keywords));
        $response = Tool_Http::get($url);
        $result = $this->_checkResponse($response);
        return $result;
    }
    /*
     * 获取博文筛选结果 param:url参数数组 level,gender,startage,endage,provice,area,starttime,endtime,page
     */
    public function getKeywordsSelect($keywords, $param)
    {
        if (empty($keywords))
        {
            throw new Comm_Exception_Program('argument keyword must not empty');
        }
        $url = sprintf(self::KEYWORDSSELECT_URL, urlencode($keywords));
        foreach ($param as $key => $value)
        {
            if (!empty($value) && $value != 0)
                $url .= "&" . $key . "=" . $value;
        }
        Tool_Log::info('KEYWORD_SELECT_URL' . $url);
        $response = Tool_Http::get($url);
        // Tool_Log_Commlog::write_log('KEYWORD_SELECT_RESPONSE', "=".$response."=");
        $result = $this->_checkResponse($response);
        $total = $this->_checkResponse($response, 'm');
        if (!$result || !$total)
        {
            $data ['result'] = array();
            $data ['total'] = '0';
        }
        else
        {
            $data ['result'] = $result;
            $data ['total'] = $total;
        }

        return $data;
    }
    private function _checkResponse($response, $key = 'result')
    {
        if (!$response)
        {
            return FALSE;
        }
        $response = json_decode($response, true);
        if (!$response)
        {
            return FALSE;
        }
        if ($response ['errno'] != 1)
        {
            return FALSE;
        }
        return isset($response [$key]) ? $response [$key] : false;
    }
}
