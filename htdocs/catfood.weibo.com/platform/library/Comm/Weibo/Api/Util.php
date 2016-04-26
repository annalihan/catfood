<?php

/**
 * 各种API的各种工具、处理过程
 */
class Comm_Weibo_Api_Util
{
    /**
     * 检查是否为整型数值
     * @param unknown_type $val
     * @throws Comm_Exception_Program
     */
    public static function checkInt($val)
    {
        if (!preg_match('/^\d+$/', $val))
        {
            throw new Comm_Exception_Program('the type of the batch parameters must be int: '.$val);
        }
    }

    /**
     *
     * 检查批量传入的int型参数格式
     * @param string $val
     */
    public static function checkBatchValues($val, $type, $delimit = ',', $max = false, $min = false)
    {
        if (empty($val))
        {
            return $val;
        }

        $values = explode($delimit, $val);

        switch ($type)
        {
            case 'int':
            case 'int64':
                foreach ($values as $k => $v)
                {
                    self::checkInt($v);
                }

                break;
        }

        $count = count($values);

        if ($max && $count > $max)
        {
            throw new Comm_Exception_Program('the number of the batch values must be less than' . $max);
        }

        if ($min && $count < $min)
        {
            throw new Comm_Exception_Program('the number of the batch values must be greater than' . $min);
        }

        return $val;
    }

    /**
     * 检查二者必选且仅可选其一参数 callback方法
     * @param Comm_Weibo_Api_Request_Platform $platform platform对象
     * @param string $one 参数1
     * @param string $other 参数2
     */
    public static function checkAlternative($one, $other, Comm_Weibo_Api_Request_Platform $platform)
    {
        if ((is_null($platform->$one) xor is_null($platform->$other)) === false)
        {
            throw new Comm_Exception_Program("one of the {$one} and {$other} params must be send, and can only send one!");
        }
    }

    /**
     * 定义2个批量参数互斥规则
     * @param array $actualNames uid和screenName参数名组合
     */
    public static function oneOrOtherMulti($request, $actualNames = array(array('uid', 'screenName')))
    {
        foreach ($actualNames as $actualName)
        {
            $request->addRule($actualName[0], "string");
            $request->addRule($actualName[1], "string");
        }

        $request->addBeforeSendCallback('Comm_Weibo_Api_Util', "checkAlternativeMulti", array($actualNames));
    }

    /**
     * 检查多个二者必选且仅可选其一参数 callback方法
     * @param object $platform 对象
     * @param array $actualNames 参数名组合
     * @throws Comm_Exception_Program
     */
    public static function checkAlternativeMulti($actualNames, $platform)
    {
        foreach ($actualNames as $actualName)
        {
            self::checkAlternative($actualName[0], $actualName[1], $platform);
        }
    }
}
