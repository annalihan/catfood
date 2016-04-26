<?php

class Catfood_LoginController extends AbstractController
{


    public function run()
    {
        try
        {
            //渲染模板
            $this->tpl = '';
            return;
        }
        catch (Exception $e)
        {
            var_dump($e);exit;
            Tool_Log::fatal($e->getMessage());
            Tool_Redirect::pageNotFound();
        }

    }
}