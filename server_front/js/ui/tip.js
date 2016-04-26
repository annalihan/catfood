steel.d("util/parseParam", [],function(require, exports, module) {
/**
 * 
 */

module.exports = function(target, obj) {
	return $.extend({}, target, obj);
};
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
steel.d("tpl/ui/tip", ["tpl/runtime"],function(require, exports, module) {
var jade = require('tpl/runtime');
var undefined = void 0;
module.exports = function template(locals) {
var buf = [];
var jade_mixins = {};
var jade_interp;
;var locals_for_with = (locals || {});(function (document, auto, type, text) {
var winWidth = document.documentElement.clientWidth;
var left = (winWidth > 640 ? auto : 0);
buf.push("<div" + (jade.attr("style", "position:fixed;top:0;left:" + (left) + "px;background-color:rgba(255,255,255,0);width:100%;height:100%;max-width: 640px;z-index:5000;", true, false)) + "></div><div style=\"position:fixed;max-width: 640px;z-index: 9000;\" class=\"E_layer_tips\">");
if (type === 'loading')
{
buf.push("<div class=\"content\"><em class=\"icon_load_loading\"></em><p>" + (((jade_interp = text) == null ? '' : jade_interp)) + "</p></div>");
}
else
{
var iconClass = 'icon_tip_' + type;
buf.push("<div class=\"content layer_tips_common\"><em" + (jade.cls(["" + (iconClass) + ""], [true])) + "></em><p>" + (((jade_interp = text) == null ? '' : jade_interp)) + "</p></div>");
}
buf.push("</div>");}.call(this,"document" in locals_for_with?locals_for_with.document:typeof document!=="undefined"?document:undefined,"auto" in locals_for_with?locals_for_with.auto:typeof auto!=="undefined"?auto:undefined,"type" in locals_for_with?locals_for_with.type:typeof type!=="undefined"?type:undefined,"text" in locals_for_with?locals_for_with.text:typeof text!=="undefined"?text:undefined));;return buf.join("");
};
});
steel.d("ui/tip", ["util/parseParam","tpl/ui/tip"],function(require, exports, module) {
/**
 * 提示层
 */

var parseParam = require('util/parseParam');
var tipTPL = require('tpl/ui/tip');

module.exports = function(text, options) {
    var bodyBox = steel.stage.getBox();

    options = parseParam({
        type: 'succ',//succ/err/warn/loading
        autoHide: 2000,//number/false
        end: null,
        mask: true
    }, options);
    options.text = text || '提示';

    var outer;
    var maskNode;
    var tipNode;
    var hideTimer;
    show();

    if(options.autoHide) {
    	hideTimer = setTimeout(hide, options.autoHide);
    }

	var that = {
		show: show,
		hide: hide
	};

    return that;


    function show() {

        outer = $(tipTPL(options));
        
        $(bodyBox).append(outer);

        outer.on('touchmove', function(e) {
            e.preventDefault();
            return false;
        });
        if (options.type !== 'loading') {
            outer.on('tap', function(e) {
                e.stopPropagation();
                hide();
            });
        }

        maskNode = outer.eq(0);
        tipNode = outer.eq(1);
        reset();
        tipNode.fadeIn(50);
    }

    function hide() {
        clearTimeout(hideTimer);
        outer.off();
        tipNode.fadeOut(50, function() {
            outer.remove();
            options.end && options.end();
            options = outer = maskNode = tipNode = undefined;
        });
    }

    function reset() {
        var winHeight = $(window).height();
        var winWidth = $(window).width();

		var top = (winHeight - tipNode.height()) * 0.382;
		var left = (winWidth - tipNode.width()) / 2;
		top = top > 0 ? top : 0;
		left = left > 0 ? left : 0;

        maskNode.css({
            width: winWidth,
            height: '100%'
        });

		tipNode.css({
			top: top + 'px',
			left: left + 'px'
		});
	}
};
});