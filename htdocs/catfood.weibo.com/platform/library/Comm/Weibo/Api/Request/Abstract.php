<?php
/**
 * HttpRequest Wrapper类
 * 隐藏!!技术!!细节，方便SDK用户调用
 * 按服务提供方的维度进行封装，例如Platform API、搜索、微盘等应该分别实现具体的子类
 *
 * 设计目标
 * 1、参数数据类型（强行转换）
 * 2、参数必要性（效验）
 * 3、支持CURL并发请求
 * 4、封装某个服务下多个接口共同的业务需求，例如身份效验等需求
 * 5、提供统一的界面供调用者使用（参数设置、获取请求结果、设置callback）
 *
 * @see http://doc.api.weibo.com/index.php/微博接口规范
 */
abstract class Comm_Weibo_Api_Request_Abstract
{
    const ERROR_TYPE_API = 1;
    const ERROR_TYPE_SYS = 2;
    const ERROR_TYPE_INFO = 3;

    protected $httpRequest;
    protected $warningTimeout = 0;

    /**
     * @var string 默认为false，由curl根据参数确定
     */
    protected $method;

    /**
     * $rules[实名] = array(
     * 'dataType' => 'int/int64/string/filepath/float',
     * 'where' => 'PARAM_IN_*',
     * 'isRequired' => 'true/false',
     * 'finalValue' => ''
     * );
     * @var array 存放参数规则
     */
    public $rules = array();

    /**
     * $ruleMethod[paramName] = array(
     *     method,
     * );
     * @var array 存放参数名对应的请求方式
     */
    protected $ruleMethod = array();

    /**
     * $alias[别名] = 实名
     * @var array 存放参数别名
     */
    protected $alias = array();

    /**
     * @var array 参数的值，key为actualName
     */
    protected $values = array();

    /**
     * @var array 设置各种的回调
     */
    protected $callback = array();

    /*
     * @var string 接口返回值格式
     */
    protected $returnFormat = "json";

    /**
     * @var int 参数位置由接口的http method决定（在url或http body中）
     */
    const PARAM_IN_BY_METHOD = 0;

    /**
     * @var int 强行将参数放在url中
     */
    const PARAM_IN_GET = 1;

    /**
     * @var int 强行将参数放在http body中
     */
    const PARAM_IN_POST = 2;

    /**
     * 供 ##接口开发者## 设置URL和HTTP REQUEST METHOD
     *
     * @param string $url
     * @param string $method
     */
    public function __construct($url, $method = false)
    {
        $this->httpRequest = new Comm_HttpRequest($url);

        $this->method = strtoupper($method);
        $this->httpRequest->setMethod($method);
    }

    /**
     * 发送请求
     * curl错误在这里被处理
     * 正确的返回值由getResult处理
     * @return [type] [description]
     */
    protected function send()
    {
        $this->applyRules();
        $this->_runCallback("before_send");
        $result = $this->httpRequest->send();

        //只有测试环境和开发环境DEBUG
        /*if (Comm_Context::isProduction() === false)
        {
            $this->debug();
        }*/

        $this->_runCallback("after_send");

        if ($result === false)
        {
            throw new Comm_Weibo_Exception_Api($this->httpRequest->getErrorMsg());
        }
    }

    protected function debug()
    {
        $isWriteLog = ($this->httpRequest->getResponseInfo('total_time') > 0.3);
        if ($isWriteLog == false)
        {
            return;
        }

        $message = array(
            'url' => Comm_Context::getServer('REQUEST_URI'),
            'ip' => Comm_Context::getClientIp(),
            'ua' => Comm_ClientProber::getAgent('browser'),
            'stack' => $this->getBacktraceInfo(),
            'api' => $this->httpRequest->url,
            'code' => $this->httpRequest->getResponseInfo('http_code'),
            'time' => $this->httpRequest->getResponseInfo('total_time') . ' s',
            'request' => $this->httpRequest->getResponseInfo('request_size') . ' byte',
            'response' => $this->httpRequest->getResponseInfo('download_content_length') . ' byte',
        );

        Core_Debug::info('api', $message);
    }

    public function getBacktraceInfo()
    {
        $trace = debug_backtrace();
        foreach ($trace as $item)
        {
            if (isset($item['file']))
            {
                if (preg_match('#/library/(.*)$#Di', $item['file'], $match))
                {
                    $info[] = $match[1] . '@' . $item['line'];
                }
            }
            else
            {
                $info[] = $item['class'] . '@' . $item['function'];
            }
        }

        if (!empty($info))
        {
            $info = array_reverse($info);
            return implode(', ', $info);
        }
        else
        {
            return '';
        }
    }
    
    /**
     * 获取结果
     * @param  boolean $throwException [description]
     * @param  array   $default        [description]
     * @return [type]                  [description]
     */
    public function getResult($throwException = false, $default = array())
    {
        try
        {
            $this->send();
        }
        catch (Comm_Weibo_Exception_Api $e)
        {
            throw $e;
        }

        $content = $this->httpRequest->getResponseContent();
        $requestCode = $this->httpRequest->getResponseInfo('http_code');
        $result = Tool_Misc::jsonDecode($content, true);
        $expMsg = $expCode = false;

        if ($requestCode != '200')
        {
            if (isset($result['error']))
            {
                $expMsg = $result['error'];
                $expCode = $result['error_code'];
                $logsType = self::ERROR_TYPE_API;
                $logsExt = $result;
            }
            else
            {
                $expMsg = "http error:" . $requestCode;
                $expCode = $requestCode;
                $logsType = self::ERROR_TYPE_SYS;
                $logsExt = array('err_msg' => $expMsg);
            }
        }
        elseif (!is_array($result))
        {
            $expMsg = "api return data can not be json_decode";
            $expCode = -1;
            $logsType = self::ERROR_TYPE_INFO;
            $logsExt = array('err_msg' => $expMsg);
        }
        elseif ((isset($result['error_code']) || isset($result['error'])) && !strpos($this->httpRequest->url, 'proxy/badges/badge'))
        {
            $expCode = isset($result['error_code']) ? $result['error_code'] : -1;
            $expMsg = isset($result['error']) ? $result['error'] : "api data is invalid";
            $logsType = self::ERROR_TYPE_API;
            $logsExt = $result;
        }

        if (false !== $expCode && false !== $expMsg)
        {
            $message = array(
                'url' => Comm_Context::getServer('REQUEST_URI'),
                'ip' => Comm_Context::getClientIp(),
                'ua' => Comm_ClientProber::getAgent('browser'),
                'stack' => $this->getBacktraceInfo(),
                'api' => $this->httpRequest->url,
                'code' => $this->httpRequest->getResponseInfo('http_code'),
                'time' => $this->httpRequest->getResponseInfo('total_time') . ' s',
                'request' => $this->httpRequest->getResponseInfo('request_size') . ' byte',
                'response' => $this->httpRequest->getResponseInfo('download_content_length') . ' byte',
                'cli' => $this->httpRequest->getCurlCli(),
                'error_type' => $logsType,
                'error_message' => $logsExt,
            );

            Core_Debug::error('api', $message);

            if ($throwException == true)
            {
                if (is_array($expMsg))
                {
                    $expMsg = json_encode($expMsg);
                }
            
                throw new Comm_Weibo_Exception_Api($expMsg, $expCode);
            }
            else
            {
                return $default;
            }
        }

        return $result;
    }

    /**
     * 供 ##接口开发者## 设置接口规则
     *
     * @param string $actualName
     * @param string $dataType
     * @param bool $isRequired
     * @param int $where
     */
    public function addRule($actualName, $dataType, $isRequired = false, $where = 0)
    {
        $this->rules[$actualName]['data_type'] = $dataType;
        $this->rules[$actualName]['is_required'] = $isRequired;
        $this->rules[$actualName]['where'] = $where;
    }

    /**
     * 为参数添加特殊的请求
     * @param string $actualName
     * @param string $method
     * @throws Comm_Exception_Program
     */
    public function addRuleMethod($actualName, $method)
    {
        $allowMethods = array('GET' => 0, 'POST' => 1, 'DELETE' => 2);
        if (!isset($allowMethods[$method]))
        {
            throw new Comm_Exception_Program("method for the param {$actualName} error:  $method");
        }

        if ($this->method != 'POST' && $method == 'POST')
        {
            $this->httpRequest->setMethod('POST');
        }

        $this->ruleMethod[$actualName] = $method;
    }

    /**
     * 供 ##接口开发者## 设置参数别名
     *
     * @param unknown_type $param
     * @param unknown_type $alias
     */
    public function addAlias($actualName, $alias)
    {
        $this->alias[$alias] = $actualName;
    }

    /**
     * 供 ##接口开发者## 增加 ##设置单个参数时## 的callback
     * 回调方法示意：（第一个参数为按引用传递的$value）
     * public function func($value, $p1, $p2..., $pn)
     *
     * @param string $name
     * @param array $callback
     */
    public function addSetCallback($actualName, $obj, $method, $param = array())
    {
        $this->callback['set'][$actualName][] = array($obj, $method);
        $this->callback['set'][$actualName][] = $param;
    }

    /**
     * 供 ##接口开发者## 增加 ##发送请求前## 的callback
     * 回调方法示意：（最后一个参数为当前$request）
     * public function func($p1, $p2..., $pn, $request)
     *
     * @param object $name
     * @param string $callback
     * @param array $param
     */
    public function addBeforeSendCallback($obj, $method, $param = array())
    {
        Comm_Assert::asException();
        Comm_Assert::false(isset($this->callback['before_send']), "don not add before send callback repeatly");
        $this->callback['before_send'][] = array($obj, $method);
        $this->callback['before_send'][] = $param;
    }

    /**
     * 供 ##接口开发者## 增加 ##发送请求后## 的callback
     * 回调方法示意：（最后一个参数为当前$request）
     * public function func($p1, $p2..., $pn, $request)
     *
     * @param string $name
     * @param array $callback
     * @param array $param
     */
    public function addAfterSendCallback($obj, $method, $param = array())
    {
        Comm_Assert::asException();
        Comm_Assert::false(isset($this->callback['after_send']), "don not add after send callback repeatly");
        $this->callback['after_send'][] = array($obj, $method);
        $this->callback['after_send'][] = $param;
    }

    /**
     * 供 ##接口调用者## 设置参数
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->setValue($name, $value);
    }

    public function setValue($name, $value)
    {
        Comm_Assert::asException();
        Comm_Assert::true($actualName = $this->_getActualName($name), "{$name} is not allowed");
        $this->values[$actualName] = $this->_runCallback('set', $actualName, $value);
    }

    /**
     * 返回values数据
     * @param string $name
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    public function getValue($name)
    {
        if (isset($this->values[$name]))
        {
            return $this->values[$name];
        }

        return null;
    }

    /**
     * 返回http请求对象
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * 发送正式请求前验证接口规则
     * 规则来自接口开发者的设定
     *
     * @throws Comm_Exception_Program
     */
    protected function applyRules()
    {
        if (empty($this->rules))
        {
            return;
        }

        foreach ($this->rules as $actualName => $rule)
        {
            if ($rule['is_required'] && !isset($this->values[$actualName]))
            {
                throw new Comm_Exception_Program("param {$actualName} is required");
            }
            elseif (!isset($this->values[$actualName]))
            {
                continue;
            }

            $value = $this->values[$actualName];
            switch ($rule['data_type'])
            {
                case "int" :
                    $value = (int)$value;
                    break;

                case "string" :
                case "filepath" :
                case "date" :
                    $value = (string)$value;
                    break;

                case "float" :
                    $value = (float)$value;
                    break;

                case "int64" :
                    if (!Tool_Misc::is64bit())
                    {
                        //if (!is_string($value) && !is_float($value)) {/*throw?*/}
                        $value = (string)$value;
                    }
                    else
                    {
                        $value = (int)$value;
                    }
                    break;

                default :
                    throw new Comm_Exception_Program("invalid data type");
            }

            if (isset($this->ruleMethod[$actualName]))
            {
                $method = $this->ruleMethod[$actualName];
            }
            else
            {
                $method = $this->method;
            }

            if (($rule['where'] == self::PARAM_IN_BY_METHOD && $method === "GET") || $method === "DELETE" || $rule['where'] == self::PARAM_IN_GET)
            {
                $this->httpRequest->addQueryField($actualName, $value);
            }
            else
            {
                if ($rule['data_type'] === 'filepath')
                {
                    $this->httpRequest->addPostFile($actualName, $value);
                }
                else
                {
                    $this->httpRequest->addPostField($actualName, $value);
                }
            }
        }
    }

    /**
     * 检查参数是否在允许范围内
     *
     * @param string $name
     */
    private function _getActualName($name)
    {
        if (isset($this->rules[$name]))
        {
            return $name;
        }

        if (array_key_exists($name, $this->alias))
        {
            return $this->alias[$name];
        }

        return false;
    }

    /**
     * 运行回调函数
     *
     * @param string $phase
     * @param string $actualName
     * @param mixed $value
     */
    private function _runCallback($phase, $actualName = '', $value = '')
    {
        //TODO callback
        if (!isset($this->callback[$phase]))
        {
            return $value;
        }

        $param = array();
        if ($phase == "set")
        {
            Comm_Assert::true($actualName != '');

            if (isset($this->callback['set'][$actualName]))
            {
                $callback = $this->callback['set'][$actualName][0];
                $param = $this->callback['set'][$actualName][1];
                $param = is_array($param) ? $param : array();
                array_unshift($param, $value);
                $value = call_user_func_array($callback, $param);
                return $value;
            }
            else
            {
                return $value;
            }
        }
        else
        {
            if (isset($this->callback[$phase]))
            {
                $callback = $this->callback[$phase][0];
                $param = $this->callback[$phase][1];
                $param[] = $this;
                call_user_func_array($callback, $param);
            }
        }
    }

    /**
     * 设定请求的超时时间
     * @param int $connect_timeout
     * @param int $time
     */
    public function setRequestTimeout($connectTimeout, $time)
    {
        $this->httpRequest->connectTimeout = $connectTimeout;
        $this->httpRequest->timeout = $time;
    }

    /**
     * 设定超时报警时间
     * @param [type] $time [description]
     */
    public function setWarningTimeout($time)
    {
        $this->warningTimeout = $time;
    }
}
