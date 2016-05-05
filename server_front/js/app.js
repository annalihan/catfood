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
steel.d("tpl/common/loading", ["tpl/runtime"],function(require, exports, module) {
var jade = require('tpl/runtime');
var undefined = void 0;
module.exports = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;
;var locals_for_with = (locals || {});(function (text) {
buf.push("<div style=\"padding: 10px 0;text-align: center;width: 100%;overflow:hidden;\" class=\"E_load\"><em class=\"icon_load_loading\"></em><span>" + (jade.escape((jade_interp = text) == null ? '' : jade_interp)) + "</span></div>");}.call(this,"text" in locals_for_with?locals_for_with.text:typeof text!=="undefined"?text:undefined));;return buf.join("");
};
});
steel.d("components/main/index/ctrl", [],function(require, exports, module) {
/**
 * 模块控制器
 */

module.exports = function(control) {
    control.set({
        tpl: 'tpl/index/index',
        data:null
    });
};
});
steel.d("components/bookList/index/ctrl", [],function(require, exports, module) {
/**
 * 图书列表
 */
module.exports = function(control) {
    control.set({
        data: null,
        component: './main'
    });
};
});
steel.d("components/login/index/ctrl", [],function(require, exports, module) {
/**
 * 模块控制器
 */

module.exports = function(control) {
    control.set({
        tpl: 'tpl/login/login',
        data:null
    });
};
});
steel.d("components/myBookList/index/ctrl", [],function(require, exports, module) {
/**
 * 模块控制器
 */

module.exports = function(control) {
    control.set({
        tpl: 'tpl/myBookList/myBookList',
        data: null
    });
};
});
steel.d("app", ["tpl/common/loading","components/main/index/ctrl","components/bookList/index/ctrl","components/login/index/ctrl","components/myBookList/index/ctrl"],function(require, exports, module) {
/**
 * 应用入口文件
 */

var $CONFIG = window.$CONFIG || {};
var loadingTpl = require('tpl/common/loading');

require('components/main/index/ctrl');
require('components/bookList/index/ctrl');
require('components/login/index/ctrl');
require('components/myBookList/index/ctrl');

steel.config({
    version: 0,
    basePath: $CONFIG.baseUrl,
    jsPath: $CONFIG.baseUrl + 'js/',
    cssPath: $CONFIG.baseUrl + '/css/',
    ajaxPath: 'http://' + location.host + '/',
    mainBox: document.getElementById('content'),
    singlePage: true,
    stage: true,
    stageCache: true,
    stageChange: true,
    useCssPrefix: true,
    router: [
        ['/catfood/', 'components/main/index/ctrl'],
        ['/catfood/index', 'components/main/index/ctrl'],
        ['/catfood/bookList', 'components/bookList/index/ctrl'],
        ['/catfood/login', 'components/login/index/ctrl'],
        ['/catfood/mybooklist', 'components/myBookList/index/ctrl']
    ]
});

steel.on('stageChange', function(box, renderFromStageCache) {
    var routerType = steel.router.get().type;
    if ((!document.getElementById('preview') && routerType === 'init') || routerType === 'forward' || routerType === 'new'  || routerType === 'replace') {
        box.innerHTML = loadingTpl({
            text: '加载中...'
        });
    }
});

//监听渲染事件
steel.on('renderError', pageError);

function pageError(res) {
    steel.stage.getBox().innerHTML = '';

    if (res && (res.code + '') === '100002') {
        location.href = '/catfood/login?r=' + encodeURIComponent(location.href);
    }
}

//监听最后一个模块domready完成事件
steel.on('allDomReady', function() {
    if (steel.isDebug) {
        return;
    }

});

steel.on('ajaxTime', function(obj) {
    if (steel.isDebug) {
        return;
    }
});



});