<?php
class Comm_ArgChecker_DataList
{
    protected static $delimiter = '';
    protected static $tempData = '';
    
    private static $_valueTypes = array('int' => true, 'float' => true, 'enum' => true, 'string' => true);

    /**
     * ???
     *
     * @param mixed $data  待检查的数据
     * @param unknown_type $type
     * @param unknown_type $delimiter
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $is_needed 空值限制选项，默认为1。 
     * @param enum $must_correct 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * @return bool
     */
    public static function datalist($data, $type, $delimiter, $rules = '', $is_needed = 1, $must_correct = 1, $default = null)
    {
        self::$tempData = '';
        
        $type = strtolower($type);
        Comm_Assert::true(isset(self::$_valueTypes[$type]), 'basic type should be only int,float,enum,string.');
        
        $delimiter = Comm_ArgChecker::extractEscapedChars($delimiter);
        $rules = Comm_ArgChecker::extractEscapedChars($rules);
        $default = $default === null ? null : Comm_ArgChecker::extractEscapedChars($default);
        $is_needed = intval($is_needed);
        $must_correct = intval($must_correct);
        
        Comm_Assert::true(is_string($delimiter) && $delimiter, 'delimiter should be a valid string');
        self::$delimiter = $delimiter;
        self::$tempData = explode($delimiter, $data);
        
        foreach (self::$tempData as $key => $value)
        {
            Comm_ArgChecker::$type($value, $rules, $is_needed, $must_correct, $default);
        }
        
        return true;
    }
    
    /**
     * 验证数据集元素容量是否小于等于某值
     *
     * @param mixed $data  待验证的数据集
     * @param int $length  最大值阈值
     * @return bool
     */
    public static function max($data, $length)
    {
        Comm_Assert::true(is_array(self::$tempData), 'must use type rule to define the subtype and rules first of all');
        return count(self::$tempData) <= intval($length);
    }
    
    /**
     * 验证数据集元素容量是否大于等于某值
     *
     * @param mixed $data  待验证的数据集
     * @param int $length  最小值阈值
     * @return bool
     */
    public static function min($data, $length)
    {
        Comm_Assert::true(is_array(self::$tempData), 'must use type rule to define the subtype and rules first of all');
        return count(self::$tempData) >= intval($length);
    }
    
    /**
     * 验证数据集元素是否唯一
     *
     * @param mixed $data  待验证的数据集
     * @return bool
     */
    public static function unique($data)
    {
        Comm_Assert::true(is_array(self::$tempData), 'must use type rule to define the subtype and rules first of all');
        return count(array_unique(self::$tempData)) === count(self::$tempData); 
    }
}
