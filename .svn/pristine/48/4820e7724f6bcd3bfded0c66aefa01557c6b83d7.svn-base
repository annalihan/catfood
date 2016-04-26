<?php

class Comm_Log_Formatter_Default extends Comm_Log_Formatter
{
    private $_levelTypes = array(
        LOG_LEVEL_ALL => 'ALL',
        LOG_LEVEL_FATAL => 'FATAL',
        LOG_LEVEL_ERROR => 'ERROR',
        LOG_LEVEL_WARN => 'WARN',
        LOG_LEVEL_INFO => 'INFO',
        LOG_LEVEL_DEBUG => 'DEBUG',
        LOG_LEVEL_TRACE => 'TRACE',
    );

    private $_replaces = array("\n", "\t");

    private function _formatArray($array)
    {
        return Tool_Json::encode($array);
    }

    private function _formatMessage($message)
    {
        if (is_array($message) || is_object($message))
        {
            $message = $this->_formatArray($message);
        }

        return str_replace($this->_replaces, '', $message);
    }

    public function formatMessage($level, $value, $type = '')
    {
        $message = $this->_formatMessage($value);

        $viewer = Comm_Context::get('viewer', false);
        $viewerId = $viewer ? $viewer->id : 0;

        $log = array(
            'level' => $this->_levelTypes[$level], //等级
            'time' => date('Y-m-d H:i:s'), //时间
            'id' => $this->logId, //日志ID
            'viewer_id' => $viewerId, //访问用户ID
            'type' => $type, //openapi,res,app,daemon等等
            'content' => $message, //日志信息
        );

        return implode($log, '|');
    }

    public function formatMessageBody($body)
    {
        if (is_array($body))
        {
            foreach ($body as $key => $value)
            {
                $body[$key] = $this->_formatMessage($value);
            }

            return implode('|', $body);
        }

        return $body;
    }
}