<?php
/**
 * 框架加载模块
 * @package Core
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class Core_Loader
{
    public $directoryInited = false;

    private $_templateRoot = '';
    private $_directoryList = null;
    private static $_instance;

    const VIEW_DIR_PRODUCTION = 'production';
    const VIEW_DIR_DEVELOPMENT = 'development';
    
    public static function getInstance()
    {
        if (self::$_instance == null)
        {
            self::$_instance = new Core_Loader();
        }

        return self::$_instance;
    }

    /**
     * 初始化Loader
     * @param  string $branchName [description]
     * @return [type]             [description]
     */
    public function initEnvironment($branchName = '')
    {
        $this->_initDirectories($branchName);

        $this->_templateRoot = $this->getTemplateRoot($branchName);

        //set_include_path(get_include_path() . PATH_SEPARATOR . $this->_templateRoot);
    }

    /**
     * 自动加载类
     * @param  [type] $className [description]
     */
    public static function loadPlatformClass($className)
    {
        $classNameLen = strlen($className);

        /*if (Core_Loader::getInstance()->includeClass($className, $classNameLen, YAF_LOADER_MODEL, YAF_LOADER_LEN_MODEL, YAF_MODEL_DIRECTORY_NAME))
        {

        }*/

        if (self::getInstance()->includeClass($className, $classNameLen, '', 0, YAF_LIBRARY_DIRECTORY_NAME))
        {
            return true;
        }

        if (substr($className, $classNameLen - YAF_LOADER_LEN_CONTROLLER) == YAF_LOADER_CONTROLLER)
        {
            if (self::getInstance()->includeClass($className, $classNameLen, YAF_LOADER_CONTROLLER, YAF_LOADER_LEN_CONTROLLER, YAF_CONTROLLER_DIRECTORY_NAME))
            {
                return true;
            }
        }

        if (substr($className, $classNameLen - YAF_LOADER_LEN_PLUGIN) == YAF_LOADER_PLUGIN)
        {
            if (self::getInstance()->includeClass($className, $classNameLen, YAF_LOADER_PLUGIN, YAF_LOADER_LEN_PLUGIN, YAF_PLUGIN_DIRECTORY_NAME))
            {
                return true;
            }
        }
    }

    public function getTemplateRoot($branchName = '')
    {
        $isProduction = Comm_Context::isProduction();
        $templateBranch = ($isProduction ? self::VIEW_DIR_PRODUCTION : self::VIEW_DIR_DEVELOPMENT);
        $path = dirname(dirname(APP_PATH));

        if ($branchName)
        {
            $path .= DS . $branchName;
        }

        $path .= DS . 'views' . DS . $templateBranch  . DS . Comm_Config::get('project.name');

        return $path;
    }

    public function getAppPath($branchName = '')
    {
        if ($branchName == '')
        {
            return APP_PATH;
        }

        $appPath = realpath(APP_PATH);
        $appPaths = explode(DS, $appPath);
        $pathName = array_pop($appPaths);
        array_pop($appPaths); //applications
        array_push($appPaths, $branchName);
        array_push($appPaths, 'applications');
        array_push($appPaths, $pathName);

        return implode(DS, $appPaths);
    }

    public function getPlatformPath($branchName = '')
    {
        if ($branchName == '')
        {
            return PLATFORM_PATH;
        }

        return substr(PLATFORM_PATH, 0, strlen(PLATFORM_PATH) - 8) . $branchName . DS . 'platform';
    }

    /**
     * 初始化框架、项目和子项目目录
     * @return [type]                       [description]
     */
    private function _initDirectories($branchName = '')
    {
        $this->_directoryList = array();

        $appPath = $this->getAppPath($branchName);

        //项目子目录
        //$subs = explode(' ', $application->getConfig()->application->subs);
        $subs = explode(',', SUB_APPS);
        foreach ($subs as $sub)
        {
            $sub = trim($sub);
            if ($sub == '')
            {
                continue;
            }
            
            $subDir = $appPath;
            $sub = str_replace('\\', '/', $sub);
            $sub = explode('/', $sub);
            foreach ($sub as $subTmp)
            {
                $subDir .= DS . 'applications' . DS . trim($subTmp);
            }

            $this->_directoryList[] = $subDir;
        }

        //项目根目录
        $this->_directoryList[] = $appPath;

        //最后是框架目录
        $this->_directoryList[] = $this->getPlatformPath($branchName);
    }

    /**
     * 根据视图路径获取模板文件
     * @param  [type] $viewPath [description]
     * @return [type]           [description]
     */
    public function getTemplateFile($viewPath, $ext = '.phtml')
    {
        if (empty($viewPath))
        {
            Core_Debug::fatal('view', "View is empty");
            
            return false;
        }

        if (substr($viewPath, strlen($viewPath) - strlen($ext)) == $ext)
        {
            $viewFile = $viewPath;
        }
        else
        {
            $viewFile = $viewPath . $ext;
        }

        //绝对路径
        if (file_exists($viewFile))
        {
            return $viewFile;
        }

        //相对路径
        $viewFile = $this->_templateRoot . DS . $viewFile;
        if (file_exists($viewFile))
        {
            return $viewFile;
        }
        
        return false;
    }

    /**
     * 查找文件
     * @param  [type]  $fileName  [description]
     * @param  boolean $recursive [description]
     * @return [type]             [description]
     */
    public function getFileByName($fileName, $recursive = false)
    {
        $files = array();

        if ($this->_directoryList === null)
        {
            return false;
        }

        foreach ($this->_directoryList as $directory)
        {
            $file = $directory . DS . $fileName;

            if (file_exists($file))
            {
                if ($recursive)
                {
                    $files[] = $file;
                }
                else
                {
                    return $file;
                }
            }
        }

        return (count($files) > 0) ? $files : false;
    }

    /**
     * 获取文件，包括library,controller,plugin,view等
     * @param  string  $className     类名称
     * @param  integer $classNameLen  类名长度
     * @param  string  $typeName      类型名称
     * @param  integer $typeNameLen   类型名长度
     * @param  string  $typeDirectory 类型所在目录
     * @param  boolen  $recursive     是否包含所有目录，默认为false
     * @return string|array|false                
     *         string:文件绝对路径
     *         false:获取失败
     */
    public function getFile($className, $classNameLen, $typeName, $typeNameLen, $typeDirectory, $recursive = false)
    {
        $classFiles = array();

        if ($this->_directoryList === null)
        {
            return false;
        }

        if ($classNameLen > $typeNameLen && substr($className, $classNameLen - $typeNameLen) == $typeName)
        {
            if ($typeNameLen > 0)
                $classPath = str_replace('_', DS, substr($className, 0, $classNameLen - $typeNameLen));
            else
                $classPath = str_replace('_', DS, $className);

            foreach ($this->_directoryList as $directory)
            {
                $classFile = $directory . DS . $typeDirectory . DS . $classPath . ".php";

                if (file_exists($classFile))
                {
                    if ($recursive)
                    {
                        $classFiles[] = $classFile;
                    }
                    else
                    {                    
                        return $classFile;
                    }
                }
            }
        }

        return $recursive ? $classFiles : false;
    }

    /**
     * 加载类库
     * @param  [type] $className     类名称
     * @param  [type] $classNameLen  类名长度
     * @param  [type] $typeName      类型名称
     * @param  [type] $typeNameLen   类型名长度
     * @param  [type] $typeDirectory 类型所在目录
     * @return boolean
     *         true:加载成功
     *         false:加载失败
     */
    public function includeClass($className, $classNameLen, $typeName, $typeNameLen, $typeDirectory)
    {
        $classFile = $this->getFile($className, $classNameLen, $typeName, $typeNameLen, $typeDirectory);
        
        if ($classFile)
        {
            return include($classFile);
        }

        return false;
    }
}