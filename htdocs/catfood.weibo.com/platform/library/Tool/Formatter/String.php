<?php
class Tool_Formatter_String
{
    public static function filterString($string)
    {
        $filter = array("\r", "\n", "\t");
        return str_replace($filter, ' ', $string);
    }
        
    /**
     * 去掉字符串前后的空格(半全角空格)
     * @param  string $str [description]
     * @return string      处理后的文本
     */
    public static function trimCn($str)
    {
        return preg_replace('/(^[\s\x{3000}]*)|([\s\x{3000}]*$)/u', '', $str);
    }
    
    /**
     * 替换所有的全半角空格
     * @param string $str
     */
    public static function trimAll($str)
    {
        $str = str_replace(array("　", "\n", "\r"), " ", $str);
        $str = preg_replace("/[ ]{1,}/", " ", $str);
        $str = str_replace('＠', '@', $str);
        return $str;
    }
    
    /**
     * 中英文混杂字符串截取
     *
     * @param string $string 原字符串
     * @param interger $length 截取的字符数
     * @param string $etc 省略字符
     * @param string $charset 原字符串的编码
     * 
     * @return string
     */
    public static function substrCn($string, $length = 80, $charset = 'UTF-8', $etc = '...')
    {
        if (mb_strwidth($string, 'UTF-8') < $length)
        {
            return $string;
        }

        return mb_strimwidth($string, 0, $length, $etc, $charset);
    }

    /**
     * 
     * 截取字符串到固定长度，并补全“...”
     * @param unknown_type $content
     * @param unknown_type $length
     */
    public static function contentTruncate($content, $length, $charset = 'UTF-8', $etc = '...', &$showTitle = false)
    {
        $utfWidth = mb_strwidth($content, $charset);
        $realWidth = (strlen($content) + mb_strlen($content, $charset)) / 2;
        if ($realWidth > $length + 2)
        {
            $getWidth = $length;
            if (($utfWidth - 1) * 2 <= $realWidth)
            {
                $getWidth = $getWidth / 2;    //特殊字符截取的长度
            }

            $content = mb_strimwidth($content, 0, $getWidth, $etc, $charset);
            $showTitle = true;
        }

        return $content;
    }
    
    public static function locationTruncate($content, $length = 30)
    {
        $sourceContent = $longContent = strip_tags($content);
        $showTitle = false;
        $truncateContent = self::contentTruncate($longContent, $length, 'UTF-8', '...', $showTitle);
        $content = str_replace($longContent, $truncateContent, $content);
        
        if ($showTitle)
        {        
            $content = str_replace('href', " title={$sourceContent} href", $content);
        }

        return $content;
    }

    /**
    * 对传入的内容标红处理，可能存在多个关键字需要标红则循环处理
    *
    * @param string $content  内容
    * @param string $searKey  标红的对象
    * @return string
    */
    public static function redTag($content, $searchKey)
    {
        if ($searchKey == '~' || $searchKey == '/')
        {
            $searchKeyArray = array($searchKey);
        }
        else
        {
            $searchKeyArray = array_unique(preg_split("/[\s|\/|~]+/", $searchKey));
        }
        
        if (count($searchKeyArray) > 0)
        {
            foreach ($searchKeyArray as $searchKey)
            {
                if ($searchKey === true)
                {
                    continue;
                }

                //过滤转义特殊字符
                $convertWords = array('+', '.', '?', '$', '^', '*', '(', ')', '[', ']', '{', '}');
                $convertWordValues = array('\\+', '\\.', '\\?', '\\$', '\\^', '\\*', '\\(', '\\)', '\\[', '\\]', '\\{', '\\}');
                $searchKey = str_replace($convertWords, $convertWordValues, $searchKey);
                $content = self::_dealRedTag($content, $searchKey);
            }
        }

        return $content;
    }

    /**
     * 
     * 昵称标红
     * @param $params
     */
    public static function nameRedTag($params)
    {
        if (empty($params['name']) || empty($params['key_word']))
        {
            return $params['name'];
        }

        $name = $params['name'];
        $keyWord = $params['key_word'];
        
        if (preg_match("/($keyWord)/i", $name))
        {
            $name = preg_replace("/($keyWord)/i", "<span style='color: red;'>\\1</span>", $name);
        }

        return $name;
    }

    /**
    * 标红处理
    *
    * @param string $content  内容
    * @param   string  $searKey  标红的对象
    * @return string
    */
    private static function _dealRedTag($content, $pregKey) 
    {
        if (empty($content) || empty($pregKey)) 
        {
            return $content;
        }

        $htmlTagChanged = array();
        $htmlTagOrigin = array();
        preg_match_all("/<(\S*?)[^>]*>.*?<\/\\1>|<[^>]+>|<sina:link[^>]*>/i", $content, $tmps);
        
        foreach ($tmps[0] as $k => $v)
        {
            $htmlTagChanged[] = "#tag{$k}#";
            $htmlTagOrigin[] = $v;
        }

        $content = str_replace($htmlTagOrigin, $htmlTagChanged, $content);
        $content = preg_replace("/($pregKey)/i", "<span style='color: red;'>\\1</span>", $content);
        $content = str_replace("＃", "#", $content);
        $content = preg_replace("/#([^#]+)#/ies", "strip_tags('#\\1#')", $content);
        $content = str_replace($htmlTagChanged, $htmlTagOrigin, $content);

        return $content;
    }
}