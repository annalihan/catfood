steel.d("logic/borrowDataList",["kit/dom/parseDOM","ui/confirm"],function(a,b,c){a("kit/dom/parseDOM"),c.exports=function(b){var c=STK,d=a("ui/confirm"),e={},f={DOM:{},objs:{},DOM_eventFun:{operatorClick:function(a){var b={};switch(a.data.book_status){case"2":b={title:"确认还书？",button1:"取消",button2:"确认"},c.custEvent.add(d(b),"button2Click",f.bindCustEvtFuns.OK,a)}}},bindCustEvtFuns:{OK:function(a){c.ajax({url:"http://catfood.wap.grid.sina.com.cn/aj/borrow/return",charset:"UTF-8",timeout:3e4,args:{book_id:a.data.data.book_id,detail_call_number:a.data.data.detail_call_number},onComplete:function(b){if(0===b.data.code){var d=a.data.el.parentNode;d.removeChild(a.data.el);var e='<div class="bookOperatorTry bookOperator">审</div>';c.addHTML(d,e),f.FUNS.backBookSuccess()}},onTimeout:null,onFail:null,method:"get",asynchronous:!0,contentType:"application/x-www-form-urlencoded",responseType:"json"})}},bindListenerFuns:{},FUNS:{backBookSuccess:function(){var a={title:"审核中!",description:'详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/css/images/lettlebook.png">按钮查看',button1:"取消",button2:"立即查看"};d(a)}}},g=function(){if(!b)throw new Error("node没有定义")},h=function(){f.DOM=c.kit.dom.parseDOM(c.builder(b).list)},i=function(){},j=function(){f.objs.delegate=c.delegatedEvent(b),f.objs.delegate.add("operatorClick","click",f.DOM_eventFun.operatorClick)},k=function(){},l=function(){},m=function(){f&&(c.foreach(f.objs,function(a){a.destroy&&a.destroy()}),f.objs.delegate.remove("operatorClick","click"),f=null)},n=function(){g(),h(),i(),j(),k(),l()};return n(),e.destroy=m,e}}),steel.d("kit/dom/parseDOM",[],function(a,b,c){STK.register("kit.dom.parseDOM",function(a){return function(a){for(var b in a)a[b]&&1==a[b].length&&(a[b]=a[b][0]);return a}})}),steel.d("ui/confirm",["kit/dom/parseDOM","ui/comfirmHTML"],function(a,b,c){var d=STK;a("kit/dom/parseDOM"),c.exports=function(b){var c={},e={objs:{},DOM:{},DOM_eventFun:{button1Fun:function(){document.body.removeChild(e.DOM.confirm),document.body.removeChild(e.DOM.blockDiv),d.custEvent.fire(c,"button1Click")},button2Fun:function(){document.body.removeChild(e.DOM.confirm),document.body.removeChild(e.DOM.blockDiv),d.custEvent.fire(c,"button2Click")}}},f=function(){var c=a("ui/comfirmHTML");html=c({arg:b});var f=d.core.dom.builder(html);document.body.appendChild(f.box),e.DOM=d.kit.dom.parseDOM(f.list),b.description&&(e.DOM.description.innerHTML=b.description)},g=function(){},h=function(){if(!b.title||!b.button1||!b.button2)throw new Error("参数错误！")},i=function(){d.core.evt.addEvent(e.DOM.button1,"click",e.DOM_eventFun.button1Fun),d.core.evt.addEvent(e.DOM.button2,"click",e.DOM_eventFun.button2Fun)},j=function(){d.custEvent.define(c,["button1Click","button2Click"])},k=function(){},l=function(){e&&(d.foreach(e.objs,function(a){a.destroy&&a.destroy()}),d.removeEvent(e.DOM.button1,"click",e.DOM_eventFun.button1Fun),d.removeEvent(e.DOM.button2,"click",e.DOM_eventFun.button2Fun),e=null)},m=function(){h(),f(),g(),i(),j(),k()};return m(),c.destroy=l,c}}),steel.d("ui/comfirmHTML",["tpl/runtime"],function(a,b,c){var d=a("tpl/runtime"),e=void 0;c.exports=function(a){var b,c=[],f=a||{};return function(a){c.push('<div node-type="blockDiv" class="blockDiv"></div><div node-type="confirm" class="confirm"><table cellspacing="1"><tr class="confirmMsg"><td rowspan="2" colspan="2"><p class="title">'+d.escape(null==(b=a.title)?"":b)+"</p>"),a.description&&c.push('<p node-type="description" class="description"></p>'),c.push('</td></tr><tr></tr><tr class="operator"><td node-type="button1">'+d.escape(null==(b=a.button1)?"":b)+'</td><td node-type="button2">'+d.escape(null==(b=a.button2)?"":b)+"</td></tr></table></div>")}.call(this,"arg"in f?f.arg:"undefined"!=typeof arg?arg:e),c.join("")}}),steel.d("tpl/runtime",[],function(a,b,c){!function(a){function b(a){return null!=a&&""!==a}function c(a){window.steel&&!steel.isDebug&&steel.logBee&&steel.logBee("9001","Seteel框架的模板错误："+a)}function d(a){return q(a)?a.map(d).filter(b).join(" "):a}var e=Object.prototype,f=(String.prototype,Function.prototype),g=Array.prototype,h=e.hasOwnProperty,i=(g.slice,e.toString),j=(f.call,!{toString:null}.propertyIsEnumerable("toString")),k=(function(){}.propertyIsEnumerable("prototype"),["toString","toLocaleString","valueOf","hasOwnProperty","isPrototypeOf","propertyIsEnumerable","constructor"]),l=k.length,m={ToObject:function(a){if(null==a)throw c(a),new TypeError("can't convert "+a+" to object");return Object(a)}},n=Object("a"),o="a"!==n[0]||!(0 in n),p=(k.length,function(a){return"[object Function]"===i.call(a)}),q=g.isArray?function(a){return g.isArray(a)}:function(a){return"[object Array]"===i.call(a)},r=function(a){return"[object String]"===i.call(a)};g.map=g.map||function(a){var b=m.ToObject(this),d=o&&r(this)?this.split(""):b,e=d.length>>>0,f=Array(e),g=arguments[1];if(!p(a))throw c(a),new TypeError(a+" is not a function");for(var h=0;e>h;h++)h in d&&(f[h]=a.call(g,d[h],h,b));return f},g.filter=g.filter||function(a){for(var b,c=m.ToObject(this),d=o&&r(this)?this.split(""):c,e=d.length>>>0,f=[],g=arguments[1],h=0;e>h;h++)h in d&&(b=d[h],a.call(g,b,h,c)&&f.push(b));return f},Object.keys=Object.keys||function(a){if("object"!=typeof a&&"function"!=typeof a||null===a){var b="Object keys method called on non-object";throw c(b),new TypeError(b)}var d=[];for(var e in a)h.call(a,e)&&d.push(e);if(j)for(var f=0;l>f;){var g=k[f];h.call(a,g)&&d.push(g),f++}return d},a.merge=function s(a,c){if(1===arguments.length){for(var d=a[0],e=1;e<a.length;e++)d=s(d,a[e]);return d}var f=a["class"],g=c["class"];(f||g)&&(f=f||[],g=g||[],q(f)||(f=[f]),q(g)||(g=[g]),a["class"]=f.concat(g).filter(b));for(var h in c)"class"!=h&&(a[h]=c[h]);return a},a.joinClasses=d,a.cls=function(b,c){for(var e=[],f=0;f<b.length;f++)c&&c[f]?e.push(a.escape(d([b[f]]))):e.push(d(b[f]));var g=d(e);return g.length?' class="'+g+'"':""},a.attr=function(b,c,d,e){return"boolean"==typeof c||null==c?c?" "+(e?b:b+'="'+b+'"'):"":0==b.indexOf("data")&&"string"!=typeof c?" "+b+"='"+jSON.stringify(c).replace(/'/g,"&apos;")+"'":d?" "+b+'="'+a.escape(c)+'"':" "+b+'="'+c+'"'},a.attrs=function(b,c){var e=[],f=Object.keys(b);if(f.length)for(var g=0;g<f.length;++g){var h=f[g],i=b[h];"class"==h?(i=d(i))&&e.push(" "+h+'="'+i+'"'):e.push(a.attr(h,i,!1,c))}return e.join("")},a.escape=function(a){var b=String(a).replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");return b===""+a?a:b},a.rethrow=function t(a,b,d,e){if(!(a instanceof Error))throw a;if(!("undefined"==typeof window&&b||e))throw a.message+=" on line "+d,c(a),a;try{e=e||_dereq_("fs").readFileSync(b,"utf8")}catch(f){t(a,null,d)}var g=3,h=e.split("\n"),i=Math.max(d-g,0),j=Math.min(h.length,d+g),g=h.slice(i,j).map(function(a,b){var c=b+i+1;return(c==d?"  > ":"    ")+c+"| "+a}).join("\n");throw a.path=b,a.message=(b||"Jade")+":"+d+"\n"+g+"\n\n"+a.message,c(a),a}}(c.exports)});