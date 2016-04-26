<?php

class Tool_Process
{
    public static function getStatus($processId)
    {
        $file = "/proc/{$processId}/status";

        if (file_exists($file) === false)
        {
            return false;
        }

        $data = array();
        $lines = file($file);
        foreach ($lines as $line)
        {
            $line = trim($line);
            list($name, $value) = explode(':', $line);
            $data[trim($name)] = trim($value);
        }

        return $data;
    }

    public function runInBackground()
    {
        $pid = pcntl_fork();
        
        if ($pid == -1)
        {
            die('can\'t fork');
        }
        else
        {
            if ($pid)
            {
                exit;
            }
        }
        
        posix_setsid();
        usleep(100000);
        
        $pid = pcntl_fork();
        
        if ($pid == -1)
        {
            die('can\'t fork');
        }
        else
        {
            if ($pid)
            {
                exit;
            }
        }            
    }
}