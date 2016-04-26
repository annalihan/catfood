<?php
class Comm_HttpRequestPool
{
    public static $mh = null;
    public static $requestPool = array();
    public static $selectTimeout = 0.01;
    
    public static $curlState;
    public static $curlPool;
    
    public static function getCurl($hostId)
    {
        if (isset(self::$curlState[$hostId]))
        {
            $ch = self::_getCurlFromPool($hostId);
            if ($ch === false)
            {
                $ch = self::_getCurlCreate($hostId);
            }
        }
        else
        {
            $ch = self::_getCurlCreate($hostId);
        }
        
        return $ch;
    }
    
    public static function attach(Comm_HttpRequest $httpRequest)
    {
        $tmp = self::$requestPool;
        self::$requestPool[] = $httpRequest;
    }
    
    public static function send($forceAll = false)
    {
        if (empty(self::$requestPool))
        {
            throw new Comm_Exception_Program('request pool is empty');
        }
        
        if (count(self::$requestPool) == 1)
        {
            self::$requestPool[0]->send();
            self::$requestPool = array();
            return;
        }
        
        if (self::$mh == null)
        {
            self::$mh = curl_multi_init();
        }
        
        $curlRequestMap = array();
        foreach (self::$requestPool as $request)
        {
            $request->curlInit();
            $curlRequestMap[$request->getCurlId()] = $request;
            curl_multi_add_handle(self::$mh, $request->getCh());
        }
        
        $running = true;
        while ($running)
        {
            $mhReturn = curl_multi_exec(self::$mh, $running);
            if ($mhReturn == CURLM_CALL_MULTI_PERFORM)
            {
                continue;
            }

            curl_multi_select(self::$mh, self::$selectTimeout);
            
            // 多个请求中单个请求的状态只有在请求期间才能获取，事后只能获取true or false，没有msg
            while ($rst = curl_multi_info_read(self::$mh, $queuePoint))
            {
                $curlId = Comm_HttpRequest::fetchCurlId($rst['handle']);
                $request = $curlRequestMap[$curlId];
                $content = curl_multi_getcontent($request->getCh());
                $info = curl_getinfo($request->getCh());
                
                if ($rst['result'] == CURLE_OK)
                {
                    $request->setResponseState(true, "");
                    $request->setResponse($content, $info);
                }
                else
                {
                    $errorMsg = curl_error($rst['handle']);
                    $request->setResponseState(false, $errorMsg);
                    $request->setResponse($content, $info, false);
                    
                    if (!$forceAll)
                    {
                        self::cleanUp();

                        throw new Comm_Exception_Program("Request " . $request->url . ": " . $errorMsg);
                    }
                }
            }
        }

        self::cleanUp();
    }
    
    public static function cleanUp()
    {
        self::resetCurlStateAll();

        foreach (self::$requestPool as $request)
        {
            $request->resetCh();

            if (is_resource($request->getCh()))
            {
                curl_multi_remove_handle(self::$mh, $request->getCh());
            }
        }

        self::$requestPool = array();
    }
    
    public static function resetCurlState($hostId, $curlId)
    {
        if (isset(self::$curlState[$hostId][$curlId]))
        {
            self::$curlState[$hostId][$curlId] = true;
        }
    }
    
    public static function resetCurlStateAll()
    {
        foreach (self::$curlState as $hostId => $states)
        {
            foreach ($states as $curlId => $state)
            {
                self::$curlState[$hostId][$curlId] = true;
            }
        }
    }
    
    public static function getAvailCurlCount($array = "")
    {
        if (empty($array))
        {
            $array = self::$curlState;
        }
        
        $i = 0;
        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $i += self::getAvailCurlCount($value);
            }
            else
            {
                if ($value)
                {
                    $i++;
                }
            }
        }
        
        return $i;
    }
    
    public static function getAllCurlCount()
    {
        $count = 0;
        foreach (self::$curlState as $host => $states)
        {
            $count += count($states);
        }

        return $count;
    }
    
    private static function _getCurlCreate($hostId)
    {
        $ch = curl_init();
        $curlId = Comm_HttpRequest::fetchCurlId($ch);
        self::$curlState[$hostId][$curlId] = false;
        self::$curlPool[$curlId] = $ch;
        return $ch;
    }
    
    private static function _getCurlFromPool($hostId)
    {
        foreach (self::$curlState[$hostId] as $curlId => $state)
        {
            if ($state)
            {
                self::$curlState[$hostId][$curlId] = false;
                return self::$curlPool[$curlId];
            }
        }
        
        return false;
    }
}