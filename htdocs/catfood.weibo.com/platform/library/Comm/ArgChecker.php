<?php
/**
 * 参数校验
 *
 * 常见规则
 *     整型：min,0 max,10000 len,5,8;max:30000000 range,0,1000
 *     字符: alpha widthMax,10 min,10 widthMin,10;widthMax,100
 *     ..
 *     
 * @package Common
 * @subpackage ArgChecker
 * @copyright copyright(2011) weibo.com all rights reserved
 * @author weibo.com php team
 */

class Comm_ArgChecker
{
    /**
     * 空值限制选项：可以为null，不使用默认值
     */
    const OPT_NO_DEFAULT = 1;
    
    /**
     * 空值限制选项：可以为null，使用默认值
     */
    const OPT_USE_DEFAULT = 2;
    
    /**
     * 空值限制选项：不可以为null
     */
    const NEED = 3;

    private static $_needValues = array(
        self::OPT_NO_DEFAULT => 1, 
        self::OPT_USE_DEFAULT => 1, 
        self::NEED => 1,
    );
    
    /**
     * 验证限制选项：可以错误，不使用默认值
     */
    const WRONG_NO_DEFAULT = 1;
    
    /**
     * 验证限制选项：可以错误，使用默认值
     */
    const WRONG_USE_DEFAULT = 2;
    
    /**
     * 验证限制选项：不可以错误
     */
    const RIGHT = 3;

    private static $_correctValues = array(
        self::WRONG_NO_DEFAULT => 1, 
        self::WRONG_USE_DEFAULT => 1, 
        self::RIGHT => 1,
    );

    const ERROR_CODE_RULE = 1;
    const ERROR_CODE_NEEDED = 2;
    const ERROR_CODE_RIGHT = 3;
    
    /**
     * 检查int
     * 
     * @param mixed $data  待检查的数据
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $isNeeded 空值限制选项，默认为1。 
     * @param enum $mustCorrect 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * 
     * @return mixed $data或$default的值 
     * @throws Comm_Exception_Program
     */
    public static function int($data, $rule = '', $isNeeded = 1, $mustCorrect = 1, $default = null)
    {
        return self::runChecker('Comm_ArgChecker_Int', $data, $rule, $isNeeded, $mustCorrect, $default);
    }
    
    /**
     * 检查字符串
     * 
     * @param mixed $data  待检查的数据
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $isNeeded 空值限制选项，默认为1。 
     * @param enum $mustCorrect 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * 
     * @return mixed $data或$default的值 
     * @throws Comm_Exception_Program
     */
    public static function string($data, $rule = '', $isNeeded = 1, $mustCorrect = 1, $default = null)
    {
        return self::runChecker('Comm_ArgChecker_String', $data, $rule, $isNeeded, $mustCorrect, $default);
    }
    
    /**
     * 检查浮点类型
     * 
     * @param mixed $data  待检查的数据
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $isNeeded 空值限制选项，默认为1。 
     * @param enum $mustCorrect 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * 
     * @return mixed $data或$default的值 
     * @throws Comm_Exception_Program
     */
    public static function float($data, $rule = '', $isNeeded = 1, $mustCorrect = 1, $default = null)
    {
        return self::runChecker('Comm_ArgChecker_Float', $data, $rule, $isNeeded, $mustCorrect, $default);
    }

    /**
     * 检查枚举类型
     * 
     * @param mixed $data  待检查的数据
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $isNeeded 空值限制选项，默认为1。 
     * @param enum $mustCorrect 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * 
     * @return mixed $data或$default的值 
     * @throws Comm_Exception_Program
     */
    public static function enum($data, $rule = '', $isNeeded = 1, $mustCorrect = 1, $default = null)
    {
        return self::runChecker('Comm_ArgChecker_Enum', $data, $rule, $isNeeded, $mustCorrect, $default);
    }
    
    /**
     * 检查多重数据
     * 如果规则的值里面包含逗号和分号，则需要将里面的逗号和分号转义为 \,和\;，否则会导致规则出错。比如：
     *      delimeter,\,  //以 ,为delimeter规则的参数
     *      delimeter2,\,,\;;delimeter3,'   //以","和";"分别为delimeter2规则的第一个参数和第二个参数，以"'"为delimeter3规则的第三个参数
     * 
     * @param mixed $data  待检查的数据
     * @param string $rule  rule规则。如"max,5;min,-3;"。
     * @param enum $isNeeded 空值限制选项，默认为1。 
     * @param enum $mustCorrect 验证限制选项，默认为1。
     * @param mixed $default 默认值，默认为null。
     * 
     * @return mixed $data或$default的值 
     * @throws Comm_Exception_Program
     */
    public static function datalist($data, $rule = '', $isNeeded = 1, $mustCorrect = 1, $default = null)
    {
        return self::runChecker('Comm_ArgChecker_DataList', $data, $rule, $isNeeded, $mustCorrect, $default);
    }
    
    /**
     * 将转义后的,和;解除转义
     * 
     * @param string $data 待转义的字符串
     * @return string 转义后的字符串
     */
    public static function extractEscapedChars($data)
    {
        return str_replace(array('\,', '\;'), array(',', ';'), $data);
    }
    
    protected static function runChecker($argcheckerType, $data, $rule, $isNeeded, $mustCorrect, $default)
    {
        if (($returnData = self::_getValue($data, $isNeeded, $default)) !== true)
        {
            return $returnData;
        }
        
        $parseRules = self::_parseRules($argcheckerType, $rule);
        if ($parseRules)
        {
            $data = self::_validate($argcheckerType, $parseRules, $data, $mustCorrect, $default);
        }
        
        return self::_getReturn($data, $isNeeded, $mustCorrect, $default);
    }
    
    private static function _parseRules($argcheckerType, $rules)
    {
        $rules = preg_split('#(?<!\\\\);#', $rules);
        if (class_exists($argcheckerType) && method_exists($argcheckerType, 'basic'))
        {
            $parseRules = array(array('method' => 'basic', 'para' => array()));
        }
        else
        {
            $parseRules = array();
        }

        if ($rules)
        {
            foreach ($rules AS $rule)
            {
                $rule = preg_split('#(?<!\\\\),#', $rule);
                $methodName = array_shift($rule);
                if (!$methodName)
                {
                    continue;
                }

                if (!method_exists($argcheckerType, $methodName))
                {
                    throw new Comm_Exception_Program("method_not_exist,{$argcheckerType},{$methodName}", self::ERROR_CODE_RULE);
                }
                else
                {
                    $parseRules[] = array(
                        'method' => $methodName,
                        'para' => $rule,
                    );
                }
            }
        }

        return $parseRules;      
    }    
    
    private static function _getValue($data, $isNeeded, $default)
    {
        if ($data !== null)
        {
            return true;
        }

        switch (true)
        {
            case $isNeeded == self::OPT_NO_DEFAULT:
                return $data;
            
            case $isNeeded == self::OPT_USE_DEFAULT:
                return $data === null ? $default : $data;
            
            case $isNeeded == self::NEED:
                if ($data === null)
                {
                    throw new Comm_Exception_Program('param is needed', self::ERROR_CODE_NEEDED);
                }

                return $data;

            default:
                throw new Comm_Exception_Program('param\'s rule is wrong', self::ERROR_CODE_RULE);
        }    
    }

    private static function _validate($argcheckerType, $rules, $data, $isCorrect, $default)
    {
        if (isset(self::$_correctValues[$isCorrect]) === false)
        {
            throw new Comm_Exception_Program('param\'s rule is wrong', self::ERROR_CODE_RULE);
        }
        
        foreach ($rules as $rule)
        {
            if (!$rule)
            {
                continue;
            }

            array_unshift($rule['para'], $data);
            $rst = call_user_func_array(array($argcheckerType, $rule['method']), $rule['para']);
            if ($rst === false)
            {
                break;
            }
        }

        if ($rst === false)
        {
            // 可以不对，且不需要使用默认值
            if ($isCorrect == self::WRONG_NO_DEFAULT)
            {
                return null;
            }

            // 可以不对，且需要使用默认值 
            if ($isCorrect == self::WRONG_USE_DEFAULT)
            {
                return $default;
            }

            // 必须要对
            if ($isCorrect == self::RIGHT)
            {
                throw new Comm_Exception_Program('param\'s value is wrong', self::ERROR_CODE_RIGHT);
            }
        }

        return $data;      
    }
    
    private static function _getReturn($data, $isNeeded, $isCorrect, $default)
    {
        if ($data === null && ($isNeeded == self::OPT_USE_DEFAULT || $isCorrect == self::WRONG_USE_DEFAULT))
        {
            return $default;
        }

        return $data;
    }
}