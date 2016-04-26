<?php
/**
 * boostrap文件
 * @author chenjie <chenjie5@staff.sina.com.cn>
 * @version 2013-10-24
 */

//加载头文件
include dirname(__FILE__) . '/header.inc.php';

class Bootstrap extends Yaf_Bootstrap_Abstract
{
    /**
     * 初始化自动加载类(没有小流量)
     * @param  Yaf_Dispatcher $dispatcher [description]
     * @return [type]                     [description]
     */
    private function _initLoadPlatform(Yaf_Dispatcher $dispatcher)
    {
        //注册自动加载
        //配置 application.system.use_spl_autoload=true
        spl_autoload_register(array('Core_Loader', 'loadPlatformClass'));

        //设定环境
        Core_Loader::getInstance()->initEnvironment();
    }
    
    /**
     * 初始化服务资源(没有小流量)
     */
    private function _initConfigs(Yaf_Dispatcher $dispatcher)
    {
        mb_internal_encoding('utf-8');
        setlocale(LC_ALL, 'zh_CN.utf-8');
        date_default_timezone_set('Asia/Chongqing');
        //Yaf_Registry::set("config", Yaf_Application::app()->getConfig());
        Comm_Context::$keepServerCopy = true;
        Comm_Context::init();
        Comm_Cache::initPool();
        Comm_Db::initPool();
    }

    /**
     * 初始化公共插件
     * 
     * @param Yaf_Dispatcher $dispatcher
     */
    private function _initPlugin(Yaf_Dispatcher $dispatcher)
    {
        //小流量插件：在未加载业务代码之前
        //注：Comm_Context、Comm_Config、Comm_Cache、Comm_Db、Tool_Array已经在此之前加载进来
        $dispatcher->registerPlugin(new BranchPlugin());

        //路由插件：主要负责路由的转换和加载
        $dispatcher->registerPlugin(new RoutePlugin());

        //认证插件：Authorize中需要加载业务的Controller
        $dispatcher->registerPlugin(new AuthorizePlugin());

        //需要获取用户信息，所以在Authorize之后
        $dispatcher->registerPlugin(new DebugPlugin());

        //自定义Plugin
        $plugins = Comm_Plugin::getPlugins();
        if (count($plugins))
        {
            foreach ($plugins as $plugin)
            {
                if ($plugin instanceof Yaf_Plugin_Abstract)
                {
                    $dispatcher->registerPlugin($plugin);
                }
            }
        }
    }
    
    /**
     * 设置模板引擎
     * @param Yaf_Dispatcher $dispatcher
     */
    private function _initView(Yaf_Dispatcher $dispatcher)
    {
        //关闭默认引擎
        $dispatcher->disableView();
    }

    /*private function _initError(Yaf_Dispatcher $dispatcher)
    {
        include('controllers/Error.php');
    }*/

    /**
     * 路由管理部分
     * 
     * @param Yaf_Dispatcher $dispatcher
     */
    private function _initRoute(Yaf_Dispatcher $dispatcher)
    {
        $routes = Comm_Route::getRoutes();
        if (count($routes))
        {
            $router = Yaf_Dispatcher::getInstance()->getRouter();
            foreach ($routes as $routeName => $route)
            {
                if ($route instanceof Yaf_Route_Interface)
                {
                    $router->addRoute($routeName, $route); 
                }
            }
        }
    }
}