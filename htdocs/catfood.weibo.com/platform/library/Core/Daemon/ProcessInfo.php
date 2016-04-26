<?php
/**
 * 子进程数据对象类
 */
class Core_Daemon_ProcessInfo extends Core_Daemon_Abstract
{
    public $pid;
    public $startTime;
    public $lastActiveTime;
    public $lastStatus = array();
    public $performance = array();
    public $resourceConfig = array();
    public $running = true;
    public $isTemp = false;
    public $closed = false;
    
    public function __construct($param = array())
    {
        $this->setParam($param);

        $this->lastStatus = array(
            'queueStatusTotalCount' => 0,
            'queueStatusTotalTimeCount' => 0,
            'queueStatusMemory' => 0,
            'queueStatusCpuU' => 0,
            'queueStatusCpuS' => 0,
            'queueStatusTotalCpuU' => 0,
            'queueStatusTotalCpuS' => 0,
        );
    }
}