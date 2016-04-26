<?php

class Comm_Config
{
    /**
     * 加载指定的配置文件
     *
     * @param string 映射configuration文件名
     * @return array
     */
    public static function load($configFile)
    {
        $fileNames = Core_Loader::getInstance()->getFile($configFile, strlen($configFile), '', 0, 'config', true);

        if (empty($fileNames))
        {
            throw new Comm_Exception_Program("{$configFile} config file not exists");
        }

        $fileNames = array_reverse($fileNames);
        $configs = array();

        //合并所有内容
        foreach ($fileNames as $fileName)
        {
            $config = include $fileName;

            //项目配置文件会覆盖框架配置
            $configs = Tool_Array::merge($configs, $config);
        }

        return $configs;
    }
    
    /**
     * 获取配置
     * @param  string  $key          支持路径，分隔符为"."
     * @param  boolean $defaultValue 默认返回值
     * @return [type]                [description]
     */
    public static function get($key, $defaultValue = false)
    {
        static $config = array();
        
        if (strpos($key, '.') !== false)
        {
            list($file, $path) = explode('.', $key, 2);
        }
        else
        {
            $file = $key;
        }
        
        if (!isset($config[$file]))
        {
            $config[$file] = self::load($file);
        }
        
        if (isset($path))
        {
            $value = Tool_Array::path($config[$file], $path, "#not_found#");
            
            if ($value === "#not_found#")
            {
                return $defaultValue;
            }
            
            return $value;
        }
        else
        {
            return $config[$file];
        }
    }
}
