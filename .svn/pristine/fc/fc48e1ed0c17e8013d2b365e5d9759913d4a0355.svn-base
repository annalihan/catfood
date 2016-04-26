<?php
class Tool_Formatter_Content
{
    public static function cutMblogText($content, $max, $etc = '...')
    {
        $result = $content;

        if (($rtn1 = self::dealLink($content, $max)) !== false)
        {
            $result = $rtn1;
        }
        elseif (($rtn2 = self::dealAt($content, $max)) !== false)
        {
            $result = $rtn2;
        }
        elseif (($rtn3 = self::dealTag($content, $max)) !== false)
        {
            $result = $rtn3;
        }
        else
        {
            $result = Tool_Formatter_String::substrCn($content, $max, 'UTF-8', '');
        }

        return $result . (strlen($content) > strlen($result) ? $etc : '');
    }
    
    public static function dealLink($content, $cut)
    {
        //短链解析
        $grep = "!http\:\/\/[a-zA-Z0-9\$\-\_\.\+\!\*\'\,\{\}\\\^\~\]\`\>\%\>\/\?\:\@\&\=(\&amp\;)\#\|]+!is";    
        preg_match_all($grep, $content, $out);
        if (count($out[0]) == 0)
        {
            return false;
        }

        return self::dealCut($content, $cut, $out[0]);
    }
    
    public static function dealAt($content, $cut)
    {
        //@信息
        $atNames = Tool_Analyze_At::getAtUserName($content);
        if (count($atNames) == 0)
        {
            return false;
        }

        foreach ($atNames as &$name)
        {
            $name = '@' . $name;
        }

        return self::dealCut($content, $cut, $atNames);
    }
    
    public static function dealTag($content, $cut)
    {
        //tag信息
        $tags = Tool_Analyze_Tag::getTags($content);
        
        if (count($tags) == 0)
        {
            return false;
        }
        
        foreach ($tags as &$tag)
        {
            $tag = '#' . $tag . '#';
        }

        return self::dealCut($content, $cut, $tags);
    }
    
    /**
     * 截取内容函数
     * @param string $content 原字符串
     * @param int $cut
     * @param array $searchStrings 需要完成保留或忽略的字符串数组（数组顺序必须以在原字符串中出现的先后是顺序一致）
     */
    public static function dealCut($content, $cut, $searchStrings)
    {
        $rtn = Tool_Formatter_String::substrCn($content, $cut, 'UTF-8', '');
        $cutPos = strlen($rtn);
        foreach ($searchStrings as $searchString)
        {
            $pos = strpos($content, $searchString);
            $end = $pos + strlen($searchString);
            if ($cutPos < $pos)
            {
                break;
            }
            elseif ($cutPos > $end)
            {
                continue;
            }
            elseif ($pos == $cutPos || $end == $cutPos)
            {
                return $rtn;
            }
            elseif ($pos < $cutPos && $end > $cutPos)
            {
                return substr($content, 0, $pos);
            }
        }

        return false;
    }
    
    /**
     * 转换字符串中的老域名成新域名　
     * 
     * @param string $string
     */
    public static function changeNewDomain($string)
    {
        if (false === strpos($string, 'http://t.sina.com.cn'))
        {
            return $string;
        }
        
        return str_replace('http://t.sina.com.cn', Comm_Config::get("domain.weibo"), $string);
    }
}