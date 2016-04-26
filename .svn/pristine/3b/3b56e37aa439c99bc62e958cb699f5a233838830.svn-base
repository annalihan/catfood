<?php

class Catfood_LoginController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
    public function run()
    {

        try
        {
            $redirectUrl =  'http://'.$_SERVER["HTTP_HOST"].'/catfood/booklist';
            $reader = Tools_Login::checkLogin($redirectUrl);

            //渲染模板
//            $this->tpl = 'login.phtml';
//            echo $this->renderHtml(array(), true);
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
