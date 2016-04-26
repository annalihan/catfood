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