<?php

abstract class TestAbstractController extends AbstractController
{
    const FUNCTION_FORMAT = "<script type=\"text/javascript\">outputResult(\"%s\", \"%s\", \"%s\", \"%s\")</script>\r\n";
    public $testName = '测试';
    public $tpl = '../common/tests.phtml';

    public final function run()
    {
        $results = array(
            'tests' => array(), 
            'name' => $this->testName,
        );

        $methods = get_class_methods($this);
        $testMethods = array();

        foreach ($methods as $method)
        {
            if (substr($method, 0, '4') != 'test')
            {
                continue;
            }

            $testMethods[] = $method;
            $methodId = substr($method, 4);
            $results['tests'][] = array(
                'id' => $methodId,
            );
        }

        $this->renderHtml($results);
        $this->_flush();

        usleep(500000);

        foreach ($testMethods as $method)
        {
            $methodId = substr($method, 4);

            try
            {
                $testResult = call_user_func(array($this, $method));
                $message = isset($testResult['message']) ? $testResult['message'] : $methodId;
                $result = (bool)(isset($testResult['result']) ? $testResult['result'] : urlencode(json_encode($testResult)));
                $ext = isset($testResult['ext']) ? urlencode(json_encode($testResult['ext'])) : '';

                printf(self::FUNCTION_FORMAT, $methodId, $message, $result, $ext);
            }
            catch (Exception $e)
            {
                printf(self::FUNCTION_FORMAT, $methodId, $methodId . ' exception, message:' . $e->getMessage() . '.', false, '');
            }
            
            $this->_flush();
        }        
    }

    private function _flush()
    {
        if (ob_get_level())
        {
            ob_flush();
        }

        flush();
    }
}
