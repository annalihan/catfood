<?php
class ErrorController extends Yaf_Controller_Abstract
{
    public function errorAction($exception)
    {
        //记录错误信息
        $traces = $exception->getTrace();
        $errorMessage = array(
            'message' => $exception->getMessage(),
            'trace' => $traces[0]['file'],
        );
        echo "<pre>\n";
        print_r($errorMessage);
        echo "\n</pre>";
    }
}