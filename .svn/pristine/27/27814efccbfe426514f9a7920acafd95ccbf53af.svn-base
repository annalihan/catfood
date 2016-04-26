<?php

include T3P_PATH . '/Flexihash/Flexihash.php';

class Comm_Cache_Redis implements Comm_Cache_Interface, Comm_Queue_Interface
{
    private $_retryAttempts = 0;
    private $_retries = 2;
    private $_startTime = null;
    private $_hash = null;
    private $_servers = array();
    private $_serverName = '';
    private $_forbiddenFunctions = array('flushDB' => true, 'flushAll' => true, 'bgrewriteaof' => true, 'bgsave' => true, 'slaveof' => true, 'keys' => true, 'getKeys' => true);

    private function _addServer($ip, $port)
    {
        $serverName = "{$ip}:{$port}";
        $this->_servers[$serverName] = array(
            'ip' => $ip, 
            'port' => $port,
        );

        if ($this->_hash)
        {
            $this->_hash->addTarget($serverName);
        }
    }

    private function _getServer($serverName, $reconnect = false)
    {
        if (empty($this->_servers[$serverName]['server']) || $reconnect)
        {
            $this->_connectServer($serverName);
        }

        return $this->_servers[$serverName]['server'];
    }

    private function _connectServer($serverName)
    {
        $this->_logStart();

        $redis = new Redis();
        $host = $this->_servers[$serverName]['ip'];
        $port = $this->_servers[$serverName]['port'];
        $result = $redis->connect($host, $port);
        $resultCode = ($result ? 0 : 1);
        $this->_logMessage('connect', array('host' => $host, 'port' => $port), $result, $resultCode);

        $this->_servers[$serverName]['server'] = $redis;
    }

    public function removeServer($key)
    {
        if ($this->_hash == null)
        {
            $serverName = $this->_serverName;
        }
        else
        {
            $serverName = $this->_hash->lookup($key);
        }

        unset($this->_servers[$serverName]['server']);
    }

    public function lookupServer($key, $reconnect = false)
    {
        if ($this->_hash == null)
        {
            $serverName = $this->_serverName;
        }
        /*else if ($count > 1)
        {
            $serverNames = $this->_hash->lookupList($key, $count);
            $servers = array();

            foreach ($serverNames as $serverName)
            {
                $servers[] = $this->_getServer($serverName);
            }

            return $servers;
        }*/
        else
        {
            $serverName = $this->_hash->lookup($key);
        }

        return $this->_getServer($serverName, $reconnect);
    }

    public function lookupServers($keys, $reconnect = false)
    {
        $serverNames = array();

        if ($this->_hash == null)
        {
            $serverName = $this->_serverName;
            $server = $this->_getServer($serverName, $reconnect);
            $serverNames[$serverName] = array(
                'server' => $server, 
                'keys' => $keys,
            );
        }
        else
        {
            foreach ($keys as $key)
            {
                $serverName = $this->_hash->lookup($key);

                if (isset($serverNames[$serverName]) === false)
                {
                    $server = $this->_getServer($serverName, $reconnect);
                    $serverNames[$serverName] = array(
                        'server' => $server, 
                        'keys' => array($key),
                    );
                }
                else
                {
                    $serverNames[$serverName]['keys'][] = $key;
                }
            }
        }

        return $serverNames;
    }

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

        }
        else
        {
            throw new Comm_Exception_Program('Config should be an array of "addr:port"s or a name of $_SERVER param');
        }

        if (count($config) > 1)
        {
            $this->_hash = new Flexihash();
        }

        foreach ($config as $server)
        {
            $serverInfo = explode(':', $server, 2);

            if (count($serverInfo) == 2)
            {
                $this->_addServer($serverInfo[0], $serverInfo[1]);
                $this->_serverName = "{$serverInfo[0]}:{$serverInfo[1]}";
            }            
        }
    }

    public function get($key, $expire = 60)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $redis = $this->lookupServer($key);
        $resultCode = 0;
        
        try
        {
            $result = $redis->get($key);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('get', array('key' => $key), $result, $resultCode);

        if ($result !== false)
        {
            $this->_retryAttempts = 0;
            return $result;
        }

        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->get($key, $expire);
        }

        return false;
    }

    public function set($key, $value, $expire = 60)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $redis = $this->lookupServer($key);
        $resultCode = 0;
        
        try
        {
            $result = $redis->setex($key, $expire, $value);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('setex', array('key' => $key, 'value' => $value, 'expire' => $expire), $result, $resultCode);

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
        $redis = $this->lookupServer($key);
        $resultCode = 0;
        
        try
        {
            $result = $redis->delete($key);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('delete', array('key' => $key), $result, $resultCode);

        if ($result !== false)
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
        $redis = $this->lookupServer($key);
        $resultCode = 0;

        try
        {
            $result = $redis->incrBy($key, $offset);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('inc', array('key' => $key, 'offset' => $offset), $result, $resultCode);

        if ($result !== false)
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
        $values = array();

        if (empty($keys))
        {
            return $values;
        }

        $this->_logStart();
        $servers = $this->lookupServers($keys);
        foreach ($servers as $value)
        {
            $tmpRedis = $value['server'];
            $tmpKeys = $value['keys'];
            $resultCode = 0;

            try
            {
                $tmpValues = $tmpRedis->mGet($tmpKeys);
            }
            catch (Exception $e)
            {
                $resultCode = $e->getCode();
            }
            
            $this->_logMessage('mGet', array('keys' => $tmpKeys), $tmpValues, $resultCode);

            if ($tmpValues === false)
            {
                $values = array_merge($values, array_fill_keys($tmpKeys, false));
            }
            else
            {
                $values = array_merge($values, array_combine($tmpKeys, $tmpValues));
            }
        }

        return $values;
    }

    public function mset(array $values, $expire = 60)
    {
        if (empty($values))
        {
            return false;
        }

        $this->_logStart();
        $servers = $this->lookupServers(array_keys($values));
        foreach ($servers as $value)
        {
            $tmpRedis = $value['server'];
            $tmpKeys = $value['keys'];
            $tmpValues = array();
            foreach ($tmpKeys as $tmpKey)
            {
                $tmpValues[$tmpKey] = $values[$tmpKey];
            }
            
            $resultCode = 0;
            try
            {
                $result = $tmpRedis->mSet($tmpValues);
            }
            catch (Exception $e)
            {
                $resultCode = $e->getCode();
            }
            
            $this->_logMessage('mSet', array('values' => $tmpValues), $result, $resultCode);

            if ($expire > 0)
            {
                foreach ($tmpKeys as $tmpKey)
                {
                    $resultCode = 0;
                    try
                    {
                        $result = $tmpRedis->setTimeout($tmpKey, $expire);
                    } 
                    catch (Exception $e)
                    {
                        $resultCode = $e->getCode();
                    }

                    $this->_logMessage('setTimeout', array('key' => $tmpKey, 'expire' => $expire), $result, $resultCode);
                }
            }
        }

        return true;
    }

    public function mdel(array $keys)
    {
        if (empty($keys))
        {
            return false;
        }

        $this->_logStart();
        $servers = $this->lookupServers($keys);
        foreach ($servers as $value)
        {
            $tmpRedis = $value['server'];
            $tmpKeys = $value['keys'];

            foreach ($tmpKeys as $tmpKey)
            {
                $resultCode = 0;

                try
                {
                    $result = $tmpRedis->delete($tmpKey);
                } 
                catch (Exception $e)
                {
                    $resultCode = $e->getCode();
                }
                
                $this->_logMessage('delete', array('key' => $tmpKey), $result, $resultCode);
            }
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
        $redis = $this->lookupServer($key);
        $resultCode = 0;

        try
        {
            $result = $redis->lPop($key);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('lPop', array('key' => $key), $result, $resultCode);

        if ($result !== false)
        {
            $this->_retryAttempts = 0;
            return $result;
        }

        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->pop($key);
        }

        return false;
    }

    public function push($key, $value)
    {
        if (empty($key))
        {
            return false;
        }

        $this->_logStart();
        $redis = $this->lookupServer($key);
        $resultCode = 0;

        try
        {
            $result = $redis->rPush($key, $value);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }
        
        $this->_logMessage('rPush', array('key' => $key, 'value' => $value), $result, $resultCode);

        if ($result !== false)
        {
            $this->_retryAttempts = 0;
            return $result;
        }

        $shouldRetry = $this->_shouldRetry();
        if ($shouldRetry === true)
        {
            return $this->push($key, $value);
        }

        return false;
    }

    public function blockPop($keys, $timeout = 1)
    {
        if (empty($keys))
        {
            return false;
        }

        $this->_logStart();
        
        //TODO
        $redis = $this->lookupServer('');
        $result = $redis->blPop($keys, $timeout);
        $this->_logMessage('blPop', array('keys' => $keys, 'timeout' => $timeout), $result, 0);

        return $result;
    }

    public function __call($name = '', $arguments = array())
    {
        if (isset($this->_forbiddenFunctions[$name]))
        {
            throw new Comm_Exception_Program("Forbidden command {$name}");
        }

        //获取Keys
        if (count($arguments) == 0)
        {
            //TODO 选择其中一个Server?
            $key = '';
        }
        else
        {
            $keys = current($arguments);

            //不支持从多个Server取数据，只从第一个对应的Server取
            $key = is_array($keys) ? current($keys) : $keys;
        }

        $redis = $this->lookupServer($key);

        $this->_logStart();
        $resultCode = 0;
        
        try
        {
            $value = call_user_func_array(array($redis, $name), $arguments);
        }
        catch (RedisException $e)
        {
            $resultCode = $e->getCode();
        }

        $this->_logMessage($name, $arguments, $value, $resultCode);
        return $value;
    }

    private function _shouldRetry()
    {
        usleep(10000);

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
        $resultInfo = $args;
        $resultInfo['function'] = $function;
        $resultInfo['result'] = $result;
        $resultInfo['code'] = $resultCode;

        Core_Debug::addMc($this->_startTime, $resultInfo);

        $this->_logStart();
    }
}
