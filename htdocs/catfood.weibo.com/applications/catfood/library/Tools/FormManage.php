<?php
/*
 * @title     表单处理跳转
 * @Author    quanjunw
 * @Date      2014-5-22下午2:55:20
 * @Copyright copyright(2014) weibo.com all rights reserved 
 */
class Tools_FormManage
{
    //参数错误表单跳转
    public static function paramErrorResponse($url, $msg = '', $code = Teacher_StoreController::EINVAL)
    {
        self::execErrorLog($msg);
        if ($code)
        {
            $url .= '?code=' . $code;
        }
        Tool_Redirect::response($url);
    }
    
    //执行结果
    public static function executeResult($url, $code, $msg = '')
    {
        if ($code)
        {
            $url .= '?code=' . $code;
            
            if ($code == Teacher_StoreController::FAILURE)
            {
                self::execErrorLog($msg);
            }
        }
        Tool_Redirect::response($url);
    }
    
    //执行操作失败log记录
    public static function execErrorLog($info)
    {
        $errorInfo = 'url=>' . Comm_Context::getServer('SCRIPT_URI');
        if (is_array($info))
        {
            foreach ($info as $key => $val)
            {
                $errorInfo .= '|' . $key . '=>' . $val;
            }
            Tool_Log::error($errorInfo);
        }
        else 
        {
            Tool_Log::error($errorInfo . '|' . $info);
        }
    }
}