<?php 

abstract class Comm_Bigpipe_Render
{
    protected $templateEngine = false;
    
    /**
     * 当前需要渲染的根Pagelet
     * 
     * @var Comm_Bigpipe_Pagelet
     */
    protected $pagelet;
    
    public function __construct(Comm_Bigpipe_Pagelet $pagelet = null)
    {
        $this->pagelet = $pagelet;
    }
    
    /**
     * BigPipe的Render
     * @return [type] [description]
     */
    public function render()
    {
        //创建引擎
        $this->templateEngine = Core_Template::getInstance();

        //元数据准备(包括Page和Pagelet)
        $this->prepareMeta($this->pagelet);

        //渲染骨架
        $this->renderSkeleton($this->pagelet);
        
        //渲染所有Pagelet
        foreach ($this->pagelet->children as $childPagelet)
        {
            if ($childPagelet->disabled)
            {
                continue;
            }

            //渲染Pagelet
            $this->renderPagelet($childPagelet);

            //自定义部分(可以一个Pl输出多段数据)
            $childPagelet->finishPage();
        }

        //结束处理
        //输出logid
        //输出</html>
        $this->pageEnd();
    }

    /**
     * 处理所有元数据
     * @param  [type] $pagelet [description]
     * @return [type]          [description]
     */
    protected function prepareMeta($pagelet)
    {
        if ($pagelet->disabled)
        {
            return;
        }

        $this->templateEngine->assignValues($pagelet->getMetaData());
        $this->templateEngine->assignValues($pagelet->getPageMetaData());
        
        foreach ($pagelet->children as $childPagelet)
        {
            if ($childPagelet->disabled)
            {
                continue;
            }

            $this->prepareMeta($childPagelet);
        }
    }
    
    /**
     * 渲染骨架
     * @param  Comm_Bigpipe_Pagelet $pagelet [description]
     * @return [type]                        [description]
     */
    abstract protected function renderSkeleton(Comm_Bigpipe_Pagelet $pagelet);

    /**
     * 渲染骨架
     * @param  Comm_Bigpipe_Pagelet $pagelet [description]
     * @return [type]                        [description]
     */
    abstract protected function renderPagelet(Comm_Bigpipe_Pagelet $pagelet);
    
    /**
     * Pagelet的BigPipe脚本渲染
     *     当pagelet->disabled时不进行渲染(三层判断，config,meta和data)
     * @param  Comm_Bigpipe_Pagelet $pagelet [description]
     * @return [type]                   [description]
     */
    protected function renderPageletWithJson(Comm_Bigpipe_Pagelet $pagelet)
    {
        $jsonValue = array();

        foreach ($pagelet->params as $key => $value)
        {
            switch ($key)
            {
                case 'js':
                case 'css':
                    //获取版本信息
                    $version = '?version=' . ($key == 'js' ? Tool_Misc::homesiteJsVersion() : Tool_Misc::homesiteCssVersion());
                    if (is_array($value))
                    {
                        foreach ($value as $source)
                        {
                            $jsonValue[$key][] = Core_Branch::formatJs($source, $version);
                        }
                    }
                    elseif ($value)
                    {
                        $jsonValue[$key] = Core_Branch::formatJs($value, $version);
                    }

                    break;

                default:
                    $jsonValue[$key] = $value;
                    break;
            }
        }

        //获取数据
        $this->templateEngine->assignValues($pagelet->prepareData());

        //在处理数据以后可能会有对pagelet的disabled进行控制的
        //所以此条件放在数据处理之后
        if ($pagelet->disabled)
        {
            return '';
        }

        //使用引擎渲染得到html部分
        $html = $this->templateEngine->render($pagelet->getTemplate());

        //避免非UTF字符导致无法encode
        $jsonValue['html'] = ($html ? iconv('UTF-8', 'UTF-8//IGNORE', $html) : '');

        //JS部分
        if (isset($jsonValue['function']))
        {
            $bpFunctionName = $jsonValue['function'];
            unset($jsonValue['function']);
        }
        elseif (isset($jsonValue['fun']))
        {
            $bpFunctionName = $jsonValue['fun'];
            unset($jsonValue['fun']);
        }
        else
        {
            $bpFunctionName = 'render_pl';
        }

        return sprintf("<script>%s(%s)</script>\r\n", $bpFunctionName, json_encode($jsonValue));
    }
    
    /**
     * 强制输出
     * @return [type] [description]
     */
    protected function flush()
    {
        if (ob_get_level())
        {
            ob_flush();
        }

        flush();
    }

    /**
     * 获取渲染模式
     * @return [type] [description]
     */
    final public static function detectRenderType()
    {
        $info = Comm_ClientProber::getAgent();
        if (Comm_ClientProber::isIE())
        {
            $version = Comm_ClientProber::getVersion();

            if ($version < 7)
            {
                //IE6
                return 'Traditional';
            }
        }
        
        if (Comm_Context::param('nojs'))
        {
            return 'Traditional';
        }

        /*if (Comm_Context::param('ajaxpagelet'))
        {
            return 'ScriptOnlyStreamline';
        }*/

        return 'Streamline';
    }

    /**
     * 创建BigPipe渲染器
     * @param  [type] $renderType [description]
     * @param  [type] $pagelet    [description]
     * @return [type]             [description]
     */
    final public static function create($renderType, Comm_Bigpipe_Pagelet $pagelet = null)
    {
        $renderClass = 'Comm_Bigpipe_' . ucfirst($renderType) . 'Render';

        if (!class_exists($renderClass) || !is_subclass_of($renderClass, __CLASS__))
        {
            if (class_exists($renderType) && is_subclass_of($renderType, __CLASS__))
            {
                $renderClass = $renderType;
            }

            throw new Comm_Bigpipe_Exception('Invalid render class:' . $renderType);
        }

        return new $renderClass($pagelet);
    }

    /**
     * 从html中删除</html>结束标签。
     *     如果html结束标签出现在尾部(最后的20字节之内)，则移除之。否则，会保留，以防止替换掉不该替换的标签。
     * @param  [type] $html [description]
     * @return [type]       [description]
     */
    final protected function pageStart($html)
    {
        $htmlCloseTagPos = strripos($html, '</html>');
        
        if ($htmlCloseTagPos !== false && abs(strlen($html) - $htmlCloseTagPos) <= 20)
        {
            $html = substr_replace($html, '', $htmlCloseTagPos, 7);
        }

        echo $html;
    }

    /**
     * Page渲染最后处理
     * @return [type] [description]
     */
    public function pageEnd()
    {
        //echo "\n<!-- logid:" . Tool_Log::getLogId() . " --!>\n";
        echo "</html>";
    }
} 