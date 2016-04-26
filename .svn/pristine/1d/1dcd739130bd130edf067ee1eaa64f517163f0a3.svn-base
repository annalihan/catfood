<?php 
/**
 * Yaf模板
 * @package Core
 * @subpackage Template
 */
class Core_Template_Yaf extends Yaf_View_Simple implements Core_Template_Interface
{
    public function clearAllAssign()
    {
        $this->clear();
    }

    public function assignValues($values)
    {
        if (is_array($values) === false)
        {
            return;
        }

        foreach ($values as $key => $value)
        {
            $this->assign($key, $value);
        }
    }

    public function display($tpl, $tplVars = null)
    {
        return $this->_render($tpl, $tplVars, true);
    }

    public function render($tpl, $tplVars = null)
    {
        return $this->_render($tpl, $tplVars, false);
    }

    private function _render($tpl, $tplVars = null, $output = false)
    {
        if (empty($tpl))
        {
            return false;
        }

        $tplFile = Core_Loader::getInstance()->getTemplateFile($tpl);
        if ($tplFile === false)
        {
            return false;
        }

        //方法1:无法解决入口和版本问题到来的异常
        //把视图目录加入include_path
        //把当前tpl目录加入include_path
        //set_include_path(get_include_path() . PATH_SEPARATOR . dirname($tplFile));       
        //方法2:在phtml中include绝对路径

        //设定当前视图，用于引用时获取数据
        if ($output)
        {
            return parent::display($tplFile, $tplVars);
        }
        else
        {
            return parent::render($tplFile, $tplVars);
        }
    }
}