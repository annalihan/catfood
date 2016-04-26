<?php
class Comm_I18n
{
    /**
     * @var string 当前语言
     */
    private static $_currentLang = 'zh-cn';
    private static $_langIndex = 'cn';
    
    /**
     * @var array 按package, file结构进程内缓存避免重复IO
     */
    public static $lang;
    
    /**
     * 获取当前语言
     * @return [type] [description]
     */
    public static function getCurrentLang()
    {
        return self::$_currentLang;
    }

    /**
     * 设置当前的语言
     * @param [type] $lang 格式：zh-cn
     */
    public static function setCurrentLang($lang)
    {
        self::$_currentLang = strtolower(str_replace(array(' ', '_'), '-', $lang));

        switch (self::$_currentLang)
        {
            case 'cn':
            case 'zh-cn':
                self::$_langIndex = 'cn';
                break;

            case 'tw':
            case 'zh-tw':
                self::$_langIndex = 'tw';
                break;

            case 'en':
            case 'en-us':
                self::$_langIndex = 'en';
                break;
            
            default:
                self::$_langIndex = 'cn';
                break;
        }
    }
    
    /**
     * 从指定配置文件获取多语言信息
     * @param  [type] $key 多语言ID，如tpl.common.xxx
     * @return [type]      [description]
     */
    public static function text($key)
    {
        if (empty($key))
        {
            throw new Comm_Exception_Program('language key is required');
        }

        list($package, $key2) = self::_splitKey($key);
        if (!isset(self::$lang[$package]))
        {
            self::_loadPackage($key);
        }
        
        $found = Tool_Array::path(self::$lang[$package], $key2, $key2);
        return $found;
    }

    /**
     * 根据简体中文获取其他语言(配置文件为all.php)
     * @param  [type] $text 默认内容（简体），也可以是KEY
     * @param  array  $args 动态值的参数，默认为空数组
     * @return [type]       [description]
     */
    public static function get($text, $args = array())
    {
        if (isset(self::$lang['all']) === false)
        {
            self::_loadAllConfig();
        }

        if (isset(self::$lang['all'][$text][self::$_langIndex]))
        {
            //存在配置时
            $value = self::$lang['all'][$text][self::$_langIndex];
        }
        else if (isset(self::$lang['all'][$text]['cn']))
        {
            //存在简体配置
            $value = self::$lang['all'][$text]['cn'];
        }
        else
        {
            //都不存在
            $value = $text;
        }

        //参数不为空时
        if ($args)
        {
            $value = vsprintf($value, $args);
        }

        return $value;
    }
    
    /**
     * 动态文本获取
     * @param  [type] $key  [description]
     * @param  [type] $val1 [description]
     * @param  [type] $val2 [description]
     * @return [type]       [description]
     */
    public static function dynamicText($key, $val1, $val2 = null)
    {
        $args = func_get_args();
        array_shift($args);
        $text = self::text($key);

        return vsprintf(preg_replace('/%(\d)%/', '%$1$s', strval($text)), $args);
    }
    
    /**
     * 返回指定语言和分组的所有信息
     *
     * @param string $key 需要载入的语言
     * @return array
     */
    private static function _loadPackage($key)
    {
        if (empty($key))
        {
            throw new Comm_Exception_Program('language key is required');
        }
        
        list($package) = self::_splitKey($key);
        
        // 预判
        if (isset(self::$lang[$package]))
        {
            return;
        }
        
        // 处理路径
        $fileName = "library/Comm/I18n/langs/" . self::$_currentLang . '/' . $package . ".php";
        $path = Core_Loader::getInstance()->getFileByName($fileName);
        if (!file_exists($path))
        {
            throw new Comm_Exception_Program("language file \"" . self::$_currentLang . ":{$package}\" not exists");
        }
        else
        {
            $lang = include($path);
        }
        
        // 保存
        self::$lang[$package] = $lang;
    }

    /**
     * 加载全语言配置文件
     * @return array
     */
    private static function _loadAllConfig()
    {
        // 处理路径
        $fileName = "library/Comm/I18n/langs/all.php";
        $fileNames = Core_Loader::getInstance()->getFileByName($fileName, true);
        if ($fileNames === false)
        {
            throw new Comm_Exception_Program("language file all.php is not exists");
        }

        $lang = array();
        $fileNames = array_reverse($fileNames);
        $configs = array();

        //合并所有内容
        foreach ($fileNames as $fileName)
        {
            $config = include $fileName;

            //项目配置文件会覆盖框架配置
            $configs = Tool_Array::merge($configs, $config);
        }
        
        // 保存
        self::$lang['all'] = $configs;
    }

    private static function _splitKey($key)
    {
        //安全起见，将连续的多个"."认为是一个"."
        $path = explode('.', preg_replace('#\.{2,}#', '.', $key));
        $acturalKey = array_pop($path);
        
        $package = implode(DIRECTORY_SEPARATOR, $path);
        return array($package, $acturalKey);
    }

    /**
     * 根据简体中文获取其他语言
     * @param  [type] $text [description]
     * @return [type]       [description]
     */
    public static function _($text)
    {
        return self::get($text);
    }
}
