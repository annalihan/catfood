require('kit/dom/parseDOM');

module.exports = function(node) {
    //+++ 常量定义区 ++++++++++++++++++
    var $ = STK;

    //+++ 变量定义区 ++++++++++++++++++
    var confirmUI = require('ui/confirm'); //弹出框ui
    var that = {};
    var _this = {
        DOM: {}, //节点容器
        objs: {}, //组件容器
        DOM_eventFun: { //DOM事件行为容器
            operatorClick: function(spec) { //点击操作（借阅/还书）
                var arg = {};
                switch (spec.data.book_status) {
                    case '2':
                        arg = {
                            title: '确认还书？',
                            button1: '取消',
                            button2: '确认'
                        };
                        $.custEvent.add(confirmUI(arg), 'button2Click', _this.bindCustEvtFuns.OK, spec);
                        break;
                }
            }
        },
        bindCustEvtFuns: { //自定义事件回调函数
            OK: function(spec) {
                $.ajax({
                    'url': 'http://catfood.wap.grid.sina.com.cn/aj/borrow/return',
                    'charset': 'UTF-8',
                    'timeout': 30 * 1000,
                    'args': {
                        book_id: spec.data.data.book_id,
                        detail_call_number: spec.data.data.detail_call_number
                    },
                    'onComplete': function(data) {
                        if (data.data.code === 0) {
                            var parentNode = spec.data.el.parentNode;
                            parentNode.removeChild(spec.data.el);
                            var tryHtml = '<div class="bookOperatorTry bookOperator">审</div>';
                            $.addHTML(parentNode, tryHtml);
                            _this.FUNS.backBookSuccess();
                        }
                    },
                    'onTimeout': null,
                    'onFail': null,
                    'method': 'get', // post or get
                    'asynchronous': true,
                    'contentType': 'application/x-www-form-urlencoded',
                    'responseType': 'json'
                });
            }

        },
        bindListenerFuns: { //广播事件回调函数

        },
        FUNS: { //自定义方法
            backBookSuccess: function() { //还书成功
                var arg = {
                    title: '审核中!',
                    description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png">按钮查看',
                    button1: '取消',
                    button2: '立即查看'
                };
                confirmUI(arg);
            }

        }
    };
    //----------------------------------------------



    //+++ 参数的验证方法定义区 ++++++++++++++++++
    var argsCheck = function() {
        if (!node) {
            throw new Error('node没有定义');
        }
    };
    //-------------------------------------------


    //+++ Dom的获取方法定义区 ++++++++++++++++++
    var parseDOM = function() {
        //内部dom节点
        _this.DOM = $.kit.dom.parseDOM($.builder(node).list);
        if (!1) {
            throw new Error('必需的节点 不存在');
        }
    };
    //-------------------------------------------


    //+++ 模块的初始化方法定义区 ++++++++++++++++++
    var initPlugins = function() {};
    //-------------------------------------------

    //+++ DOM事件绑定方法定义区 ++++++++++++++++++
    var bindDOM = function() {
        _this.objs.delegate = $.delegatedEvent(node);
        _this.objs.delegate.add('operatorClick', 'click', _this.DOM_eventFun.operatorClick);
    };
    //-------------------------------------------


    //+++ 自定义事件绑定方法定义区 ++++++++++++++++++
    var bindCustEvt = function() {

    };
    //-------------------------------------------


    //+++ 广播事件绑定方法定义区 ++++++++++++++++++
    var bindListener = function() {};
    //-------------------------------------------


    //+++ 组件销毁方法的定义区 ++++++++++++++++++
    var destroy = function() {
        if (_this) {
            $.foreach(_this.objs, function(o) {
                if (o.destroy) {
                    o.destroy();
                }
            });
            _this.objs.delegate.remove('operatorClick', 'click');
            _this = null;
        }
    };
    //-------------------------------------------

    //+++ 组件的初始化方法定义区 ++++++++++++++++++
    var init = function() {
        argsCheck();
        parseDOM();
        initPlugins();
        bindDOM();
        bindCustEvt();
        bindListener();
    };
    //-------------------------------------------
    //+++ 执行初始化 ++++++++++++++++++
    init();
    //-------------------------------------------


    //+++ 组件公开属性或方法的赋值区 ++++++++++++++++++
    that.destroy = destroy;
    //------------------------------------------
    return that;
};