<?php
class Comm_Weibo_MIDConverter
{
    private static $_string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private static $_encodeBlockSize = 7;
    private static $_decodeBlockSize = 4;
    private static $_compatMIDs = array(
        '109' => true,
        '110' => true,
        '201' => true,
        '211' => true,
        '221' => true,
        '231' => true,
        '241' => true
    );

    /**
     * 批量从10进制转换到62进制
     *
     * @param array $mids
     * @return array 以10进制mid为key的62进制mid数组
     */
    public static function multiFrom10To62(array $mids)
    {
        $ret = array();
        foreach ($mids as $mid)
        {
            $ret[$mid] = self::from10to62($mid);
        }

        return $ret;
    }

    /**
     * 批量从62进制转换到10进制
     *
     * @param array $mids
     * @param bool $compat
     * @param bool $forMid
     * @return array 以62进制mid为key的10进制mid数组
     */
    public static function multiFrom62to10(array $mids, $compat = false, $forMid = true)
    {
        $ret = array();
        foreach ($mids as $mid)
        {
            $ret[$mid] = self::from62to10($mid, $compat, $forMid);
        }

        return $ret;
    }

    /**
     * 将mid从10进制转换成62进制字符串
     *
     * @param string $mid
     * @return string
     */
    public static function from10to62($mid)
    {
        $str = "";
        $midlen = strlen($mid);
        $segments = ceil($midlen / self::$_encodeBlockSize);
        $start = $midlen;
        for ($i = 1; $i < $segments; $i += 1)
        {
            $start -= self::$_encodeBlockSize;
            $seg = substr($mid, $start, self::$_encodeBlockSize);
            $seg = self::_encodeSegment($seg);
            $str = str_pad($seg, self::$_decodeBlockSize, '0', STR_PAD_LEFT) . $str;
        }

        $str = self::_encodeSegment(substr($mid, 0, $start)) . $str;
        return $str;
    }

    /**
     * 将62进制字符串转成10进制mid
     *
     * $compat $forMid 参数意图不确定，直接从原作者处考过来。使用时直接使用默认参数即可。
     *
     * @param string $str
     * @param bool $compat
     * @param bool $forMid
     * @return string
     */
    public static function from62to10($str, $compat = false, $forMid = true)
    {
        $mid = "";
        $strlen = strlen($str);
        $segments = ceil($strlen / self::$_decodeBlockSize);
        $start = $strlen;
        for ($i = 1; $i < $segments; $i += 1)
        {
            $start -= self::$_decodeBlockSize;
            $seg = substr($str, $start, self::$_decodeBlockSize);
            $seg = self::_decodeSegment($seg);

            /*
             * Note by Rodin: 由于 decodeBlockSize设置为4，而 encodeBlockSize 设置为 7 而 base 62里4位最大的数为 ZZZZ，对应到10进制为 14776336，比encodeBlockSize 多1位……所以，将10进制转成62进制且在不足位时左补0，不会有任何问题。反之， 在62转成10进制的时候就可能会出现溢出，这是个潜在的bug，不清楚为什么目前没有 bug爆出且当初采用了这种不标准不严谨的62进制转换法…… 目前常用的mid里存在类似： l4ETJ4DfL(9位)，在每segment大约出现大于 GZZZ 的串以后 总位数就会不符合目前的预期…… 例如： ZZZZZZZZZ转换成10进制会成为 611477633614776336，总位数已经达到了20位，而常见的7位 mid的62进制数字通常只有16位
             */
            $mid = str_pad($seg, self::$_encodeBlockSize, '0', STR_PAD_LEFT) . $mid;
        }

        $mid = self::_decodeSegment(substr($str, 0, $start)) . $mid;

        // 判断v3、v4版本mid
        if ($forMid)
        {
            $midlen = strlen($mid);
            $first = substr($mid, 0, 1);
            if ($midlen == 16 && ($first == '3' || $first == '4'))
            {
                return $mid;
            }

            if ($midlen == 19 && $first == '5')
            {
                return $mid;
            }
        }

        if ($compat && self::$_compatMIDs[substr($mid, 0, 3)] === false)
        {
            $mid = self::_decodeSegment(substr($str, 0, 4)) . self::_decodeSegment(substr($str, 4));
        }

        if ($forMid)
        {
            if (substr($mid, 0, 1) == '1' && substr($mid, 7, 1) == '0')
            {
                $mid = substr($mid, 0, 7) . substr($mid, 8);
            }
        }

        return $mid;
    }

    /**
     * 将10进制转换成62进制
     *
     * @param string $str
     * @return string
     */
    private static function _encodeSegment($str)
    {
        $out = '';
        while ($str > 0)
        {
            $idx = $str % 62;
            $out = substr(self::$_string, $idx, 1) . $out;
            $str = floor($str / 62);
        }

        return $out;
    }

    /**
     * 将62进制转换成10进制
     *
     * @param string $str
     * @return string
     */
    private static function _decodeSegment($str)
    {
        $out = 0;
        $base = 1;
        for ($t = strlen($str) - 1; $t >= 0; $t -= 1)
        {
            $out = $out + $base * strpos(self::$_string, substr($str, $t, 1));
            $base *= 62;
        }
        
        return strval($out);
    }
}
