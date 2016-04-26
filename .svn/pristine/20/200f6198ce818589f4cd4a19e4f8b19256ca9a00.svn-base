<?php
/**
 * 登陆注销接口
 *
 */
class Aj_Login_LoginController extends AbstractController
{
    const NOT_LOGIN = 1;
    const MUST_CHECK_SESSION = false;
    public $authorizeType = self::NOT_LOGIN;
    public $header = array(
        'Content-Type' => 'application/x-www-form-urlencoded'
    );
    public $url = 'http://katpard.mcp.wap.grid.sina.com.cn/login/loginWeibo';

    public function checkLogin()
    {
//        $username = Comm_Context::get('user_name');
        $username = $this->getRequest()->get('user_name');
//        $password = Comm_Context::get('user_password');
        $password = $this->getRequest()->get('user_password');
        $checkLogin  = Tool_Http::post($this->url,array(
            'username' => $username,
            'password' => $password
            ),
            $this->header);
        $code = json_decode($checkLogin, true);
        if($code['code'] == 100000) {
            $flag = setcookie("reader", $username,0,'/',$_SERVER["HTTP_HOST"]);
            $data = array(
                'code' => 0,
                'msg' =>'成功'
            );
            Tool_Jsout::normal('100000', '成功', $data);
        } else{
            $data = array(
                'code' => -1,
                'msg' =>'登陆失败'
            );
            Tool_Jsout::normal('100000', '登陆失败', $data);
        }
    }
    public function loginOut()
    {
        setcookie("reader", "");
        $data = array(
            'code' => 0,
            'msg' =>'成功'
        );
        Tool_Jsout::normal('100000', '成功', $data);
    }
    public function run()
    {
        $this->checkLogin();
//        $this->loginOut();
    }
}