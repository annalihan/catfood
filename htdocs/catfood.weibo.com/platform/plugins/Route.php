<?php
/**
 * 路由 PLUGIN,处理特殊路由，加载controller
 * @package Plugin
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */
class RoutePlugin extends Yaf_Plugin_Abstract
{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        $controllerName = $request->getControllerName();

        if (strpos($controllerName, 'I_') === 0)
        {
            header('Content-type: application/json; charset=utf-8');
            $controllerName = 'Interface_' . substr($controllerName, 2);
            $request->setControllerName($controllerName);
            $request->setDispatched();
        }

        Core_Loader::getInstance()->includeClass($controllerName, strlen($controllerName), '', 0, YAF_CONTROLLER_DIRECTORY_NAME);
    }
}
