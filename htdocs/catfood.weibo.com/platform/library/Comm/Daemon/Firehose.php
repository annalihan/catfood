<?php
    class Comm_Daemon_Firehose
    {
        const APP_ID = 'iospush';

        private $_server = null;
        private $_servers = null;
        private $_host = '';
        private $_port = 0;
        private $_type = '';
        private $_event = 'add';
        private $_lastId = 0;
        private $_maxLastId = 0;
        private $_maxLastIdDataFile = '';
        private $_connectTimeOut = 3;
        private $_connectHandler = 0;
        private $_connectFailedCount = 0;
        private $_connectMessageMaxCount = 100000;
        private $_connectMessageCount = 0;
        private $_allMessageCount = 0;
        private $_allMessageSize = 0;
        private $_chunkSize = 0;
        private $_lastWriteTime = 0;

        //分片
        private $_split = 1;
        private $_spart = 0;

        /**
         * 长连接超时时间，2秒
         * @var integer
         */
        private $_streamTimeOutSec = 2;
        private $_streamTimeOutUSec = 0;

        /**
         * 创建Firehose
         * @param string  $type       数据类型,status\comment\direct_message\user\like...
         * @param string  $event      动作类型,add\new\...
         * @param integer $split      分片数
         * @param boolean $refresh    是否从头开始读取
         */
        public function __construct($type, $event, $split = 1, $refresh = false)
        {
            $this->_type = $type;
            $this->_event = $event;
            $this->_maxLastIdDataFile = Comm_Config::get('daemon.dir.run') . DIRECTORY_SEPARATOR . $this->_type . '_' . $this->_event . '_run.data';

            $this->_servers = array(
                'status' => array('host' => 'firehose0.i.api.weibo.com', 'port' => 8082),
                'comment' => array('host' => 'firehose1.i.api.weibo.com', 'port' => 8082),
                'user' => array('host' => 'firehose2.i.api.weibo.com', 'port' => 8082),
                'direct_message' => array('host' => 'firehose3.i.api.weibo.com', 'port' => 8082),
                'others' => array('host' => 'firehose4.i.api.weibo.com', 'port' => 8082),
            );

            if (isset($this->_servers[$this->_type]))
            {
                $this->_server = $this->_servers[$this->_type];
            }
            else
            {
                $this->_server = $this->_servers['others'];
            }

            // 重置，删除info文件，让长连接从头开始读取数据
            if ($refresh)
            {
                if (file_exists($this->_maxLastIdDataFile))
                {
                    unlink($this->_maxLastIdDataFile);
                }
            }

            //分片
            $this->_split = $split;
        }

        public function __destruct()
        {
            $this->_close();
        }

        /**
         * 初始化Firehose参数
         * @param  [type] $process [description]
         * @return [type]          [description]
         */
        public function init($process)
        {
            //分片处理
            $this->_spart = $process->index;
            $this->_host = $this->_server['host'];
            $this->_port = $this->_server['port'];

            // 读取上次ID
            $this->_readLastId();
            $this->_lastWriteTime = time();

            // 连接
            //$this->_connect();
        }

        private function _readLastId()
        {
            if (file_exists($this->_maxLastIdDataFile))
            {
                $runData = unserialize(file_get_contents($this->_maxLastIdDataFile));
                $this->_maxLastId = isset($runData['lastId']) ? intval($runData['lastId']) : 0;
            }
            else
            {
                $this->_maxLastId = 0;
            }
        }

        private function _writeLastId()
        {
            if ($this->_maxLastId == 0)
            {
                return;
            }

            // 最后最大ID
            $runData = array(
                'lastId' => $this->_maxLastId,
            );

            if (!file_put_contents($this->_maxLastIdDataFile, serialize($runData)))
            {
                $prj->CLog->w('ERROR', 'FIREHOSE WRITE RUN DATA FILE ERROR ' . $this->lastIDFileName . ' ' . print_r(error_get_last(), true));
            }
        }

        private function _close()
        {
            $this->_writeLastId();

            if (is_resource($this->_connectHandler))
            {
                fclose($this->_connectHandler);
            }

            $this->_connectHandler = NULL;
        }

        /**
         * 连接Firehose长连接
         * @param  string $connectReason 连接原因
         * @return [type]          [description]
         */
        private function _connect($connectReason = '')
        {
            // 初始化参数 
            $this->_connectMessageCount = 0;

            // 先关闭连接
            $this->_close();

            // 连接服务器
            $errorNo = 0;
            $errorMessage = '';
            $this->_connectHandler = fsockopen($this->_host, $this->_port, $errorNo, $errorMessage, $this->_connectTimeOut);

            if ($this->_connectHandler === false)
            {
                $this->_connectFailedCount++;
                Tool_Log::error("FIREHOSE CONNECTION FAILED - CONNECTION - {$this->_host} - {$this->_port} - {$errorMessage}", 'firehose');

                return false;
            }
            else
            {
                $queryString = "/comet?appid=" . self::APP_ID . "&filter={$this->_type},{$this->_event}";

                //分片处理
                if ($this->_split > 1)
                {
                    $queryString .= "&split=" . $this->_split . "&spart=" . $this->_spart;
                }

                // 重连时，最大ID+1
                if ($this->_maxLastId > 0)
                {
                    $queryString .= "&loc=" . ($this->_maxLastId + 1);
                }

                $request = "GET " . $queryString . " HTTP/1.1\r\n";
                $request .= "User-Agent: Sina Mobile Cloud Platfrom Browser 1.0.0\r\n";
                $request .= "Host: " . $this->_host . ':' . $this->_port . "\r\n";
                $request .= "Accept: */*\r\n";
                $request .= "Keep-Alive: 600\r\n\r\n";

                if (fwrite($this->_connectHandler, $request) === false)
                {
                    $this->_connectFailedCount++;
                    Tool_Log::error("FIREHOSE CONNECTION FAILED - REQUEST - {$this->_host} - {$this->_port} - {$request}", 'firehose');

                    return false;
                }
            }

            // 设定超时
            stream_set_timeout($this->_connectHandler, $this->_streamTimeOutSec, $this->_streamTimeOutUSec);
            //socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec'=>$sec, 'usec'=>$usec));
            //socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, array('sec'=>$sec, 'usec'=>$usec));

            // 读取长度
            if ($this->_readChunkSize() === false)
            {
                Tool_Log::error("FIREHOSE CONNECTION FAILED - HEADER SIZE - {$this->_host} - {$this->_port} - {$queryString}", 'firehose');
                return false;
            }

            // 读取Header
            if ($this->_readHeader() === false)
            {
                Tool_Log::error("FIREHOSE CONNECTION FAILED - HEADER - {$this->_host} - {$this->_port} - {$queryString}", 'firehose');
                return false;
            }

            Tool_Log::notice("FIREHOSE CONNECTED - {$connectReason} - {$this->_host} - {$this->_port} - {$queryString}", 'firehose');

            return true;
        }

        private function _readChunkSize()
        {
            $sizeHeader = fread($this->_connectHandler, 16);

            if ($sizeHeader === false)
            {
                return false;
            }
            
            $size = unpack("I", substr($sizeHeader, 0, 4));
            $this->_chunkSize = $size[1];

            return true;
        }

        private function _readHeader()
        {
            $header = '';

            while (true)
            {
                $buffer = fgets($this->_connectHandler, 102400);

                if ($buffer === false)
                {
                    return false;
                }

                $header .= $buffer;

                if (strpos($header, "\r\n\r\n") !== false)
                    break;
            }

            return true;
        }

        /**
         * 获取数据
         * @return [type] [description]
         */
        public function getData($process)
        {
            /*// 当超过一定数量时，重新连接
            if ($this->_connectMessageCount > $this->_connectMessageMaxCount)
            {
                Tool_Log::notice("FIREHOSE RECONNECT BY COUNT - {$this->_connectMessageCount}", 'firehose');

                // 重连
                $this->_connect();

                return false;
            }*/

            // 连接检查
            if ($this->_connectHandler == false)
            {
                // 重连
                $this->_connect('FIRST');

                return false;
            }

            // 取长度
            $buffer = fgets($this->_connectHandler, 1024);

            if ($buffer === false)
            {
                // 重连
                $this->_connect('READ');

                return false;
            }

            $chunkSize = (integer)hexdec($buffer);

            // 最后一个chunk
            if ($chunkSize === 0)
            {
                $this->_connect('FORMAT');

                return false;
            }

            // 取内容
            $buffer = fgets($this->_connectHandler, $chunkSize + 2);
            //$buffer = fgets($this->_connectHandler, 102400);

            if ($buffer === false)
            {
                $this->_connect('READ');

                return false;
            }

            // 跳过\r\n
            $buffer1 = fread($this->_connectHandler, 2);

            if ($buffer1 === false)
            {
                $this->_connect('READ');
                
                return false;
            }

            // 计数
            $this->_allMessageCount++;
            $this->_allMessageSize += $chunkSize;
            $this->_connectMessageCount++;

            //设定最后ID
            $this->setId(substr($buffer, 6, 16));

            return $buffer;
        }

        /**
         * 设定最后一个ID
         * @param [type] $lastId [description]
         */
        public function setId($lastId)
        {
            // 获取当前ID
            $this->_lastId = $lastId;

            // 最大ID
            if ($this->_lastId > $this->_maxLastId)
            {
                $this->_maxLastId = $this->_lastId;
            }

            $currentTime = time();
            if ($currentTime > $this->_lastWriteTime)
            {
                $this->_writeLastId();
                $this->_lastWriteTime = $currentTime;
            }
        }

        /**
         * 打印日志
         * @return [type] [description]
         */
        public function printLog()
        {
            Tool_Log::debug("SIZE - {$this->_type} - {$this->_lastId} - {$this->_connectMessageCount} - {$this->_allMessageCount} - " . round($this->_allMessageSize / (1024 * 1024), 2) . "M - " . $this->_getMemoryUsage(), 'firehose');
        }

        private function _getMemoryUsage()
        {
            $memoryUsage = memory_get_usage(true); 

            switch (true)
            {
                case $memoryUsage < 1024:
                    return $memoryUsage; 

                case $memoryUsage < 1048576:
                    return round($memoryUsage / 1024, 2) . 'K'; 
                    
                default:
                    return round($memoryUsage / 1048576, 2) . 'M';
            }
        }
    }
