<?php

class Comm_Bigpipe_Pagelet
{
    static private $_rootPage = false;
    static private $_pageletNames = array();
    static private $_pageConfigs = false;
    
    public $pageContainer = null;
    public $params = array();
    public $disabled = false;

    const BP_MODE_TRADITIONAL = 0;
    const BP_MODE_STREAMLINE = 1;
    const BP_MODE_SCRIPTSTREAM = 2;

    /**
     * PAGE的page_id
     * @var string
     */
    public $name = '';

    public $children = array();
    public $tpl = '';
    public $isSkeleton = false;

    //初始化的数据(用于手动创建的pl)
    public $pageletMeta = array();
    public $pageletData = array();

    public final function __construct($name = '')
    {
        if ($name)
        {
            $this->name = $name;
        }

        //每次请求只有一个Skeleton Page
        //这次请求的所有Pl都属于这个Page
        if ($this->isSkeleton && self::$_rootPage == false)
        {
            self::$_rootPage = $this;
        }

        //创建pagelet
        $this->_createPagelets();

        //construct final以后，预留一个initPage
        $this->initPage();

        //自定义参数
        $this->initParams();
    }

    private function _createPagelets()
    {
        if ($this->isSkeleton == false)
        {
            return;
        }

        //从page_config加载配置
        $config = $this->_getConfig();

        //初始化本页
        if ($config)
        {
            $skeletonClassName = get_class($this);
            foreach ($config as $pageletName => $pageletParams)
            {
                $this->createChild($pageletParams, $pageletName, $skeletonClassName);
            }
        }
    }

    private function _getConfig()
    {
        //先从当前目录获取
        $config = $this->_getConfigFromLocal();
        if ($config)
        {
            return $config;
        }

        //如果当前目录没有，再从根目录获取
        return $this->_getConfigFromRoot();
    }

    private function _getConfigFromLocal()
    {
        if ($this->tpl)
        {
            $configFile = Core_Loader::getInstance()->getTemplateFile(dirname($this->tpl) . '/page_config.json', '');
            if ($configFile)
            {
                return json_decode(file_get_contents($configFile), true);
            }
        }

        return false;
    }

    private function _getConfigFromRoot()
    {
        if (isset(self::$_pageConfigs[$this->name]))
        {
            return self::$_pageConfigs[$this->name];
        }

        if (self::$_pageConfigs !== false)
        {
            return false;
        }

        self::$_pageConfigs = array();
        $configFile = Core_Loader::getInstance()->getTemplateFile('page_config.json', '');
        if ($configFile === false || file_exists($configFile) === false)
        {
            return false;
        }

        self::$_pageConfigs = json_decode(file_get_contents($configFile), true);
        if (self::$_pageConfigs === null)
        {
            //解析失败
            Core_Debug::warn('config', "page_config cannt decode, file:{$configFile}.");
        }

        return isset(self::$_pageConfigs[$this->name]) ? self::$_pageConfigs[$this->name] : false;
    }

    public static function getRootSkeleton()
    {
        return self::$_rootPage;
    }

    public static function addPagelet($data)
    {
        $pageletParams = json_decode($data, true);

        return self::$_rootPage ? self::$_rootPage->createChild($pageletParams) : false;
    }

    /**
     * 自定义$params的值
     * @return [type] [description]
     */
    protected function initParams()
    {
        
    }

    public function setParams($params)
    {
        $this->params = $params;
    }

    public function createChild($pageletParams, $pageletName = '', $skeletonClassName = '')
    {
        //获取pagelet class
        //  page_config中的key或者参数中的tpl
        $pid = $pageletName ? $pageletName : (isset($pageletParams['tpl']) ? $pageletParams['tpl'] : '');
        $pageletClass = Tool_Misc::underScoreToCamel($pid, '_', true);
        $pageletClassWithSkeleton = Tool_Misc::underScoreToCamel($skeletonClassName . '_' . $pid, '_', true);

        if (class_exists($pageletClassWithSkeleton)) 
        {
            //支持pl在page目录下

            unset($pageletParams['tpl']); //避免覆盖Pagelet指定tpl
            $pagelet = new $pageletClassWithSkeleton($pid);
        }
        else if (class_exists($pageletClass)) 
        {
            //支持pl在pl目录下

            unset($pageletParams['tpl']); //避免覆盖Pagelet指定tpl
            $pagelet = new $pageletClass($pid);
        }
        else
        {
            //当pagelet不存在时，创建空的pagelet
            //对于一些不需要数据的pagelet，可以不创建class
            $pagelet = new Comm_Bigpipe_Pagelet($pid);
        }

        //参数设定
        $pagelet->setParams($pageletParams);
        $this->addChild($pagelet);

        return $pagelet;
    }
    
    public function addChild(Comm_Bigpipe_Pagelet $child)
    {
        $child->pageContainer = $this;
        
        if (isset($this->children[$child->name]))
        {
            throw new Comm_Bigpipe_Exception('pl added already');
        }

        if ($this->isSkeleton == false)
        {
            throw new Comm_Bigpipe_Exception('Skeleton pagelet cannot added to a non-skeleton parent');
        }

        $this->children[$child->name] = $child;
    }
    
    public function getChild($name)
    {
        return isset($this->children[$name]) ? $this->children[$name] : null;
    }
    
    public function getChildrenNames()
    {
        return array_keys($this->children);
    }
    
    /**
     * Page初始化时的Meta数据（如WeiboPage统一的初始化）
     * @return [type] [description]
     */
    public function getMetaData()
    {
        return $this->pageletMeta;
    }

    /**
     * 自定义的Page/Pl Meta数据获取
     * @return [type] [description]
     */
    public function getPageMetaData()
    {
        return array();
    }

    /**
     * Page/Pl的数据获取
     * @return [type] [description]
     */
    public function prepareData()
    {
        return $this->pageletData;
    }

    /**
     * Page渲染前的动作
     * @return [type] [description]
     */
    public function initPage()
    {
        return true;
    }

    /**
     * Page渲染结束后的动作
     * @return [type] [description]
     */
    public function finishPage()
    {
        return false;
    }

    public function getTemplate()
    {
        //当tpl没有在page/pl中定义时，采用page/pl的名称组合
        if ($this->tpl == '')
        {
            if ($this->isSkeleton)
            {
                //默认page的template是index.phtml
                $this->tpl = "{$this->name}/index.phtml";
                //$this->tpl = "{$this->name}/{$this->name}.phtml";
            }
            else
            {
                //和skeleton同目录
                //$this->tpl = "{$this->pageContainer->name}/{$this->name}.phtml";
                $this->tpl = dirname($this->pageContainer->tpl) . "/{$this->name}.phtml";
            }
        }

        return $this->tpl;
    }

    public function getBigpipeMode()
    {
        $bpModes = array(
            'Streamline' => self::BP_MODE_STREAMLINE,
            'Traditional' => self::BP_MODE_TRADITIONAL,
            'ScriptOnlyStreamline' => self::BP_MODE_SCRIPTSTREAM
        );

        $bpMode = Comm_Bigpipe_Render::detectRenderType();
        return isset($bpModes[$bpMode]) ? $bpModes[$bpMode] : self::BP_MODE_STREAMLINE;
    }
}