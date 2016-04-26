<?php

class Comm_Db
{

    protected static $defaultConfigName = 'db_pool';

    protected static $dbs = array();

    /** 根据配置文件自动配置数据库池
     *
     * @param string $configName
     *            [可选] 配置名。若为空，则使用 db_pool 作为默认配置名。该配置由Comm_Config加载
     * @throws Comm_Exception_Program */
    public static function initPool($configName = null)
    {
        if ($configName === null)
        {
            $configName = self::$defaultConfigName;
        }
        
        $configs = Comm_Config::get($configName);
        
        foreach ($configs as $type => $aliases)
        {
            $class = "Comm_Db_$type";
            
            if (class_exists($class) && in_array('Comm_Db_Interface', class_implements($class)))
            {
                
            }
            elseif (class_exists($type) && in_array('Comm_Db_Interface', class_implements($type)))
            {
                $class = $type;
            }
            else
            {
                throw new Comm_Exception_Program("Db type \"$type\" must implements Comm_Db_Interface");
            }
            
            foreach ($aliases as $alias => $config)
            {
                self::$dbs[$alias] = new $class();
                self::$dbs[$alias]->configure($alias, $config);
            }
        }
        
        return;
    }

    /** 初始化DB对象
     *
     * @param string $dbAlias
     *            数据库连接对象别名。该别名在配置文件中定义。
     *            
     * @return object */
    private static function _initDb($dbAlias)
    {
        $configs = Comm_Config::get(self::$defaultConfigName);
        if (isset($configs["PdoMysql"][$dbAlias]) === false)
        {
            return false;
        }
        $config = $configs["PdoMysql"][$dbAlias];

        self::$dbs[$dbAlias] = new Comm_Db_PdoMysql();
        self::$dbs[$dbAlias]->configure($dbAlias, $config);
        
        return self::$dbs[$dbAlias];
    }

    /** 根据数据库别名从池中获取数据库连接对象
     *
     * @param string $dbAlias
     *            数据库连接对象别名。该别名在配置文件中定义。
     * @return Comm_Db_Interface 返回一个符合接口定义的数据库连接对象。
     * @throws Comm_Exception_Program */
    public static function connect($dbAlias)
    {
        if (empty(self::$dbs[$dbAlias]))
        {
            throw new Comm_Exception_Program('Db alias "' . $dbAlias . '" not exist');
        }
        
        //命令行执行，防止超时，每次都需要初始化新对象
        if (Comm_Context::isCli())
        {
            self::_initDb($dbAlias);
        }
        
        return self::$dbs[$dbAlias];
    }

    /** 清除当前池中所有的数据库连接对象。 */
    public static function clearAll()
    {
        $ret = self::$dbs;
        self::$dbs = array();
        
        return $ret;
    }
}