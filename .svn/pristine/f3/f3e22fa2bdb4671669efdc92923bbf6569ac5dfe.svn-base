<?php
/**
 * DEBUG PLUGIN,初始化DEBUG,输出DEBUG
 * @package Plugin
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class DebugPlugin extends Yaf_Plugin_Abstract
{
    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //DEBUG功能只有请求参数可以开启(用于FireBug)
        $debug = $request->getQuery('sys_debug');
        Core_Debug::openDebug($debug);

        //请求参数开启性能分析日志
        $log = $request->getQuery('sys_log');
        if ($log)
        {
            Core_Debug::openLog(true);
            return;
        }

        //基于项目配置的开关
        $log = Comm_Config::get('project.debug', null);
        if ($log)
        {
            Core_Debug::openLog(true);
            return;
        }
    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //基于用户的性能分析数据开放策略:白名单+抽样(0.2%)
        $viewer = Comm_Context::get('viewer', false);
        if ($viewer == false)
        {
            return;
        }

        $debugConfig = Comm_Config::get('debug', null);
        $whiteList = isset($debugConfig['white']) ? $debugConfig['white'] : array();
        if (isset($whiteList[$viewer->id]))
        {
            Core_Debug::openLog(true);
            return;
        }

        //10%流量
        $whiteScale = isset($debugConfig['scale']) ? $debugConfig['scale'] : 0.1;

        //全部开启
        if ($whiteScale == 1)
        {
            Core_Debug::openLog(true);
            return;
        }

        //按比例开启
        $size = (($whiteScale > 0 && $whiteScale < 1) ? intval(1 / $whiteScale) : 10);
        if ((($viewer->id / 10) % $size) == rand(0, $size - 1))
        {
            Core_Debug::openLog(true);
            return;
        }
    }

    public function dispatchLoopShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        Core_Debug::sessionFinished();
    }
}
