<?php

class Tools_StringUtil
{

    public static function get_name_from_path($path)
    {
        $temp = explode("/", $path);
        return array_pop($temp);
    }

    public static function getName($path)
    {
        $temp = explode("/", $path);
        $name =  array_pop($temp);
        return substr($name, 0, strrpos($name, '.'));
    }
    
    public static function getExtFromPath($path)
    {
        $temp = explode("/", $path);
        $name =  array_pop($temp);
        return substr($name, strrpos($name, '.'));
    }
    
    public static function encode_path($path)
    {
        $temp = explode("/", $path);
        foreach ($temp as &$one)
        {
            $one = rawurlencode($one);
        }
        return implode("/", $temp);
    }

    public static function println($message)
    {
        echo $message . "<br/>";
    }

    public static function addNickNameHref($nickName)
    {
        $href = Tools_MiniBlogApiWrapper::WEIBO_URL . $nickName;
        return $href;
    }

    public static function get_extend_type($file_name)
    {
        $text = substr($file_name, strrpos($file_name, "."));
        return $text;
    }

    public static function is_document($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".pdf" || $extend_type == ".ppt")
        {
            return true;
        }
        return false;
    }

    public static function is_ppt($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".ppt")
        {
            return true;
        }
        return false;
    }
    
    public static function is_pdf($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".pdf")
        {
            return true;
        }
        return false;
    }
    
    public static function is_doc($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".doc")
        {
            return true;
        }
        return false;
    }
    
    
    public static function is_mp4($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".mp4")
        {
            return true;
        }
        return false;
    }
    
    public static function is_flv($file_name)
    {
        $extend_type = self::get_extend_type($file_name);
        if ($extend_type == ".flv")
        {
            return true;
        }
        return false;
    }

    public static function get_picture_id($path)
    {
        if (empty($path))
        {
            return '';
        }
        else
        {
            $temp = explode("/", $path);
            $suffix = array_pop($temp);
            $temp = explode(".", $suffix);
            return array_shift($temp);
        }
    }
    
    //字符串转化成新版商学院路由格式
    public static function StringReplace ($str)
    {
        $httpHpst  = 'http://xueyuan.weibo.com';
        $data   = explode('/', $str);
        
        //判断域名为空时返回空
        
        if (empty($data['2']))
        {
            return '';
        }
        
        $newdata = explode('/', $httpHpst);
        
        $newArr = array_intersect($data, $newdata);
        $newStr = implode('/', $newArr);

        //参数
        $param = empty($data[count($data) - 1]);
        
        //获取url值
        $val = $data[count($data) - 1];
        if ($newStr == $httpHpst)
        {
            if ($param)
            {
                if ($data['3'] == 'topic')
                {
                    return '/topic/index';
                }
                elseif ($data['3'] == 'teacher')
                {
                    return '/teacher/index';
                }
                elseif ($data['3'] == 'course')
                {
                    return '/course/index';
                }
                elseif ($data['3'] == 'train')
                {
                    return '/train/index';
                }
                elseif ($data['3'] == 'activity')
                {
                    return '/activity/index';
                }
                elseif ($data['3'] == 'corperation')
                {
                    return '/about/apply';
                }
                elseif ($data['3'] == 'contribute')
                {
                    return '/about/submission';
                }
            }
            else
            {
                if ($data['3'] == 'topic')
                {
                    return '/topic/detail?tid=' . $val;
                }
                elseif ($data['3'] == 'teacher' && $data['4'] == 'apply')
                {
                    return '/teacher/apply';
                }
                elseif ($data['3'] == 'course')
                {
                    return '/course/detail?courseid=' . $val;
                }
                elseif ($data['3'] == 'train')
                {
                    return '/train/detail?trainid=' . $val;
                }
            }
        }
        else 
        {
            return $str;
        }
    }
    
    //获取二维数组中固定的字段<系统函数需要php版本为5.5才可使用，所以先判断>
    public static function array_column($input, $columnKey, $indexKey = null)
    {
        if (!function_exists('array_column'))
        {
            $columnKeyIsNumber      = (is_numeric($columnKey)) ? true : false;
            $indexKeyIsNull         = (is_null($indexKey)) ? true : false;
            $indexKeyIsNumber       = (is_numeric($indexKey)) ? true : false;
            $result                 = array();
            foreach ((array)$input as $key => $row)
            {
                if ($columnKeyIsNumber)
                {
                    $tmp            = array_slice($row, $columnKey, 1);
                    $tmp            = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
                }
                else
                {
                    $tmp            = isset($row[$columnKey]) ? $row[$columnKey] : null;
                }
                if (!$indexKeyIsNull)
                {
                    if ($indexKeyIsNumber)
                    {
                        $key        = array_slice($row, $indexKey, 1);
                        $key        = (is_array($key) && !empty($key)) ? current($key) : null;
                        $key        = is_null($key) ? 0 : $key;
                    }
                    else
                    {
                        $key        = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                    }
                }
                $result[$key]       = $tmp;
            }
            return $result;
        }
        else
        {
            return array_column($input, $columnKey);
        }
    }
    
    //判断字符串长度
    public static function checkStrLen($str)
    {
        preg_match_all("/./us", $str, $match);
        return count($match[0]);
    }
    
    //根据固定字段对数组进行排序
    public static function arraySortByTag($arr, $keys, $type)
    {
        if (!empty($arr))
        {
            $keysvalue = $newArr = array();
            foreach ($arr as $k => $v)
            {
                $keysvalue[$k] = $v[$keys];
            }
            if ($type == 'asc')
            {
                asort($keysvalue);
            }
            else
            {
                arsort($keysvalue);
            }
            reset($keysvalue);
            foreach ($keysvalue as $k => $v)
            {
                $newArr[] = $arr[$k];
            }
            return $newArr;
        }
        else
        {
            return $arr;
        }
    }
    
    //按照推荐课程权重排序
    public static function arraySortByWeigh($arr, $order = 'asc')
    {
        $trainArr = array();
        for ($i = 0; $i < count($arr); $i++)
        {
            
            if ($arr[$i]['weigh'] == $arr[$i + 1]['weigh'])
            {
                
                if ($order == 'asc')
                {
                    if (strtotime($arr[$i]['train_time']) < strtotime($arr[$i + 1]['train_time']))
                    {
                        $trainArr[] = $arr[$i];
                    }
                    else
                    {
                        $trainArr[] = $arr[$i + 1];
                        $arr[$i + 1] = $arr[$i];
                    }
                }
                else
                {
                    if (strtotime($arr[$i]['train_time']) > strtotime($arr[$i + 1]['train_time']))
                    {
                        $trainArr[] = $arr[$i];
                    }
                    else 
                    {
                        $trainArr[] = $arr[$i + 1];
                        $arr[$i + 1] = $arr[$i];
                    }
                }
            }
            else 
            {
                $trainArr[] = $arr[$i];
            }
        }
        return $trainArr;
    }

}