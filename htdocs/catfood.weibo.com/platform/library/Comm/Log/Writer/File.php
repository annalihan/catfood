<?php

class Comm_Log_Writer_File extends Comm_Log_Writer
{
    private $_levelTypes = array(
        LOG_LEVEL_ALL => '',
        LOG_LEVEL_FATAL => '.wf',
        LOG_LEVEL_ERROR => '.wf',
        LOG_LEVEL_WARN => '.wf',
        LOG_LEVEL_INFO => '',
        LOG_LEVEL_DEBUG => '',
        LOG_LEVEL_TRACE => '',
    );

    private $_rootDirectory = '';
    private $_subDirectory = '';
    private $_directory = '';
    private $_levelFiles = array();
    
    /**
     * 创建实例和目录
     * @param string $directory 日志目录
     * @param string $formatter 格式化名称
     */
    public function __construct($directory = '', $formatterName = 'Default')
    {
        //父类创建formatter
        parent::__construct($formatterName);

        $this->_rootDirectory = $directory;

        $this->_initDirectory();
    }

    private function _initDirectory()
    {
        if (empty($this->_rootDirectory))
        {
            $this->_rootDirectory = Comm_Config::get('log.dir', '');
        }

        $directory = $this->_rootDirectory . '/' . Comm_Config::get('project.name', 'project') . '/';

        if ($this->_subDirectory)
        {
            $directory .= $this->_subDirectory . '/';
        }

        if (!is_dir($directory))
        {
            @mkdir($directory, 0777, true);
        }

        if (!is_writable($directory))
        {
            //throw new Comm_Exception_Program('log directory not available');
        }

        $this->_directory = realpath($directory);
    }

    /**
     * 将日志写入文件
     *     文件名:ymd.log.{wf|''}
     *     内容:H:i:s - message
     * @param  integer      $level    [description]
     * @param  string|array $message  [description]
     * @param  string       $type     [description]
     * @return [type]                 [description]
     */
    public function write($level, $message, $type = '')
    {
        $date = date('Ymd');
        $level = isset($this->_levelTypes[$level]) ? $level : LOG_LEVEL_ALL;
        $logFile = "{$this->_directory}/{$date}.log{$this->_levelTypes[$level]}";
        $message = $this->formatter->formatMessage($level, $message, $type);

        $result = file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
        if ($result == false)
        {
            $this->_initDirectory();
            file_put_contents($logFile, $message . PHP_EOL, FILE_APPEND);
        }
    }

    /**
     * 设定写入目标
     *     用于daemon等工程写日志
     * @param [type] $target [description]
     */
    public function setSubTarget($target)
    {
        $this->_subDirectory = $target;
        $this->_initDirectory();
    }
}
