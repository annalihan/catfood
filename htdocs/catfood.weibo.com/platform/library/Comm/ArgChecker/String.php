<?php
class Comm_ArgChecker_String
{
    
    /**
     * 验证是否仅包含安全字符（字母、数字、运算符、标点符号、回车、换行、删除、tab）
     * 
     * This function is compatible with multi-bytes utf-8.
     * 
     * @param string $string 待验证的字符串
     * @return bool 
     */
    static protected function safechars($string)
    {
        $string = (string)$string;
        for ($i = 0, $i_count = strlen($string); $i < $i_count; $i++)
        {
            $charValue = ord($string[$i]);

            if (($charValue < 32 && ($charValue !== 13 && $charValue !== 10 && $charValue !== 9)) || $charValue == 127)
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * 默认规则（是否仅包含安全字符）
     * 
     * @param string $data  待验证的字符串
     * @return bool 
     */
    public static function basic($data)
    {
        return self::safechars($data);
    }
    
    /**
     * 是否仅包含可打印字符
     * 
     * @param string $string  待验证的字符串
     * @param bool $utf8Compatible 是否兼容utf8，如果为真，则认为多字节utf-8(包含0x80~0xFF)也为可打印字符。否则，该函数只允许包含32~126的之间的字符。可选，默认true。
     * @return bool
     */
    public static function printable($string, $utf8Compatible = true)
    {
        $string = (string) $string;
        for ($i = 0, $i_count = strlen($string); $i < $i_count; $i++)
        {
            $charValue = ord($string[$i]);

            if ($charValue < 32 || $charValue === 127 || !$utf8Compatible && $charValue > 127)
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * 验证字符串长度是否小于等于某值
     *
     * @param string $data  待验证的字符串
     * @param int $length 最大值阈值
     * @return bool
     */
    public static function max($data, $length)
    {
        return strlen($data) <= $length;
    }
    
    /**
     * 验证字符串长度是否大于等于某值
     *
     * @param string $data  待验证的字符串
     * @param int $length 最大值阈值
     * @return bool
     */
    public static function min($data, $length)
    {
        return strlen($data) >= $length;
    }
    
    /**
     * 验证字符串长度是否小于等于某值（支持宽字符）
     *
     * @param string $data  待验证的字符串
     * @param int $length 最大值阈值
     * @return bool
     */
    public static function widthMax($data, $length)
    {
        return mb_strwidth($data, 'utf-8') <= $length;
    }
    
    /**
     * 验证字符串长度是否大于等于某值（支持宽字符）
     *
     * @param string $data  待验证的字符串
     * @param int $length 最大值阈值
     * @return bool
     */
    public static function widthMin($data, $length)
    {
        return mb_strwidth($data, 'utf-8') >= $length;
    }
    
    /**
     * 验证是否匹配某正则表达式
     * 
     * @param string $data  待验证的字符串
     * @param string $regularExpression  正则表达式
     * @return bool
     */
    public static function preg($data, $regularExpression)
    {
        // ,和;在Comm_ArgChecker有特殊的用途，是转过义的，这里要转回来(\,和\;)
        $regularExpression = Comm_ArgChecker::extractEscapedChars($regularExpression);
        return (bool)preg_match($regularExpression, $data);
    }
    
    /**
     * Comm_ArgChecker_String::preg()的别名
     * 
     * @see Comm_ArgChecker_String::preg()
     * @param string $data    待验证的字符串
     * @param string $regularExpression   正则表达式
     * @return bool
     */
    public static function re($data, $regularExpression)
    {
        return self::preg($data, $regularExpression);
    }
    
    public static function charslist($data, $charlist)
    {
        return !trim($data, $charlist);
    }
    
    public static function num($data)
    {
        return (bool)self::charslist($data, '0123456789'); 
    }
    
    /**
     * 验证是否只包含字母或数字
     *
     * @param string $data  待验证的字符串
     * @return bool
     */
    public static function alnum($data)
    {
        return self::preg($data, '/^[a-z0-9]*$/iD');
    }
    
    /**
     * 验证是否只包含字母
     *
     * @param string $data  待验证的字符串
     * @return bool
     */
    public static function alpha($data)
    {
        return self::preg($data, '/^[a-z]*$/iD');
    }
    
    /**
     * 验证是否只包含小写字母
     *
     * @param string $data  待验证的字符串
     * @return bool
     */
    public static function lower($data)
    {
        return self::preg($data, '/^[a-z]*$/D');
    }
    
    /**
     * 验证是否只包含大写字母
     *
     * @param string $data  待验证的字符串
     * @return bool
     */
    public static function upper($data)
    {
        return self::preg($data, '/^[A-Z]*$/D');
    }
    
    /**
     * 验证是否符合十六进制字符串规则
     *
     * @param string $data  待验证的字符串
     * @return bool
     */
    public static function hex($data)
    {
        return self::preg($data, '/^[a-f0-9]*$/iD');
    }

    /**
     * 验证是否为JSON字符串
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function json($data)
    {
        return json_decode($data, true) !== null;
    }
}