<?php 
class Comm_Helpers 
{
    public static function p($obj = __LINE__, $flag = 0)
    {
    
        //0 print_r 2var_dump 4json 6text 8htmlspecialchars
        $ishtml = ($flag == 6 || $flag == 7) ? FALSE : TRUE;
        $obj = is_object($obj) ? self::objectToArray($obj) : $obj;
        if ($ishtml)
            echo '<pre><div style="background:#fff;color:#000;border:1px dashed #f00;padding:20px;">';
    
        switch ($flag)
        {
            case 0:
            case 1:
                print_r($obj);
                break;
            case 2:
            case 3:
                var_dump($obj);
                break;
            case 4:
            case 5:
                print_r(json_encode($obj));
                break;
            case 6:
            case 7:
                $dirname = 'ptestlog';
                if (!is_dir($dirname))
                {
                    mkdir($dirname);
                }
                date_default_timezone_set('Asia/Shanghai');
                $filename = $dirname . '/' . date('YmdHis') . '.txt';
                file_put_contents($filename, json_encode($obj));
                break;
            case 8:
            case 9:
                print_r(self::array_htmlspecialchars($obj, ENT_QUOTES));
                break;
            default :
                print_r($obj);
                break;
        }
        $e = $ishtml == TRUE ? '</div></pre>' : '';
        if ($flag % 2 == 1)
        {
            exit($e);
        }
        else if ($ishtml)
        {
            echo $e;
        }
    }
    
    public static function array_htmlspecialchars($obj = array())
    {
        if (is_array($obj))
        {
            $obj = array_map(__FUNCTION__, $obj);
        }
        else
        {
            $obj = htmlspecialchars($obj);
        }
        return $obj;
    }
    
    public static function objectToArray($object = null)
    {
        $result = array();
        if (empty($object))
        {
            return $result;
        }
        $object = is_object($object) ? get_object_vars($object) : $object;
        if (is_array($object))
        {
            foreach ($object as $key => $val)
            {
                $val = (is_object($val) || is_array($val)) ? self::objectToArray($val) : $val;
                $result[$key] = $val;
            }
        }
        return $result;
    }
}
