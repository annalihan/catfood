<?php

class Pl_Login_Login extends Pl_Abstract
{
    public $name = 'pl_login_login';

    public $tpl = 'login/pl_login_login.phtml';

    public function prepareData()
    {
        return array();
    }
    public function getPageMetaData(){
        //            $userName = Comm_Context::form('user_name');
//            $password = Comm_Context::form('password');
        $userName = "tingting33";
        $password = "";
        $user = Tool_Ldap::loginUser($userName, $password);
        session_start();
        $_SESSION['user'] = $user;
        if($user) {
            $data = array(
                'code' => 0,
                'msg' =>'成功'
            );
            $this->renderAjax(Comm_Config::get("riacode.succ"), 'success', $data);
        } else{
            $data = array(
                'code' => 1,
                'msg' =>'登陆失败'
            );
            $this->renderAjax(Comm_Config::get("riacode.error"), 'error', $data);
        }
    }
}