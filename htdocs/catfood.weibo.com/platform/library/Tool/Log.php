<?php
/**
 * 日志处理类
 * @package Tool
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class Tool_Log
{
    private $_currentWriter = null;
    private $_minLevel = LOG_LEVEL_INFO;
    public $logId = null;

    private static $_instance;

    public static function getInstance()
    {
        if (empty(self::$_instance))
        {
            self::$_instance = new Tool_Log();
        }

        return self::$_instance;
    }

    public function __construct()
    {
        $this->_minLevel = Comm_Config::get('log.level', LOG_LEVEL_INFO);
        $this->_createId();
    }

    public static function setLevel($level)
    {
        return self::getInstance()->setMinLevel($level);
    }

    public static function getLogId()
    {
        return self::getInstance()->logId;
    }

    public static function resetLogId()
    {
        return self::getInstance()->resetId();
    }

    public static function trace($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_TRACE, $message, $type);
    }

    public static function debug($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_DEBUG, $message, $type);
    }

    public static function info($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_INFO, $message, $type);
    }

    public static function notice($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_INFO, $message, $type);
    }

    public static function warn($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_WARN, $message, $type);
    }

    public static function warning($message, $type = 'app')
    {
        self::getInstance()->logMessage(LOG_LEVEL_WARN, $message, $type);
    }

    public static function error($message, $type = 'app')
    {
        $viewer = Comm_Context::get('viewer', false);
        $uid = $viewer ? $viewer->id : 0;

        $log = array(
            'cip' => Comm_Context::getClientIp(),
            'dpool' => Comm_Context::getServer('SERVER_ADDR'),
            'uid' => $uid,
            'content' => is_array($message) ? json_encode($message) : $message,
        );

        self::getInstance()->logMessage(LOG_LEVEL_ERROR, implode('|', $log), $type);
    }

    public static function fatal($message, $type = 'app')
    {
        $viewer = Comm_Context::get('viewer', false);
        $uid = $viewer ? $viewer->id : 0;

        $log = array(
            'cip' => Comm_Context::getClientIp(),
            'dpool' => Comm_Context::getServer('SERVER_ADDR'),
            'uid' => $uid,
            'content' => is_array($message) ? json_encode($message) : $message,
        );

        self::getInstance()->logMessage(LOG_LEVEL_FATAL, implode('|', $log), $type);
    }

    public function setMinLevel($level)
    {
        $this->_minLevel = $level;
    }

    private function _createWriter()
    {
        if ($this->_currentWriter == false)
        {
            $this->_currentWriter = new Comm_Log_Writer_File();
            $this->_currentWriter->formatter->setLogId($this->logId);
        }
    }

    /**
     * 格式化内容
     * @param  [type] $message [description]
     * @return [type]          [description]
     */
    public function formatMessage($message)
    {
        $this->_createWriter();

        return $this->_currentWriter->formatter->formatMessageBody($message);
    }

    /**
     * 记录日志
     * @param  integer $level      日志等级
     * @param  [type]  $message    日志内容
     * @param  string  $type       日志类型
     */
    public function logMessage($level, $message, $type = 'app')
    {
        if ($level > $this->_minLevel && $this->_minLevel > LOG_LEVEL_ALL)
        {
            return;
        }

        $this->_createWriter();

        $this->_currentWriter->write($level, $message, $type);
    }

    /**
     * 添加Writer
     * @param [type] $writerName [description]
     * @param [type] $writer     [description]
     */
    public function setWriter($writer)
    {
        $this->_currentWriter = $writer;
    }

    /**
     * 设定文件日志记录
     * @param  [type] $directory     [description]
     * @param  string $formatterName [description]
     * @return [type]                [description]
     */
    public function setFileWriter($directory, $formatterName = 'Default')
    {
        $this->setWriter(new Comm_Log_Writer_File($directory, $formatterName));
    }

    public function resetId()
    {
        $this->_createWriter();

        $this->_createId();
        $this->_currentWriter->formatter->setLogId($this->logId);

        return $this->logId;
    }

    public function setSubTarget($target)
    {
        $this->_createWriter();
        $this->_currentWriter->setSubTarget($target);
    }

    private function _createId()
    {
        $this->logId = sprintf("%u%04u", substr(sprintf("%.0f", microtime(true) * 1000000), 5), rand(0, 9999));
    }
}
