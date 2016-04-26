steel.d("kit/dom/parseDOM", [],function(require, exports, module) {
/**
 * kit.dom.parseDOM
 * 对 core.dom.builder 返回的列表进行过滤
 * 对传入的list遍历一次，每个成员数组，如果只包含一个dom就直接返回该dom；如果包含多个dom则不处理，直接返回数组
 * @id STK.kit.dom.parseDOM
 * @author WK | wukan@staff.sina.com.cn
 * @example
	var buffer = STK.core.dom.builder($.E("example"));
	buffer.list = STK.kit.dom.parseDOM(buffer.list);
 */
STK.register('kit.dom.parseDOM', function($){
	return function(list){
		for(var a in list){
			if(list[a] && (list[a].length == 1)){
				list[a] = list[a][0];
			}
		}
		return list;
	};
});
});
steel.d("common/listener", [],function(require, exports, module) {
/**
 * 进一步封装core.util.listener, 增加白名单策略, 避免在项目中, 广播混乱
* @author FlashSoft | fangchao@staff.sina.com.cn
* @changelog WK | wukan@ move to common folder
* @changelog Finrila | wangzheng4@ add data_cache_get 
 */
STK.register('common.listener', function($){
	var listenerList = {};
	var that = {};
	/**
	 * 创建广播白名单
	 * @param {String} sChannel
	 * @param {Array} aEventList
	 */
	that.define = function(sChannel, aEventList){
		if (listenerList[sChannel] != null) {
			throw 'common.listener.define: 频道已被占用';
		}
		listenerList[sChannel] = aEventList;
		
		var ret = {};
		ret.register = function(sEventType, fCallBack){
			if (listenerList[sChannel] == null) {
				throw 'common.listener.define: 频道未定义';
			}
			$.listener.register(sChannel, sEventType, fCallBack);
		};
		ret.fire = function(sEventType,oData){
			if (listenerList[sChannel] == null) {
				throw 'commonlistener.define: 频道未定义';
			}
			$.listener.fire(sChannel, sEventType, oData);
		};
		ret.remove = function(sEventType, fCallBack){
			$.listener.remove(sChannel, sEventType, fCallBack);
		};
		
		/**
		 * 使用者可以在任意时刻获取到listener缓存的(某频道+事件)最后一次触发(fire)的数据；如果没有fire过为undefined;
		 * @method cache 
		 * @param {String} sEventType
		 */
		ret.cache = function(sEventType){
			return $.listener.cache(sChannel, sEventType);
		};
		return ret;
	};
	
	// that.register = function(sChannel, sEventType, fCallBack){
	// 		if (listenerList[sChannel] == null) {
	// 			throw 'common.listener.define: 频道未定义';
	// 			
	// 		}
	// 		$.core.util.listener.register(sChannel, sEventType, fCallBack);
	// 	};
	// 	that.fire = function(sChannel, sEventType, oData){
	// 		if (listenerList[sChannel] == null) {
	// 			throw 'commonlistener.define: 频道未定义';
	// 		}
	// 		$.core.util.listener.fire(sChannel, sEventType, oData);
	// 	};
	// 	that.conn = function(){
	// 	
	// 	};
	return that;
});

});
steel.d("common/channel/onSearch", ["common/listener"],function(require, exports, module) {
require('common/listener');

module.exports = STK.common.listener.define('common.channel.onSearch', ['search','cancel'] );
});
steel.d("logic/search", ["kit/dom/parseDOM","common/channel/onSearch"],function(require, exports, module) {
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
});