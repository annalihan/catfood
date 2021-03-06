//+++ 常量定义区 ++++++++++++++++++
var $ = STK;
//-------------------------------------------

require('kit/dom/parseDOM');

module.exports = function(arg) {
    //+++ 变量定义区 ++++++++++++++++++
    var that = {};
    var _this = {
        objs: {},
        DOM: {},
        DOM_eventFun: {
            button1Fun: function() {
                document.body.removeChild(_this.DOM.confirm);
                document.body.removeChild(_this.DOM.blockDiv);
                $.custEvent.fire(that, 'button1Click');
            },
            button2Fun: function() {
                document.body.removeChild(_this.DOM.confirm);
                document.body.removeChild(_this.DOM.blockDiv);
                $.custEvent.fire(that, 'button2Click');
            }
        }
    };
    //-------------------------------------------


    //+++ Dom的获取方法定义区 ++++++++++++++++++
    var parseDOM = function() {
        var comfirmUI = require('tpl/ui/comfirmHTML');
        html = comfirmUI({
            arg: arg
        });
        var htmlBuilder = $.core.dom.builder(html);
        document.body.appendChild(htmlBuilder.box);
        _this.DOM = $.kit.dom.parseDOM(htmlBuilder.list);
        if (arg.description) {
            _this.DOM.description.innerHTML = arg.description;
        }
    };
    //-------------------------------------------


    //+++ 模块的初始化方法定义区 ++++++++++++++++++
    var initPlugins = function() {};
    //-------------------------------------------

    //+++ 参数的验证方法定义区 ++++++++++++++++++
    var argsCheck = function() {
        if (!arg.title || !arg.button1 || !arg.button2) {
            throw new Error('参数错误！');
        }
    };
    //-------------------------------------------

    //+++ DOM事件绑定方法定义区 ++++++++++++++++++
    var bindDOM = function() {
        $.core.evt.addEvent(_this.DOM.button1, 'click', _this.DOM_eventFun.button1Fun);
        $.core.evt.addEvent(_this.DOM.button2, 'click', _this.DOM_eventFun.button2Fun);
    };
    //-------------------------------------------

    //+++ 自定义事件绑定方法定义区 ++++++++++++++++++
    var bindCustEvt = function() {
        $.custEvent.define(that, ['button1Click', 'button2Click']);
    };
    //-------------------------------------------

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
            $.removeEvent(_this.DOM.button1, 'click', _this.DOM_eventFun.button1Fun);
            $.removeEvent(_this.DOM.button2, 'click', _this.DOM_eventFun.button2Fun);
            _this = null;
        }


    };

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