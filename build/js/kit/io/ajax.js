steel.d("kit/io/ajax",["kit/extra/merge","kit/io/orignAjax"],function(a,b,c){a("kit/extra/merge"),a("kit/io/orignAjax"),STK.register("kit.io.ajax",function(a){return function(b){var c,d,e,f,g,h,i;h=function(a){g=!1,b.onComplete(a,c.args),setTimeout(j,0)},i=function(a){g=!1,b.onFail(a,c.args),setTimeout(j,0)},e=[],f=null,g=!1,c=a.parseParam({url:"",method:"get",responseType:"json",timeout:3e4,onTraning:a.funcEmpty,isEncode:!0},b),c.onComplete=h,c.onFail=i;var j=function(){e.length&&g!==!0&&(g=!0,c.args=e.shift(),f=a.kit.io.orignAjax(c))},k=function(a){for(;e.length;)e.shift();if(g=!1,f)try{f.abort()}catch(b){}f=null};return d={},d.request=function(c){if(c||(c={}),b.noQueue&&k(),window.$CONFIG&&$CONFIG.pageextra){var d=a.queryToJson($CONFIG.pageextra);if(d)for(var g in d)c[g]=d[g]}b.uniqueRequest&&f||(e.push(c),c._t=0,j())},d.abort=k,d}})}),steel.d("kit/extra/merge",[],function(a,b,c){STK.register("kit.extra.merge",function(a){return function(a,b){var c={};for(var d in a)c[d]=a[d];for(var d in b)c[d]=b[d];return c}})}),steel.d("kit/io/orignAjax",[],function(require,exports,module){STK.register("kit.io.orignAjax",function($){return function(oOpts){var opts=$.core.obj.parseParam({url:"",charset:"UTF-8",timeout:3e4,args:{},onComplete:null,onTimeout:$.core.func.empty,uniqueID:null,onFail:$.core.func.empty,method:"get",asynchronous:!0,header:{},isEncode:!1,responseType:"json"},oOpts);if(""==opts.url)throw"ajax need url in parameters object";var tm,trans=$.core.io.getXHR(),cback=function(){if(4==trans.readyState){clearTimeout(tm);var data="";if("xml"===opts.responseType)data=trans.responseXML;else if("text"===opts.responseType)data=trans.responseText;else try{data=trans.responseText&&"string"==typeof trans.responseText?eval("("+trans.responseText+")"):{}}catch(exp){data=opts.url+"return error : data error"}200==trans.status?null!=opts.onComplete&&opts.onComplete(data):0==trans.status||null!=opts.onFail&&opts.onFail(data,trans)}else null!=opts.onTraning&&opts.onTraning(trans)};if(trans.onreadystatechange=cback,opts.header["Content-Type"]||(opts.header["Content-Type"]="application/x-www-form-urlencoded"),opts.header["X-Requested-With"]||(opts.header["X-Requested-With"]="XMLHttpRequest"),"get"==opts.method.toLocaleLowerCase()){var url=$.core.util.URL(opts.url,{isEncodeQuery:opts.isEncode});url.setParams(opts.args),url.setParam("__rnd",(new Date).valueOf()),trans.open(opts.method,url,opts.asynchronous);try{for(var k in opts.header)trans.setRequestHeader(k,opts.header[k])}catch(exp){}trans.send("")}else{trans.open(opts.method,opts.url,opts.asynchronous);try{for(var k in opts.header)trans.setRequestHeader(k,opts.header[k])}catch(exp){}trans.send($.core.json.jsonToQuery(opts.args,opts.isEncode))}return opts.timeout&&(tm=setTimeout(function(){try{trans.abort()}catch(a){}opts.onTimeout({},trans),opts.onFail({code:"100001",msg:"Request timeout！"})},opts.timeout)),trans}})});