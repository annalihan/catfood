<?php
class Comm_ArgChecker_Float
{
    public static function basic($data)
    {
        return is_numeric($data);
    }   

    /**
     * 验证是否大于等于某值
     *
     * @param string $data  待验证的数据
     * @param int $min  最小值阈值
     * @return bool
     */
    public static function min($data, $min)
    {
        return $data >= $min;
    }
    
    /**
     * 验证是否小于等于某值
     *
     * @param string $data  待验证的数据
     * @param int $max  最大值阈值
     * @return bool
     */
    public static function max($data, $max)
    {
        return $data <= $max;
    }    
    
    /**
     * 验证数据值是否在某两值之间（含）
     *
     * @param string $data  待验证的数据
     * @param int $leftValue  区间最小值
     * @param int $rightValue  区间最大值
     * @return bool
     */
    public static function range($data, $leftValue, $rightValue)
    {
        if ($leftValue >= $rightValue)
        {
            $min = $rightValue;
            $max = $leftValue;
        }
        else
        {
            $min = $leftValue;
            $max = $rightValue;
        }

        return ($data <= $max && $data >= $min);
    }
}