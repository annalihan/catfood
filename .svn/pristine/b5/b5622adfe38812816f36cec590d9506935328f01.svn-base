<?php
/**
 * Created by PhpStorm.
 * User: lihan
 * Date: 15/11/10
 * Time: 下午7:50
 */
class Catfood_LogoutController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;

    public function logOut()
    {
        Tools_Login::logout();
    }

    public function run()
    {
        $this->logOut();
    }
}