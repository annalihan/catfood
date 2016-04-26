<?php
/**
 * @title   接入新浪口袋统一登录工具类
 * @User: lihan
 * @Date: 15/11/16
 * @Time: 下午7:07
 */
class Tools_Login
{
    //检查登录
    public static function checkLogin($localService)
    {

        $reader = isset($_COOKIE["reader"]) ? $_COOKIE["reader"]:"";
        $readerId = isset($_COOKIE["reader_id"]) ? $_COOKIE["reader_id"]:"";
        $readerName = isset($_COOKIE["reader_name"]) ? $_COOKIE["reader_name"]:"";

        if(empty($reader)){

            $localService = $localService? $localService : 'http://'.$_SERVER["HTTP_HOST"].'/catfood/index';
            $loginServer = Comm_Config::get("api.loginServer");

            //验证登录
            $ticket = isset($_REQUEST["ticket"]) ? $_REQUEST["ticket"] : "";
            if(empty($ticket)){
                Tool_Redirect::response($loginServer."/login?service=".urlencode($localService)."&ext=");
            }

            //根据ticket获取用户登录信息
            $validateUrl = $loginServer."/validate?ticket=".$ticket."&service=".urlencode($localService);
            $validate = file_get_contents($validateUrl);
            $userInfo = simplexml_load_string($validate);
            $reader = (string)$userInfo->info->email;
            $readerId = (string)$userInfo->info->username;
            $readerName = (string)$userInfo->info->name;

            //登录失败
            if(!$reader){
                $data = array(
                    'code' => -1,
                    'msg' =>'登陆失败'
                );
                Tool_Jsout::normal('100000', '登陆失败', $data);
                exit;
            }

            //登录成功设置cookie
            setcookie('reader', $reader, time() + 3600, "/", $_SERVER["HTTP_HOST"]);
            setcookie('reader_id', $readerId, time() + 3600, "/", $_SERVER["HTTP_HOST"]);
            setcookie('reader_name', $readerName, time() + 3600, "/", $_SERVER["HTTP_HOST"]);

        }
        return $reader;
    }

    //退出登录
    public static function logout($localService){
        //删除cookie
        setcookie('reader', '', time() - 3600, "/", $_SERVER["HTTP_HOST"]);
        setcookie('reader_id', '', time() - 3600, "/", $_SERVER["HTTP_HOST"]);
        setcookie('reader_name', '', time() - 3600, "/", $_SERVER["HTTP_HOST"]);

        $localService = $localService ? $localService : 'http://'.$_SERVER["HTTP_HOST"].'/catfood/index';
        $loginServer = Comm_Config::get("api.loginServer");
        header("Location: ".$loginServer."/logout?service=".urlencode($localService));
    }
}