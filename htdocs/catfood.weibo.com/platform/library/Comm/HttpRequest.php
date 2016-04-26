<?php

class Comm_HttpRequest
{
    const CRLF = "\r\n";
    public $cookies = array();
    public $headers = array();
    public $postFields = array();
    public $queryFields = array();
    public $hasUpload = false;
    
    public $url;
    public $method = false;
    public $hostName;
    public $hostPort = "80";
    public $isSSL = false;
    public $actualHostIp;
    public $noBody = false;
    public $requestRange = array();
    public $queryString = '';
    
    public $responseState;
    public $curlInfo;
    public $errorMsg;
    public $errorNo;
    public $responseHeader;
    public $responseContent;
    
    public $debug = false;
    public $urlEncode = "urlEncodeRfc3986";
    
    public $connectTimeout = 1000;
    public $timeout = 1000;
    
    private $_ch = null;
    private $_curlId = false;
    
    private $_callbackMethod;
    private $_callbackObject;
    
    private $_curlCli;
    public $gzip = false;
    public $user = null; 
    public $password = null;
    
    private static $_supportMSTimeout = null;

    private $_originQuery = '';

    public function __construct($url = "")
    {
        if (!empty($url))
        {
            $this->setUrl($url);
        }
    }
    
    public function setUrl($url) 
    {
        if (!empty($this->url))
        {
            throw new Comm_Exception_Program("url be setted");
        }
        
        $urlElements = parse_url($url);
        
        if ($urlElements["scheme"] == "https")
        {
            $this->isSSL = true;
            $this->hostPort = '443';
        }
        elseif ($urlElements["scheme"] != "http")
        {
            throw new Comm_Exception_Program("only support http now");
        }
        
        $this->hostName = $urlElements['host'];
        
        $this->url = $urlElements['scheme'] . '://' . $this->hostName;
        if (isset($urlElements['port']))
        {
            $this->hostPort = $urlElements['port'];
            $this->url .= ':' . $urlElements['port'];
        }

        if (isset($urlElements['path']))
        {
            $this->url .= $urlElements['path'];
        }

        if (!empty($urlElements['query']))
        {
            $this->_originQuery = $urlElements['query'];
            /*parse_str($urlElements['query'], $queryFields);
            $keys = array_map(array($this, "runUrlEncode"), array_keys($queryFields));
            $values = array_map(array($this, "runUrlEncode"), array_values($queryFields));
            $this->queryFields = array_merge($this->queryFields, array_combine($keys, $values));
            */
        }
    }
    
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
    }
    
    public function setActualHost($ip)
    {
        $this->actualHostIp = $ip;
    }
    
    public function setConnectTimeout($timeout)
    {
        $this->connectTimeout = (int)$timeout;
    }
    
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
    }
    
    public function setRequestRange($start, $end)
    {
        $this->requestRange = array($start, $end);
    }
    
    public function setDebug($debug)
    {
        $this->debug = $debug;
    }
    
    public function setUrlEncode($urlEncode)
    {
        $this->urlEncode = $urlEncode;
    }
    
    public function setCallback($method, $obj)
    {
        $this->_callbackMethod = $method;
        $this->_callbackObject = $obj;
    }
    
    public function addHeader($primary, $secondary, $urlEncode = false)
    {
        $primary = $this->runUrlEncode($primary, $urlEncode);
        $secondary = $this->runUrlEncode($secondary, $urlEncode);
        $this->headers[$primary] = $secondary;
    }
    
    public function addUserPassword($user, $pwd)
    {
        $this->user = $user;
        $this->password = $pwd;
    }
    
    public function addCookie($name, $value, $urlEncode = false) 
    {
        $name = $this->runUrlEncode($name, $urlEncode);
        $value = $this->runUrlEncode($value, $urlEncode);
        $this->cookies[$name] = $value;
    }
    
    public function addQueryField($name, $value, $urlEncode = false)
    {
        $name = $this->runUrlEncode($name, $urlEncode);
        $value = $this->runUrlEncode($value, $urlEncode);
        $this->queryFields[$name] = $value;
    }
    
    public function addPostField($name, $value, $urlEncode = false)
    {
        $name = $this->runUrlEncode($name, $urlEncode);
        $value = $this->runUrlEncode($value, $urlEncode);
        $this->postFields[$name] = $value;
    }
    
    public function addPostFile($name, $path)
    {
        $this->hasUpload = true;
        $name = $this->runUrlEncode($name);
        $this->postFields[$name] = '@' . $path;
    }
    
    public function runUrlEncode($input, $urlEncode = false)
    {
        if ($urlEncode === false)
        {
            return $input;
        }
        elseif ($urlEncode && method_exists($this, $urlEncode))
        {
            return $this->{$urlEncode}($input);
        }
        elseif ($this->urlEncode && method_exists($this, $this->urlEncode))
        {
            return $this->{$this->urlEncode}($input);
        }
        else
        {
            return $input;
        }
    }
    
    public function curlInit()
    {
        //if ($this->_ch !== null)
        //{
        //    throw new Comm_Exception_Program('curl init already');
        //}
        
        //$ch = Comm_HttpRequestPool::getCurl($this->_getHostId());
        $ch = curl_init();
        $this->_curlId = self::fetchCurlId($ch);
        $this->_ch = $ch;
        $this->_curlCli = 'curl -v ';

        $this->_setBranchHeader();
        $this->_curlSetOpt();
    }
    
    public function getCh()
    {
        return $this->_ch;
    }
    
    public function getCurlId()
    {
        return $this->_curlId;
    }
    
    public function send()
    {
        $startTime = microtime(true);

        $this->curlInit();

        $content = curl_exec($this->_ch);
        if (curl_errno($this->_ch) === 0)
        {
            $this->setResponseState(true, "", curl_errno($this->_ch));
            $result = true;
        } 
        else 
        {
            $this->setResponseState(false, curl_error($this->_ch), curl_errno($this->_ch));
            $result = false;
        }

        $this->setResponse($content, curl_getinfo($this->_ch));
        //Comm_HttpRequestPool::resetCurlState($this->_getHostId(), $this->getCurlId());
        $this->resetCh();
        
        //记录性能数据
        Core_Debug::addHttp($startTime, $this->curlInfo);

        return $result;
    }
    
    public function resetCh()
    {
        curl_close($this->_ch);
        $this->_ch = null;
        $this->_curlId = false;
    }
    
    public function getCurlCli()
    {
        return $this->_curlCli;
    }
    
    public function setResponseState($state, $errorMsg, $errorNo)
    {
        $this->responseState = $state;
        $this->errorMsg = $errorMsg;
        $this->errorNo = $errorNo;
    }
    
    public function setResponse($content, $info, $invokeCallback = true)
    {
        $this->curlInfo = $info;
        
        if (empty($content))
        {
            return;
        }
        
        $sectionSeparator = str_repeat(self::CRLF, 2);
        $sectionSeparatorLength = strlen($sectionSeparator);
        // pick out http 100 status header
        $http100 = "HTTP/1.1 100 Continue" . $sectionSeparator;
        if (false !== strpos($content, $http100))
        {
            $content = substr($content, strlen($http100));
        }
        
        $lastHeaderPosition = 0;
        // put header and content into each var, 3xx response will generate many header :(
        for ($i = 0, $pos = 0; $i <= $this->curlInfo['redirect_count']; $i++)
        {
            if ($i + 1 > $this->curlInfo['redirect_count'] && $pos)
            {
                $lastHeaderPosition = $pos + $sectionSeparatorLength;
            }

            $pos += $i > 0 ? $sectionSeparatorLength : 0;
            $pos = strpos($content, $sectionSeparator, $pos);
        }
        
        $this->responseContent = substr($content, $pos + $sectionSeparatorLength);
        $headers = substr($content, $lastHeaderPosition, $pos - $lastHeaderPosition);
        $headers = explode(self::CRLF, $headers);
        foreach ($headers as $header)
        {
            if (false !== strpos($header, "HTTP/1.1"))
            {
                continue;
            }
            
            $tmp = explode(":", $header, 2);
            $responseHeaderKey = strtolower(trim($tmp[0]));

            if (!isset($this->responseHeader[$responseHeaderKey]))
            {
                $this->responseHeader[$responseHeaderKey] = isset($tmp[1]) ? trim($tmp[1]) : '';
            }
            else
            {
                if (!is_array($this->responseHeader[$responseHeaderKey]))
                {
                    $this->responseHeader[$responseHeaderKey] = (array)$this->responseHeader[$responseHeaderKey];
                }

                $this->responseHeader[$responseHeaderKey][] = isset($tmp[1]) ? trim($tmp[1]) : '';
            }
        }

        // is there callback?
        if ($invokeCallback && !empty($this->_callbackObject) && !empty($this->_callbackMethod))
        {
            call_user_func_array(array($this->_callbackObject, $this->_callbackMethod), array($this));
        }
    }
    
    public function getResponseState()
    {
        return $this->responseState;
    }
    
    public function getErrorMsg()
    {
        return $this->errorMsg;
    }
    
    public function getErrorNo()
    {
        return $this->errorNo;
    }
    
    public function getResponseTime()
    {
        return $this->getResponseInfo('total_time');
    }
    
    public function getResponseInfo($key = "")
    {
        if (empty($key))
        {
            return $this->curlInfo;
        }
        else
        {
            if (isset($this->curlInfo[$key]))
            {
                return $this->curlInfo[$key];
            }
            else
            {
                throw new Comm_Exception_Program("info: " . $key . " not exists");
            }
        }
    }
    
    public function getResponseHeader($key = "")
    {
        if (empty($key))
        {
            return $this->responseHeader;
        }
        else
        {
            if (isset($this->responseHeader[$key]))
            {
                return $this->responseHeader[$key];
            }
            else
            {
                throw new Comm_Exception_Program("header: " . $key . " not exists");
            }
        }
    }
    
    public function getResponseContent()
    {
        return $this->responseContent;
    }
    
    public function getMethod()
    {
        return isset($this->method) ? $this->method : null;
    }
    
    public function getUrl()
    {
        return isset($this->url) ? $this->url : '';   
    }
    
    public static function urlEncode($input)
    {
        if (is_array($input))
        {
            return array_map(array('Comm_HttpRequest', 'urlEncode'), $input);
        }
        else if (is_scalar($input))
        {
            return urlencode($input);
        }
        else
        {
            return '';
        }
    }
    
    public static function urlEncodeRaw($input)
    {
        if (is_array($input))
        {
            return array_map(array('Comm_HttpRequest', 'urlEncodeRaw'), $input);
        }
        else if (is_scalar($input))
        {
            return rawurlencode($input);
        }
        else
        {
            return '';
        }
    }
    
    public static function urlEncodeRfc3986($input)
    {
        if (is_array($input))
        {
            return array_map(array('Comm_HttpRequest', 'urlEncodeRfc3986'), $input);
        }
        else if (is_scalar($input))
        {
            return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
        }
        else
        {
            return '';
        }
    }
    
    public static function fetchCurlId($ch)
    {
        preg_match('/[^\d]*(\d+)[^\d]*/', (string)$ch, $matches);
        return $matches[1];
    }
    
    /**
     * 拼装http查询串（不经过urlencode）
     */
    public static function httpBuildQuery($queryData = array())
    {
        if (empty($queryData))
        {
            return '';
        }

        if (is_array($queryData) === false)
        {
            return $queryData;
        }

        $pairs = array();
        foreach ($queryData as $key => $value)
        {
            if (is_array($value))
            {
                foreach ($value as $v)
                {
                    // POST/GET参数支持格式：k[]=v1&k[]=v2&k[]=v3
                    $pairs[] = "{$key}[]={$v}";
                }
            }
            else
            {
                $pairs[] = "{$key}={$value}";
            }
        }

        return implode("&", $pairs);
    }
    
    private function _getHostId()
    {
        return $this->hostName . ':' . $this->hostPort;
    }

    private function _setBranchHeader()
    {
        if (Core_Branch::$branchName)
        {
            $value = array(
                'bn' => Core_Branch::$branchName,
            );

            $this->addHeader(Core_Branch::HEADER_NAME_BRANCH, json_encode($value));
        }

        if (Core_Branch::$grayVersion)
        {
            $value = array(
                'gv' => Core_Branch::$grayVersion,
            );

            $this->addHeader(Core_Branch::HEADER_NAME_GRAY, json_encode($value));
        }
    }
    
    private function _curlSetOpt()
    {
        curl_setopt($this->_ch, CURLOPT_URL, $this->url);
        curl_setopt($this->_ch, CURLOPT_HEADER, true);
        
        if ($this->isSSL)
        {
            curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
            $this->_curlCli .= " -k";
        }
        
        if ($this->noBody)
        {
            curl_setopt($this->_ch, CURLOPT_NOBODY, true);
        }
        
        if (!empty($this->requestRange))
        {
            curl_setopt($this->_ch, CURLOPT_RANGE, $this->requestRange[0]."-".$this->requestRange[1]);
        }
        
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->_ch, CURLOPT_USERAGENT, "Weibo.com Swift framework HttpRequest class");
        
        if ($this->debug)
        {
            curl_setopt($this->_ch, CURLINFO_HEADER_OUT, true);
        }
        
        if ($this->gzip)
        {
            curl_setopt($this->_ch, CURLOPT_ENCODING, "gzip");
            $this->_curlCli .= " --compressed ";
        }
        
        //curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($this->_ch, CURLOPT_MAXREDIRS, 1);
        //$this->_curlCli .= " --max-redirs 1";
       
        if (self::$_supportMSTimeout === null)
        {
            $version = curl_version();
            self::$_supportMSTimeout = $version["version"] >= "7.16.2";
        }

        if (self::$_supportMSTimeout == false)
        {
            //对于不支持毫秒超时的版本，当timeout<1000ms时，使用1s代替... 
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, ceil($this->connectTimeout / 1000));
            curl_setopt($this->_ch, CURLOPT_TIMEOUT, ceil($this->timeout / 1000));
        }
        else
        {
            //对于支持毫秒超时的版本，当timeout<1000ms时，需要忽略信号
            curl_setopt($this->_ch, CURLOPT_NOSIGNAL, 1);
            curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
            curl_setopt($this->_ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        }

        $this->_curlCli .= " --connect-timeout " . ceil($this->connectTimeout / 1000);
        $this->_curlCli .= " -m " . ceil($this->timeout / 1000);
        
        if (!empty($this->actualHostIp))
        {
            curl_setopt($this->_ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            curl_setopt($this->_ch, CURLOPT_PROXY, $this->actualHostIp);
            curl_setopt($this->_ch, CURLOPT_PROXYPORT, $this->hostPort);
            $this->_curlCli .= " -x " . $this->actualHostIp . ":" . $this->hostPort;
        }
        
        $this->_loadCookies();
        $this->_loadHeaders();
        $this->_loadQueryFields();
        $this->_loadPostFields();
        $this->_loadUserPassword();
        
        if ($this->method)
        {
            curl_setopt($this->_ch, CURLOPT_CUSTOMREQUEST, strtoupper($this->method));
            $this->_curlCli .= " -X \"{$this->method}\"";
        }

        $this->_curlCli .= " \"" . $this->url . ($this->queryString ? '?' . $this->queryString : '') . "\"";
    }
    
    private function _loadUserPassword()
    {
        if (is_null($this->user) || is_null($this->password))
        {
            return;
        }

        $strUserpwd = $this->user . ':' . $this->password;
        $this->_curlCli .= "-u \"{$strUserpwd}\" ";
        curl_setopt($this->_ch, CURLOPT_USERPWD, $strUserpwd);
    }
    
    private function _loadCookies()
    {
        if (empty($this->cookies))
        {
            return;
        }
        
        foreach ($this->cookies as $name => $value)
        {
            $pairs[] = $name . '=' . $value;
        }
        
        $cookie = implode('; ', $pairs);
        curl_setopt($this->_ch, CURLOPT_COOKIE, $cookie);
        $this->_curlCli .= " -b \"" . $cookie . "\"";
    }
    
    private function _loadHeaders()
    {
        if (empty($this->headers))
        {
            return;
        }

        $headers = array();
        foreach ($this->headers as $k => $v)
        {
            $tmp = $k . ":" . $v;
            $this->_curlCli .= " -H \"" . $tmp . "\"";
            $headers[] = $tmp;
        }
        
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    private function _loadQueryFields()
    {
        $this->queryString = '';
        if (empty($this->queryFields) && $this->_originQuery == '')
        {
            return;
        }

        $this->queryString = self::httpBuildQuery($this->queryFields);

        if ($this->_originQuery)
        {
            $this->queryString = $this->queryString . ($this->queryString ? '&' : '') . $this->_originQuery;
        }

        curl_setopt($this->_ch, CURLOPT_URL, $this->url . '?' . $this->queryString);
    }
    
    private function _loadPostFields()
    {
        if (empty($this->postFields))
        {
            return;
        }

        // 如果有文件要上传只能使用数组的方式，Content-Type: multipart/form-data
        // 其它情况使用k1=v1&k2=v2字符串格式，Content-Type: application/x-www-form-urlencoded
        $postFields = $this->hasUpload ? $this->postFields : self::httpBuildQuery($this->postFields);
      
        curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postFields);

        if ($this->hasUpload)
        {
            foreach ($postFields as $key => $value)
            {
                $this->_curlCli .= " -form \"{$key}={$value}\"";
            }
        }
        else
        {
            $this->_curlCli .= " -d \"{$postFields}\"";
        }
    }
}
