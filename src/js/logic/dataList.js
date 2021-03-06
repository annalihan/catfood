//-------------------------------------------

require("common/trans/operate");
require('kit/dom/parseDOM');

module.exports = function(node) {
    //+++ 常量定义区 ++++++++++++++++++
    var $ = STK;

    //+++ 变量定义区 ++++++++++++++++++
    var scrollTop = document.body.scrollTop;
    var scrollHeight = document.body.scrollHeight;
    var clientHeight = document.body.clientHeight;
    var onSearch = require('common/channel/onSearch'); //search事件
    var onBorrow = require('common/channel/onBorrow'); //借书事件
    var dataListTpl = require('tpl/common/dataList'); //数据列表
    var confirmUI = require('ui/confirm'); //弹出框ui
    var data = {
        page: 1,
        size: 10,
        key: ''
    };
    var that = {};
    var _this = {
        DOM: {}, //节点容器
        objs: {}, //组件容器
        DOM_eventFun: { //DOM事件行为容器
            scrollFun: function() { //向下滚动
                var scrollTop = document.body.scrollTop;
                var scrollHeight = document.body.scrollHeight;
                var clientHeight = document.body.clientHeight;
                if (scrollTop + clientHeight === scrollHeight) {
                    data.page++;
                    _this.FUNS.scrollDataList();
                } else {
                    _this.DOM.footer.innerHTML = '<span>努力加载中...</span><img src="http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif"/>';
                }
            },
            operatorClick: function(spec) { //点击操作（借阅/还书）
                var arg = {};
                switch (spec.data.book_status) {
                    case '0':
                        arg = {
                            title: '确认借阅？',
                            description: '（最大借阅数3本）',
                            button1: '取消',
                            button2: '确认'
                        };
                        if(!$CONFIG.username) {
                            steel.router.push('/catfood/login');
                        } else {
                            _this.objs.objConfirm = confirmUI(arg);
                            $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.borrow, spec);
                        }
                        break;
                    case '2':
                        arg = {
                            title: '确认还书？',
                            button1: '取消',
                            button2: '确认'
                        };
                        _this.objs.objConfirm = confirmUI(arg);
                        $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.give, spec);
                        break;
                }
            },
            goTop: function() { //返回顶部
                window.scrollTo(0, 0);
            }
        },
        bindCustEvtFuns: { //自定义事件回调函数
            borrow: function(spec) { //确认借书
                /*在回调函数中销毁组件*/
                $.custEvent.remove(_this.objs.objConfirm);
                _this.objs.objConfirm.destroy();
                $.common.trans.operate.getTrans('borrow', {
                    'onSuccess': function(data) {
                        if (data.data.code === 0) {
                            onBorrow.fire('borrow');
                            var parentNode = spec.data.el.parentNode;
                            parentNode.removeChild(spec.data.el);
                            var applyHtml = '<a class="bookOperatorReturn bookOperator" href="javascript:void(0);" ' + 'action-type="operatorClick" action-data="book_id=' + spec.data.book_id + '&book_status=' + 2 + '&detail_call_number=' + spec.data.detail_call_number + '">还</a>';
                            $.addHTML(parentNode, applyHtml);
                            _this.FUNS.applyBookSuccess();
                        } else if (data.data.code === -1) {
                            arg = {
                                title: '借书已达上限！',
                                button1: '取消',
                                button2: '去还书'
                            };
                            _this.objs.objConfirm = confirmUI(arg);
                            $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.look, spec);
                        }
                    },
                    'onError': null,
                    'onFail': null,
                }).request({
                    book_id: spec.data.data.book_id,
                    detail_call_number: spec.data.data.detail_call_number,
                });
            },
            give: function(spec) { //确认还书
                /*在回调函数中销毁组件*/
                $.custEvent.remove(_this.objs.objConfirm);
                _this.objs.objConfirm.destroy();
                $.common.trans.operate.getTrans('give', {
                    'onSuccess': function(data) {
                        if (data.data.code === 0) {
                            var parentNode = spec.data.el.parentNode;
                            parentNode.removeChild(spec.data.el);
                            var tryHtml = '<div class="bookOperatorTry bookOperator">审</div>';
                            $.addHTML(parentNode, tryHtml);
                            _this.FUNS.backBookSuccess();
                        }
                    },
                    'onError': null
                }).request({
                    book_id: spec.data.data.book_id,
                    detail_call_number: spec.data.data.detail_call_number
                });
            },
            look: function() { //点击查看
                /*在回调函数中销毁组件*/
                $.custEvent.remove(_this.objs.objConfirm);
                _this.objs.objConfirm.destroy();
                
                steel.router.push('/catfood/mybooklist');//相当于window.location
            }
        },
        bindListenerFuns: { //广播事件回调函数
            searchClick: function(searchText) { //点击搜索
                var html = '<div style="text-align: center;margin-top:7em;height:40em;">' +
                    '<span style="display:inline-block;font-size:1.5em;">努力搜索中...</span>' +
                    ' <img src="http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif" style="display:inline-block;width:1.5em;"/></div>';
                node.innerHTML = html;
                data.key = searchText;
                _this.FUNS.ajSearch();
            },
            cancelSearch: function() { //取消搜索
                data.key = '';
                _this.FUNS.ajSearch();
            }
        },
        FUNS: { //非事件回调函数
            applyBookSuccess: function() { //借书成功
                $.custEvent.remove(_this.objs.objConfirm);
                _this.objs.objConfirm.destroy();
                var arg = {
                    title: '借阅成功!',
                    description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png">按钮查看',
                    button1: '取消',
                    button2: '立即查看'
                };
                _this.objs.objConfirm = confirmUI(arg);
                $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.look);
            },
            backBookSuccess: function() { //还书成功
                var arg = {
                    title: '审核中!',
                    description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png"/>按钮查看',
                    button1: '取消',
                    button2: '立即查看',
                };
                _this.objs.objConfirm = confirmUI(arg);
                $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.look);
            },
            scrollDataList: function() { //滚动到最下触发方法
                _this.DOM.footer.innerHTML = '<span>努力加载中...</span><img src="http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif"/>';
                var url = '';
                if (href == 'bookList') {
                    $.common.trans.operate.getTrans('scroll_booklist', {
                        'onSuccess': function(data) {
                            var html = _this.FUNS.getDataLis(data.data);
                            var a = $.insertHTML(_this.DOM.footer, html, 'beforebegin');
                        },
                        'onError': function() {
                            _this.DOM.footer.innerHTML = '<a href="javascript:void(0);"><span>出错了，请重试</span><img  src="http://js.catfood.wap.grid.sina.com.cn/img/refresh.png"/></a>';
                            $.addEvent(_this.DOM.footer, 'click', _this.FUNS.scrollDataList);
                        }
                    }).request(data);
                } else {
                    $.common.trans.operate.getTrans('scroll_mybooklist', {
                        'onSuccess': function(data) {
                            var html = _this.FUNS.getDataLis(data.data);
                            $.insertHTML(_this.DOM.footer, html, 'beforebegin');
                        },
                        'onError': function() {
                            _this.DOM.footer.innerHTML = '<a href="javascript:void(0);"><span>出错了，请重试</span><img  src="http://js.catfood.wap.grid.sina.com.cn/img/refresh.png"/></a>';
                            $.addEvent(_this.DOM.footer, 'click', _this.FUNS.scrollDataList);
                        }
                    }).request(data);
                }
            },
            getDataLis: function(dataBookList) { //将请求的数据拼装成图书列表html
                var html = '';
                if (dataBookList.bookList.length === 0) {
                    if (href == 'bookList') {
                        _this.DOM.footer.innerHTML = '<span>暂无更多了，欢迎贡献</span>';
                    } else {
                        _this.DOM.footer.innerHTML = '<a href="http://catfood.wap.grid.sina.com.cn/catfood/bookList"><span>暂无更多了，去借书</span><img class="toBorrow" src="http://js.catfood.wap.grid.sina.com.cn/img/angle.png"/></a>';
                    }
                    return html;
                }
                var dataListLisTpl = require('tpl/common/dataListLis');
                html = dataListLisTpl(dataBookList);
                return html;
            },
            ajSearch: function() {
                data.page = 1;
                $.common.trans.operate.getTrans('search', {
                    'onSuccess': function(data) {
                        var htmlData = dataListTpl(data.data);
                        node.innerHTML = htmlData;
                    },
                    'onError': null
                }).request(data);
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
        $.addEvent(window, 'scroll', _this.DOM_eventFun.scrollFun);
        _this.objs.delegate = $.delegatedEvent(node);
        _this.objs.delegate.add('operatorClick', 'click', _this.DOM_eventFun.operatorClick);
        $.addEvent(_this.DOM.goTop, 'click', _this.DOM_eventFun.goTop);
    };
    //-------------------------------------------


    //+++ 自定义事件绑定方法定义区 ++++++++++++++++++
    var bindCustEvt = function() {};
    //-------------------------------------------


    //+++ 广播事件绑定方法定义区 ++++++++++++++++++
    var bindListener = function() {
        onSearch.register('search', _this.bindListenerFuns.searchClick);
        onSearch.register('cancel', _this.bindListenerFuns.cancelSearch);
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
            $.removeEvent(window, 'scroll', _this.DOM_eventFun.scrollFun);
            _this.objs.delegate.remove('operatorClick', 'click');
            onSearch.remove('search', _this.bindListenerFuns.searchClick);
            onSearch.remove('cancel', _this.bindListenerFuns.cancelSearch);
            onBorrow.remove('borrow', _this.bindListenerFuns.borrow);
            $.removeEvent(_this.DOM.footer, 'click', _this.FUNS.scrollDataList);
            _this = null;
        }
    };

    var initData = function() {
        var pathname = window.location.pathname.split('/');
        href = pathname[pathname.length - 1];

        var search = window.location.search;
        if (search) {
            var query = $.queryToJson(window.location.search.split('?')[1]);
            if (query.book_type) {
                data.book_type = query.book_type;
            }
        }
    };
    //-------------------------------------------

    //+++ 组件的初始化方法定义区 ++++++++++++++++++
    var init = function() {
        argsCheck();
        parseDOM();
        initData();
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
