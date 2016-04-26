/**
 * 帐号设置操作接口管理
 * @author gaoyuan3@staff.sina.com.cn
 *
 */
$Import("kit.io.inter");
STK.register('common.trans.operate', function ($) {
    var t = $.kit.io.inter();
    var g = t.register;

    //图书列表的aj请求
    g('borrow', {'url': '/aj/borrow/borrow', 'method': 'get'});//确认借书
    g('give', {'url':'/aj/borrow/return', 'method': 'get'});//确认还书
    g('search', {'url':'/aj/booklist/booklist', 'method':'get'});//搜索
    g('scroll_booklist', {'url':'/aj/booklist/booklist', 'method':'get'});//图书列表
    g('scroll_mybooklist', {'url':'/aj/booklist/historybooklist', 'method':'get'});//历史借阅
    return t;
});