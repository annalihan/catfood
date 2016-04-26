<?php
class BranchPlugin extends Yaf_Plugin_Abstract
{
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response)
    {
        //灰度和小流量初始化
        Core_Branch::init();
    }
}
