<?php
define('CONTEXT_KEY_API_LAST_ERROR', '_API_LAST_ERROR');

abstract class Interface_AbstractController extends AbstractController
{
    private $_fromAliases = array(
        'post' => 'form',
        'get' => 'param',
    );

    private $_requestData = array();

    public function runBefore()
    {
        $this->_checkParams();

        return true;
    }
    
    private function _checkParams()
    {
        if (empty($this->paramInfos))
        {
            return true;
        }

        foreach ($this->paramInfos as $key => $paramInfo)
        {
            //来源:form(post),param(get),request(post,get)
            $from = isset($paramInfo['from']) ? strtolower($paramInfo['from']) : 'request';
            $from = isset($this->_fromAliases[$from]) ? $this->_fromAliases[$from] : $from;

            if (method_exists('Comm_Context', $from) === false)
            {
                $this->failure(API_CODE_FIELD_ILLEGAL, "wrong_param:{$key}");
            }

            $value = Comm_Context::$from($key);
            
            try
            {
                $rule = isset($paramInfo['rule']) ? $paramInfo['rule'] : $paramInfo;

                $type = $rule[0];
                $rule[0] = $value;
                $value = call_user_func_array(array('Comm_ArgChecker', $type), $rule);
                $this->$key = $value;
            }
            catch (Exception $e)
            {
                $errorMessage = $e->getMessage();
                $errorCode = $e->getCode();
                $message = "param:{$key},message:{$errorMessage},value:{$value}";

                switch ($errorCode)
                {
                    case Comm_ArgChecker::ERROR_CODE_RULE:
                        $code = API_CODE_FIELD_ILLEGAL;
                        break;

                    case Comm_ArgChecker::ERROR_CODE_NEEDED:
                        $code = API_CODE_FIELD_MISSING;
                        break;

                    case Comm_ArgChecker::ERROR_CODE_RIGHT:
                        $code = API_CODE_FIELD_INVALID;
                        break;
                    
                    default:
                        $code = API_CODE_FIELD_ILLEGAL;
                        break;
                }

                $this->failure($code, $message);
            }
        }

        return true;
    }

    public function __get($name)
    {
        return $this->getValue($name);
    }
    
    public function __set($name, $value)
    {
        $this->setValue($name, $value);
    }

    public function getValue($name)
    {
        return isset($this->_requestData[$name]) ? $this->_requestData[$name] : null;
    }

    public function setValue($name, $value)
    {
        $this->_requestData[$name] = $value;
    }

    protected function setError($code, $message)
    {
        Comm_Context::set(CONTEXT_KEY_API_LAST_ERROR, array('code' => $code, 'message' => $message), false, true);
    }

    public function failure($code = 0, $message = 'failure', $dext = '')
    {
        if (empty($dext))
        {
            $dext = Comm_Context::get(CONTEXT_KEY_API_LAST_ERROR, '');
        }

        $data = array(
            'sys_data' => $dext, 
            'sys_log_id' => Tool_Log::getLogId(),
        );

        $this->renderApiJson($code, strval($message), $data);
        exit();
    }

    public function success($data, $message = 'success', $dext = '')
    {
        if ($dext && is_array($data))
        {
            $data['sys_data'] = $dext;
        }

        if (is_array($data))
        {
            $data['sys_log_id'] = Tool_Log::getLogId();    
        }

        $this->renderApiJson(API_CODE_SUCCESS, strval($message), $data);
    }
}
