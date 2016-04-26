<?php
class Comm_ArgChecker_Enum 
{
    /**
     * 验证数据值是否在枚举列表中
     *
     * @param mixed $data  待验证的数据
     * @param mixed $enumerates 枚举列表，多参数
     * @return bool
     */
    public static function enum($data, $enumerates)
    {
        $args = func_get_args();
        array_shift($args);
        
        return in_array(strval($data), $args, true);
    }
}
