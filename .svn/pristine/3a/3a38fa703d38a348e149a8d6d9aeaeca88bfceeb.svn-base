<?php

class Catfood_IndexController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
    public function run()
    {
        try
        {
            $reader = Tools_Login::checkLogin();

            //渲染模板
            $this->tpl = 'index.phtml';
            $maxCount = 3;

            $this->renderHtml(array('reader' => $reader,'maxcount' => $maxCount));

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
