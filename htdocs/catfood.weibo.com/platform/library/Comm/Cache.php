<?php 
class Comm_Cache
{
    protected static $pools = array();
    public static $keyConfigName = 'cache_key';
    public static $poolConfigName = 'cache_pool';
    public static $configs = array();
    
    /**
     * 根据名字获取一个缓存实例
     * @param  [type] $name   [description]
     * @param  [type] $engine [description]
     * @return [type]         [description]
     */
    public static function get($name, $engine = CACHE_ENGINE_MEMCACHED)
    {
        return self::pool($name, $engine);
    }

    /**
     * 根据名字获取一个缓存实例
     * @param  [type] $name   [description]
     * @param  [type] $engine [description]
     * @return [type]         [description]
     */
    public static function pool($name, $engine = CACHE_ENGINE_MEMCACHED)
    {
        $poolKey = "{$engine}:{$name}";
        if (isset(self::$pools[$poolKey]))
        {
            return self::$pools[$poolKey];
        }
        else
        {
            $cache = self::connect($name, $engine);
            self::$pools[$poolKey] = $cache;

            return $cache;
        }
    }

    /**
     * 创建一个缓存实例
     * 
     * @param string $name config/cache/pool.php中池子的名称
     * @param string $backend 配置中池子的类型，有CACHE_ENGINE_REDIS和CACHE_ENGINE_MEMCACHED两种
     */
    public static function connect($name, $backend = CACHE_ENGINE_MEMCACHED)
    {
        /*$poolKey = "{$engine}:{$name}";
        if (isset(self::$pools[$poolKey]))
        {
            return self::$pools[$poolKey];
        }*/

        if (!isset(self::$configs[$backend][$name]))
        {
            throw new Comm_Exception_Program("pool $name not defined");
        }

        $config = self::$configs[$backend][$name];
        $class = 'Comm_Cache_' . $backend;
        if (!class_exists($class) || !in_array('Comm_Cache_Interface', class_implements($class)))
        {
            throw new Comm_Exception_Program('Cache type must be a valid backend type and implements Comm_Cache_Interface');
        }

        $cache = new $class;
        $cache->configure($config);

        //self::$pools[$poolKey] = $cache;

        return $cache;
    }
    
    /**
     * 将缓存绑定到一个名字。当缓存名字已经占用的时候，会抛出一个异常。
     * 
     * @param string $name
     * @param Comm_Cache_Interface $cache
     * @throws Comm_Exception_Program
     */
    public static function bind($name, Comm_Cache_Interface $cache)
    {
        if (isset(self::$pools[$name]))
        {
            throw new Comm_Exception_Program("Cache $name already defined");
        }
        
        self::$pools[$name] = $cache;
    }
    
    /**
     * 将指定的缓存名字与其实例解绑。并返回被解绑的实例。
     * @param string $name
     * @return Comm_Cache_Interface
     */
    public static function unbind($name)
    {
        if (isset(self::$pools[$name]))
        {
            $instance = self::$pools[$name];
            unset(self::$pools[$name]);
        }
        else
        {
            $instance = null;
        }

        return $instance;
    }
    
    /**
     * 清除所有缓存名字与其实例的绑定。并返回被解绑的实例数组。
     * @return array of Comm_Cache_Interface
     */
    public static function unbindAll()
    {
        $pools = self::$pools;
        self::$pools = array();

        return $pools;
    }
    
    /**
     * 根据key_config获取指定的$prefix与cache id的对应关系
     * 
     * 通常用 string => int 来减小key的总长度……
     * 
     * @param string    $prefix 前缀名
     * @param mixed     $extra  [可选]额外数据。支持多个参数。后续参数会与找到的prefix一起用下划线连接成一个完整的cache key
     * @param mixed     ...     [可选]
     * @return string   完整的key名字
     */
    public static function key($prefix, $extra = null)
    {
        $id = Comm_Config::get(self::$keyConfigName . '.' . $prefix);
        $args = func_get_args();
        $args[0] = $id;

        return implode('_', $args);
    }
    
    /**
     * 初始化
     * @param  string $configName [description]
     * @return [type]             [description]
     */
    public static function initPool($configName = '')
    {
        $config = Comm_Config::get($configName ? $configName : self::$poolConfigName);
        
        foreach ($config as $type => $backendConfigs)
        {
            $class = 'Comm_Cache_' . $type;
            if (class_exists($class) && in_array('Comm_Cache_Interface', class_implements($class)))
            {

            }
            else if (class_exists($type) && in_array('Comm_Cache_Interface', class_implements($type)))
            {

            }
            else
            {
                throw new Comm_Exception_Program('Cache type must be a valid backend type and implements Comm_Cache_Interface'); 
            }

            foreach ($backendConfigs as $name => $config)
            {
                self::$configs[$type][$name] = Comm_Context::getServer($config, $config);
            }
        }
    }
}