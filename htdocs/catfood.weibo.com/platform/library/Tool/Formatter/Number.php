<?php
class Tool_Formatter_Number
{
    public static function number($num)
    {
        $num += 0;
        return $num < 99999 ? $num : (floor($num / 10000) . "万");
    }
}