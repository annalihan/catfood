<?php

class Tool_Bytes
{
    public static function getString($size, $sep = '')
    {
        $unit = array('B', 'K', 'M', 'G', 'T', 'P');
        return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $sep . $unit[$i];
    }
}