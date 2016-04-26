<?php
/**
 * Js输出同一格式
 *
 * @package    tool
 * @copyright  copyright(2011) weibo.com all rights reserved
 * @author     weibo.com php team
 */
class Tool_Jsout
{
    /**
     * 根据与RIA的约定按json格式输出响应
     * 
     * @param string $code
     * @param string $msg
     * @param mixed $data
     */
    public static function normal($code, $msg = null, $data = null)
    {
        if (!headers_sent())
        {
            header('Content-type: application/json; charset=utf-8', true);
        }
        
        //_k可能会被输出，防止xss漏洞
        $k = htmlspecialchars(Comm_Context::param('_k', ''), ENT_QUOTES);
        if ($k)
        {
            Comm_Response::setMetaData('key', $k);
        }

        $type = Comm_Context::param('_t', 0);
        $data = ($data === null ? new stdClass() : $data);
        
        if ($type === '1')
        {
            //解决ie6下jsonp请求第一次失败问题
            header("Cache-Control: maxage=1");
            Comm_Response::useJsonpAsCallback();
            Comm_Response::outJsonp(Comm_Context::param('_v', 'callback'), $code, $msg, $data);
        }
        elseif ($type === '2')
        {
            Comm_Response::useJsonpAsVar();
            Comm_Response::outJsonpIframe(Comm_Context::param('_v', 'v'), $code, $msg, $data, false);
        }
        else
        {
            Comm_Response::outJson($code, $msg, $data, false);
        }
    }
    
    /**
     *  
     * 小黄签JSONP格式
     * 
     * @param string $code
     * @param string $msg
     * @param mixed $data
     */
    public static function notice($code, $msg = null, $data = null)
    {
        $callback = Comm_Context::request('callback', ''); 
        $_GET['_t'] = "1";
        $_GET['_v'] = $callback;

        self::normal($code, $msg, $data);
    }
}