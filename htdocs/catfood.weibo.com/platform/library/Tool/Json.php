<?php

class Tool_Json
{
    /**
     * 减少空间的json_encode，不使用unicode编码，而直接使用utf8
     * @param  array $array [description]
     * @return [type]       [description]
     */
    public static function encode($array)
    {
        return urldecode(json_encode(self::_urlEncode($array)));
    }

    private static function _urlEncode($array)
    {
        $returnValue = array();

        if (is_object($array))
        {
            $array = (array)$array;
        }

        if (is_array($array))
        {
            foreach ($array as $key => $value)
            {
                $returnValue[urlencode($key)] = self::_urlEncode($value);
            }
        } 
        else
        {
            $returnValue = urlencode($array);
        }

        return $returnValue;
    }
}