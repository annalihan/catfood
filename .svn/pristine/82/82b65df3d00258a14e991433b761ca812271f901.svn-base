<?php
class Tool_Analyze_Tag
{
    private static $_tagReplaceKeys = array('＃', '&#039;', '&#39;');
    private static $_tagReplaceValues = array('#', '\'', '\'');

    /**
     * 渲染tag显示
     *
     * @param string $content
     * @return string
     */
    public static function renderTag($content, $isTarget = false)
    {
        $isTarget = $isTarget ? 1 : 0;
        $content = str_replace(self::$_tagReplaceKeys, self::$_tagReplaceValues, $content);
        return preg_replace("/#([^#<>\"]+)#/ise", "self::stripTag('\\1','\\0', {$isTarget})", $content);
    }
    
    public static function stripTag($str, $linkWord, $isTarget = false)
    {
        $str = trim($str);
        if ($str == "")
        {
            return "##";
        }
        
        $target = $isTarget ? ' target="_blank"' : '';
        $str = strip_tags($str);
        $linkWord = strip_tags($linkWord);

        //增大字符的长度 避免用户点击话题出现问题
        if (mb_strwidth($str) > 80)
        {
            $str = mb_strimwidth($str, 0, 80, '', 'UTF-8');
        }

        $huatiUrl = Comm_Config::get('domain.huati');
        $url = sprintf($huatiUrl, urlencode(htmlspecialchars_decode($str)));
        return '<a class="a_topic" href="' . $url . '"' . $target . '>' . $linkWord . '</a>';
    }

    /**
     * user_tag_to_link 
     * 
     * 用户标签转化为连接
     * @param string $content  用户标签格式为[TAG]XX,XX[TAG]
     * @access public
     * @return void
     */
    public static function userTagToLink($content)
    {
        $out = array();
        preg_match_all("/\[TAG\](.*?)\[TAG\]/is", $content, $out);
        if (empty($out[0]) || !is_array($out[0]) || empty($out[1][0]))
        {
            return $content;
        }

        $searchDomain = Comm_Config::get('domain.search') . '/user/&tag=%s';
        foreach ($out[1] as $kk => $tag)
        {
            $sourceStr = $out[0][$kk];
            $url = sprintf($searchDomain, urlencode(urlencode(htmlspecialchars_decode($tag))));
            $replaceStr = '<a href="' . $url . '">' . $tag . '</a>、'; 
            $replaceStr = mb_substr($replaceStr, 0, -1, 'UTF-8'); //?
            $content = str_replace($sourceStr, $replaceStr, $content);
        }

        return $content;
    }

    public static function getTags($content)
    {
        $content = str_replace("＃", "#", $content);
        $result = preg_match_all("/#([^#<>]+?)#/ise", $content, $tags);
        return $result == false ? array() : array_unique($tags[1]);
    }
}
