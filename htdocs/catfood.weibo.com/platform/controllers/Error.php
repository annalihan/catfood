<?php
/**
 * @name ErrorController
 * @desc 错误控制器, 在发生未捕获的异常时刻被调用
 * @see http://www.php.net/manual/en/yaf-dispatcher.catchexception.php
 */
class ErrorController extends Yaf_Controller_Abstract
{
    public function errorAction($exception)
    {
        $traces = debug_backtrace(1);

        $errorMessage = array(
            'message' => $exception->getMessage(),
            'trace' => $traces[0]['file'],
        );
        Tool_Log::fatal($errorMessage);

        //TODO

        //do nothing.
        return false;
    }
}
