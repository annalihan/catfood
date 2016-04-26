<?php

abstract class Comm_Log_Writer
{
    public $formatter = null;

    public function __construct($formatterName = 'Default')
    {
        $formatterClass = "Comm_Log_Formatter_{$formatterName}";
        
        if (class_exists($formatterClass) && get_parent_class($formatterClass) == 'Comm_Log_Formatter')
        {
            $this->formatter = new $formatterClass();
        }
        else
        {
            $this->formatter = new Comm_Log_Formatter_Default();
        }
    }
    
    /**
     * 日志记录
     * @param  [type] $level   [description]
     * @param  [type] $message [description]
     * @param  string $type    [description]
     * @return [type]          [description]
     */
    abstract public function write($level, $message, $type = '');

    /**
     * 二级目录设定
     * @param [type] $target [description]
     */
    abstract public function setSubTarget($target);
}