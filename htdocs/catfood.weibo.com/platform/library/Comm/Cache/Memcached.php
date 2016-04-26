<?php
class Comm_Cache_Memcached extends Memcached implements Comm_Cache_Interface, Comm_Queue_Interface
{
    private $_retryAttempts = 0;
    private $_retries = 2;
    private $_startTime = null;

    public function configure($config)
    {
        if (is_string($config))
        {
            if (isset($_SERVER[$config]))
            {
                $config = $_SERVER[$config];
            }

            $config = explode(' ', $config);
        }
        elseif (is_array($config))
        {
            //no need to do 
        }
        else
        {
            throw new Comm_Exception_Program('Config should be an array of "addr:port"s or a name of $_SERVER param');
        }

        $this->setOption(Memcached::OPT_NO_BLOCK, true);
        $this->setOption(Memcached::OPT_CONNECT_TIMEOUT, 200);
        $this->setOption(Memcached::OPT_POLL_TIMEOUT, 50);

        $connectInfos = array();
        $this->_logStart();

        foreach ($config as $server)
        {
            $serverInfo = explode(':', $server, 2);

            if (count($serverInfo) == 2)
            {
                $host = $serverInfo[0];
                $port = $serverInfo[1];
                $connectStart = microtime(true);
                $result = $this->addServer($host, $port);
                $connectInfos[] = array(
                    'host' => $host,
                    'port' => $port,
                    'duration' => (microtime(true) - $connectStart) * 1000,
                    'result' => $result,
                );

                //$result = $this->addServer($serverInfo[0], $serverInfo[1]);
                //$resultCode = ($result ? 0 : 1);
                //$this->_logMessage('connect', array('host' => $serverInfo[0], 'port' => $serverInfo[1]), $result, $resultCode);
            }
        }

        //合并输出
        if (count($connectInfos) > 0)
        {
            $this->_logMessage('connect_pool', array(), $connectInfos, 0);
        }
    }

    public function get($key, $expire = 60)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::get($key);
        $resultCode = parent::getResultCode();
        $this->_logMessage('get', array('key' => $key), $result, $resultCode);

        return $result;
    }

    public function set($key, $value, $expire = 60)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::set($key, $value, $expire);
        $resultCode = parent::getResultCode();
        $this->_logMessage('set', array('key' => $key, 'value' => $value), $result, $resultCode);

        if ($result !== false)
        {
            $this->_retryAttempts = 0;
            return $result;
        }

        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->set($key, $value, $expire);
        }

        return false;
    }

    public function del($key)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::delete($key);
        $resultCode = parent::getResultCode();
        $this->_logMessage('delete', array('key' => $key), $result, $resultCode);

        if ($result !== false || $resultCode == Memcached::RES_SUCCESS || $resultCode == Memcached::RES_NOTFOUND)
        {
            $this->_retryAttempts = 0;
            return $result;
        }

        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->del($key);
        }

        return false;
    }
    
    public function inc($key, $offset = 1)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::increment($key, $offset);    
        $resultCode = parent::getResultCode();
        $this->_logMessage('increment', array('key' => $key, 'offset' => $offset), $result, $resultCode);

        if ($result !== false || $resultCode == Memcached::RES_SUCCESS || $resultCode == Memcached::RES_NOTFOUND)
        {
            $this->_retryAttempts = 0;
            return $result;
        }
        
        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->inc($key, $offset);
        }

        return false;
    }

    public function mget(array $keys)
    {
        $this->_logStart();

        $result = parent::getMulti($keys);
        $resultCode = parent::getResultCode();
        $this->_logMessage('getMulti', array('keys' => $keys), $result, $resultCode);

        if (false === $result)
        {
            $result = array();
        }

        foreach ($keys as $key)
        {
            if (!isset($result[$key]))
            {
                $result[$key] = false;
            }
        }

        return $result;
    }

    public function mset(array $values, $expire = 60)
    {
        $this->_logStart();
        $result = parent::setMulti($values, $expire);
        $resultCode = parent::getResultCode();
        $this->_logMessage('setMulti', array('values' => $values), $result, $resultCode);
        
        return $result;
    }

    public function mdel(array $keys)
    {
        foreach ($keys as $key)
        {
            $this->_logStart();
            $result = parent::delete($key);
            $resultCode = parent::getResultCode();
            $this->_logMessage('delete', array('key' => $key), $result, $resultCode);
        }

        return true;
    }

    public function pop($key)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::get($key);
        $resultCode = parent::getResultCode();
        $this->_logMessage('get', array('key' => $key), $result, $resultCode);

        return $result;
    }

    public function push($key, $value)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $result = parent::set($key, $value);
        $resultCode = parent::getResultCode();
        $this->_logMessage('set', array('key' => $key, 'value' => $value), $result, $resultCode);

        return $result;
    }

    public function blockPop($keys, $timeout = 1)
    {
        return false;
    }

    private function _shouldRetry()
    {
        if ($this->_retryAttempts < $this->_retries)
        {
            $this->_retryAttempts += 1;
            return true;
        }

        return false;
    }

    private function _logStart()
    {
        $this->_startTime = microtime(true);
    }

    private function _logMessage($function, $args, $result, $resultCode)
    {
        //$resultInfo = $args;
        $resultInfo['function'] = $function;
        //$resultInfo['result'] = $result;
        $resultInfo['code'] = $resultCode;

        Core_Debug::addMc($this->_startTime, $resultInfo);

        $this->_logStart();
    }
}
