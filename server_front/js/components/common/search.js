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
steel.d("components/common/search", ["common/channel/onSearch"],function(require, exports, module) {
var onSearch = require('common/channel/onSearch');
var $ = STK;

module.exports = React.createClass({displayName: "exports",
    getInitialState:function(){
        return {
            searching: false,
            key:''
        }
    },
    componentDidMount:function(){
    },
    DOM_eventFun: { //DOM事件行为容器
        searchInputClick: function() {
            this.setState({searching:true});
            // this.refs.searchInputReal.focus();
        },
        searchInputChange: function(e) {
            var searchText = e.target.value;
            this.setState({key:searchText});
        },
        searchButtonClick: function() {
            var searchText = this.state.key;
            if (searchText) {
                onSearch.fire('search', [searchText]);
                this.setState({key:''});
            } else {
                this.setState({
                    searching:false
                });
                onSearch.fire('cancel', []);
            }

        }
    },
    render: function(){
        var searchTextInit = "查找我想借的书";
        return (
            React.createElement("div", {className: "search"}, 
                React.createElement("div", {className: "searchInput", 
                    style: {display:(this.state.searching?"none":'block')}, 
                    onClick: this.DOM_eventFun.searchInputClick.bind(this)}, 
                    React.createElement("img", {className: "searchInputImg", src: "http://js.catfood.wap.grid.sina.com.cn/img/search.png"}), 
                    React.createElement("p", {className: "placeholder"}, searchTextInit)
                ), 
                React.createElement("div", {className: "searchTextReal", 
                    style: {display:(this.state.searching?'block':'none')}}, 
                    React.createElement("img", {className: "searchTextRealImg", src: "http://js.catfood.wap.grid.sina.com.cn/img/search.png"}), 
                    React.createElement("div", {className: "searchInputBorder"}, 
                        React.createElement("input", {className: "searchInputReal", 
                            type: "text", maxlength: "30", ref: "searchInputReal", 
                            value: this.state.key, 
                            onChange: this.DOM_eventFun.searchInputChange.bind(this)})
                    ), 
                    React.createElement("a", {className: "searchButton", href: "javascript:void(0);", 
                        onClick: this.DOM_eventFun.searchButtonClick.bind(this)}, 
                        this.state.key?"确定":"取消"
                    )
                )
            )

        );
    }
});


});