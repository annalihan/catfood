<?php 
abstract class Do_Abstract extends ArrayObject
{
    /**
     * 输入模式。用于在数据从创建到写入存储的过程中使用。在该模式下，会调用规则检查，子对象会递归创建，同时进行数据检查。
     * @staticvar
     * @final
     */
    const MODE_INPUT = 'input';
    
    /**
     * 输出模式。用于在数据从存储读出到过程处理和展示的过程中使用。在该模式下，创建和写入时信任数据，不会调用规则检查，子对象仍然会递归创建，同时信任数据。
     * @staticvar
     * @final
     */
    const MODE_OUTPUT = 'output';
    
    protected $props = array();
    protected $mode;

    public function __construct($data = null, $mode = self::MODE_INPUT)
    {
        parent::setFlags(ArrayObject::ARRAY_AS_PROPS);
        $this->setDataObjectMode($mode);
        
        if (!is_null($data))
        {
            $this->initData($data);
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayObject::offsetSet()
     */
    public function offsetSet($propName, $value)
    {
        if (!isset($this->props[$propName]))
        {
            return null;
        }

        $functionName = 'set' . Tool_Misc::underScoreToCamel($propName, '', true);
        
        if (method_exists($this, $functionName))
        {
            return $this->$functionName($value);
        }
        else
        {
            return parent::offsetSet($propName, $this->applyRuleOnProperty($propName, $value));
        }
    }
    
    /**
     * 覆盖父类方法，提供类似数组方式的访问。
     * 
     * <ul>
     * <li>对于未定义检查规则的项，认为是可设置项，在未被初始化的情况下返回Null。</li>
     * <li>对于定义了get_*系列方法的项，则会直接返回get_*方法的返回值。</li>
     * <li>对于定义了检查规则的项，认为是必设置项，在未被初始化的情况下抛出异常。</li>
     * </ul>
     * 
     * @see ArrayObject::offsetGet()
     */
    public function offsetGet($propName)
    {
        if (!isset($this->props[$propName]))
        {
            return null;
        }

        $functionName = 'get' . Tool_Misc::underScoreToCamel($propName, '', true);

        if (method_exists($this, $functionName))
        {
            return $this->$functionName();
        }
        else
        {
            if ($this->props[$propName] && !parent::offsetExists($propName))
            {
                if ($this->mode === self::MODE_OUTPUT)
                {
                    return null;
                }
                else
                {
                    throw new Do_Exception('property not set:' . $propName);
                }
            }

            return parent::offsetExists($propName) ? parent::offsetGet($propName) : null;
        }
    }
    
    /**
     * 获取数据对象模式
     * 
     * @return enum self::MODE_* 系列常量。
     */
    public function getDataObjectMode()
    {
        return $this->mode;
    }
    
    /**
     * 设置数据对象模式
     * 
     * @param enum $mode self::MODE_* 系列常量。
     * @throws Do_Exception
     */
    public function setDataObjectMode($mode)
    {
        if ($mode !== self::MODE_INPUT && $mode !== self::MODE_OUTPUT)
        {
            throw new Do_Exception('mode incorrect');
        }

        $this->mode = $mode;
    }
    
    /**
     * 返回当前对象的数组形式
     * 
     * @param bool $recursive 是否对子对象递归调用toArray。可选，默认为false。
     * @return array
     */
    public function toArray($recursive = false)
    {
        if (!$recursive)
        {
            return $this->getArrayCopy();
        }
        
        $array = $this->getArrayCopy();
        foreach ($array as $k => $v)
        {
            if (is_object($v) && method_exists($v, 'toArray'))
            {
                $array[$k] = $v->toArray($recursive);
            }
        }

        return $array;
    }
    
    public function __isset($prop)
    {
        return $this->offsetExists($prop);
    }
    
    public function __unset($prop)
    {
        return $this->offsetUnset($prop);
    }
    
    public function __get($prop)
    {
        return $this->offsetGet($prop);
    }
    
    public function __set($prop, $value)
    {
        return $this->offsetSet($prop, $value);
    }
    
    /**
     * 删除所有的引用，释放对象
     */
    public function __destruct()
    {
        parent::exchangeArray(array());
    }   
    
    protected function initData($data)
    {
        if (is_object($data) || is_array($data))
        {
            foreach ($data as $key => $value)
            {
                $this->offsetSet($key, $value);
            }
        }
        else
        {
            throw new Do_Exception('init data should be an object or array');
        }
    }
    
    /**
     * 根据$this->props中定义的规则，进行规则检查。
     * 
     * 可以在 set_* 系列自定义函数中使用以实现默认规则的处理。
     * 
     * @see offsetSet()
     * @param mixed $property
     * @param mixed $value
     * @throws Do_Exception
     */
    protected function applyRuleOnProperty($property, $value)
    {
        $validatedValue = $value;

        if (is_array($this->props[$property]))
        {
            if ($this->mode === self::MODE_INPUT)
            {
                $args = $this->props[$property];
                $argBaseType = $args[0];
                $args[0] = $value;

                //如果不能通过规则校验，ArgChecker会抛出异常。
                $validatedValue = call_user_func_array(array('Comm_ArgChecker', $argBaseType), $args);
            }
        }
        elseif ($this->props[$property])
        {
            $class = $this->props[$property];
            if (!class_exists($class))
            {
                throw new Do_Exception("data class of $property not exist: \{$class\}");
            }

            if (!is_object($value) || get_class($value) !== $class)
            {
                $validatedValue = new $class($value, $this->mode);
            }
        }

        return $validatedValue;
    }
    
    /**
     * 调用 ArrayObject::offsetSet() 来完成数据存储。此方法应该在set_*系列自定义方法中被使用用来替代 parent::offsetSet 防止产生循环引用。
     * 
     * @see ArrayObject::offsetSet()
     * @final
     * @access protected
     * @param mixed $key
     * @param mixed $value
     */
    protected final function setData($key, $value)
    {
        parent::offsetSet($key, $value);
    }
    
    /**
     * 调用 ArrayObject::offsetGet() 来完成数据获取。此方法应该在 get_* 系列自定义方法中被使用以替代 parent::offsetGet 防止产生循环引用。
     * 
     * @see ArrayObject::offsetGet()
     * @final
     * @access protected
     * @param mixed $key
     * @return mixed
     */
    protected final function getData($key)
    {
        return parent::offsetGet($key);
    }
}