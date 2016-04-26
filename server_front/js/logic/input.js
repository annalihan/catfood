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
steel.d("common/trans/login", ["kit/io/inter"],function(require, exports, module) {
/**
 * 帐号设置操作接口管理
 * @author gaoyuan3@staff.sina.com.cn
 *
 */
require("kit/io/inter");
STK.register('common.trans.login', function ($) {
    var t = $.kit.io.inter();
    var g = t.register;

    //登录请求
    g('login', {'url': '/aj/login/login', 'method': 'get'});
    //注销请求
    g('loginout',{'url': '/aj/login/loginout', 'method': 'get'});
    return t;
});
});
steel.d("logic/input", ["kit/dom/parseDOM","common/trans/login"],function(require, exports, module) {
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
});