<?php
/**
 * DEBUG功能模块
 * 
 * @todo 抽象出处理类(新增、结束)，抽象格式化类
 * @package Core
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */

include T3P_PATH . '/FirePHPCore/FirePHP.class.php';

class Core_Debug
{
    const STATUS_TYPE_MC = 'mc';
    const STATUS_TYPE_REDIS = 'redis';
    const STATUS_TYPE_HTTP = 'http';
    const STATUS_TYPE_MYSQL = 'mysql';
    const STATUS_TYPE_ALL = 'all';

    private static $_sqlReplace = array('`', ' ', "\r", "\n");

    /**
     * 是否开启日志，抽样开放用于做性能分析(1%)
     * @var boolean
     */
    private static $_logOpened = false;

    /**
     * 是否开启Firebug，使用参数开放用于调试
     * @var boolean
     */
    private static $_debugOpened = false;

    /**
     * 是否开启统计，Daemon时开启
     * @var boolean
     */
    private static $_summaryOpened = false;
    
    private static $_summaries = array();

    /**
     * 设定Log状态
     * @param [type] $value [description]
     */
    public static function openLog($value = true)
    {
        self::$_logOpened = $value;
    }

    /**
     * 设定DEBUG状态
     * @param [type] $value [description]
     */
    public static function openDebug($value = true)
    {
        self::$_debugOpened = $value;
    }

    /**
     * 获取Debug开启状态
     * @return [type] [description]
     */
    public static function debugIsOpen()
    {
        return self::$_debugOpened;
    }

    /**
     * 设定Summary状态
     * @param [type] $value [description]
     */
    public static function openSummary($value = true)
    {
        self::$_summaryOpened = $value;
    }

    public static function addHttp($startTime, $info = array())
    {
        return self::_addStatus(self::STATUS_TYPE_HTTP, $startTime, $info);
    }

    public static function addMc($startTime, $info = array())
    {
        return self::_addStatus(self::STATUS_TYPE_MC, $startTime, $info);
    }

    public static function addRedis($startTime, $info = array())
    {
        return self::_addStatus(self::STATUS_TYPE_REDIS, $startTime, $info);
    }

    public static function addMysql($startTime, $info = array())
    {
        return self::_addStatus(self::STATUS_TYPE_MYSQL, $startTime, $info);
    }

    public static function getUrlId($url)
    {
        list($url) = explode('?', $url);
        return md5(strtolower($url));
    }

    public static function getSqlId($sql)
    {
        $sql = str_replace(self::$_sqlReplace, '', $sql);
        return md5(strtolower($sql));
    }

    /**
     * 输出日志
     * @param  [type]  $type      [description]
     * @param  integer $startTime [description]
     * @param  integer $runTime   [description]
     * @param  array   $info      [description]
     * @return [type]             [description]
     */
    private static function _logStatus($type, $startTime = 0, $runTime = 0, $info = array())
    {
        //日志内容包括:开始时间，消耗时间，名称，状态和附加属性
        switch ($type)
        {
            case self::STATUS_TYPE_MYSQL:
                //去掉不必要的空格和换行，转换成小写
                $sql = isset($info['sql']) ? $info['sql'] : json_encode($info);
                $id = self::getSqlId($sql);
                $code = isset($info['code']) ? $info['code'] : 0;
                break;

            case self::STATUS_TYPE_ALL:
                $url = isset($info['url']) ? $info['url'] : json_encode($info);
                $id = self::getUrlId($url);
                $code = isset($info['code']) ? $info['code'] : 0;
                break;

            case self::STATUS_TYPE_HTTP:
                $url = isset($info['url']) ? $info['url'] : json_encode($info);
                $id = self::getUrlId($url);
                $code = isset($info['http_code']) ? $info['http_code'] : 200;
                break;

            default:
                $id = isset($info['function']) ? $info['function'] : json_encode($info);
                $code = isset($info['code']) ? $info['code'] : 0;
                break;
        }

        $info['trace'] = self::_getTraceInfo();

        $logMessage = array(
            'type' => $type,
            'start_time' => $startTime,
            'run_time' => $runTime,
            'code' => $code,
            'id' => $id,
            'info' => json_encode($info),
        );

        $message = Tool_Log::getInstance()->formatMessage($logMessage);

        Tool_Log::getInstance()->logMessage(LOG_LEVEL_INFO, $message, 'res');
    }

    private static function _getTraceInfo()
    {
        $traceInfos = array();
        $traces = debug_backtrace();
        $appPathLength = strlen(APP_PATH);
        $indexFile = APP_PATH . DS . 'index.php';
        foreach ($traces as $trace)
        {
            if (isset($trace['file']))
            {
                $file = $trace['file'];
                if (strpos($file, APP_PATH) !== false && $file != $indexFile)
                {
                    $traceInfos[] = array(substr($file, $appPathLength), $trace['line']);
                }
            }
        }

        return $traceInfos;
    }

    /**
     * 用于FireBug
     * @param  [type]  $type      [description]
     * @param  integer $startTime [description]
     * @param  integer $runTime   [description]
     * @param  array   $info      [description]
     * @return [type]             [description]
     */
    private static function _debugStatus($type, $startTime = 0, $runTime = 0, $info = array())
    {
        $debugInfo = array(
            'type' => $type,
            'start_time' => $startTime,
            'run_time' => $runTime,
            'info' => $info,
        );

        self::_debugInfo(LOG_LEVEL_DEBUG, $type, $debugInfo, false);
    }

    /**
     * 用于Daemon统计
     * @param  [type]  $type      [description]
     * @param  integer $startTime [description]
     * @param  integer $runTime   [description]
     * @param  array   $info      [description]
     * @return [type]             [description]
     */
    private static function _summaryStatus($type, $startTime = 0, $runTime = 0, $info = array())
    {
        if (isset(self::$_summaries[$type]) === false)
        {
            self::$_summaries[$type] = array(
                'summary' => array(
                    'min' => PHP_INT_MAX, 
                    'max' => 0, 
                    'length' => 0, 
                    'count' => 0,
                ), 
                'detail' => array(),
            );
        }

        $summary = self::$_summaries[$type]['summary'];
        $summary['count']++;

        if ($runTime > 0)
        {
            if ($runTime > $summary['max'])
            {
                $summary['max'] = $runTime;
            }

            if ($runTime < $summary['min'])
            {
                $summary['min'] = $runTime;
            }

            $summary['length'] += $runTime;
        }

        $detailValue['time'] = $runTime;

        self::$_summaries[$type]['summary'] = $summary;
        //self::$_summaries[$type]['detail'][] = $detailValue;
    }

    /**
     * [_addStatus description]
     * @param [type]  $type      [description]
     * @param integer $startTime [description]
     * @param integer $code      [description]
     * @param string  $name      [description]
     * @param string  $info      [description]
     */
    private static function _addStatus($type, $startTime = 0, $info = array())
    {
        $runTime = ($startTime > 0 ? round((microtime(true) - $startTime) * 1000, 2) : 0);

        //写 Log
        if (self::$_logOpened)
        {
            self::_logStatus($type, $startTime, $runTime, $info);
        }

        //显示 Debug
        if (self::$_debugOpened)
        {
            self::_debugStatus($type, $startTime, $runTime, $info);
        }

        //记录 Summary
        if (self::$_summaryOpened)
        {
            self::_summaryStatus($type, $startTime, $runTime, $info);
        }

        return true;
    }

    /**
     * 打印状态数据
     * TODO
     * @return [type] [description]
     */
    public static function sessionFinished()
    {
        $runTime = self::getRunTime();
        $sessionName = Comm_Context::getCurrentUrl(false);

        //所有请求都记录
        $info = array(
            'url' => $sessionName,
            'b' => Core_Branch::$branchName,
            'g' => Core_Branch::$grayVersion,
            'p' => intval(self::$_logOpened),
        );

        self::_logStatus(self::STATUS_TYPE_ALL, PLATFORM_START_TIME, $runTime, $info);
    }

    public static function trace($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_TRACE, $type, $message);
    }

    public static function debug($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_DEBUG, $type, $message);
    }

    public static function info($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_INFO, $type, $message);
    }

    public static function warn($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_WARN, $type, $message);
    }

    public static function error($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_ERROR, $type, $message);
    }

    public static function fatal($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_FATAL, $type, $message);
    }

    public static function table($type, $message)
    {
        self::_debugInfo(LOG_LEVEL_OTHER, $type, $message);
    }

    /**
     * 通过FirePHP显示日志
     * @param  [type]  $level    [description]
     * @param  [type]  $type     [description]
     * @param  [type]  $value    [description]
     * @param  boolean $writeLog [description]
     * @return [type]            [description]
     */
    private static function _debugInfo($level, $type, $value, $writeLog = true)
    {
        //写日志
        if ($writeLog)
        {
            Tool_Log::getInstance()->logMessage($level, $value, $type);
        }

        //如果头已经设定
        if (headers_sent())
        {
            return;
        }

        switch (true)
        {
            case $level === LOG_LEVEL_FATAL:
                FirePHP::getInstance(true)->dump($type, $value);
                break;

            case $level === LOG_LEVEL_WARN:
                FirePHP::getInstance(true)->warn($value, $type);
                break;

            case $level === LOG_LEVEL_ERROR:
                FirePHP::getInstance(true)->error($value, $type);
                break;

            case $level === LOG_LEVEL_INFO:
                FirePHP::getInstance(true)->info($value, $type);
                break;

            case $level === LOG_LEVEL_ALL:
            case $level === LOG_LEVEL_DEBUG:
                FirePHP::getInstance(true)->log($value, $type);
                break;

            case $level === LOG_LEVEL_TRACE:
                FirePHP::getInstance(true)->trace($value, $type);
                break;

            case $level === LOG_LEVEL_OTHER:
            default:
                FirePHP::getInstance(true)->table($type, $value);
                break;
        }
    }

    /**
     * 获取脚本运行时间
     * @return float 单位为毫秒
     */
    public static function getRunTime()
    {
        return round((microtime(true) - PLATFORM_START_TIME) * 1000, 2);
    }

    /**
     * 获取状态数据
     * @return [type] [description]
     */
    public static function getSummary()
    {
        return self::$_summaries;
    }
}
