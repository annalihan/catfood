<?php
/**
 * Controller抽象类
 * @package Controller
 */
abstract class AbstractController extends Yaf_Controller_Abstract
{
    const MUST_LOGIN = 0;
    const NOT_LOGIN = 1;
    const MAYBE_LOGIN = 2; 
    const MUST_CHECK_SESSION = true;

    public $authorizeType = self::MUST_LOGIN;
    public $initOwner = false;
    public $initSkin = false;
    public $checkSession = false;

    public $tpl = '';
    public $tplData = array();

    /**
     * 当indexAction出现异常时的默认输出
     * @var string,array
     */
    public $defaultOutput = null;

    private function _runFailure($exception)
    {
        try
        {
            $this->failure($exception);
        }
        catch (Exception $e)
        {
            $this->_outDefault($e);
        }
    }

    private function _outDefault($exception = null)
    {
        if ($this->defaultOutput === null)
        {
            $this->renderApiJson(100001, $exception->getMessage(), '');
        }
        else
        {
            if (is_array($this->defaultOutput))
            {
                if (isset($this->defaultOutput['code']) && isset($this->defaultOutput['msg']))
                {
                    $this->renderApiJson($this->defaultOutput['code'], $this->defaultOutput['msg'], isset($this->defaultOutput['data']) ? $this->defaultOutput['data'] : null);
                }
                else
                {
                    $this->renderJson($this->defaultOutput);
                }
            }
            else
            {
                echo $this->defaultOutput;
            }
        }
    }
    
    /**
     * Controller 执行函数
     */
    public abstract function run();

    /**
     * Controller 执行函数前的函数
     * @return boolean [description]
     */
    public function runBefore()
    {
        return true;
    }

    /*
    public function runAfter()
    {
        
    }
    */

    /**
     * Controller 容错处理
     */
    public function failure($exception)
    {
        $this->_outDefault($exception);
    }

    /**
     * Controller 默认动作
     */
    public final function indexAction()
    {
        try
        {
            if ($this->runBefore())
            {
                $this->run();    
            }

            //$this->runAfter();
        }
        catch (Exception $exception)
        {
            $errorMessage = array(
                'message' => $exception->getMessage(),
                'trace' => debug_backtrace(3),
            );
            Tool_Log::fatal($errorMessage);

            $this->_runFailure($exception);
        }
    }

    /**
     * 渲染单个页面
     * @param  Comm_Bigpipe_Pagelet $pl           [description]
     * @param  boolean              $returnString [description]
     * @return [type]                             [description]
     */
    public function renderPagelet(Comm_Bigpipe_Pagelet $pl, $returnString = false)
    {
        $meta = $pl->getMetaData();
        $data = $pl->prepareData();
        
        $engine = Core_Template::getInstance();
        $engine->assignValues($meta);
        $engine->assignValues($data);

        if ($returnString)
        {
            return $engine->render($pl->getTemplate());
        }
        else
        {
            return $engine->display($pl->getTemplate());
        }
    }

    /**
     * 渲染页面
     * @param  Comm_Bigpipe_Pagelet $page [description]
     * @return [type]                     [description]
     */
    public function renderPage(Comm_Bigpipe_Pagelet $page)
    {
        $render = Comm_Bigpipe_Render::create(Comm_Bigpipe_Render::detectRenderType(), $page);
        $render->render();
    }
    
    /**
     * AJAX输出
     * @param  [type] $code [description]
     * @param  string $msg  [description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function renderAjax($code, $msg = '', $data = null)
    {
        Tool_Jsout::normal($code, $msg, $data);
    }
    
    /**
     * 默认接口输出
     * @param  [type] $code   [description]
     * @param  string $msg    [description]
     * @param  [type] $data   [description]
     * @return [type]         [description]
     */
    public function renderApiJson($code, $msg = '', $data = null)
    {
        Tool_Jsout::normal($code, $msg, $data);
    }
    
    /**
     * 自定义JSON输出
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function renderJson($data)
    {
        if (!headers_sent())
        {
            header('Content-type: application/json; charset=utf-8', true);
        }

        echo json_encode($data);
    }

    /**
     * 渲染HTML并输出
     * @param  array   $data         [description]
     * @param  boolean $returnString [description]
     * @return [type]                [description]
     */
    public function renderHtml($data = array(), $returnString = false)
    {
        $engine = Core_Template::getInstance();

        $engine->assignValues($this->tplData);
        $engine->assignValues($data);

        if ($returnString)
        {
            return $engine->render($this->tpl);
        }
        else
        {
            return $engine->display($this->tpl);
        }
    }
}
