<?php 

class Comm_Response
{
    static protected $useJsonpVar = false;
    static protected $meta = array();
    
    /**
     * 提供json格式中元数据的定制
     * 
     * Tutorial:
     * <code>
     * //默认:
     *  Comm_Response::out_json(0, 'okay', array('That is right'));
     *  //{'code' : 0, 'msg' : 'okay', 'data' : ['That is right']}
     * //定制参数：
     *  Comm_Response::set_meta_data('key', 'foo');
     *  Comm_Response::out_json(0, 'okay', array('That is right'));
     *  //{'key': 'foo', 'code' : 0, 'msg' : 'okay', 'data' : ['That is right']}
     * </code>
     * @param string $name
     * @param mixed $value
     */
    public static function setMetaData($name, $value)
    {
        self::$meta[$name] = $value;
    }
    
    /**
     * 获取json结构定制的元数据
     * 
     * @param string|null $name 数据名。可选，若空，则取null作为默认值。
     * @return mixed 当$name为空时，返回全部meta数据，否则只返回指定的数据。若指定数据未设置过，则返回一个null
     */
    public static function getMetaData($name = null)
    {
        return $name ? (isset(self::$meta[$name]) ? self::$meta[$name] : null) : self::$meta; 
    }
    
    /**
     * 设置在输出jsonp的时候，将$callback参数作为变量处理。
     * 
     */
    public static function useJsonpAsVar()
    {
        self::$useJsonpVar = true;
    }
    
    /**
     * 设置在输出jsonp的时候，将$callback参数作为变量处理
     * 
     */
    public static function useJsonpAsCallback()
    {
        self::$useJsonpVar = false;
    }
    
    /**
     * 按json格式输出响应
     * 
     * @param string|int    $code           js的错误代码/行为代码
     * @param string        $message        可选。行为所需文案或者错误详细描述。默认为空。
     * @param mixed         $data           可选。附加数据。
     * @param bool          $returnString   可选。是否返回一个字符串。默认情况将直接输出。
     * @return string|void  取决与$returnString的设置。如果returnString为真，则返回渲染结果的字符串，否则直接输出，返回空
     */
    public static function outJson($code, $message = '', $data = null, $returnString = false)
    {
        $value = array(
            "code" => $code,
            "msg" => strval($message),
            "data" => $data,
        );
        $jsonString = json_encode(array_merge(self::$meta, $value));

        if ($returnString)
        {
            return $jsonString;
        }
        else
        {
            echo $jsonString;
        }
    }
    
    /**
     * 按jsonp格式输出响应
     * 
     * @param string        $callback       Javascript所需的回调函数名字。如果不合法，则会抛出一个异常。
     * @param string        $code           Javascript所需的行为代码。
     * @param string        $message        可选。行为所需文案或者错误详细描述。默认为空。
     * @param mixed         $data           可选。附加数据。
     * @param bool          $returnString   可选。是否返回一个字符串。默认情况将直接输出。
     * @return string|void  取决于$returnString的设置。如果returnString为真，则返回渲染结果的字符串，否则直接输出，返回空
     * 
     * @throws Comm_Exception_Program
     */
    public static function outJsonp($callback, $code, $message = '', $data = null, $returnString = false)
    {
        if (preg_match('/^[\w\$\.]+$/iD', $callback))
        {
            $jsonp = (!self::$useJsonpVar ? "window.{$callback} && {$callback}(" : "var {$callback}=") . self::out_json($code, $message, $data, true) . (!self::$useJsonpVar ? ")" : "") . ';';
            if ($returnString)
            {
                return $jsonp; 
            }
            else
            {
                echo $jsonp;
                return;
            }
        }

        throw new Comm_Exception_Program('callback name invalid');
    }
    
    /**
     * 输出需要用iframe嵌套的jsonp
     * 
     * @param string        $callback       Javascript所需的回调函数名字。如果不合法，则会抛出一个异常。
     * @param string        $code           Javascript所需的行为代码。
     * @param string        $message        可选。行为所需文案或者错误详细描述。默认为空。
     * @param mixed         $data           可选。附加数据。
     * @see outJsonp
     */
    public static function outJsonpIframe($callback, $code, $message = '', $data = null)
    {
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<script type="text/javascript">document.domain="weibo.com";';
        echo self::outJsonp($callback, $code, $message, $data, true);
        echo '</script>';
    }
    
    /**
     * 直接输出内容
     * 
     * @param string $text
     */
    public static function outPlain($text)
    {
        echo $text;
    }
}