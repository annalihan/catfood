steel.d("components/common/header", [],function(require, exports, module) {
module.exports= React.createClass({displayName: "exports",
    render: function(){
        function build(){
            var isLogin = $CONFIG.username;
            if(isLogin){
                return(
                    React.createElement("div", {className: "headerTop", "node-type": "headerTop"}, 
                        React.createElement("a", {href: "mybooklist"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/mybook.png", "node-type": "mybook", className: "mybook"}), 
                            React.createElement("i", {className: "circlePoint", "node-type": "circlePoint", style: {display:"none"}})
                        ), 
                        React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/back.png", "node-type": "back", className: "back"}), 
                        React.createElement("a", {href: "index"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif", className: "book_angle"})
                        ), 
                        React.createElement("div", {className: "user", "node-type": "user"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/log.png", "node-type": "user_image"}), 
                            React.createElement("span", {"node-type": "name", className: "name"}, $CONFIG.username)
                        )
                    )
                );
            }
            else{
                return(
                    React.createElement("div", {className: "headerTop", "node-type": "headerTop"}, 
                        React.createElement("a", {href: "mybooklist", style: {display:"none"}}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/mybook.png", "node-type": "mybook", className: "mybook"})
                        ), 
                        React.createElement("a", {href: "index"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif", className: "book_angle"})
                        )
                    )
                );
            }
        }
        
        return build();
    }
})

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
steel.d("components/common/search", ["common/channel/onSearch"],function(require, exports, module) {
var onSearch = require('common/channel/onSearch');
var $ = STK;

module.exports = React.createClass({displayName: "exports",
    componentDidMount:function(){
    },
    DOM_eventFun: { //DOM事件行为容器
        searchInputClick: function() {
            $.setStyle(this.refs.searchInput, 'display', 'none');
            $.setStyle(this.refs.searchTextReal, 'display', 'block');
            this.refs.searchInputReal.focus();
        },
        searchInputChange: function() {
            var searchText = this.refs.searchInputReal.value;
            if (searchText) {
                this.refs.searchButton.innerHTML = '搜索';
            } else {
                this.refs.searchButton.innerHTML = '取消';
            }

        },
        searchButtonClick: function() {
            var searchText = this.refs.searchInputReal.value;
            if (this.refs.searchButton.innerHTML === '搜索') {
                onSearch.fire('search', [searchText]);
                this.refs.searchButton.innerHTML = '取消';
            } else {
                $.setStyle(this.refs.searchTextReal, 'display', 'none');
                $.setStyle(this.refs.searchInput, 'display', 'block');
                this.refs.searchInputReal.value = '';

                onSearch.fire('cancel', []);
            }

        }
    },
    render: function(){
        var searchTextInit = "查找我想借的书";
        return (
            React.createElement("div", {className: "search"}, 
                React.createElement("div", {className: "searchInput", ref: "searchInput", 
                    onClick: this.DOM_eventFun.searchInputClick.bind(this)}, 
                    React.createElement("img", {className: "searchInputImg", src: "http://js.catfood.wap.grid.sina.com.cn/img/search.png"}), 
                    React.createElement("p", {className: "placeholder"}, searchTextInit)
                ), 
                React.createElement("div", {className: "searchTextReal", ref: "searchTextReal", style: {display:"none"}}, 
                    React.createElement("img", {className: "searchTextRealImg", src: "http://js.catfood.wap.grid.sina.com.cn/img/search.png"}), 
                    React.createElement("div", {className: "searchInputBorder"}, 
                        React.createElement("input", {className: "searchInputReal", type: "text", ref: "searchInputReal", maxlength: "30", 
                            onChange: this.DOM_eventFun.searchInputChange.bind(this)})
                    ), 
                    React.createElement("a", {className: "searchButton", href: "javascript:void(0);", ref: "searchButton", 
                        onClick: this.DOM_eventFun.searchButtonClick.bind(this)}, " 取消 ")
                )
            )

        );
    }
});
        

});
steel.d("kit/extra/merge", [],function(require, exports, module) {
/**
* 合并参数
* @id STK.core.obj.merge
* @alias STK.core.obj.merge
* @param {Object} a 第一个对象
* @param {Object} b 第二个对象
* @author WK | wukan@staff.sina.com.cn
* @example
* var a={a:1,b:2,d:6}
* var b={a:2,b:3,c:4}
* var c=STK.core.obj.merge(a, b);//以后传入的为准:a:2,b:3,c:4,d:6
*/
STK.register('kit.extra.merge', function($){
	return function(a,b){
		var buf = {};
		for (var k in a) {
			buf[k] = a[k];
		}
		for (var k in b) {
			buf[k] = b[k];
		}
		return buf;
	};
});

});
steel.d("kit/io/orignAjax", [],function(require, exports, module) {
STK.register("kit.io.orignAjax", function($) {
    return function(oOpts) {
        var opts = $.core.obj.parseParam({url: "",charset: "UTF-8",timeout: 30 * 1000,args: {},onComplete: null,onTimeout: $.core.func.empty,uniqueID: null,onFail: $.core.func.empty,method: "get",asynchronous: true,header: {},isEncode: false,responseType: "json"}, oOpts);
        if (opts.url == "") {
            throw "ajax need url in parameters object"
        }
        var tm;
        var trans = $.core.io.getXHR();
        var cback = function() {
            if (trans.readyState == 4) {
                clearTimeout(tm);
                var data = "";
                if (opts.responseType === "xml") {
                    data = trans.responseXML
                } else {
                    if (opts.responseType === "text") {
                        data = trans.responseText
                    } else {
                        try {
                            if (trans.responseText && typeof trans.responseText === "string") {
                                data = eval("(" + trans.responseText + ")")
                            } else {
                                data = {}
                            }
                        } catch (exp) {
                            data = opts.url + "return error : data error"
                        }
                    }
                }
                if (trans.status == 200) {
                    if (opts.onComplete != null) {
                        opts.onComplete(data)
                    }
                } else {
                    if (trans.status == 0) {
                    } else {
                        if (opts.onFail != null) {
                            opts.onFail(data, trans)
                        }
                    }
                }
            } else {
                if (opts.onTraning != null) {
                    opts.onTraning(trans)
                }
            }
        };
        trans.onreadystatechange = cback;
        if (!opts.header["Content-Type"]) {
            opts.header["Content-Type"] = "application/x-www-form-urlencoded"
        }
        if (!opts.header["X-Requested-With"]) {
            opts.header["X-Requested-With"] = "XMLHttpRequest"
        }
        if (opts.method.toLocaleLowerCase() == "get") {
            var url = $.core.util.URL(opts.url, {isEncodeQuery: opts.isEncode});
            url.setParams(opts.args);
            url.setParam("__rnd", new Date().valueOf());
            trans.open(opts.method, url, opts.asynchronous);
            try {
                for (var k in opts.header) {
                    trans.setRequestHeader(k, opts.header[k])
                }
            } catch (exp) {
            }
            trans.send("")
        } else {
            trans.open(opts.method, opts.url, opts.asynchronous);
            try {
                for (var k in opts.header) {
                    trans.setRequestHeader(k, opts.header[k])
                }
            } catch (exp) {
            }
            trans.send($.core.json.jsonToQuery(opts.args, opts.isEncode))
        }
        if (opts.timeout) {
            tm = setTimeout(function() {
                try {
                    trans.abort()
                } catch (exp) {
                }
                opts.onTimeout({}, trans);
                opts.onFail({"code":"100001","msg":"Request timeout！"})
            }, opts.timeout)
        }
        return trans
    }
});
});
steel.d("kit/io/ajax", ["kit/extra/merge","kit/io/orignAjax"],function(require, exports, module) {
require("kit/extra/merge");
require("kit/io/orignAjax");

STK.register('kit.io.ajax', function($) {
	/*** url			: 
	 -----------args-------------
	 * onComplete	: 
	 * onTraning	: 
	 * onFail		: 
	 * method		: 
	 * asynchronous	: 
	 * contentType	: 
	 * encoding		: 
	 * responseType	: 
	 * timeout		: 
	 * 
	 */
	return function(args){
		var conf, that, queue, current, lock, complete, fail;
		
		complete = function(res){
			lock = false;
			args.onComplete(res, conf['args']);
			setTimeout(nextRequest,0);//跳出递归
		};
		
		fail = function(res){
			lock = false;
			args.onFail(res, conf['args']);
			setTimeout(nextRequest,0);//跳出递归
		};
		
		queue = [];
		current = null;
		lock = false;
		
		conf = $.parseParam({
			'url'			: '',
			'method'		: 'get',
			'responseType'	: 'json',
			'timeout'		: 30 * 1000,
			'onTraning'		: $.funcEmpty,
			'isEncode' 		: true
		}, args);
		
		conf['onComplete'] = complete;
		conf['onFail'] = fail;
		
		var nextRequest = function(){
			if(!queue.length){
				return ;
			}
			if(lock === true){
				return;
			}
			lock = true;
			conf.args = queue.shift();
			current = $.kit.io.orignAjax(conf);
		};
		
		var abort = function(params){
			while(queue.length){
				queue.shift();
			}
			lock = false;
			if(current){
				try{
					current.abort();
				}catch(exp){
				
				}
			}
			current = null;
		};
		
		that = {};
		
		that.request = function(params){
			if(!params){
				params = {};
			}
			if(args['noQueue']){
				abort();
			}

			//page化，为区分原有ajax请求，在$CONFIG中增加pageextra字段，默认将这里的数据当作请求参数。add by wenxiu3 2013-07-19.
			if(window.$CONFIG && $CONFIG['pageextra']){
				var extraObj = $.queryToJson($CONFIG['pageextra']);
				if (extraObj) {
					for(var key in extraObj){
						params[key] = extraObj[key];
					}
				};
			}


			if(!args['uniqueRequest'] || !current){
				queue.push(params);
				params['_t'] = 0;
				nextRequest();
			}
		};
		
		that.abort = abort;
		return that;
	};
});
});
steel.d("kit/io/jsonp", ["kit/extra/merge"],function(require, exports, module) {
require("kit/extra/merge");

STK.register('kit.io.jsonp', function($) {
	/*** url			: 
	 -----------args-------------
	 * onComplete	: 
	 * onTraning	: 
	 * onFail		: 
	 * method		: 
	 * asynchronous	: 
	 * contentType	: 
	 * encoding		: 
	 * responseType	: 
	 * timeout		: 
	 * 
	 */
	return function(args){
		var conf, that, queue, current, lock;
		
		conf = $.parseParam({
			'url'			: '',
			'method'		: 'get',
			'responseType'	: 'json',
			'varkey'		: '_v',
			'timeout'		: 30 * 1000,
			'onComplete'	: $.funcEmpty,
			'onTraning'		: $.funcEmpty,
			'onFail'		: $.funcEmpty,
			'isEncode' 		: true
		}, args);
		queue = [];
		current = {};
		lock = false;
		
		var nextRequest = function(){
			if(!queue.length){
				return ;
			}
			if(lock === true){
				return;
			}
			lock = true;
			
			
			current.args = queue.shift();
			current.onComplete = function(res){
				lock = false;
				conf.onComplete(res,current['args']);
				setTimeout(nextRequest,0);
			};
			current.onFail = function(res){
				lock = false;
				conf.onFail(res);
				setTimeout(nextRequest,0);
			};
			
			$.jsonp($.kit.extra.merge(conf,{
				'args' : current.args,
				'onComplete' : function(res){current.onComplete(res);},
				'onFail' : function(res){try{current.onFail(res);}catch(exp){}}
			}));
		};
		
		that = {};
		
		that.request = function(params){
			params=params || {};
			if(!params.superuid){
				!!$CONFIG['superuid'] && (params.superuid = $CONFIG['superuid']);
			}
			if(!params){
				params = {};
			}
			queue.push(params);
			params['_t'] = 1;
			nextRequest();
		};
		
		that.abort = function(params){
			while(queue.length){
				queue.shift();
			}
			lock = false;
			current = null;
		};
		return that;
	};
});

});
steel.d("kit/io/inter", ["kit/io/ajax","kit/io/jsonp","kit/extra/merge"],function(require, exports, module) {
require("kit/io/ajax");
require("kit/io/jsonp");
require("kit/extra/merge");

STK.register('kit.io.inter',function($){
	return function(){
		var that, argsList, hookList;
		that = {};
		argsList = {};
		hookList = {};
		that.register = function(name,args){
			if(argsList[name] !== undefined){
				throw name + ' interface has been registered';
			}
			argsList[name] = args;
			hookList[name] = {};
		};
		that.hookComplete = function(name,func){
			var key = $.core.util.getUniqueKey();
			hookList[name][key] = func;
			return key;
		};
		that.removeHook = function(name,key){
			if(hookList[name] && hookList[name][key]){
				delete hookList[name][key];
			}
		};
		that.getTrans = function(name, spec){
			var conf = $.kit.extra.merge(argsList[name], spec);
			conf.onComplete = function(req, params){
				try{
					spec.onComplete(req, params);
				}catch(exp){
				
				}
				if(req['code'] === '100000' || req['code'] === 'A00006'){
					try{
						spec.onSuccess(req, params);
					}catch(exp){
						
					}
				}else{
					try{
						if(req['code'] === '100002'){//登陆
							location.href = '/catfood/login';
							return;
						}
						if(req['code'] === '100107'){//加关注
							return;
						}
						spec.onError(req, params);
					}catch(exp){
						// alert("err:"+exp.message);

					}
				}
				for(var k in hookList[name]){
					try{
						hookList[name][k](req, params);
					}catch(exp){

					}
				}
			};
			if(argsList[name]['requestMode'] === 'jsonp'){
				return $.kit.io.jsonp(conf);
			}else if(argsList[name]['requestMode'] === 'ijax'){
				return $.kit.io.ijax(conf);
			}else{
				return $.kit.io.ajax(conf);
			}
		};
		that.request = function(name, spec, args){
			var conf = $.core.json.merge(argsList[name], spec);

			conf.onComplete = function(req, params){
				try{
					spec.onComplete(req, params);
				}catch(exp){

				}
				if(req['code'] === '100000' || req['code'] === 'A00006'){
					try{
						spec.onSuccess(req, params);
					}catch(exp){

					}
				}else{
					try{
						if(req['code'] === '100002'){
							window.location.href=req['data'];
							return;
						}
						spec.onError(req, params);

					}catch(exp){

					}
				}
				for(var k in hookList[name]){
					try{
						hookList[name][k](req, params);
					}catch(exp){

					}
				}
			};
			conf = $.core.obj.cut(conf, ['noqueue']);

			conf.args = args;

			if(argsList[name]['requestMode'] === 'jsonp'){
				return $.jsonp(conf);
			}else if(argsList[name]['requestMode'] === 'ijax'){
				return $.ijax(conf);
			}else{
				return $.ajax(conf);
			}
			return that;
		};
		return that;
	};
});

});
steel.d("common/trans/operate", ["kit/io/inter"],function(require, exports, module) {
/**
 * 帐号设置操作接口管理
 * @author gaoyuan3@staff.sina.com.cn
 *
 */
require("kit/io/inter");
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
});
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
steel.d("common/channel/onBorrow", ["common/listener"],function(require, exports, module) {
require('common/listener');

module.exports = STK.common.listener.define('common.channel.onBorrow', 'borrow');
});
steel.d("components/common/datalist", ["common/trans/operate","kit/dom/parseDOM","common/channel/onSearch","common/channel/onBorrow"],function(require, exports, module) {
var $ = STK;
require("common/trans/operate");
require('kit/dom/parseDOM');
var onSearch = require('common/channel/onSearch'); //search事件
var onBorrow = require('common/channel/onBorrow'); //借书事件

module.exports=React.createClass({displayName: "exports",
    getInitialState: function(){
        return {
            searching:false,
            search: {
                page: 1,
                size: 10,
                key: ''
            },
            bookList:[]
        }
    },
    componentWillMount:function(){
        // this.setState({bookList: this.props.data.bookList});
    },
    componentWillReceiveProps:function(nextProps){
        // this.setState({bookList: nextProps.data.bookList});
    },
    componentDidMount: function(){
        this.scrollDataList();
        onSearch.register('search', this.bindListenerFuns.searchClick.bind(this));
        onSearch.register('cancel', this.bindListenerFuns.cancelSearch.bind(this));
    },
    bindListenerFuns: { //广播事件回调函数
        searchClick: function(searchText) { //点击搜索
            this.setState({searching:true,error:false});
            var data = this.state.search;
            data.key = searchText;            
            this.ajSearch('search',data);
        },
        cancelSearch: function() { //取消搜索
            this.setState({searching:true,error:false});
            var data = this.state.search;
            data.key = '';            
            this.ajSearch('search',data);
        }
    },
    FUNS: { //非事件回调函数
        /*applyBookSuccess: function() { //借书成功
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
        },*/
        
    },
    scrollDataList: function() { //滚动到最下触发方法
        this.setState({loading:true,error:false});
        if (this.props.url == 'booklist') {
            this.ajSearch('scroll_booklist');
            
        } else {
            this.ajSearch('scroll_mybooklist');
        }
    },
    ajSearch: function(api,data) {
        var _this = this;
        if(data){
            this.setState({search:data});                
        }
        $.common.trans.operate.getTrans(api, {
            'onSuccess': function(data) {
                _this.setState({
                    bookList: data.data.bookList,
                    searching: false,
                    loading: false,
                    error: false
                });
            },
            'onError': function(){
                _this.setState({
                    searching: false,
                    loading: false,
                    error: true
                });
            }
        }).request(data);
    },
    build:function(bookList){
        function buildType(book){
            switch(book.book_category){
                case '1':
                    return "编程实践";
                    break;
                case '2':
                    return "架构与设计";
                break;
                case '3':
                    return "思想与领导力";
                    break;
                case '4':
                    return "方法学";
                    break; 
            }
        }
        function buildOpt(book){
            switch(book.book_status){
                case '0':
                    return (
                        React.createElement("a", {className: "bookOperatorBorrow bookOperator", 
                            href: "javascript:void(0);", "action-type": "operatorClick", 
                            "action-data": "book_id={book.book_id}&book_status={book.book_status}&detail_call_number={book.detail_call_number}"}, " 借 ")
                    );
                    break;
                case '1':
                    return (React.createElement("div", {className: "bookOperatorTry bookOperator"}, "审"));
                    break;
                case '3':
                    return (React.createElement("div", {className: "bookOperatorLack bookOperator"}, "缺"));
                    break;
                case '2':
                    return (
                        React.createElement("a", {className: "bookOperatorReturn bookOperator", 
                            href: "javascript:void(0);", 
                            "action-type": "operatorClick", 
                            "action-data": "book_id={book.book_id}&book_status={book.book_status}&detail_call_number={book.detail_call_number}"}, " 还 ")
                    );
                    break;
            }
        }
        var result = [];
        for(var i = 0,len = bookList.length;i < len; i++){
            result.push(React.createElement("li", {className: "bookMsgLi"}, 
                React.createElement("div", {className: "bookData"}, 
                    React.createElement("div", {className: "bookImg"}, 
                        React.createElement("img", {src: bookList[i].book_img})
                    ), 
                    React.createElement("div", {className: "bookMsg"}, 
                        React.createElement("h3", {title: bookList[i].book_name}, " ", bookList[i].book_name, " "), 
                        React.createElement("p", null, bookList[i].book_press), 
                        React.createElement("p", null, " ", bookList[i].book_author), 
                        React.createElement("br", null), 
                        React.createElement("p", {className: "bookType"}, 
                            buildType(bookList[i])
                        )
                    ), 
                    buildOpt(bookList[i])
                )
            ));
        }
        return result;
    },
    buildeFooter:function(){
        if(this.state.loading){
            return (
                React.createElement("footer", {"node-type": "footer"}, 
                    React.createElement("span", null, "努力加载中..."), 
                    React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif"})
                )
            );
        }
        else if(this.state.error){
            return (
                React.createElement("footer", {"node-type": "footer", onClick: this.scrollDataList}, 
                    React.createElement("a", {href: "javascript:void(0);"}, 
                        React.createElement("span", null, "出错了，请重试"), 
                        React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/refresh.png"})
                    )
                )
            );
        }
        else if(this.state.bookList.length==0){
            return (
                React.createElement("footer", {"node-type": "footer"}, 
                    React.createElement("span", null, "暂无更多了，欢迎贡献")
                )
            );
        }
        else{
            return (
                React.createElement("footer", {"node-type": "footer"}, 
                    React.createElement("a", {href: "http://catfood.wap.grid.sina.com.cn/catfood/bookList"}, React.createElement("span", null, "暂无更多了，去借书"), React.createElement("img", {className: "toBorrow", src: "http://js.catfood.wap.grid.sina.com.cn/img/angle.png"}))
                )
            );
        } 
    },

    render: function(){
        return (
            React.createElement("div", null, 
                React.createElement("ul", {className: "dataListContent", "node-type": "dataListContent"}, 
                    React.createElement("div", {style: {textAlign: 'center',marginTop:'7em',height:'40em', display: this.state.searching?'':'none'}}, 
                        React.createElement("span", {style: {display:'inline-block',fontSize:'1.5em'}}, "努力搜索中..."), 
                        React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif", style: {display:'inline-block',width:'1.5em'}})
                    ), 
                    this.build(this.state.bookList)
                ), 
                this.buildeFooter()
            )
        );
    }
});
            

});
steel.d("components/bookList/index/main", ["../../common/header","../../common/search","../../common/datalist"],function(require, exports, module) {
var Header = require('../../common/header');
var Search = require('../../common/search');
var Datalist = require('../../common/datalist');

module.exports = React.createClass({
    displayName: 'BookList',
    render: function(){
        return(
            React.createElement("div", null, 
                React.createElement(Header, null), 
                React.createElement(Search, null), 
                React.createElement(Datalist, {url: "booklist"})
            )
        );
    }
});

});