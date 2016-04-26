<?php

abstract class Cache_Abstract
{

    private $_isCli = false;

    /** 缓存配置，如下：
     * protected $configs = array(
     * 'user_info' => array('%s_1_0_%s', 10), //用户信息
     * 'screen_name_to_uid' => array('%s_3_%s', 86400), //昵称转uid
     * );
     *
     * name => array(key, livetime)
     *
     * @var array */
    protected $configs = array();

    /** 定义key前缀，参考configs/cache_key.php */
    protected $keyPrefix = '';

    /** 缓存池，必须指定
     *
     * @var string */
    protected $cachePool = '';

    protected $cacheObject = null;

    protected static $cache = array();
    
    //TODO 多机房
    //public $needSync = false;
    public function __construct($pool = null)
    {
        $pool = ($pool ? $pool : $this->cachePool);
        if (empty($pool))
        {
            throw new Comm_Exception_Program(get_class($this) . ' property cachePool must be assigned');
        }
        $this->setPool($pool);
        $this->_isCli = Comm_Context::isCli();
    }

    /** 重新设定$cache变量,防止大量设定cache值造成的缓存溢出错误(批量处理用) */
    public static function resetCacheData()
    {
        self::$cache = array();
    }

    /** 动态设置缓存池
     *
     * @param string $pool             */
    public function setPool($pool)
    {
        $this->cacheObject = Comm_Cache::connect($pool);
    }

    /** 将缓存池设置成默认 */
    public function resetPool()
    {
        $this->setPool($this->cachePool);
    }

    /** 清除cache对象 */
    public function clearCacheObject()
    {
        $this->cacheObject = null;
    }

    /** 获取缓存单元key
     *
     * @param
     *            $name
     * @return string */
    public function key($name)
    {
        $args = func_get_args();
        $id = join('_', $args);
        
        if ($this->_isCli)
        {
            self::$cache = array();
        }
        
        if (isset(self::$cache['key'][$id]))
        {
            return self::$cache['key'][$id];
        }
        
        if (empty($name))
        {
            throw new Comm_Exception_Program('key name does not empty');
        }
        
        if (!isset($this->configs[$name][0]))
        {
            throw new Comm_Exception_Program('Key name ' . $name . ' illegal');
        }
        
        if (isset(self::$cache['key_prefix'][$this->keyPrefix]))
        {
            $args[0] = self::$cache['key_prefix'][$this->keyPrefix];
        }
        else
        {
            $args[0] = Comm_Config::get('cache_key.' . $this->keyPrefix);
            self::$cache['key_prefix'][$this->keyPrefix] = $args[0];
        }
        
        return self::$cache['key'][$id] = vsprintf($this->configs[$name][0], $args);
    }

    /** 获取缓存单元缓存时间，默认缓存时间为60秒
     *
     * @param [type] $name
     *            [description]
     * @return [type] [description] */
    public function livetime($name)
    {
        if (empty($name))
        {
            throw new Comm_Exception_Program('key name does not empty');
        }
        
        if (isset($this->configs[$name][1]) && !is_integer($this->configs[$name][1]))
        {
            throw new Comm_Exception_Program('live time must be is valid integer');
        }
        
        return isset($this->configs[$name][1]) ? $this->configs[$name][1] : 60;
    }

    public function del($key)
    {
        return $this->cacheObject->del($key);
    }

    public function mdel($keys)
    {
        return $this->cacheObject->mdel($keys);
    }

    public function set($key, $value, $expire = 60)
    {
        return $this->cacheObject->set($key, $value, $expire);
    }

    public function mset($values, $expire = 60)
    {
        return $this->cacheObject->mset($values, $expire);
    }

    public function get($key)
    {
        return $this->cacheObject->get($key);
    }

    public function inc($key, $offset)
    {
        return $this->cacheObject->inc($key, $offset);
    }

    public function mget($keys)
    {
        return $this->cacheObject->mget($keys);
    }
}