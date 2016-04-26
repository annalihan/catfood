<?php
/**
 * ajax 通用controller
 *
 */
class Aj_AjaxController extends AbstractController
{
    public function __construct()
    {
        $viewer = Comm_Context::get('viewer', '');
        if (!$viewer)
        {
            //Tools_Redirect::unloginHome();
        }
        
    }
    
    //实现抽象类AbstractController的run方法
    public function run()
    {
        
    }
}