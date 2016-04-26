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
steel.d("tpl/runtime", [],function(require, exports, module) {
//'use strict';

/**
 * Merge two attribute objects giving precedence
 * to values in object `b`. Classes are special-cased
 * allowing for arrays and merging/joining appropriately
 * resulting in a string.
 *
 * @param {Object} a
 * @param {Object} b
 * @return {Object} a
 * @api private
 */
 //--------------迁移from es5-shim------ Start ------------------------

var ObjectPrototype = Object.prototype;
var StringPrototype = String.prototype;
var FunctionPrototype = Function.prototype;
var ArrayPrototype = Array.prototype;
var hasOwnProperty = ObjectPrototype.hasOwnProperty;
var array_slice = ArrayPrototype.slice;

var to_string = ObjectPrototype.toString;
var call = FunctionPrototype.call;

var hasDontEnumBug = !({'toString': null}).propertyIsEnumerable('toString');
var hasProtoEnumBug = function () {}.propertyIsEnumerable('prototype');
var dontEnums = [
        'toString',
        'toLocaleString',
        'valueOf',
        'hasOwnProperty',
        'isPrototypeOf',
        'propertyIsEnumerable',
        'constructor'
    ];
var DONT_ENUM_PROPERTIES_LENGTH = dontEnums.length;
var ES = {
    ToObject: function (o) {
        /*jshint eqnull: true */
        if (o == null) { // this matches both null and undefined
            throw new TypeError("can't convert " + o + ' to object');
        }
        return Object(o);
    }
};
var boxedString = Object('a');
var splitString = boxedString[0] !== 'a' || !(0 in boxedString);
var dontEnumsLength = dontEnums.length;
var isFunction = function(val) {
    return to_string.call(val) === '[object Function]';
};

var isArray = ArrayPrototype.isArray ? function(arr) {
    return ArrayPrototype.isArray(arr);
} : function(arr) {
    return '[object Array]' === to_string.call(arr);
}

var isString = function isString(obj) {
    return to_string.call(obj) === '[object String]';
};

var isArguments = function isArguments(value) {
    var str = to_string.call(value);
    var isArgs = str === '[object Arguments]';
    if (!isArgs) {
        isArgs = !isArray(value) &&
          value !== null &&
          typeof value === 'object' &&
          typeof value.length === 'number' &&
          value.length >= 0 &&
          isFunction(value.callee);
    }
    return isArgs;
};

ArrayPrototype.map = ArrayPrototype.map || function(fun /*, thisp */) {
    var object = ES.ToObject(this),
        self = splitString && isString(this) ? this.split('') : object,
        length = self.length >>> 0,
        result = Array(length),
        thisp = arguments[1];

    if (!isFunction(fun)) {
        throw new TypeError(fun + ' is not a function');
    }
    for (var i = 0; i < length; i++) {
        if (i in self) {
            result[i] = fun.call(thisp, self[i], i, object);
        }
    }
    return result;
}

ArrayPrototype.filter = ArrayPrototype.filter ||  function(fun /*, thisp */) {
    var object = ES.ToObject(this),
        self = splitString && isString(this) ? this.split('') : object,
        length = self.length >>> 0,
        result = [],
        value,
        thisp = arguments[1];

    for (var i = 0; i < length; i++) {
        if (i in self) {
            value = self[i];
            if (fun.call(thisp, value, i, object)) {
                result.push(value);
            }
        }
    }
    return result;
}

Object.keys = Object.keys || function(object) {
    if (typeof object !== 'object' && typeof object !== 'function' || object === null) {
        throw new TypeError('Object keys method called on non-object');
    }
    var keys = [];
    for (var name in object) {
        if (hasOwnProperty.call(object, name)) {
            keys.push(name);
        }
    }
    if (hasDontEnumBug) {
        var i = 0;
        while (i < DONT_ENUM_PROPERTIES_LENGTH) {
            var dontEnumProperty = dontEnums[i];
            if (hasOwnProperty.call(object, dontEnumProperty)) {
                keys.push(dontEnumProperty);
            }
            i++;
        }
    }
    return keys;
}

//--------------迁移from es5-shim------ End ------------------------

exports.merge = function merge(a, b) {
    if (arguments.length === 1) {
        var attrs = a[0];
        for (var i = 1; i < a.length; i++) {
            attrs = merge(attrs, a[i]);
        }
        return attrs;
    }
    var ac = a['class'];
    var bc = b['class'];

    if (ac || bc) {
        ac = ac || [];
        bc = bc || [];
        if (!isArray(ac)) ac = [ac];
        if (!isArray(bc)) bc = [bc];
        a['class'] = ac.concat(bc).filter(nulls);
    }

    for (var key in b) {
        if (key != 'class') {
            a[key] = b[key];
        }
    }

    return a;
};

function nulls(val) {
    return val != null && val !== '';
}

exports.joinClasses = joinClasses;
function joinClasses(val) {
    return isArray(val) ? val.map(joinClasses).filter(nulls).join(' ') : val;
}

exports.cls = function cls(classes, escaped) {
    var buf = [];
    for (var i = 0; i < classes.length; i++) {
        if (escaped && escaped[i]) {
            buf.push(exports.escape(joinClasses([classes[i]])));
        } else {
            buf.push(joinClasses(classes[i]));
        }
    }
    var text = joinClasses(buf);
    if (text.length) {
        return ' class="' + text + '"';
    } else {
        return '';
    }
};

exports.attr = function attr(key, val, escaped, terse) {
    if ('boolean' == typeof val || null == val) {
        if (val) {
            return ' ' + (terse ? key : key + '="' + key + '"');
        } else {
            return '';
        }
    } else if (0 == key.indexOf('data') && 'string' != typeof val) {
        return ' ' + key + "='" + jSON.stringify(val).replace(/'/g, '&apos;') + "'";
    } else if (escaped) {
        return ' ' + key + '="' + exports.escape(val) + '"';
    } else {
        return ' ' + key + '="' + val + '"';
    }
};

exports.attrs = function attrs(obj, terse){
    var buf = [];

    var keys = Object.keys(obj);

    if (keys.length) {
        for (var i = 0; i < keys.length; ++i) {
            var key = keys[i]
                , val = obj[key];

            if ('class' == key) {
                if (val = joinClasses(val)) {
                    buf.push(' ' + key + '="' + val + '"');
                }
            } else {
                buf.push(exports.attr(key, val, false, terse));
            }
        }
    }

    return buf.join('');
};

exports.escape = function escape(html){
    var result = String(html)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
    if (result === '' + html) return html;
    else return result;
};

exports.rethrow = function rethrow(err, filename, lineno, str){
    if (!(err instanceof Error)) throw err;
    if ((typeof window != 'undefined' || !filename) && !str) {
        err.message += ' on line ' + lineno;
        throw err;
    }
    try {
        str = str || _dereq_('fs').readFileSync(filename, 'utf8')
    } catch (ex) {
        rethrow(err, null, lineno)
    }
    var context = 3
        , lines = str.split('\n')
        , start = Math.max(lineno - context, 0)
        , end = Math.min(lines.length, lineno + context);

    var context = lines.slice(start, end).map(function(line, i){
        var curr = i + start + 1;
        return (curr == lineno ? '  > ' : '    ')
            + curr
            + '| '
            + line;
    }).join('\n');
    err.path = filename;
    err.message = (filename || 'Jade') + ':' + lineno
        + '\n' + context + '\n\n' + err.message;
    throw err;
};
});
steel.d("tpl/ui/comfirmHTML", ["tpl/runtime"],function(require, exports, module) {
var jade = require('tpl/runtime');
var undefined = void 0;
module.exports = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;
;var locals_for_with = (locals || {});(function (arg) {
buf.push("<div node-type=\"blockDiv\" class=\"blockDiv\"></div><div node-type=\"confirm\" class=\"confirm\"><table cellspacing=\"1\"><tr class=\"confirmMsg\"><td rowspan=\"2\" colspan=\"2\"><p class=\"title\">" + (jade.escape((jade_interp = arg.title) == null ? '' : jade_interp)) + "</p>");
if(arg.description) {
{
buf.push("<p node-type=\"description\" class=\"description\"></p>");
}
}
buf.push("</td></tr><tr></tr><tr class=\"operator\"><td node-type=\"button1\">" + (jade.escape((jade_interp = arg.button1) == null ? '' : jade_interp)) + "</td><td node-type=\"button2\">" + (jade.escape((jade_interp = arg.button2) == null ? '' : jade_interp)) + "</td></tr></table></div>");}.call(this,"arg" in locals_for_with?locals_for_with.arg:typeof arg!=="undefined"?arg:undefined));;return buf.join("");
};
});
steel.d("ui/confirm", ["kit/dom/parseDOM","tpl/ui/comfirmHTML"],function(require, exports, module) {
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
steel.d("common/channel/onBorrow", ["common/listener"],function(require, exports, module) {
require('common/listener');

module.exports = STK.common.listener.define('common.channel.onBorrow', 'borrow');
});
steel.d("logic/header", ["kit/dom/parseDOM","common/trans/login","../ui/confirm","common/channel/onBorrow"],function(require, exports, module) {
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
});