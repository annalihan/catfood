<?php
/*
 * 常规一调度进度多工作进程队列。调度进程的主要功能
 * 1、接收信号控制工作进程与自身的退出
 * 2、检查工作进程运行状态
 * 3、控制工作进程启动
 * 4、调度进程以最简化的代码运行，保证了自身能长期稳定的在后台运行
 * 
 * 工作进程必须按一定策略重启
 */
class Core_Daemon_Main extends Core_Daemon_Abstract
{
    public $mainDaemonName;//队列调度名称
    public $mainDaemonPid;//队列调度进程ID
    public $mainDaemonPidFile;//队列调度进程ID文件

    /*Daemon通讯Socket*/
    public $sockFile;//本地Sock文件名
    public $sockChannel;//Sock通讯通道
    private $_checkCommands = array('ping', 'status', 'performance');

    /*本地管理接口*/
    public $host = '127.0.0.1';
    public $port = '1980';
    public $netManage = true;
    public $netManageServer;//管理Sock
    public $netManageClients = array();//连接管理接口的客户端列表

    public $startTime = 0.0;
    public $mainDaemonSleep = 0.001;//队列调度进程循环间隔，单位秒，支持小数点。比如0.001
    public $mainDaemonWorkDir;//队列调度进程工作目录
    public $mainDoFunc = '';//队列调度进程执行函数名
    public $processDaemonStartInterval = 5.0;//队列工作进程重启最小间隔，单位秒,支持小数点，比如0.1
    public $processDaemonCheckInterval = 2.0;//队列工作进程检查最小间隔，单位秒,支持小数点，比如0.1
    public $netCheckInterval = 0.01;//网络数据接收最小间隔，单位秒,支持小数点，比如0.1
    public $processList = array();//队列工作进程列表
    public $clientList = array();//子进程连接Socket的客户端列表
    public $maxWorker = 1;//最大工作进程数
    public $CProcess;//队列子进程对象
    public $CLog;

    public $restartWaitingMax = 60;//重启时最长等待时间

    /*管理中心参数*/
    public $centerManager = false;
    public $centerHost = "172.16.38.128";
    public $centerPort = "10000";
    private $_centerClient = 0;
    private $_centerErrorCount = 0;
    private $_centerErrorMax = 5;

    private $_runHost = '';

    const UNIX_PATH_MAX = 108;
    const COMMAND_FROM_MANAGER = 0;
    const COMMAND_FROM_PROCESS = 1;
    const COMMAND_FROM_CENTER = 2;

    public function __construct($param = array())
    {
        $this->_runHost = Comm_Context::getServerIp();

        //设置默认工作目录
        $this->mainDaemonWorkDir = Comm_Config::get('daemon.dir.run', '/data1/www/run') . DIRECTORY_SEPARATOR;
        
        //参数初始化
        $this->CLog = Tool_Log::getInstance();
        $this->CProcess = new Core_Daemon_Process(isset($param['CProcess']) ? $param['CProcess'] : array());

        //参数初始化
        $this->setParam($param);
        
        //TODO:必选参数检查

        //日志相关
        $this->CLog->setSubTarget($this->mainDaemonName);

        //队列调度进程pid文件名
        $this->mainDaemonPidFile = $this->mainDaemonWorkDir . $this->mainDaemonName . '.pid';
        
        //队列调度进程Sock文件名
        $this->sockFile = $this->mainDaemonWorkDir . $this->mainDaemonName . '.sock';
        if (strlen($this->sockFile) > self::UNIX_PATH_MAX)
        {
            $this->_exitByError('Sock path [' .  $this->sockFile . '] is too long!');
        }

        //队列工作进程初始化
        //$this->CProcess->CQueue = $this->CQueue;
        $this->CProcess->CP = $this;
        $this->CProcess->CLog = $this->CLog;
        $this->CProcess->maxWorker = $this->maxWorker;
        $this->CProcess->mainProcessId = $this->mainDaemonPid;
        $this->CProcess->checkInter = $this->processDaemonCheckInterval + 1;
    }

    private function _exitByError($message, $code = 0)
    {
        $this->CLog->logMessage(LOG_LEVEL_ERROR, $message, 'daemon');
        exit($message);
    }

    private function _createSocketChannel()
    {
        //初始化队列调度进程Sock文件
        if (file_exists($this->sockFile))
        {
            //队列调度进程Sock文件存在
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Main Daemon Sock File Exists', 'daemon');

            unlink($this->sockFile);
        }

        $this->sockChannel = socket_create(AF_UNIX, SOCK_STREAM, 0);

        if (!$this->sockChannel)
        {
            //无法创建Sock
            $this->_exitByError('Main Daemon Can\'t Create Sock');
        }

        if (!socket_bind($this->sockChannel, $this->sockFile))
        {
            //无法创建Sock文件
            $this->_exitByError('Main Daemon Can\'t Create Sock File');
        }
        
        if (!socket_listen($this->sockChannel))
        {
            //无法监听Socket
            $this->_exitByError("Main Daemon Can't Listen Socket");
        }
        
        socket_set_nonblock($this->sockChannel);
    }

    private function _createNetChannel()
    {
        if ($this->netManage === false)
            return false;

        $this->netManageServer = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if (!$this->netManageServer)
        {
            //无法创建Sock
            $this->_exitByError('Main Daemon Can\'t Create Sock Resource');
        }

        socket_set_option($this->netManageServer, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_nonblock($this->netManageServer);
        
        if (!socket_bind($this->netManageServer, $this->host, $this->port))
        {
            //无法创建Sock资源
            $this->_exitByError('Main Daemon Can\'t Bind Sock Resource');
        }

        // 当port为0时，从新获取
        if ($this->port == 0)
        {
            socket_getsockname($this->netManageServer, $this->host, $this->port);
        }
        
        if (!socket_listen($this->netManageServer))
        {
            //无法监听Socket
            $this->_exitByError('Main Daemon Can\'t Listen Socket Resouce');
        }
    }

    private function _closeNetChannel()
    {
        //关闭服务器
        if (is_resource($this->netManageServer))
            socket_close($this->netManageServer);

        //重置客户端列表
        $this->netManageClients = array();
    }

    private function _connectCenter($reconnect = false)
    {
        if ($this->centerManager === false)
        {
            return false;
        }

        if (is_resource($this->_centerClient))
        {
            socket_close($this->_centerClient);
        }

        if ($reconnect)
        {
            $currentTime = time();

            if (isset($this->_lastCenterConnectTime) === false)
            {
                $this->_centerConnectInterval = 10;
                $this->_lastCenterConnectTime = 0;
            }

            if ($currentTime - $this->_lastCenterConnectTime < $this->_centerConnectInterval)
            {
                return false;
            }

            $this->_lastCenterConnectTime = $currentTime;
        }

        $this->_centerClient = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->_centerClient == false)
        {
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Unable to create AF_INET socket', 'daemon');
            return false;
        }

        if (@socket_connect($this->_centerClient, $this->centerHost, $this->centerPort) === false)
        {
            $this->_centerClient = false;
            $this->CLog->logMessage(LOG_LEVEL_ERROR, "Cannt connect to {$this->centerHost}:{$this->centerPort}", 'daemon');
            return false;
        }

        socket_set_nonblock($this->_centerClient);
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Daemon center connected.', 'daemon');

        $multiBulk = array('type', 'center', 'daemon');
        Core_Daemon_Protocol::sendMultiBulk($this->_centerClient, $multiBulk);
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Send type to center.', 'daemon');
    }
    
    /**
     * 启动队列
     * @return [type] [description]
     */
    public function startDaemon()
    {
        Tool_Process::runInBackground();
        
        //信号处理
        declare(ticks = 1);

        //忽略HLD信号，避免子进程僵死
        pcntl_signal(SIGCHLD, SIG_IGN);
        pcntl_signal(SIGCLD, SIG_IGN);
        pcntl_signal(SIGPIPE, SIG_IGN);
        
        //初始化工作目录
        if (getcwd() != $this->mainDaemonWorkDir)
            chdir($this->mainDaemonWorkDir);
        
        //初始化队列调度进程pid文件            
        if (file_exists($this->mainDaemonPidFile))
        {
            //队列调度进程pid文件存在
            $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Main Daemon Pid File Exists', 'daemon');

            //获取之前队列调度进程pid
            $existsPid = file_get_contents($this->mainDaemonPidFile);

            //获取之前队列调度进程信息            
            $queueDaemonStatus = Tool_Process::getStatus($existsPid);
            
            if ($queueDaemonStatus === false)
            {
                //之前队列调度进程异常退出
                $this->CLog->logMessage(LOG_LEVEL_ERROR, 'Main Daemon Pid ' . $existsPid . ' Has Gone', 'daemon');
                unlink($this->mainDaemonPidFile);
            }
            else
            {
                //队列调度进程还存在，取消运行
                $this->_exitByError('Same Main Daemon Process[' . $existsPid . '] Exists. Quit');
            }
        }
        
        //获取队列调度进程pid
        $this->mainDaemonPid = posix_getpid();

        //主进程使用第二CPU
        $tasksetCommand = "taskset -cp 1-20 {$this->mainDaemonPid}";
        $tasksetResult = shell_exec($tasksetCommand);
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Set process cpu - {$tasksetResult}", 'daemon');
        
        //由于队列主进程通过手工方式启动，所以先不考虑文件锁
        file_put_contents($this->mainDaemonPidFile, $this->mainDaemonPid);
        
        //创建socket通道
        $this->_createSocketChannel();

        $this->startTime = microtime(true);
        
        //开始创建子进程
        for ($i = 0; $i < $this->maxWorker; $i++)
        {
            //创建工作进程
            $fockPid = pcntl_fork();
            
            //失败退出
            if ($fockPid == -1)
            {
                $this->_exitByError("could not fork");
            }
            
            if ($fockPid)
            {
                $this->processList[$i] = new Core_Daemon_ProcessInfo(array('pid' => $fockPid, 'startTime' => microtime(true), 'lastActiveTime' => 0));
                $this->processList[$i]->running = true;
            }
            else
            {
                socket_close($this->sockChannel);

                // 设定子进程CPU使用
                $processPid = posix_getpid();
                $tasksetCommand = "taskset -cp 2-20 {$processPid}";
                $tasksetResult = shell_exec($tasksetCommand);
                $this->CLog->logMessage(LOG_LEVEL_INFO, "Set process cpu - {$tasksetResult}", 'daemon');

                //子进程参数
                $this->CProcess->index = $i;
                
                //启动工作进程主程序
                $this->CProcess->startDaemon();
            }
        }

        //启动调度进程主程序
        $this->startMainDaemon();
    }
    
    //单独启动队列子进程
    public function startProcessDaemon($index)
    {
        //过滤临时启动的进程
        if ($this->processList[$index]->isTemp || $this->processList[$index]->closed)
        {
            unset($this->processList[$index]);
            return true;
        }

        $currentMicroTime = microtime(true);

        //避免频繁启动子进程
        if ($this->processDaemonStartInterval > 0)
        {
            if (($currentMicroTime - $this->processList[$index]->startTime) < $this->processDaemonStartInterval)
            {
                //队列子进程启动频繁过高
                $this->CLog->logMessage(LOG_LEVEL_ERROR, 'No:' . $index . ' sub queue start interval too high', 'daemon');
                return false;
            }
        }
        
        //上次运行状态日志
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Worker Last Info No:' . $index . ' Pid:' . $this->processList[$index]->pid . ' StartTime[' . date('Y-m-d H:i:s', $this->processList[$index]->startTime) . '] ' . ' LastActiveTime[' . date('Y-m-d H:i:s', floor($this->processList[$index]->lastActiveTime)) . '] ' . $this->processList[$index]->lastActiveTime, 'daemon');
        
        //设置队列子进程最后启动时间
        $this->processList[$index]->startTime = $currentMicroTime;
        $this->processList[$index]->lastActiveTime = $this->processList[$index]->startTime;
        
        //创建队列子进程
        $fockPid = pcntl_fork();

        //失败退出
        if ($fockPid == -1)
        {
            $this->_exitByError("could not fork");
        }
        
        if ($fockPid)
        {
            //主进程继续执行
            $this->processList[$index]->pid = $fockPid;
            $this->processList[$index]->running = true;
        }
        else
        {
            socket_close($this->sockChannel);

            //启动队列子进程主程序
            if (isset($this->processList[$index]->lastStatus['queueStatusTotalCpuU']))
            {
                $this->processList[$index]->lastStatus['queueStatusTotalCpuU'] += $this->processList[$index]->lastStatus['queueStatusCpuU'];
                $this->processList[$index]->lastStatus['queueStatusTotalCpuS'] += $this->processList[$index]->lastStatus['queueStatusCpuS'];
            }

            if (count($this->processList[$index]->lastStatus) > 0)
            {
                foreach ($this->processList[$index]->lastStatus as $key => $value)
                {
                    $this->CProcess->$key = $value;
                }
            }

            $this->CProcess->index = $index;
            //$this->CProcess->resourceConfig = $this->processList[$index]->resourceConfig;

            //$this->CLog->logMessage(LOG_LEVEL_INFO, 'Config : ' . json_encode($this->CProcess->resourceConfig), 'daemon');

            $this->CProcess->startDaemon();
        }
    }

    /**
     * 单独启动队列子进程(临时进程)
     * @param  [type] $ext   [description]
     * @return [type]        [description]
     */
    public function startTempProcessDaemon($ext = array())
    {
        $this->processList[] = clone $this->processList[0];
        $index = max(array_keys($this->processList));

        //创建队列子进程
        $fockPid = pcntl_fork();

        //失败退出
        if ($fockPid == -1)
        {
            $this->_exitByError("could not fork");
        }
        
        if ($fockPid)
        {
            //主进程继续执行
            $this->processList[$index]->pid = $fockPid;
            $this->processList[$index]->running = true;
            $this->processList[$index]->isTemp = true;
        }
        else
        {
            socket_close($this->sockChannel);

            $this->CProcess->ext = $ext;
            $this->CProcess->index = $index;
            //$this->CProcess->resourceConfig = $this->processList[0]->resourceConfig;
            $this->CProcess->startDaemon();
        }
    }
    
    //队列退出
    public function mainDaemonQuit($signo = '')
    {
        foreach ($this->clientList as $processClient)
        {
            $this->_sendCommandToProcess($processClient['client'], 'quit');
        }

        //删除队列主进程pid文件
        unlink($this->mainDaemonPidFile);
        
        //关闭进程Socket
        if (is_resource($this->sockChannel))
        {
            socket_close($this->sockChannel);
        }
        
        //关闭管理socket
        if (is_resource($this->netManageServer))
        {
            socket_close($this->netManageServer);
        }
        
        //删除Socket File
        unlink($this->sockFile);
        
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Send Quit Status To Queue Daemon', 'daemon');
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Main Daemon Quit', 'daemon');

        exit;
    }
    
    //队列主进程主程序
    public function startMainDaemon()
    {
        //注册信号句柄
        pcntl_signal(SIGTERM, array($this, 'mainDaemonQuit'));
        pcntl_signal(SIGHUP, array($this, 'mainDaemonQuit'));
        
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Main Daemon Start', 'daemon');
        
        //创建管理接口
        $this->_createNetChannel();

        //连接管理中心
        $this->_connectCenter();
        
        $time = microtime(true);
        $lastCheckProcessTime = $time;
        $lastCheckNetTime = $time;
        
        //主循环体
        while (true)
        {
            $now = microtime(true);

            // 检查状态
            if (($now - $lastCheckNetTime) > $this->netCheckInterval)
            {
                $lastCheckNetTime = $now;

                //处理管理指令
                $this->_checkManagerCommand();

                //处理中心指令
                $this->_checkCenterCommand();
            }

            // 检查进程
            if (($now - $lastCheckProcessTime) > $this->processDaemonCheckInterval)
            {
                $lastCheckProcessTime = $now;
                
                //发送指令到子进程
                $this->_checkProcessCommand();

                //检查队列子进程状态
                $missingWorkerList = $this->_checkProcessDaemonStatus();

                if (empty($missingWorkerList) === false)
                {
                    foreach ($missingWorkerList as $index)
                    {
                        $this->startProcessDaemon($index);    
                    }
                }
            }

            //调用自定义函数
            if ($this->mainDoFunc !== '')
                call_user_func($this->mainDoFunc, $this);

            // sleep
            usleep($this->mainDaemonSleep * 1000000);
        }
    }

    /**
     * 从服务端口获取数据和请求
     * @param  [type]  $socket     [description]
     * @param  [type]  $clientList [description]
     * @param  integer $from       [description]
     * @return [type]              [description]
     */
    private function _receiveDataFromSocket($socket, &$clientList, $from = 0)
    {
        // 新连接
        $newClient = @socket_accept($socket);
        $deleteList = array();

        if ($newClient)
        {
            //非阻塞模式
            socket_set_nonblock($newClient);
            socket_getsockname($newClient, $ip, $port);
            $clientList[] = array(
                'client' => $newClient,
                'ip' => $ip,
                'port' => $port,
            );
        }

        foreach ($clientList as $key => $clientInfo)
        {
            $client = $clientInfo['client'];
            $responseValue = Core_Daemon_Protocol::read($client);

            if (empty($responseValue))
            {
                if (is_resource($client))
                {
                    $errorCode = socket_last_error($client);
                    
                    //确实错误
                    if ($errorCode != SOCKET_EAGAIN)
                    {
                        $deleteList[] = $key;
                    }
                }
                else
                {
                    $deleteList[] = $key;
                }
            }
            else
            {
                //处理指令
                $this->_responseCommand($client, $responseValue, $from);
            }
        }
        
        foreach ($deleteList as $key)
        {
            //先关闭
            if (is_resource($clientList[$key]))
            {
                socket_close($clientList[$key]);
            }
            
            //再删除
            unset($clientList[$key]);
        }

        return true;
    }

    /**
     * 检查队列子进程状态
     * @return [type] [description]
     */
    private function _checkProcessDaemonStatus()
    {
        //默认状态
        $missingWorkerList = array();
        
        foreach ($this->processList as $index => $processObject)
        {
            $pid = $processObject->pid;

            //TODO:检查子进程心跳，对于没有心跳或者心跳迟迟未到的处理

            //获取进程信息
            $queueDaemonStatus = Tool_Process::getStatus($pid);
            
            if ($queueDaemonStatus === false)
            {
                $this->CLog->logMessage(LOG_LEVEL_INFO, 'Worker Gone No:' . $index . ' Pid:' . $pid . ' StartTime[' . date('Y-m-d H:i:s', floor($processObject->startTime)) . '] ' . ' LastActiveTime[' . date('Y-m-d H:i:s', floor($processObject->lastActiveTime)) . '] ' . $processObject->lastActiveTime, 'daemon');

                if ($processObject->isTemp || $processObject->closed)
                {
                    unset($this->processList[$index]);
                }
                else
                {
                    $missingWorkerList[] = $index;    
                }
            }
            else
            {
                //如果队列工作进程父进程pid与队列主进程不一致，说明不是由队列主进程启动的，是另外的进程
                if (intval($queueDaemonStatus['PPid']) != $this->mainDaemonPid)
                {
                    $this->CLog->logMessage(LOG_LEVEL_INFO, 'Worker Gone PPid Not Match No:' . $index . ' Pid:' . $pid . ' StartTime[' . date('Y-m-d H:i:s', floor($processObject->startTime)) . '] ' . ' LastActiveTime[' . date('Y-m-d H:i:s', floor($processObject->lastActiveTime)) . '] ' . $processObject->lastActiveTime, 'daemon');

                    if ($processObject->isTemp || $processObject->closed)
                    {
                        unset($this->processList[$index]);
                    }
                    else
                    {
                        $missingWorkerList[] = $index;    
                    }
                }
            }
        }

        return $missingWorkerList;
    }

    private function _checkProcessCommand()
    {
        // 新连接
        $newClient = @socket_accept($this->sockChannel);

        if ($newClient)
        {
            //非阻塞模式
            socket_set_nonblock($newClient);
            socket_getsockname($newClient, $ip, $port);
            $this->clientList[] = array(
                'client' => $newClient,
                'ip' => $ip,
                'port' => $port,
            );
        }

        //发送ping指令
        foreach ($this->clientList as $key => $clientInfo)
        {
            $client = $clientInfo['client'];

            foreach ($this->_checkCommands as $command)
            {
                if ($this->_sendCommandToProcess($client, $command) === false)
                {
                    unset($this->clientList[$key]);
                    continue 2;
                }
            }
        }
    }

    private function _checkManagerCommand()
    {
        if ($this->netManage == false)
            return false;

        return $this->_receiveDataFromSocket($this->netManageServer, $this->netManageClients, self::COMMAND_FROM_MANAGER);
    }

    private function _checkCenterCommand()
    {
        if ($this->centerManager == false)
            return false;

        if (is_resource($this->_centerClient) === false)
        {
            $this->_connectCenter(true);
            
            return false;
        }

        $responseValue = Core_Daemon_Protocol::read($this->_centerClient);

        if (empty($responseValue) === false)
        {
            $this->_centerErrorCount = 0;
            $this->_responseCommand($this->_centerClient, $responseValue, self::COMMAND_FROM_CENTER);
        }
        else
        {
            $errorCode = socket_last_error($this->_centerClient);

            if ($errorCode === SOCKET_EAGAIN)
            {
                //EAGAIN次数超过每个周期5次以后重新连接服务器
                if ($this->_centerErrorCount++ > $this->_centerErrorMax / $this->netCheckInterval)
                {
                    $this->CLog->logMessage(LOG_LEVEL_INFO, "Center connection is disconnected - {$this->_centerErrorCount} - reconnecting", 'daemon');
                    $this->_centerErrorCount = 0;
                    $this->_connectCenter(true);
                }
            }

            //重新连接   
            if ($errorCode === SOCKET_ECONNRESET || 
                $errorCode === SOCKET_EPIPE || 
                $errorCode === SOCKET_ETIMEDOUT || 
                $errorCode === SOCKET_ECONNREFUSED)
            {
                $this->CLog->logMessage(LOG_LEVEL_ERROR, "Center connection is broken - {$errorCode} - reconnecting", 'daemon');
                $this->_connectCenter(true);
            }
        }

        return true;
    }

    private function _sendCommandToProcess($client, $command, $arguments = array())
    {
        //$status = stream_get_meta_data($client);
        $write = Core_Daemon_Protocol::write($client, $command, $arguments);
        
        if ($write === false)
        {
            return false;
        }

        $responseValue = Core_Daemon_Protocol::read($client, false);

        if (empty($responseValue) === false)
        {
            $this->_responseCommand($client, $responseValue, self::COMMAND_FROM_PROCESS);
        }

        return true;
    }

    private function _responseCommand($client, $responseValue, $from = 0)
    {
        if (is_array($responseValue))
        {
            $command = array_shift($responseValue);
            $arguments = $responseValue;
        }
        else
        {
            $command = $responseValue;
            $arguments = array();
        }

        switch ($from)
        {
            case self::COMMAND_FROM_PROCESS:
                $commandHandle = "_{$command}ProcessResponseHandle";
                break;

            case self::COMMAND_FROM_MANAGER:
                $commandHandle = "_{$command}ManagerCommandHandle";
                break;

            case self::COMMAND_FROM_CENTER:
                $commandHandle = "_{$command}ManagerCommandHandle";
                $commandId = array_pop($arguments);
                break;
            
            default:
                Core_Daemon_Protocol::sendLine($client, 'Wrong client.');
                break;
        }

        if (method_exists($this, $commandHandle))
        {
            if ($from == self::COMMAND_FROM_CENTER)
            {
                return call_user_func_array(array($this, $commandHandle), array($client, $arguments, $from, $commandId));
            }
            else
            {
                return call_user_func_array(array($this, $commandHandle), array($client, $arguments, $from));
            }
        }
        else
        {
            if ($from == self::COMMAND_FROM_MANAGER || $from == self::COMMAND_FROM_CENTER)
            {
                Core_Daemon_Protocol::sendError($client, "ERR unknown command '{$command}'");
            }

            return false;
        }
    }

    private function _updateChildActiveTime($childIndex)
    {
        $this->processList[$childIndex]->lastActiveTime = microtime(true);
    }

    private function _pingProcessResponseHandle($client, $arguments = array())
    {
        $this->_updateChildActiveTime($arguments[0]);
        return true;
    }

    private function _restartProcessResponseHandle($client, $arguments = array())
    {
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Process restart by '{$arguments[3]}'.", 'daemon');

        //子进程重启消息
        //$this->processList[$arguments[0]]->lastActiveTime = microtime(true);
        $socketPort = $arguments[2];

        foreach ($this->clientList as $key => $clientInfo)
        {
            if ($clientInfo['port'] == $socketPort)
            {
                $this->CLog->logMessage(LOG_LEVEL_DEBUG, "Remove process client '{$socketPort}'.", 'daemon');
                unset($this->clientList[$key]);
                break;
            }
        }

        return true;
    }

    private function _statusProcessResponseHandle($client, $arguments = array())
    {
        $childIndex = $arguments[0];
        $this->_updateChildActiveTime($childIndex);
        $data = json_decode($arguments[3], true);

        if (is_array($data) === false)
            return true;

        foreach ($data as $key => $value)
        {
            if (strpos($key, 'queueStatus') === 0)
            {
                $this->processList[$childIndex]->lastStatus[$key] = $value;
            }
        }          

        return true;
    }

    private function _performanceProcessResponseHandle($client, $arguments = array())
    {
        $childIndex = $arguments[0];
        $this->_updateChildActiveTime($childIndex);
        $data = json_decode($arguments[3], true);

        if (is_array($data) === false)
            return true;

        $this->processList[$childIndex]->performance = $data;
        return true;
    }

    private function _shutdownManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Receive Shutdown Command.', 'daemon');

        $this->_sendResponseToManager($client, 'shutdown', 'OK', $from, $commandId);

        $this->mainDaemonQuit();
        return true;
    }

    private function _quitManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->_sendResponseToManager($client, 'quit', 'OK', $from, $commandId);

        usleep(100000);
        socket_close($client);
        return true;
    }

    private function _debugManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->_sendResponseToManager($client, 'debug', date('r'), $from, $commandId);

        return true;
    }

    private function _increaseManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->processList[] = new Daemon_Queue_Cluster_Base_Process_Stru(array('pid' => 0, 'startTime' => 0, 'lastActiveTime' => 0));
        
        $this->_sendResponseToManager($client, 'increase', 'OK', $from, $commandId);

        return true;
    }

    private function _decreaseManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->maxWorker--;
        $max = max(array_keys($this->processList));

        for ($i = $max; $i >= 0; $i--)
        {
            if ($this->processList[$i]->isTemp === false)
            {
                $this->CLog->logMessage(LOG_LEVEL_INFO, 'Decrease process .', 'daemon');
                $this->processList[$i]->closed = true;
                break;
            }
        }

        $this->_sendResponseToManager($client, 'decrease', 'OK', $from, $commandId);

        return true;
    }

    private function _ctempManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->startTempProcessDaemon();

        $this->_sendResponseToManager($client, 'ctemp', 'OK', $from, $commandId);

        return true;
    }

    private function _infoManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $format = isset($arguments[0]) ? $arguments[0] : 'string';
        if ($format === 'json')
        {
            $status = $this->_getStatus(false);
            $status = Tool_Json::encode($status);
        }
        else
        {
            $status = $this->_getStatus(true);    
        }

        $this->_sendResponseToManager($client, 'info', $status, $from, $commandId);

        return true;
    }

    private function _runManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Receive Run Command : ' . json_encode($arguments), 'daemon');

        //合并
        $command = implode(' ', $arguments);

        //执行
        $output = array();
        $result = exec($command, $output);

        //指令返回
        $this->_sendResponseToManager($client, 'run', $output, $from, $commandId);

        return true;
    }

    private function _setManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        //TODO
        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Receive Set Command : ' . json_encode($arguments), 'daemon');

        if (count($arguments) < 4)
        {
            Core_Daemon_Protocol::sendError($client, 'ERR wrong number of arguments for \'set\' command, Usage:set [type] [name] [field] [value], Sample: set performance all switch 1');
            return true;
        }

        //合并
        $oldConfig = $this->processList[0]->resourceConfig;
        $oldConfig[$arguments[0]][$arguments[1]][$arguments[2]] = $arguments[3];

        //保存
        foreach ($this->processList as $index => $processObject)
        {
            $processObject->resourceConfig = $oldConfig;
        }

        //发送指令
        foreach ($this->clientList as $processClient)
        {
            $this->_sendCommandToProcess($processClient['client'], 'set', $arguments);
        }

        //TODO 如果是性能分析参数，主进程也需要做处理

        //指令返回
        $this->_sendResponseToManager($client, 'set', 'OK', $from, $commandId);

        return true;
    }

    private function _restartManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        //关闭所有子进程
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Restart - Closing process", 'daemon');
        foreach ($this->clientList as $processClient)
        {
            $this->_sendCommandToProcess($processClient['client'], 'quit');
        }

        //等待关闭子进程
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Restart - Waitting process", 'daemon');
        $count = 0;
        $running = false;

        while ($running)
        {
            $running = false;
            $count++;

            foreach ($this->processList as $index => $processObject)
            {
                if ($processObject->running === false)
                    continue;

                $processStatus = Tool_Process::getStatus($processObject->pid);
                
                if ($processStatus === false)
                {
                    $processObject->running = false;
                }
                else
                {
                    $running = true;
                    break;
                }
            }

            if ($count > $this->restartWaitingMax)
            {
                foreach ($this->processList as $index => $processObject)
                {
                    if ($processObject->running)
                    {
                        $this->CLog->logMessage(LOG_LEVEL_INFO, 'Restart - Killing process - ' . $processObject->pid, 'daemon');
                        Tool_Process::killProcess($processObject->pid);
                    }
                }

                break;
            }

            sleep(1);
        }

        //TODO
        //重新加载资源
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Restart - Reloading resource", 'daemon');

        //重新启动子进程
        $this->CLog->logMessage(LOG_LEVEL_INFO, "Restart - Starting process", 'daemon');
        foreach ($this->processList as $index => $processObject)
        {
            $this->startProcessDaemon($index);
        }

        $this->CLog->logMessage(LOG_LEVEL_INFO, "Restart - Done", 'daemon');

        //指令返回
        $this->_sendResponseToManager($client, 'restart', 'OK', $from, $commandId);

        return true;
    }

    private function _getManagerCommandHandle($client, $arguments = array(), $from = 0, $commandId = 0)
    {
        $count = count($arguments);
        if ($count < 2)
        {
            Core_Daemon_Protocol::sendError($client, 'ERR wrong number of arguments for \'get\' command, Usage:get [type] [output] [detail] [format]');
            return true;
        }

        //获取类型
        $type = $arguments[0];

        //输出类型
        $output = $arguments[1];
        $value = '';

        //输出详细
        $contentType = isset($arguments[2]) ? $arguments[2] : 'simple';
        $format = isset($arguments[3]) ? $arguments[3] : 'string';

        //获取结构
        switch (true)
        {
            case $type == 'performance':
                $value = $this->_getPerformance($format, $contentType);
                break;

            case $type == 'debug':
                $value = date('r');
                break;

            case $type == 'config':
                $value = $this->_getConfig();
                break;

            default:
                Core_Daemon_Protocol::sendError($client, "ERR wrong get type '{$type}' command");
                return true;
        }

        //输出
        if (strpos($output, ':') !== false)
        {
            $pos = strpos($output, ':');
            $target = substr($output, $pos + 1);
            $output = substr($output, 0, $pos);
        }

        switch (true)
        {
            case $output == 'file':
                file_put_contents($target, $value);
                $this->_sendResponseToManager($client, 'get', 'OK', $from, $commandId);
                break;

            case $output == 'http':
                HttpHelper::post($target, $value);
                $this->_sendResponseToManager($client, 'get', 'OK', $from, $commandId);
                break;

            case $output == 'print':
            case $output == 'echo':
                echo $value;
                $this->_sendResponseToManager($client, 'get', $value, $from, $commandId);
                break;

            default:
                $this->_sendResponseToManager($client, 'get', $value, $from, $commandId);
                break;
        }

        return true;
    }

    private function _sendResponseToManager($client, $command, $response, $from, $commandId = 0)
    {
        if ($from == self::COMMAND_FROM_CENTER)
        {
            $this->_sendResponseToCenter($client, $command, $response, $commandId);
        }
        else
        {
            if (is_array($response))
            {
                Core_Daemon_Protocol::sendMultiBulk($client, $response);
            }
            else
            {
                Core_Daemon_Protocol::sendBulk($client, $response);
            }
        }
    }

    private function _sendResponseToCenter($client, $command, $response, $commandId)
    {
        if (is_array($response))
        {
            $response = json_encode($response);
        }

        $multiBulk = array('response', 'center', $command, $commandId, $response);
        Core_Daemon_Protocol::sendMultiBulk($client, $multiBulk);
        return true;
    }

    private function _getStatus($string = false)
    {
        $totalCount = 0;
        $totalTime = 0.0;
        $totalQPS = 0.0;
        $totalMemory = 0;
        $totalCpuU = 0;
        $totalCpuS = 0;
                                
        foreach ($this->processList as $childInfo)
        {
            $totalCount += $childInfo->lastStatus['queueStatusTotalCount'];
            $totalTime += $childInfo->lastStatus['queueStatusTotalTimeCount'];
            $totalMemory += $childInfo->lastStatus['queueStatusMemory'];
            $totalCpuU += $childInfo->lastStatus['queueStatusCpuU'] + $childInfo->lastStatus['queueStatusTotalCpuU'];
            $totalCpuS += $childInfo->lastStatus['queueStatusCpuS'] + $childInfo->lastStatus['queueStatusTotalCpuS'];
        }
        
        $totalQPS = $totalTime ? $totalCount / $totalTime : 0;
        
        $status = array(
            'host_name' => $this->_runHost,
            'host' => $this->host,
            'port' => $this->port,
            'daemon_name' => $this->mainDaemonName,
            'start_time' => date('Y-m-d H:i:s', floor($this->startTime)),
            'max_worker' => $this->maxWorker,
            'total_count' => $totalCount,
            'total_time' => $totalTime,
            'total_qps' => $totalQPS,
            'total_memory' => $totalMemory,
            'total_cpu_u' => $totalCpuU,
            'total_cpu_s' => $totalCpuS,
            'total_worker_client' => count($this->clientList),
            'total_manage_client' => count($this->netManageClients),
        );

        if ($string)
        {
            $output = "";

            foreach ($status as $key => $value)
            {
                $output .= "{$key}:{$value}\r\n";
            }

            return $output;
        }

        return $status;
    }

    private function _getConfig()
    {
        $output = "";

        foreach ($this->processList[0]->resourceConfig as $type => $names)
        {
            foreach ($names as $name => $fields)
            {
                foreach ($fields as $field => $value)
                {
                    $output .= "{$type}->{$name}->{$field}:{$value}\r\n";
                }
            }
        }

        return $output;
    }

    private function _getProcessPerformanceFull(&$performanceValues, $performance)
    {
        $this->_getProcessPerformanceSimple($performanceValues, $performance);

        //HTTP
        if (isset($performanceValues['http']['detail']) === false)
        {
            $performanceValues['http']['detail'] = array();
        }

        $httpPerformance = $performance['http']['detail'];

        foreach ($httpPerformance as $host => $hostInfo)
        {
            if (isset($performanceValues['http']['detail'][$host]) === false)
            {
                $performanceValues['http']['detail'][$host] = array();
            }

            foreach ($hostInfo as $path => $pathInfo)
            {
                if (isset($performanceValues['http']['detail'][$host][$path]) === false)
                {
                    $performanceValues['http']['detail'][$host][$path] = array(
                        'cc' => 0,
                        'fc' => 0,
                        'we' => 0,
                        'ce' => 0,
                        're' => 0,
                        'tl' => 0,
                        'rps' => 0,
                        'tpr' => 0,
                        'min' => 0,
                        'max' => 0,
                    );
                }

                if (isset($pathInfo['cc']) === false)
                {
                    continue;
                }

                $pathValue = $performanceValues['http']['detail'][$host][$path];

                $pathValue['cc'] += $pathInfo['cc'];
                $pathValue['fc'] += $pathInfo['fc'];
                $pathValue['we'] += $pathInfo['we'];
                $pathValue['ce'] += $pathInfo['ce'];
                $pathValue['re'] += $pathInfo['re'];
                $pathValue['tl'] += $pathInfo['tl'];
                $pathValue['rps'] = round($pathValue['cc'] / $pathValue['tl'], 2);
                $pathValue['tpr'] = round($pathValue['tl'] / $pathValue['cc'], 2);
                
                if ($pathValue['min'] === 0)
                    $pathValue['min'] = $pathInfo['min'];

                $pathValue['min'] = min($pathInfo['min'], $pathValue['min']);
                $pathValue['max'] = max($pathInfo['max'], $pathValue['max']);

                $performanceValues['http']['detail'][$host][$path] = $pathValue;
            }
        }

        //其他类型
        $types = array('queue', 'cache', 'db');

        foreach ($types as $type)
        {
            if (isset($performance[$type]['detail']) === false)
            {
                continue;
            }

            if (isset($performanceValues[$type]['detail']) === false)
            {
                $performanceValues[$type]['detail'] = array();
            }

            foreach ($performance[$type]['detail'] as $resourceName => $resourceValue)
            {
                if (isset($performanceValues[$type]['detail'][$resourceName]) === false)
                {
                    $performanceValues[$type]['detail'][$resourceName] = array();
                }

                foreach ($resourceValue as $name => $value)
                {
                    if (isset($performanceValues[$type]['detail'][$resourceName][$name]) === false)
                    {
                        $performanceValues[$type]['detail'][$resourceName][$name] = array(
                            'c' => 0,
                            't' => 0,
                            'qps' => 0,
                            'tqps' => 0,
                            'min' => 0,
                            'max' => 0,
                        );
                    }

                    if (isset($value['c']) === false)
                    {
                        continue;
                    }

                    $nameValue = $performanceValues[$type]['detail'][$resourceName][$name];
                    
                    $nameValue['c'] += $value['c'];
                    $nameValue['t'] += $value['t'];
                    $nameValue['qps'] = round($nameValue['c'] / $nameValue['t'], 2);
                    $nameValue['tqps'] += round($value['c'] / $value['t'], 3);

                    if ($nameValue['min'] === 0)
                        $nameValue['min'] = $value['min'];

                    $nameValue['min'] = min($value['min'], $nameValue['min']);
                    $nameValue['max'] = max($value['max'], $nameValue['max']);

                    $performanceValues[$type]['detail'][$resourceName][$name] = $nameValue;
                }
            }
        }
    }

    private function _getProcessPerformanceSimple(&$performanceValues, $performance)
    {
        //http
        if (isset($performanceValues['http']['all']['cc']) === false)
        {
            $performanceValues['http']['all'] = array(
                'cc' => 0,
                'fc' => 0,
                'we' => 0,
                'ce' => 0,
                're' => 0,
                'tl' => 0,
                'rps' => 0,
                'tpq' => 0,
                'min' => 0,
                'max' => 0,
            );
        }

        if (isset($performance['http']['all']))
        {
            $httpValue = $performance['http']['all'];

            if (isset($httpValue['cc']))
            {
                $performanceValue = $performanceValues['http']['all'];

                $performanceValue['cc'] += $httpValue['cc'];
                $performanceValue['fc'] += $httpValue['fc'];
                $performanceValue['we'] += $httpValue['we'];
                $performanceValue['ce'] += $httpValue['ce'];
                $performanceValue['re'] += $httpValue['re'];
                $performanceValue['tl'] += $httpValue['tl'];
                $performanceValue['rps'] = round($performanceValue['cc'] / $performanceValue['tl'], 2);
                $performanceValue['tpr'] = round(1000 * $performanceValue['tl'] / $performanceValue['cc'], 2);

                if ($performanceValue['min'] === 0)
                    $performanceValue['min'] = $httpValue['min'];

                $performanceValue['min'] = min($performanceValue['min'], $httpValue['min']);
                $performanceValue['max'] = max($performanceValue['max'], $httpValue['max']);

                $performanceValues['http']['all'] = $performanceValue;
            }
        }

        $types = array('queue', 'cache', 'db');

        foreach ($types as $type)
        {
            if (isset($performanceValues[$type]['all']['cc']) === false)
            {
                $performanceValues[$type]['all'] = array(
                    'c' => 0,
                    't' => 0,
                    'qps' => 0,
                    'tqps' => 0,
                    'min' => 0,
                    'max' => 0,
                );
            }

            $performanceValue = $performanceValues[$type]['all'];

            if (isset($performance[$type]['all']['c']) === false)
            {
                continue;
            }

            $queueValue = $performance[$type]['all'];

            $performanceValue['c'] += $queueValue['c'];
            $performanceValue['t'] += $queueValue['t'];
            $performanceValue['qps'] = round($performanceValue['c'] / $performanceValue['t'], 2);
            $performanceValue['tqps'] += round($queueValue['c'] / $queueValue['t'], 3);

            if ($performanceValue['min'] === 0)
                $performanceValue['min'] = $queueValue['min'];

            $performanceValue['min'] = min($performanceValue['min'], $queueValue['min']);
            $performanceValue['max'] = max($performanceValue['max'], $queueValue['max']);

            $performanceValues[$type]['all'] = $performanceValue;
        }
    }

    private function _getPerformance($format = 'string', $contentType = 'simple')
    {
        $performanceValues = array(
            'http' => array(),
            'cache' => array(),
            'queue' => array(),
            'db' => array(),
        );

        foreach ($this->processList as $processIndex => $childInfo)
        {
            $performance = $childInfo->performance;

            if (isset($performance['from']))
            {
                $from = $performance['from'];

                switch (true)
                {
                    case $from === 'file':
                        $file = $this->mainDaemonWorkDir . $this->mainDaemonName . '_' . $processIndex . '.per';

                        if (file_exists($file))
                        {
                            $performance = json_decode(Io_Fs_File_Base::get($file), true);
                        }

                        break;
                    
                    default:
                        $performance = array();
                        break;
                }
            }

            if ($contentType === 'full')
            {
                $this->_getProcessPerformanceFull($performanceValues, $performance);
            }
            else
            {
                $this->_getProcessPerformanceSimple($performanceValues, $performance);
            }
        }

        switch (true)
        {
            case $format == 'string':
                return $contentType === 'full' ? $this->prj->getPerformanceStringFull($performanceValues) : $this->prj->getPerformanceStringSimple($performanceValues);
            
            case $format == 'json':
                return json_encode($performanceValues);

            case $format == 'array':
                return $performanceValues;

            default:
                return $performanceValues;
        }
    }
}
