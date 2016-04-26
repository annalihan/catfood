<?php
class Tool_Analyze_At
{
    private static $_filterChars = array("`", "~", "!", "#", "$", "%", "^", "&", "*", "(", ")", "=", "+", "[", "]", "{", "}", "|", "'", ";", ":", "\"", "?", "/", ">", "<", ",", ".", "｀", "～", "·", "！", "◎", "＃", "￥", "％", "※", "×", "（", "）", "＋", "－", "＝", "§", "÷", "】", "【", "『", "』", "‘", "’", "“", "”", "；", "：", "？", "、", "》", "。", "《", "，", "／", "＞", "＜", "｛", "｝", "＼");

    //存储替换规则  '被替换字符串'=>'替换成字符串'
    private static $_stripArray = array();

    const AT_LOCATION_SEARCH_URL = "http://weibo.com/n/";

    /**
     * 提取出@
     *
     * @param string $content
     * @return array all the @username
     */
    public static function getAtUserName($content)
    {
        $content = strip_tags($content);
        $content = self::_stripEmail($content, false);
        $content = self::_mbFilter($content);

        $names = array();
        $result = preg_match_all("/@([\x{4e00}-\x{9fa5}\x{ff00}-\x{ffff}\x{0800}-\x{4e00}\x{3130}-\x{318f}\x{ac00}-\x{d7a3}a-zA-Z0-9_\-]+)/u", $content, $names);
        if ($result === false)
        {
            return array();
        }

        $atName = array_unique($names[1]);
        $atName = array_combine($atName, $atName); //?
        foreach ($atName as $key => $value)
        {
            if (preg_match("/^[0-9]{3,10}$/", $value))
            {
                //解析微号
                try
                {
                    $userInfo = Dr_User::getUserInfoByDomain($value);
                    if (isset($userInfo['weihao']) && $userInfo['weihao'] == $value)
                    {
                        $atName[$key] = $userInfo['screen_name'];
                    }
                }
                catch (Comm_Weibo_Exception_Api $e)
                {
                    $names[1] = array();
                }
            }
        }

        return $atName;
    }
    
    /**
     * 将微号替换成昵称
     * @param  [type] $content [description]
     * @param  [type] $atName  [description]
     * @return [type]          [description]
     */
    public static function replaceWeihaoToNick($content, $atName)
    {
        foreach ($atName as $key => $value)
        {
            if (preg_match("/^[0-9]{3,10}$/", $key))
            { 
                $content = str_replace($key, $value, $content);
            }
        }

        return $content;
    }

    /**
     * 提取文本中的email地址
     * @param  [type] $content [description]
     * @return [type]      [description]
     */
    public static function getEmail($content)
    {
        $pattern = "/[a-z0-9]([a-z0-9]*[-_\.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[\.][a-z]{2,3}([\.][a-z]{2})?/i";
        preg_match_all($pattern, $content, $emails);
        return $emails[0];
    }

    /**
     * 去除文本中的email地址
     * @param  [type]  $content [description]
     * @param  boolean $replaceString [description]
     * @return [type]           [description]
     */
    private static function _stripEmail($content, $replaceString = false)
    {
        $emails = self::getEmail($content);

        foreach ($emails as $no => $email)
        {
            if ($replaceString)
            {
                $replaceString = "reSinaEmail" . $no;
                self::$_stripArray[$replaceString] = $email;
                $content = str_replace($email, $replaceString, $content);
            }
            else
            {
                $content = str_replace($email, "", $content);
            }
        }

        return $content;
    }

    public static function atToLink($content, $atUsers, $isTarget = false)
    {
        if (!is_array($atUsers))
        {
            return $content;
        }

        mb_internal_encoding("utf-8");

        //去除字符串中的email地址
        self::$_stripArray = array();
        $content = self::_stripEmail($content, true);

        //对要替换昵称长度排序
        usort($atUsers, array('Tool_Analyze_At', 'sortByLength'));
        $targetString = $isTarget ? "target=\"_blank\"" : "";
        foreach ($atUsers as $nick)
        {
            $content = preg_replace('|(?!>.*)((<span[^>]+>)?@(<span[^>]+>)?(' . $nick . ')(</span>)?)(?![^<]*<\/)|e', "'<a href=\"'.self::AT_LOCATION_SEARCH_URL . urlencode('\\4') . '\" usercard=\"name=\\4\" {$targetString}>\\1</a>'", $content);
        }

        //还原原始$content
        foreach (self::$_stripArray as $pat => $replaceString)
        {
            $content = mb_ereg_replace($pat, $replaceString, $content);
        }

        return $content;
    }

    public static function stripMinblogTags($text)
    {
        mb_internal_encoding("utf-8");
        $pattern = ">#(.*?)#<\/a>";
        $result = array();
        preg_match_all($pattern, $text, $result);

        if (is_array($result) && count($result) > 0)
        {
            $paResult = $result[1];
            foreach ($paResult as $key => $value)
            {
                $pattern2 = "<[^>]*>";
                $content = mb_ereg_replace($pattern2, "", $value);
                $paResult[$key] = $content;
            }

            $rep = $result[0];
            foreach ($rep as $key => $ma)
            {
                $text = str_replace($ma, "#" . $paResult[$key] . "#</a", $text);
            }
        }

        return $text;
    }

    /**
     * 过滤掉字符串中的特殊字符
     *
     * @param string $str
     * @return string the filtered string
     */
    private static function _mbFilter($str)
    {
        mb_internal_encoding("utf-8");
        return str_replace(self::$_filterChars, ' ', $str);
    }
    
    public static function sortByLength($a, $b)
    {
        if (strlen($a) == strlen($b))
        {
            return 0;
        }

        return (strlen($a) < strlen($b)) ? 1 : - 1;
    }
}

