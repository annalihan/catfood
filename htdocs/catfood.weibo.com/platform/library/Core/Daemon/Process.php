<?php
/**
 * 常规双进程队列。子进程的主要功能
 * 1、获取队列列表内容，解析出每条队列内容，调用自定义处理函数
 * 2、接收外部命令，控制队列子进程
 * 
 * 实际做逻辑处理的子进程很容易出现异常或者内存泄漏，子进程需要设定重启策略。
 */
class Core_Daemon_Process extends Core_Daemon_Abstract
{
    private $_readBuff = '';//读缓冲
    
    public $runStatusStartTime = 0.0;//工作进程启动时间
    public $queueStartTime = 0.0;
    
    public $queueStatusTotalCount = 0;//工作进程队列总处理数，不受工作进程重启影响
    public $queueStatusTotalTimeCount = 0;//工作进程队列总时间消耗，不受工作进程重启影响
    public $queueStatusTotalQPS = 0;//工作进程队列平均QPS，不受工作进程重启影响
    public $queueStatusTotalCpuU = 0;//工作进程队列总用户态Cpu消耗，不受工作进程重启影响
    public $queueStatusTotalCpuS = 0;//工作进程队列总系统态Cpu消耗，不受工作进程重启影响
    
    public $queueStatusMemory = 0;
    public $queueStatusTicks = 0;
    public $queueStatusCpuU = 0;
    public $queueStatusCpuS = 0;
    
    public $queueStatusCurrentCount = 0;//工作进程队列处理数
    public $queueStatusCurrentQueueStartTime = 0;//工作进程当前队列开始时间
    public $queueStatusCurrentQueueEndTime = 0;//工作进程当前队列结束时间
    public $queueStatusCurrentQueueRunTime = 0;//工作进程当前队列所消耗的时间
    public $queueStatusCurrentTimeCount = 0;//工作进程队列总消耗的时间
    public $queueStatusCurrentQPS = 0;//工作进程队列处理数
    public $queueStatusCurrentCheckTime = 0.0;//心跳最后检查时间

    public $queueStatusCurrentRoundRunTime = 0;
    public $queueStatusCurrentRoundCount = 0;
    public $queueStatusCurrentRoundQPS = 0;

    public $queueHandleInit = '';//队列初始化句柄
    public $queueHandleListDoBefore = '';//队列列表执行前句柄
    public $queueHandleGetData = '';//队列获取数据句柄
    public $queueHandleAfterGetFailed = '';//队列获取失败句柄
    public $queueHandleDobefore = '';//队列执行前句柄
    public $queueHandleDo = '';//队列执行句柄
    public $queueHandleDoAfter = '';//队列执行完后句柄
    public $queueHandleListDoAfter = '';//队列列表执行完后句柄
    public $queueHandleQuit = '';//队列退出
    public $queueBlockPop = false;//取队列是否采用堵塞模式
    public $queueBlockTimeout = 1;//堵塞模式超时时间
    public $enable = true;//队列子进程激活开关
    public $healthTime = 0;//执行时间控制
    public $healthCount = 0;//执行次数控制
    public $queueListSleep = 0.001;//睡眠间隔
    public $checkInter = 2.0;//心跳检查间隔
    public $index;//队列序号
    public $maxWorker;//队列总数
    public $pid;//进程号
    public $mainProcessId;//主进程id
    public $sockChannel;//Sock通道
    public $CP;//父对象
    public $CLog;//日志对象
    public $idleTime = 60;//最长空闲时间(默认1分钟)
    public $idleLastTime = 0;//上次数据时间
    public $resourceConfig = array();//resource新的配置
    public $openLog = false; //是否全量开启性能分析日志

    /*队列配置*/
    public $queueEngine = QUEUE_ENGINE_REDIS;
    public $queueResource;//队列资源名
    public $queueKey;//队列Key
    private $_queueObject = null;

    private $_run = true;
    private $_restartReason = '';

    private $_socketPort = 0;

    public function __construct($param = array())
    {
        //参数初始化
        $this->setParam($param);

        //分散开 healthTime
        if ($this->healthTime > 0)
        {
            $this->healthTime = $this->healthTime * 100 / rand(80, 120);
        }
    }

    /**
     * 队列子进程主程序
     * @return [type] [description]
     */
    public function startDaemon()
    {
        declare(ticks = 1);

        //初始化
        $this->initDaemon();

        //忽略Kill信号
        pcntl_signal(SIGTERM, array($this, 'stopDaemon'));
        pcntl_signal(SIGHUP, array($this, 'stopDaemon'));

        //调用初始化事件函数
        if ($this->queueHandleInit !== '')
            call_user_func($this->queueHandleInit, $this);

        //主循环体
        while ($this->_run)
        {
            $this->queueStartTime = microtime(true);

            //检查状态
            $this->_checkStatus();

            //处理指令
            $this->_checkCommand();

            //如果队列子进程暂停
            if (!$this->enable)
            {
                //睡眠一下
                usleep($this->queueListSleep * 1000000);
                continue;
            }

            // 没有数据时，睡眠一下下
            if ($this->_doQueue() === false)
            {
                usleep($this->queueListSleep * 1000000);
            }

            $this->queueStatusCurrentRoundCount++;
            $this->queueStatusCurrentRoundRunTime += microtime(true) - $this->queueStartTime;
            $this->queueStatusCurrentRoundQPS = round($this->queueStatusCurrentRoundCount / $this->queueStatusCurrentRoundRunTime, 2);
        }

        $this->_quitDaemon();
    }

    /*
     * 初始化队列子进程
     */
    public function initDaemon()
    {
        if (!function_exists($this->queueHandleDo))
        {
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Queue Daemon User Function ' . $this->queueHandleDo . ' No Exists', 'daemon');
            $this->queueHandleDo = '';
        }

        $this->pid = posix_getpid();
        
        //连接到主进程socket
        $this->_connectToSocket();
        
        //初始化参数
        $this->runStatusStartTime = microtime(true);
        $this->queueStatusCurrentCheckTime = $this->runStatusStartTime;
        $this->idleLastTime = $this->runStatusStartTime;
        $this->queueStatusCurrentCount = 0;//工作进程队列处理数
        $this->queueStatusCurrentQueueStartTime = 0;//工作进程当前队列开始时间
        $this->queueStatusCurrentQueueEndTime = 0;//工作进程当前队列结束时间
        $this->queueStatusCurrentQueueRunTime = 0;//工作进程当前队列所消耗的时间
        $this->queueStatusCurrentTimeCount = 0;//工作进程队列总消耗的时间
        $this->queueStatusCurrentQPS = 0;//工作进程队列处理数
        $this->queueStatusCurrentCheckTime = 0.0;//心跳最后检查时间

        //TODO
        //恢复配置
        //$this->_configResources();
        
        //由于是多进程，不能再主进程初始化资源
        //初始化队列资源
        if ($this->queueResource)
        {
            $this->_queueObject = Comm_Cache::connect($this->queueResource, $this->queueEngine);
        }
    }
            
    /**
     * 检查健康状态及执行命令
     * @return [type] [description]
     */
    private function _checkStatus()
    {
        //检查健康状态
        if ($this->_checkHealth() === false)
        {
            $this->_restartDaemon($this->_restartReason);
        }
        
        return true;
    }
    
    /**
     * 健康状态检查
     * @return [type] [description]
     */
    private function _checkHealth()
    {
        //执行时间检查
        if ($this->healthTime !== 0)
        {
            if (($this->queueStartTime - $this->runStatusStartTime) > $this->healthTime)
            {
                //已经超过执行时间，重启队列子进程
                $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Health Check. Life Cycle Done[' . $this->healthTime . ']' . ', Process Index : ' . $this->index, 'daemon');
                $this->_restartReason = 'Life cycle done.';
                return false;
            }
        }

        //执行次数检查
        if ($this->healthCount !== 0)
        {
            if ($this->healthCount < $this->queueStatusCurrentCount)
            {
                //已经超过执行次数，重启队列子进程
                $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Health Check. Queue Limit[' . $this->healthCount . ']' . ', Process Index : ' . $this->index, 'daemon');
                $this->_restartReason = 'Queue limit reached.';
                return false;
            }
        }

        //检查空闲时间
        if ($this->idleTime !== 0)
        {
            if (($this->queueStartTime - $this->idleLastTime) > $this->idleTime)
            {
                //已经空闲时间，重启队列子进程
                $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Health Check. Idle Time[' . $this->idleTime . ']' . ', Process Index : ' . $this->index, 'daemon');
                $this->_restartReason = 'Idel time reached.';
                return false;
            }
        }
        
        //检查主进程
        if ($this->checkInter !== 0 && $this->queueStatusCurrentCheckTime > 1)
        {
            //检查心跳
            if (($this->queueStartTime - $this->queueStatusCurrentCheckTime) > $this->checkInter)
            {
                //TODO:确认是当前主进程（主进程关闭以后pid被其他进程使用）
                //主进程状态
                $mainProcessStatus = Tool_Process::getStatus($this->mainProcessId);
            
                if ($mainProcessStatus === false)
                {
                    $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Queue Daemon Ping Failed, Process Index : ' . $this->index, 'daemon');
                    $this->_restartReason = 'Main process is gone.';
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 重启队列子进程
     * @return [type] [description]
     */
    private function _restartDaemon($reason = '')
    {
        //告诉主进程要重启
        $this->_sendResponse('restart', $reason);

        //重启前汇报状态
        //TODO:汇报失败时从文件恢复状态?
        $this->_statusCommandHandle();
        
        //退出
        $this->stopDaemon();
    }

    /**
     * 关闭子进程循环
     * @return [type] [description]
     */
    public function stopDaemon()
    {
        $this->_run = false;
    }
    
    /**
     * 队列子进程退出
     * @return [type] [description]
     */
    private function _quitDaemon()
    {
        try
        {
            socket_close($this->sockChannel);
        }
        catch (Exception $e)
        {
            
        }

        //调用退出事件函数
        if ($this->queueHandleQuit !== '')
        {
            call_user_func($this->queueHandleQuit, $this);
        }

        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Quit' . ', Process Index : ' . $this->index, 'daemon');

        exit;
    }
    
    /**
     * 获取当前状态
     * @return [type] [description]
     */
    private function _getStatus()
    {
        $this->queueStatusMemory = memory_get_peak_usage(true);

        $data = posix_times();

        $this->queueStatusTicks = $data['ticks'];
        $this->queueStatusCpuU = $data['utime'];
        $this->queueStatusCpuS = $data['stime'];
        
        $status = array();
        
        foreach ($this as $key => $value)
        {
            if (strpos($key, 'queueStatus') === 0)
            {
                $status[$key] = $value;
            }
        }

        return $status;
    }

    /**
     * 获取数据
     * @return [type] [description]
     */
    public function getData()
    {
        if ($this->_queueObject == false)
        {
            return false;
        }

        if ($this->queueBlockPop)
        {
            return $this->_queueObject->blockPop($this->queueKey, $this->queueBlockTimeout);
        }
        else
        {
            return $this->_queueObject->pop($this->queueKey);
        }
    }
    
    /**
     * 队列处理流程
     * @return [type] [description]
     */
    private function _doQueue()
    {
        //记录队列开始执行时间
        $this->queueStatusCurrentQueueStartTime = microtime(true);

        //重置日志ID，每次循环一个ID
        $logId = $this->CLog->resetId();

        //抽样开启性能日志
        $open = $this->openLog || $this->queueStatusCurrentRoundCount % 1000 === 0;
        Core_Debug::openLog($open);

        if ($this->queueHandleGetData !== '')
        {
            // 非队列方法获取数据
            $data = call_user_func($this->queueHandleGetData, $this);
        }
        else
        {
            // 从队列获取数据
            if ($this->_queueObject)
            {
                if ($this->queueBlockPop)
                {
                    $data = $this->_queueObject->blockPop($this->queueKey, $this->queueBlockTimeout);
                }
                else
                {
                    $data = $this->_queueObject->pop($this->queueKey);
                }
            }
            else
            {
                //TODO
                $data = false;
            }
        }

        if ($data === false)
        {
            // 数据获取失败句柄
            if ($this->queueHandleAfterGetFailed !== '')
            {
                call_user_func($this->queueHandleAfterGetFailed, $this);
            }

            return false;
        }

        //累加总处理队列数
        $this->queueStatusTotalCount++;
        
        //累加当前处理队列数
        $this->queueStatusCurrentCount++;
        
        //$this->CLog->w('QUEUE', 'Queue No.' . $this->queueStatusCurrentCount . '[' . $this->queueStatusTotalCount . '] Processing');
        
        // 上次空闲时间刷新
        $this->idleLastTime = $this->queueStatusCurrentQueueStartTime;
        
        //队列执行前句柄
        if ($this->queueHandleDobefore !== '')
        {
            call_user_func($this->queueHandleDobefore, $data);
        }
        
        //队列执行句柄
        if ($this->queueHandleDo !== '')
        {
            call_user_func($this->queueHandleDo, $data, $this);
        }
        
        $this->queueStatusCurrentQueueEndTime = microtime(true);
        
        $this->queueStatusCurrentQueueRunTime = $this->queueStatusCurrentQueueEndTime - $this->queueStatusCurrentQueueStartTime;
        
        if ($this->queueHandleDoAfter !== '')
        {
            call_user_func($this->queueHandleDoAfter, $data);
        }
        
        //$this->CLog->w('QUEUE', 'Queue No.' . $this->queueStatusCurrentCount . '[' . $this->queueStatusTotalCount . '] Done[' . $this->queueStatusCurrentQueueRunTime . ']');

        //计算总平均每秒处理队列数
        $this->queueStatusTotalTimeCount += $this->queueStatusCurrentQueueRunTime;
        
        //全局QPS计算
        if ($this->queueStatusTotalTimeCount != 0)
        {
            $this->queueStatusTotalQPS = round($this->queueStatusTotalCount / $this->queueStatusTotalTimeCount, 2);
        }
        
        $this->queueStatusCurrentTimeCount += $this->queueStatusCurrentQueueRunTime;
        
        //计算当前每秒处理队列数
        $this->queueStatusCurrentQPS = round($this->queueStatusCurrentCount / $this->queueStatusCurrentTimeCount, 2);
        
        return true;
    }

    /**
     * 连接主进程的unix socket
     * @return [type] [description]
     */
    private function _connectToSocket()
    {
        if (is_resource($this->sockChannel))
        {
            socket_close($this->sockChannel);
        }

        $this->sockChannel = socket_create(AF_UNIX, SOCK_STREAM, 0);
        
        if (!$this->sockChannel)
        {
            //无法创建Sock
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Queue Daemon Can\'t Create Sock, Process Index : ' . $this->index, 'daemon');
            exit;
        }

        if (!socket_connect($this->sockChannel, $this->CP->sockFile))
        {
            //无法连接Sock
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Queue Daemon Can\'t Connect To Main Daemon : ' . socket_strerror(socket_last_error($this->sockChannel)) . ', Process Index : ' . $this->index, 'daemon');
            exit;
        }
            
        if (!socket_set_nonblock($this->sockChannel))
        {
            //无法设置Sock
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Queue Daemon Can\'t socket_set_nonblock : ' . socket_strerror(socket_last_error($this->sockChannel)) . ', Process Index : ' . $this->index, 'daemon');
            exit;
        }

        //获取socket连接端口
        socket_getsockname($this->sockChannel, $ip, $this->_socketPort);
    }

    private function _updateMainProcessActiveTime()
    {
        $this->queueStatusCurrentCheckTime = microtime(true);
    }
    
    private function _checkCommand()
    {
        $commandValue = Core_Daemon_Protocol::read($this->sockChannel, false);

        if (empty($commandValue))
        {
            return false;
        }

        $returnValue = $this->_responseCommand($commandValue);
        return $returnValue;
    }

    private function _responseCommand($commandValue)
    {
        if (is_array($commandValue))
        {
            $command = array_shift($commandValue);
            $arguments = $commandValue;
        }
        else
        {
            $command = $commandValue;
            $arguments = array();
        }

        $commandHandle = "_{$command}CommandHandle";

        if (method_exists($this, $commandHandle))
        {
            return call_user_func_array(array($this, $commandHandle), array($arguments));
        }
        else
        {
            return false;
        }
    }

    private function _quitCommandHandle($arguments = array())
    {
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Quiting' . ', Process Index : ' . $this->index, 'daemon');
        $this->stopDaemon();
        return true;
    }

    private function _pingCommandHandle($arguments = array())
    {
        $this->_updateMainProcessActiveTime();

        return $this->_sendResponse('ping', 'PONG');
    }

    private function _restartCommandHandle($arguments = array())
    {
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Restarting' . ', Process Index : ' . $this->index, 'daemon');
        $this->_restartDaemon();
        return true;
    }

    private function _stopCommandHandle($arguments = array())
    {
        usleep($this->queueListSleep * 1000000);

        if ($this->enable)
        {
            $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Stop' . ', Process Index : ' . $this->index, 'daemon');
            $this->enable = false;
        }

        return true;
    }

    private function _continueCommandHandle($arguments = array())
    {
        if (!$this->enable)
        {
            $this->CLog->logMessage(LOG_LEVEL_INFO, 'Queue Daemon Continue' . ', Process Index : ' . $this->index, 'daemon');
            $this->enable = true;
        }

        return true;
    }

    private function _statusCommandHandle($arguments = array())
    {
        $status = $this->_getStatus();
        return $this->_sendResponse('status', Tool_Json::encode($status));
    }

    private function _performanceCommandHandle($arguments = array())
    {
        $performance = Core_Debug::getSummary();
        $content = json_encode($performance);
        return $this->_sendResponse('performance', $content);
    }

    private function _setCommandHandle($arguments = array())
    {
        $commandString = json_encode($arguments);

        if (count($arguments) != 4)
        {
            return $this->_setCommandError('Set command param error - ' . $commandString);
        }

        $type = $arguments[0];
        $name = $arguments[1];
        $field = $arguments[2];
        $value = $arguments[3];

        if (method_exists($this, "_{$type}ResourceConfig"))
        {
            $result = call_user_func_array(array($this, "_{$type}ResourceConfig"), array($name, $field, $value));
            
            if ($result === false)
            {
                $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Set command dont exists - ' . $commandString, 'daemon');
                return $this->_setCommandError('Set command dont exists - ' . $commandString);
            }
            else
            {
                $this->CLog->w('DEBUG', 'Set command done - ' . $commandString);
                return $this->_sendResponse('set', 'Done');
            }
        }
        else
        {
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Set command type wrong - ' . $commandString, 'daemon');
            return $this->_setCommandError('Set command type wrong - ' . $commandString);
        }
    }

    private function _sendResponse($command, $response = '')
    {
        $multiBulk = array($command, $this->index, $this->pid, $this->_socketPort, $response);
        $result = Core_Daemon_Protocol::sendMultiBulk($this->sockChannel, $multiBulk);

        if ($result === false)
        {
            $errorCode = socket_last_error($this->sockChannel);
            
            if ($errorCode === SOCKET_EPIPE)
            {
                //连接已经断开或者无法写入，尝试重新连接
                $this->CLog->logMessage(LOG_LEVEL_ERROR, 'SOCKET_EPIPE, reconnecting', 'daemon');
                $this->_connectToSocket();                    
            }
            else if ($errorCode === SOCKET_EAGAIN)
            {
                //连接已经断开或者无法写入，尝试重新连接
                $this->CLog->logMessage(LOG_LEVEL_ERROR, 'SOCKET_EAGAIN, reconnecting', 'daemon');
                $this->_connectToSocket();                    
            }
        }

        return $result;
    }
    
    private function _setCommandError($errorMessage)
    {
        $this->CLog->logMessage(LOG_LEVEL_ERROR, $errorMessage, 'daemon');
        $this->_sendResponse('set', $errorMessage);
        return false;
    }
}
