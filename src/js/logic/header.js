/*
 * author xxx | xxx2@staff.sina.com.cn
 * 示例功能
 * 写清楚作者和功能
 */
require("kit/dom/parseDOM");
require("common/trans/login");
//---常量定义区----------------------------------
var $ = STK;

module.exports = function (node) {
    //---变量定义区---------------------------------
    var that = {};
    var confirmUI = require('../ui/confirm'); //弹出框组件对象
    var onBorrow = require('common/channel/onBorrow'); //借书事件

    var _this = {
        DOM: {},//节点容器
        objs: {},//组件容器
        //直接与dom操作相关的方法都存放在DOM_eventFun
        DOM_eventFun: {
            user: function () { //用户图标点击
                if (!_this.DOM.logout) {
                    return;
                }
                $.stopEvent();
                if ($.getStyle(_this.DOM.logout, 'display') === 'none') {
                    $.setStyle(_this.DOM.logout, 'display', 'block');
                } else {
                    $.setStyle(_this.DOM.logout, 'display', 'none');
                }
            },
            logout: function () { //注销
                var data = {};
                data = {
                    title: '确认注销？',
                    description: '',
                    button1: '取消',
                    button2: '确认'
                };
                _this.objs.confirmObj = confirmUI(data);
                $.custEvent.add(_this.objs.confirmObj, 'button2Click', _this.bindCustEvtFuns.loginout);
            },
            back: function () { //返回上一页面
                window.history.back();
            },
            hide: function () { //隐藏注销
                if (!_this.DOM.logout) {
                    return;
                }
                if ($.getStyle(_this.DOM.logout, 'display') === 'block') {
                    $.setStyle(_this.DOM.logout, 'display', 'none');
                }
            },
            myBookClick: function () {
                $.setStyle(_this.DOM.circlePoint, 'display', 'none');

            }
        },
        bindCustEvtFuns: {
            loginout: function () {
                // window.location = 
                // $.common.trans.login.getTrans('loginout', {
                //     'onSuccess': function (data) {
                //         if (data.data.code == 0) {
                //             location.href = "login";
                //         }
                //     },
                //     'onError': function () {
                //         alert('注销失败！');
                //     }
                // }).request({});
                window.location = "http://catfood.wap.grid.sina.com.cn/catfood/logout";
            }
        },
        bindListenerFuns: {
            circlePoint: function () {
                $.setStyle(_this.DOM.circlePoint, "display", "inline-block");
            }
        }
    };

    var argsCheck = function () {
        if (node == null || (node != null && !$.isNode(node))) {
            throw "[]:argsCheck()-The param node is not a DOM node.";
        }
    };
    //-------------------------------------------

    //---Dom的获取方法定义区---------------------------
    var parseDOM = function () {
        _this.DOM = $.kit.dom.parseDOM($.builder(node).list);
    };
    //-------------------------------------------

    //---模块的初始化方法定义区-------------------------
    var initPlugins = function () {
    };
    //-------------------------------------------

    //---DOM事件绑定方法定义区-------------------------
    var bindDOM = function () {
        $.addEvent(_this.DOM.user, 'click', _this.DOM_eventFun.user);
        $.addEvent(_this.DOM.logout, 'click', _this.DOM_eventFun.logout);
        $.addEvent(_this.DOM.back, 'click', _this.DOM_eventFun.back);
        $.addEvent(document, 'click', _this.DOM_eventFun.hide);
        $.addEvent(_this.DOM.mybook, 'click', _this.DOM_eventFun.myBookClick);
    };
    //-------------------------------------------

    //---自定义事件绑定方法定义区------------------------
    var bindCustEvt = function () {
    };
    //-------------------------------------------

    //---广播事件绑定方法定义区------------------------
    var bindListener = function () {//页面显示的时候调用，以便对页面处理
        onBorrow.register('borrow', _this.bindListenerFuns.circlePoint);
    };
    //-------------------------------------------

    //---模块初始化数据------------------------
    var inintData = function() {
        var pathname = window.location.pathname.split('/');
        var href = pathname[pathname.length - 1];

        if (href === 'login') {
            $.setStyle(_this.DOM.user, 'display', 'none');
            $.setStyle(_this.DOM.mybook, 'display', 'none');
        } else if (href === 'mybooklist') {
            $.setStyle(_this.DOM.mybook, 'display', 'none');
            $.setStyle(_this.DOM.back, 'display', 'block');
        }
    }
    //-------------------------------------------

    //---组件公开方法的定义区---------------------------
    var destroy = function () {
        if (_this) {
            $.removeEvent(_this.DOM.user, 'click', _this.DOM_eventFun.user);
            $.removeEvent(_this.DOM.logout, 'click', _this.DOM_eventFun.logout);
            $.removeEvent(_this.DOM.back, 'click', _this.DOM_eventFun.back);
            $.removeEvent(document, 'click', _this.DOM_eventFun.hide);
            $.removeEvent(_this.DOM.mybook, 'click', _this.DOM_eventFun.myBookClick);
            $.foreach(_this.objs, function (o) {
                if (o && o.destroy) {
                    o.destroy();
                }
            });
            _this = null;
        }
    };
    //-------------------------------------------
    //---组件的初始化方法定义区-------------------------
    var init = function () {
        argsCheck();
        parseDOM();
        inintData();
        initPlugins();
        bindDOM();
        bindCustEvt();
        bindListener();
    };
    //-------------------------------------------
    //---执行初始化---------------------------------
    init();
    //-------------------------------------------

    //---组件公开属性或方法的赋值区----------------------
    that.destroy = destroy;
    //-------------------------------------------

    return that;
};