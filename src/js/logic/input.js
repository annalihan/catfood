/*
 * author xxx | xxx2@staff.sina.com.cn
 * 示例功能
 * 写清楚作者和功能
 */
require("kit/dom/parseDOM");
require("common/trans/login");
//---常量定义区----------------------------------
var $ = STK;

//密码加密所需变量
var E = "3";
var RSA_N = "7634a7eefd1bf048aa006f5ee7c0962a7ac74ca42599d6e0b5f01850d12a535cd867d00ba2f0e24b2ebf769162e5091eb1bc4fe14fa4419da0aad6ad464a3f77779bed69bfcaffc0b98788a05ee0144690849a5c45f83830737b00682c2828d1e3208e66e010a2bcf98ae279c0d512117ac396e6f8303e2ffb4726b335dbabd1";


module.exports = function (node) {
    //---变量定义区---------------------------------
    var that = {};
    var do_encrypt = function (value) {
        var rsa = new RSAKey();
        rsa.setPublic(RSA_N, E);
        var res = rsa.encrypt(value);
        return res;
    };

    var _this = {
        DOM: {},//节点容器
        objs: {},//组件容器
        //直接与dom操作相关的方法都存放在DOM_eventFun
        DOM_eventFun: {
            login: function () {
                var mv = _this.DOM.mail.value;
                var pv = _this.DOM.password.value;

                var user = mv;
                var pswd = do_encrypt(pv);

                if ((mv === '') || (pv === '')) {
                    $.setStyle(_this.DOM.warn, 'display', 'block');
                    _this.DOM.warn.innerHTML = '用户名和密码不能为空！';
                    return false;
                } else {
                    $.common.trans.login.getTrans('login', {
                        'onSuccess': function (data) {
                            if (data.data.code == 0) {
                                location.href = "index";
                            } else if (data.data.code == -1) {
                                $.core.dom.setStyle(_this.DOM.warn, 'display', 'block');
                                _this.DOM.warn.innerHTML = '用户名或密码错误！';
                                return false;
                            }
                        },
                        'onError': function () {
                            $.core.dom.setStyle(_this.DOM.warn, 'display', 'block');
                            _this.DOM.warn.innerHTML = '网络繁忙！';
                        }
                    }).request({user_name: user, user_password: pswd});
                }
            }
        },
        bindCustEvtFuns: {
        },
        bindListenerFuns: {
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
        $.addEvent(_this.DOM.login, 'click', _this.DOM_eventFun.login);
    };
    //-------------------------------------------

    //---自定义事件绑定方法定义区------------------------
    var bindCustEvt = function () {
    };
    //-------------------------------------------

    //---广播事件绑定方法定义区------------------------
    var bindListener = function () {//页面显示的时候调用，以便对页面处理
    };
    //-------------------------------------------

    //---组件公开方法的定义区---------------------------
    var destroy = function () {
        if (_this) {
            $.removeEvent(_this.DOM.login, 'click', _this.DOM_eventFun.login);
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