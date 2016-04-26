<?php
/**
 * 登陆注销接口
 *
 */
class Aj_Login_LoginoutController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;

    public function loginOut()
    {
        setcookie("reader", '',0,'/',$_SERVER["HTTP_HOST"]);
        $data = array(
            'code' => 0,
            'msg' =>'成功'
        );
        Tool_Jsout::normal('100000', '成功', $data);
    }
    public function run()
    {
        $this->loginOut();
    }
}