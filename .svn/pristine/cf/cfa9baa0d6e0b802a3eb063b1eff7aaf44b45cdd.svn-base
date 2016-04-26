<?php 

class Comm_Db_PdoMysql implements Comm_Db_Interface
{
    const MODE_AUTO = 0;
    const MODE_READ = 1;
    const MODE_WRITE = 2;
    
    protected $readConfig = array();
    protected $writeConfig = array();
    protected $readInst;
    protected $writeInst;
    protected $lastInst;
    protected $mode = self::MODE_AUTO;

    protected $alias = 'Undefined';
    
    static protected $configKeys = array('host', 'port', 'name', 'user', 'pass', '*attr');
    private $_writeCommands = array('lastinsertid' => true, 'begintransaction' => true, 'intransaction' => true, 'commit' => true, 'rollback' => true);

    private $_startTime = null;

    /**
     * 配置连接池
     * @param  [type] $alias  [description]
     * @param  [type] $config [description]
     * @return [type]         [description]
     */
    public function configure($alias, $config)
    {
        $this->alias = $alias;
        $readConfigs = array();
        $writeConfigs = array();

        foreach ($config as $k => $v)
        {
            $this->checkConfigFormat($v);
            
            if (strpos($k, 'read') !== false)
            {
                $readConfigs[] = $v;
            }

            if (strpos($k, 'write') !== false)
            {
                $writeConfigs[] = $v;
            }
        }
        
        $readConfigs AND $this->readConfig = $readConfigs[array_rand($readConfigs)];
        $writeConfigs AND $this->writeConfig = $writeConfigs[array_rand($writeConfigs)];
        
        if (!$this->readConfig && !$this->writeConfig)
        {
            throw new Comm_Exception_Program('Must define at least one db for "' . $this->alias . '"!');
        }
    }
    
    /**
     * 强制使用读库
     * 
     * @return Comm_Db_PdoMysql
     */
    public function setRead()
    {
        $this->mode = self::MODE_READ;
        return $this;
    }

    /**
     * 强制使用写库
     * @return Comm_Db_PdoMysql
     */
    public function setWrite()
    {
        $this->mode = self::MODE_WRITE;
        return $this;
    }
    
    /**
     * 根据sql语句自行判断
     * @return Comm_Db_PdoMysql
     */
    public function setAuto()
    {
        $this->mode = self::MODE_AUTO;
        return $this;
    }
    
    /**
     * 执行一个sql语句并返回影响行数。
     * 
     * 如果在insert或者replace语句后需要获取 last insert id 请使用lastInsertId()方法
     * 
     * @param string $sql   sql语句。不能为select语句
     * @param array $data
     * @throws Comm_Exception_Program
     * @throws Comm_Db_PdoMysqlException
     */
    public function exec($sql, array $data = null)
    {
        $verb = $this->extractSqlVerb($sql);
        
        if ($verb === 'select')
        {
            throw new Comm_Exception_Program('Can not execute a select sql');
        }
        
        $statement = $this->executeSql($sql, $data);
        
        return $statement->rowCount();
    }

    public function lastInsertId()
    {
        return $this->lastInst->lastInsertId();
    }

    /**
     * 批量插入
     * @param  string $tableName 表名
     * @param  array  $fields    字段名列表，如['a', 'b', 'c']
     * @param  array  $data      数据数组，不带key，顺序和$fields的一致，如[[1, 2, 3], [2, 2, 3]]
     * @return [type]            [description]
     */
    public function multiInsert($tableName, $fields, $data)
    {
        $questionMark = array_fill(0, count($fields), '?');
        $questionMark = '(' . implode(', ', $questionMark) . ')';
        $questionMarks = array_fill(0, count($data), $questionMark);

        $sql = "INSERT INTO `{$tableName}` (" . implode(', ', $fields) . ") VALUES " . implode(', ', $questionMarks);
        
        $insertValue = array();
        foreach ($data as $key => $value)
        {
            $insertValue = array_merge($insertValue, $value);
        }
        
        $statement = $this->executeSql($sql, $insertValue);

        return $statement->rowCount();
    }

    /**
     * 执行提供的select语句并返回结果集。
     * 
     * @param string $sql   sql语句。只能为select语句
     * @param array $data
     * @param bool $fetchIndex
     * @return array 
     */
    public function fetchAll($sql, array $data = null, $fetchIndex = false)
    {
        $verb = self::extractSqlVerb($sql);
        
        if ($verb !== 'select')
        {
            throw new Comm_Exception_Program('Can not fetch on a non-select sql');
        }

        $statement = $this->executeSql($sql, $data);
        
        return $statement->fetchAll($fetchIndex ? PDO::FETCH_NUM : PDO::FETCH_ASSOC);
    }
    
    public function prepare($sql)
    {
        $pdo = $this->getInst($this->detectSqlType($sql));
        $args = func_get_args();

        return call_user_func_array(array($pdo, 'prepare'), $args);
    }
    
    public function query($sql)
    {
        $pdo = $this->getInst($this->detectSqlType($sql));
        $args = func_get_args();

        return call_user_func_array(array($pdo, 'query'), $args);
    }
    
    public function getAttribute($attribute)
    {
        return isset($this->attributes[$attribute]) ? $this->attributes[$attribute] : null;
    }
    
    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;
    }
    
    public function __call($func, $args)
    {
        $startTime = microtime(true);

        $func = str_replace('_', '', strtolower($func));
        $mode = self::MODE_AUTO;
        if (isset($this->_writeCommands[$func]))
        {
            $mode = self::MODE_WRITE;
        }

        $result = call_user_func_array(array($this->getInst($mode), $func), $args);

        $this->_logMessage($func, $args, $result, 0);

        return $result;
    }
    
    /**
     * 执行一个sql并返回PDOStatement对象和执行结果。
     * 
     * @param string $sql
     * @param array $data
     * @return mixed
     */
    protected function executeSql($sql, array $data = null)
    {
        $this->_logStart();
        $statement = $this->prepare($sql);

        if ($data)
        {
            $result = $statement->execute($data);
        }
        else
        {
            $result = $statement->execute();
        }

        if ($result === false)
        {
            $code = $statement->errorCode();
            $error = $statement->errorInfo();

            $this->_logMessage('execute', array('sql' => $sql, 'data' => $data), $error, $code);

            throw new Comm_Db_PdoMysqlException($error[2], $error[1]);
        }
        
        $this->_logMessage('execute', array('sql' => $sql, 'data' => $data), $result, 0);

        return $statement;
    }
    
    /**
     * 根据指定的类型获取pdo实例。
     * 
     * @param int $mode
     * @return PDO
     */
    protected function getInst($mode)
    {
        if ($mode === self::MODE_AUTO)
        {
            if (null === $this->lastInst)
            {
                $mode = ($this->mode === self::MODE_WRITE ? self::MODE_WRITE : self::MODE_READ);
                $this->lastInst = $this->getInst($mode);
            }

            return $this->lastInst;
        }
        else if ($mode === self::MODE_READ)
        {
            if (null === $this->readInst)
            {
                if (!$this->readConfig)
                {
                    return $this->getInst(self::MODE_WRITE);
                }

                $this->readInst = $this->getPdo($this->readConfig);
            }

            $this->lastInst = $this->readInst;
            return $this->lastInst;
        }
        else if ($mode === self::MODE_WRITE)
        {
            if (null === $this->writeInst)
            {
                if (!$this->writeConfig)
                {
                    throw new Comm_Exception_Program('Writable db must be defined');
                }

                $this->writeInst = $this->getPdo($this->writeConfig);
            }

            $this->lastInst = $this->writeInst;
            return $this->lastInst;
        }

        return null;
    }
    
    protected function getPdo($config)
    {
        $startTime = microtime(true);

        try
        {
            $pdoConnectString = "mysql:dbname={$config['name']};host={$config['host']};port={$config['port']}";
            $inst = new PDO($pdoConnectString, $config['user'], $config['pass']);
            
            $this->_logMessage('connect', array('host' => $config['host'], 'port' => $config['port'], 'user' => $config['user']), $inst, 0);
        }
        catch (Exception $ex)
        {
            $this->_logMessage('connect', array('host' => $config['host'], 'port' => $config['port'], 'user' => $config['user']), $ex->getMessage(), $ex->getCode());

            Core_Debug::fatal('db_connect', $ex->getMessage());

            throw new Comm_Db_PdoMysqlException($ex->getMessage());
        }
        
        if (!empty($config['attr']) && is_array($config['attr']))
        {
            foreach ($config['attr'] as $k => $v)
            {
                $inst->setAttribute($k, $v);
            }
        }
        
        return $inst;
    }
    
    /**
     * 提取sql语句的动词
     * 
     * @param string $sql
     * @return string 动词
     */
    static protected function extractSqlVerb($sql)
    {
        $sqlComponents = explode(' ', ltrim($sql), 2);
        return strtolower($sqlComponents[0]);
    }
    
    /**
     * 检测sql所需的数据库类型
     * @param string $sql
     * @return ENUM
     */
    static protected function detectSqlType($sql)
    {
        return self::extractSqlVerb($sql) === 'select' ? self::MODE_AUTO : self::MODE_WRITE;
    }
    
    /**
     * 检查配置文件格式是否合格
     * 
     * @param array $config
     * @throws Comm_Exception_Program
     */
    protected function checkConfigFormat(array $config)
    {
        $validKeys = array_fill_keys(self::$configKeys, 0);

        foreach ($config as $k => $v)
        {
            //检查是否是必选或者可选参数。可选参数以*号开头
            if (!isset($validKeys[$k]) && !isset($validKeys["*$k"]))
            {
                throw new Comm_Exception_Program('Unused PdoMysql "' . $this->alias . '" config "' . $k . '"');
            }

            unset($validKeys[$k]);
        }
        
        if ($validKeys)
        {
            $keys = array_keys($validKeys);
            
            while (true)
            {
                $key = array_pop($keys);

                if ($key[0] !== '*')
                {
                    break;
                }
            }

            if ($key && $key[0] !== '*')
            {
                throw new Comm_Exception_Program('Missing PdoMysql "' . $this->alias . '" config value "' . $key . '"');
            }
        }
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

        Core_Debug::addMysql($this->_startTime, $resultInfo);

        $this->_logStart();
    }
} 