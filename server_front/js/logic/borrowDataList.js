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
steel.d("logic/borrowDataList", ["kit/dom/parseDOM","ui/confirm"],function(require, exports, module) {
require('kit/dom/parseDOM');

module.exports = function(node) {
    //+++ 常量定义区 ++++++++++++++++++
    var $ = STK;

    //+++ 变量定义区 ++++++++++++++++++
    var confirmUI = require('ui/confirm'); //弹出框ui
    var that = {};
    var _this = {
        DOM: {}, //节点容器
        objs: {}, //组件容器
        DOM_eventFun: { //DOM事件行为容器
            operatorClick: function(spec) { //点击操作（借阅/还书）
                var arg = {};
                switch (spec.data.book_status) {
                    case '2':
                        arg = {
                            title: '确认还书？',
                            button1: '取消',
                            button2: '确认'
                        };
                        $.custEvent.add(confirmUI(arg), 'button2Click', _this.bindCustEvtFuns.OK, spec);
                        break;
                }
            }
        },
        bindCustEvtFuns: { //自定义事件回调函数
            OK: function(spec) {
                $.ajax({
                    'url': 'http://catfood.wap.grid.sina.com.cn/aj/borrow/return',
                    'charset': 'UTF-8',
                    'timeout': 30 * 1000,
                    'args': {
                        book_id: spec.data.data.book_id,
                        detail_call_number: spec.data.data.detail_call_number
                    },
                    'onComplete': function(data) {
                        if (data.data.code === 0) {
                            var parentNode = spec.data.el.parentNode;
                            parentNode.removeChild(spec.data.el);
                            var tryHtml = '<div class="bookOperatorTry bookOperator">审</div>';
                            $.addHTML(parentNode, tryHtml);
                            _this.FUNS.backBookSuccess();
                        }
                    },
                    'onTimeout': null,
                    'onFail': null,
                    'method': 'get', // post or get
                    'asynchronous': true,
                    'contentType': 'application/x-www-form-urlencoded',
                    'responseType': 'json'
                });
            }

        },
        bindListenerFuns: { //广播事件回调函数

        },
        FUNS: { //自定义方法
            backBookSuccess: function() { //还书成功
                var arg = {
                    title: '审核中!',
                    description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png">按钮查看',
                    button1: '取消',
                    button2: '立即查看'
                };
                confirmUI(arg);
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
        _this.objs.delegate = $.delegatedEvent(node);
        _this.objs.delegate.add('operatorClick', 'click', _this.DOM_eventFun.operatorClick);
    };
    //-------------------------------------------


    //+++ 自定义事件绑定方法定义区 ++++++++++++++++++
    var bindCustEvt = function() {

    };
    //-------------------------------------------


    //+++ 广播事件绑定方法定义区 ++++++++++++++++++
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
            _this.objs.delegate.remove('operatorClick', 'click');
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