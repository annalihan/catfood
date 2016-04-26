<?php
    abstract class Core_Daemon_Abstract
    {
        /*
         * 为方便类对象初始化时，参数可灵活扩展
         * 
         * @param array $param 类对象成员名与值的列表，如果名称第一个为大写C，说明是对象
         * 
         */
        public function setParam($param = array())
        {
            if (!$param || !is_array($param))
            {
                return false;
            }
    
            //循环设置类成员，在5.3.xx以上，成员需要设置成public或者protected才能起作用
            foreach ($param as $key => $value)
            {
                $object = substr($key, 0, 1);
                
                if ($object === 'C')
                {
                    //Class类型的，创建时初始化，有些初始化需要这些参数
                    //不再后续设定参数
                }
                else if ($object === 'O')
                {
                    $this->$key = &$value;
                }
                else
                {
                    $this->$key = $value;
                }
            }
        }
    }