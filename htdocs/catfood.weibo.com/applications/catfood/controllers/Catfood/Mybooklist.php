<?php
//获取读书列表
class Catfood_MybooklistController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
    public function run()
    {
        try
        {
            $this->tpl = 'mybooklist.phtml';
            $reader = $_COOKIE['reader'];
            $this->renderHtml(array('reader' => $reader));
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
