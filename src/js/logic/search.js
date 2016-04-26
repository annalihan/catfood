//-------------------------------------------

require('kit/dom/parseDOM');

module.exports = function(node) {
    //+++ 常量定义区 ++++++++++++++++++
    var $ = STK;


    //+++ 变量定义区 ++++++++++++++++++
    var onSearch = require('common/channel/onSearch');
    var that = {};
    var _this = {
        DOM: {}, //节点容器
        objs: {}, //组件容器
        DOM_eventFun: { //DOM事件行为容器
            searchInputClick: function() {
                $.setStyle(_this.DOM.searchInput, 'display', 'none');
                $.setStyle(_this.DOM.searchTextReal, 'display', 'block');
                _this.DOM.searchInputReal.focus();
            },
            searchInputChange: function() {
                var searchText = _this.DOM.searchInputReal.value;
                if (searchText) {
                    _this.DOM.searchButton.innerHTML = '搜索';
                } else {
                    _this.DOM.searchButton.innerHTML = '取消';
                }

            },
            searchButtonClick: function() {
                var searchText = _this.DOM.searchInputReal.value;
                if (_this.DOM.searchButton.innerHTML === '搜索') {
                    onSearch.fire('search', [searchText]);
                    _this.DOM.searchButton.innerHTML = '取消';
                } else {
                    $.setStyle(_this.DOM.searchTextReal, 'display', 'none');
                    $.setStyle(_this.DOM.searchInput, 'display', 'block');
                    _this.DOM.searchInputReal.value = '';

                    onSearch.fire('cancel', []);
                }

            }
        },
        bindCustEvtFuns: { //自定义事件回调函数

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
        $.addEvent(_this.DOM.searchInput, 'click', _this.DOM_eventFun.searchInputClick);
        $.addEvent(_this.DOM.searchInputReal, 'input', _this.DOM_eventFun.searchInputChange);
        $.addEvent(_this.DOM.searchButton, 'click', _this.DOM_eventFun.searchButtonClick);
    };
    //-------------------------------------------


    //+++ 自定义事件绑定方法定义区 ++++++++++++++++++
    var bindCustEvt = function() {};
    //-------------------------------------------


    //+++ 广播事件绑定方法定义区 ++++++++++++++++++
    var bindListener = function() {

    };
    //-------------------------------------------


    //+++ 组件销毁方法的定义区 ++++++++++++++++++
    var destroy = function() {
        if (_this) {
            $.foreach(_this.objs, function(o) {
                if (o.destroy) {
                    o.destroy();
                }
            });
            $.removeEvent(_this.DOM.searchInput, 'click', _this.DOM_eventFun.searchInputClick);
            $.removeEvent(_this.DOM.searchInputReal, 'input', _this.DOM_eventFun.searchInputChange);
            $.removeEvent(_this.DOM.searchButton, 'click', _this.DOM_eventFun.searchButtonClick);
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