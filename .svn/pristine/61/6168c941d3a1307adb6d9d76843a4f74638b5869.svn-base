<?php

class Tool_Array
{
    /**
     * 根据key从数组中找到相关值，其中key是依据$delimiter分离的，默认为"."
     *
     * // 比如获取值： $array['foo']['bar']
     * $value = Tool_Array::path($array, 'foo.bar');
     *
     * 使用 "*"作为匿名
     *
     * // Get the values of "color" in theme
     * $colors = Tool_Array::path($array, 'theme.*.color');
     *
     * @param array 数组
     * @param string key字符串，多维的由$delimiter连接
     * @param mixed 如果没有查到数组中的该值返回的默认值
     * @param string key path 的分隔符
     * @return mixed
     */
    public static function path($array, $path, $default = null)
    {
        if (isset($array[$path]))
        {
            return $array[$path];
        }

        $delimiter = ".";
        $keys = explode($delimiter, $path);

        while ($keys)
        {
            $key = array_shift($keys);

            if (isset($array[$key]))
            {
                if ($keys)
                {
                    if (is_array($array[$key]))
                    {
                        $array = $array[$key];
                    }
                }
                else
                {
                    return $array[$key];
                }
            }
            elseif ($key === '*')
            {
                $values = array();
                $innerPath = implode($delimiter, $keys);

                foreach ($array as $arr)
                {
                    $value = is_array($arr) ? self::path($arr, $innerPath) : $arr;
                    if ($value)
                    {
                        $values[] = $value;
                    }
                }
                
                if ($values)
                {
                    return $values;
                }
            }
        }

        return $default;
    }
    
    /**
     * 数组合并，后者覆盖前者
     *     如果值为数组，那么继续合并
     * @param  [type] $array1 [description]
     * @param  [type] $array2 [description]
     * @return [type]         [description]
     */
    public static function merge($array1, $array2)
    {
        $arrayReturn = $array1;

        foreach ($array2 as $key => $value)
        {
            if (is_array($value) && isset($array1[$key]) && is_array($array1[$key]))
            {
                $arrayReturn[$key] = self::merge($array1[$key], $value);
            }
            else
            {
                $arrayReturn[$key] = $value;
            }
        }

        return $arrayReturn;
    }
}