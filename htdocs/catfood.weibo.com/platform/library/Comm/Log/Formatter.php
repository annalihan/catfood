<?php

abstract class Comm_Log_Formatter
{
    protected $logId = null;

    public function setLogId($logId)
    {
        $this->logId = $logId;
    }

    abstract public function formatMessage($level, $message, $type = '');

    public function formatMessageBody($message)
    {
        return $message;
    }
}