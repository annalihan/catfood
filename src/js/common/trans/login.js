/**
 * 帐号设置操作接口管理
 * @author gaoyuan3@staff.sina.com.cn
 *
 */
$Import("kit.io.inter");
STK.register('common.trans.login', function ($) {
    var t = $.kit.io.inter();
    var g = t.register;

    //登录请求
    g('login', {'url': '/aj/login/login', 'method': 'get'});
    //注销请求
    g('loginout',{'url': '/aj/login/loginout', 'method': 'get'});
    return t;
});