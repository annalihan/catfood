/**
 * Steel Hybrid SPA
 */
! function(window, undefined) {
var steel = window.steel || {
    v : 0.1,
    t : now()
};
var userAgent = navigator.userAgent,
    document = window.document,
    docElem = document.documentElement,
    head = document.head || getElementsByTagName( 'head' )[ 0 ] || docElem,
    setTimeout = window.setTimeout,
    clearTimeout = window.clearTimeout,
    parseInt = window.parseInt,
    parseFloat = window.parseFloat,
    slice = [].slice,
    location = window.location,
    decodeURI = window.decodeURI,
    toString = Object.prototype.toString,
    isHTML5 = !!history.pushState,
    webkit = userAgent.match(/Web[kK]it[\/]{0,1}([\d.]+)/),
    webkitVersion = webkit && parseFloat(webkit[1]),
    iphone = userAgent.match(/(iPhone\sOS)\s([\d_]+)/),
    iphoneVersion = iphone && parseFloat(iphone[2].replace(/_/g, '.')),
    android = userAgent.match(/(Android);?[\s\/]+([\d.]+)?/),
    androidVersion = android && parseFloat(android[2]),
    isAddEventListener = document.addEventListener,
    isDebug,
    logLevels = 'Debug|Info|Warn|Error|Fatal',
    logLevel = 'Info',
    logNotice = 'logNotice',
    IE = /msie (\d+\.\d+)/i.test( userAgent ) ? ( document.documentMode || + RegExp[ '$1' ] ) : 0;
var mainBox;
//检验history.state的支持性
if (isHTML5) {
    (function() {
        var lastState = history.state;
        history.replaceState(1, undefined);
        isHTML5 = (history.state === 1);
        history.replaceState(lastState, undefined);
    })();
}
/*
 * log
 */
function log() {
    var console = window.console;
    //只有debug模式打日志
    if (!isDebug || !console) {
        return;
    }
    var args = arguments;
    if (!RegExp('^(' + logLevels.slice(logLevels.indexOf(logLevel)) + ')').test(args[0])) {
        return;
    }
    var evalString = [];
    for (var i = 0, l = args.length; i < l; ++i) {
        evalString.push('arguments[' + i + ']');
    }
    new Function('console.log(' + evalString.join(',') + ')').apply(this, args);
}
/*
 * 空白方法
 */
function emptyFunction() {}
/*
 * id取节点
 * @method getElementById
 * @private
 * @param {string} id
 */
function getElementById( id ) {
    return document.getElementById( id );
}
/*
 * tagName取节点
 * @method getElementsByTagName
 * @private
 * @param {string} tagName
 */
function getElementsByTagName( tagName, el ) {
    return ( el || document ).getElementsByTagName( tagName );
}
/*
 * now
 * @method now
 * @private
 * @return {number} now time
 */
function now() {
    return Date.now ? Date.now() : +new Date;
}
function RegExp(pattern, attributes) {
    return new window.RegExp(pattern, attributes);
}
var config_list = [];
function config(config) {
  var parseParamFn = config_parseParamFn(config);
  for (var i = 0, l = config_list.length; i < l; ++i) {
    config_list[i](parseParamFn, config);
  }
}
function config_push(fn) {
  config_list.push(fn);
}
function config_parseParamFn(config) {
  return function(key, defaultValue) {
    if (key in config) {
      return config[key];
    }
    return defaultValue;
  };
}
 //模块相关全局变量
var require_base_module_deps = {};
var require_base_module_fn = {};
var require_base_module_loaded = {};
var require_base_module_defined = {};
var require_base_module_runed = {};
//事件
var require_base_event_defined = '-require-defined';
var require_global_loadingNum = 0;



/*
 * parse URL
 * @method core_parseURL
 * @private
 * @param {string} str
 *    可以传入 protocol//host 当protocol不写时使用location.protocol;
 * @return {object}
 * @example
 * core_parseURL( 'http://t.sina.com.cn/profile?beijing=huanyingni' ) ===
    {
        hash : ''
        host : 't.sina.com.cn'
        path : '/profile'
        port : ''
        query : 'beijing=huanyingni'
        protocol : http
        href : 'http://t.sina.com.cn/profile?beijing=huanyingni'
    }
 */
function core_parseURL(url) {
    var parse_url = /^(?:([a-z]+:)?(\/{2,3})([0-9.\-a-z-]+)(?::(\d+))?)?(\/?[^?#]*)?(?:\?([^#]*))?(?:#(.*))?$/i;
    var names = ["url", "protocol", "slash", "host", "port", "path", "query", "hash"];
    var results = parse_url.exec(url);
    var retJson = {};
    if (!results) {
        log('Error:parseURL:"' + url + '" is wrong!');
        return;
    }
    for (var i = 0, len = names.length; i < len; i += 1) {
        retJson[names[i]] = results[i] || "";
    }
    if (retJson.host) {
        retJson.protocol = retJson.protocol || location.protocol;
        retJson.port = retJson.port || 80;
    }
    if (retJson.port) {
        retJson.port = parseInt(retJson.port);
    }
    retJson.path = retJson.path.replace(/\/+/g, '/') || '/';
    return retJson;
}
/*
 * query to json
 * @method core_queryToJson
 * @private
 * @param {string} query
 * @return {json} JSON
 * @example
 * var q1 = 'a=1&b=2&c=3';
 * core_queryToJson( q1 ) === {'a':1,'b':2,'c':3};
 */
function core_queryToJson( query ) {
    var queryList = query.split( '&' );
    var retJson  = {};
    for( var i = 0, len = queryList.length; i < len; ++i ){
        if ( queryList[ i ] ) {
            var hsh = queryList[ i ].split( '=' );
            var key = hsh[ 0 ];
            var value = hsh[ 1 ] || '';
            retJson[ key ] = retJson[ key ] ? [].concat( retJson[ key ], value ) : value;
        }
    }
    return retJson;
}
/*
 * typeof
 */
function core_object_typeof( value ) {
    return value === null ? '' : Object.prototype.toString.call( value ).slice( 8, -1 ).toLowerCase();
}
/*
 * json to query
 * @method core_jsonToQuery
 * @private
 * @param {json} json
 * @return {string} query
 */
function core_jsonToQuery( json ) {
    var queryString = [];
    for ( var k in json ) {
        if ( core_object_typeof( json[ k ] ) === 'array' ) {
            for ( var i = 0, len = json[ k ].length; i < len; ++i ) {
                queryString.push( k + '=' + json[ k ][ i ] );
            }
        } else {
            queryString.push( k + '=' + json[ k ] );
        }
    }
    return queryString.join( '&' );
}
/**
 * is String
 */
function core_object_isString(value) {
    return core_object_typeof(value) === 'string';
}
/**
 * 扩展内容
 */
function core_object_extend(target, key, value) {
    if (core_object_isString(key)) {
        target[key] = value;
    } else {
        for (var _key in key) {
            target[_key] = key[_key];
        }
    }
    return target;
}
/**
 * 判断地址中是否有协议
 * @param  {string} url
 * @return {boolean}
 */
function core_hasProtocol(url) {
    return /^([a-z]+:)?\/\/\w+/i.test(url);
}
/*
 * 根据相对路径得到绝对路径
 * @method core_fixUrl
 * @private
 * @return {String}
 */
function core_fixUrl(baseUrl, path) {
    baseUrl = baseUrl || '.';
    var baseUrlJson = core_parseURL(baseUrl);
    var origin;
    if (baseUrlJson.path.indexOf('/') !== 0) {
        baseUrl = core_fixUrl(location.href, baseUrl);
        baseUrlJson = core_parseURL(baseUrl);
    }
    if (baseUrlJson.protocol) {
        origin = baseUrlJson.protocol + '//' + baseUrlJson.host + (baseUrlJson.port === 80 ? '' : (':' + baseUrlJson.port));
    } else {
        origin = origin = location.origin || location.toString().replace(/^([^\/]*\/\/[^\/]*)\/.*$/, '$1');
        baseUrl = origin + baseUrl;
    }
    var originPath = origin + '/';
    var basePath = baseUrlJson.path;
    basePath = origin + (basePath.indexOf('/') === 0 ? '' : '/') + basePath.slice(0, basePath.lastIndexOf('/') + 1);
    if (core_hasProtocol(path)) {
        return path;
    }
    if (path === '/') {
        return originPath;
    }
    if (path === '.' || path === '') {
        return baseUrl;
    }
    if (path.indexOf('./') === 0) {
        path = path.replace(/^\.\//, '');
        return basePath + path;
    }
    if (path === '..') {
        path = path.replace(/\.\./, '');
        basePath = core_fixUrl_handleTwoDots(basePath);
        return basePath + path;
    }
    if (path.indexOf('?') === 0) {
        return origin + baseUrlJson.path + path;
    }
    if (path.indexOf('&') === 0) {
        return origin + baseUrlJson.path + '?' + core_jsonToQuery(core_object_extend(core_queryToJson(baseUrlJson.query), core_queryToJson(path)));
    }
    if (/^\/[^\/]+/.test(path)) {
        return origin + path;
    }
    while (path.indexOf('../') === 0) {
        if (originPath === basePath) {
            path = path.replace(/(\.\.\/)/g, '');
            basePath = originPath;
            break;
        }
        path = path.replace(/^\.\.\//, '');
        basePath = core_fixUrl_handleTwoDots(basePath);
    }
    return basePath + path;
}
function core_fixUrl_handleTwoDots(url) {
    url = url.charAt(url.length - 1) === '/' ? (url.slice(0, url.length - 1)) : url;
    return url.slice(0, url.lastIndexOf('/') + 1);
}



/**
 * Describe 对url进行解析变化
 * @id  core_URL
 * @alias
 * @param {String} url
 * @param {Object}
    {
        'isEncodeQuery'  : {Boolean}, //对query编码
        'isEncodeHash'   : {Boolean}  //对hash编码
    }
 * @return {Object}
    {
        setParam    : {Function}
        getParam    : {Function}
        setParams   : {Function}
        setHash     : {Function}
        getHash     : {Function}
        toString    : {Function}
    }
 * @example
 *  alert(
 *      core_URL('http://abc.com/a/b/c.php?a=1&b=2#a=1').
 *      setParam('a', 'abc').
 *      setHash('a', 67889).
 *      setHash('a1', 444444).toString()
 *  );
 */



/*
 * 合并参数，不影响源
 * @param {Object} oSource 需要被赋值参数的对象
 * @param {Object} oParams 传入的参数对象
 * @param {Boolean} isown 是否仅复制自身成员，不复制prototype，默认为false，会复制prototype
*/
function core_object_parseParam(oSource, oParams, isown){
    var key, obj = {};
    oParams = oParams || {};
    for (key in oSource) {
        obj[key] = oSource[key];
        if (oParams[key] != null) {
            if (isown) {// 仅复制自己
                if (oSource.hasOwnProperty(key)) {
                    obj[key] = oParams[key];
                }
            } else {
                obj[key] = oParams[key];
            }
        }
    }
    return obj;
}

function core_URL(sURL,args){
    var opts = core_object_parseParam({
        'isEncodeQuery'  : false,
        'isEncodeHash'   : false
    },args||{});
    var retJson = {};
    var url_json = core_parseURL(sURL);
    var query_json = core_queryToJson(url_json.query);
    var hash_json = core_queryToJson(url_json.hash);
    /**
     * Describe 设置query值
     * @method setParam
     * @param {String} sKey
     * @param {String} sValue
     * @example
     */
    retJson.setParam = function(sKey, sValue){
        query_json[sKey] = sValue;
        return this;
    };
    /**
     * Describe 取得query值
     * @method getParam
     * @param {String} sKey
     * @example
     */
    retJson.getParam = function(sKey){
        return query_json[sKey];
    };
    /**
     * Describe 设置query值(批量)
     * @method setParams
     * @param {Json} oJson
     * @example
     */
    retJson.setParams = function(oJson){
        for (var key in oJson) {
            retJson.setParam(key, oJson[key]);
        }
        return this;
    };
    /**
     * Describe 设置hash值
     * @method setHash
     * @param {String} sKey
     * @param {String} sValue
     * @example
     */
    retJson.setHash = function(sKey, sValue){
        hash_json[sKey] = sValue;
        return this;
    };
    /**
     * Describe 设置hash值
     * @method getHash
     * @param {String} sKey
     * @example
     */
    retJson.getHash = function(sKey){
        return hash_json[sKey];
    };
    /**
     * Describe 取得URL字符串
     * @method toString
     * @example
     */
    retJson.valueOf = retJson.toString = function(){
        var url = [];
        var query = core_jsonToQuery(query_json, opts.isEncodeQuery);
        var hash = core_jsonToQuery(hash_json, opts.isEncodeQuery);
        if (url_json.protocol) {
            url.push(url_json.protocol);
            url.push(url_json.slash);
        }
        if (url_json.host != '') {
            url.push(url_json.host);
            if(url_json.port != ''){
                url.push(':');
                url.push(url_json.port);
            }
        }
        // url.push('/');
        url.push(url_json.path);
        if (query != '') {
            url.push('?' + query);
        }
        if (hash != '') {
            url.push('#' + hash);
        }
        return url.join('');
    };
    return retJson;
};
/**
 * 资源变量
 */
var resource_jsPath;
var resource_cssPath;
var resource_ajaxPath;
var resource_basePath;
var resource_base_apiRule;
var resource_base_version;
//资源列表{url->[[access_cb, fail_cb],....]}
var resource_queue_list = {};


//router资源

var core_uniqueKey_index = 1;
var core_uniqueKey_prefix = 'SL_' + now();
/*
 * 唯一字符串
 * @method core_uniqueKey
 * @private
 * @return {string}
 */
function core_uniqueKey() {
    return core_uniqueKey_prefix + core_uniqueKey_index++;
}
//污染到对象上的属性定义
var core_uniqueID_attr = '__SL_ID';
/*
 * 得到对象对应的唯一key值
 * @method core_uniqueID
 * @private
 * @return {string}
 */
function core_uniqueID( obj ) {
    return obj[ core_uniqueID_attr ] || ( obj[ core_uniqueID_attr ] = core_uniqueKey() );
}
/*
 * 返回在数组中的索引
 * @method core_array_indexOf
 * @private
 * @param {Array} oElement
 * @param {Any} oElement
 *  需要查找的对象
 * @return {Number}
 *  在数组中的索引,-1为未找到
 */
function core_array_indexOf( oElement, aSource ) {
    if ( aSource.indexOf ) {
        return aSource.indexOf( oElement );
    }
    for ( var i = 0, len = aSource.length; i < len; ++i ) {
        if ( aSource[ i ] === oElement ) {
            return i;
        }
    }
    return -1;
}

/*
 * 把类数组改变成数组
 * @method core_array_makeArray
 * @private
 * @param {arrayLike} obj
 *  需要查找的对象
 * @return {Array}
 */
function core_array_makeArray( obj ) {
    return slice.call(obj, 0, obj.length);
}
var core_notice_data_SLKey = '_N';
var core_notice_data = steel[ core_notice_data_SLKey ] = steel[ core_notice_data_SLKey ] || {};
/*
 * 对缓存的检索
 * @method core_notice_find
 */
function core_notice_find( type ) {
    return core_notice_data[ type ] || ( core_notice_data[ type ] = [] );
}
/*
 * 添加事件
 * @method core_notice_on
 * @param {string} type
 * @param {Function} fn
 */
function core_notice_on( type, fn ) {
    core_notice_find( type ).unshift( fn );
}
/*
 * 移除事件
 * @method core_notice_off
 * @param {string} type
 * @param {Function} fn
 */
function core_notice_off( type, fn ) {
    var typeArray = core_notice_find( type ),
        index,
        spliceLength;
    if ( fn ) {
        if ( ( index = core_array_indexOf( fn, typeArray ) ) > -1 ) {
            spliceLength = 1;
        }
    } else {
        index = 0;
        spliceLength = typeArray.length;
    }
    spliceLength && typeArray.splice( index, spliceLength );
}
/*
 * 事件触发
 * @method core_notice_trigger
 * @param {string} type
 */
function core_notice_trigger( type ) {
    var typeArray = core_notice_find( type );
    var args = core_array_makeArray(arguments);
    args = args.slice(1, args.length);
    for ( var i = typeArray.length - 1; i > -1; i-- ) {
        typeArray[ i ] && typeArray[ i ].apply( undefined, args );
    }
}


/**
 * is Number
 */
function core_object_isNumber(value) {
    return core_object_typeof(value) === 'number';
}

/**
 * is Object
 */
function core_object_isObject(value) {
    return core_object_typeof(value) === 'object';
}
function core_crossDomainCheck(url) {
    var urlPreReg = /^[^:]+:\/\/[^\/]+\//;
    var locationMatch = location.href.match(urlPreReg);
    var urlMatch = url.match(urlPreReg);
    return (locationMatch && locationMatch[0]) === (urlMatch && urlMatch[0]);
}
/**
 * arguments 简单多态 要求参数顺序固定
 * @param  {Arguments} args  参数对象
 * @param  {array} keys  参数名数组
 * @param  {array} types 类型数组 array/object/number/string/function
 * @return {object}      使用参数key组成的对象
 * @example
 * function test(a, b, c, d, e) {
 *    console.log(core_argsPolymorphism(arguments, ['a', 'b', 'c', 'd', 'e'], ['number', 'string', 'function', 'array', 'object']));
 * }
 * test(45, 'a', [1,3], {xxx:343}) => Object {a: 45, b: "a", d: Array[2], e: Object}
 */
function core_argsPolymorphism(args, keys, types) {
    var result = {};
    var newArgs = [];
    var typeIndex = 0;
    var typeLength = types.length;
    for (var i = 0, l = args.length; i < l; ++i) {
        var arg = args[i];
        if (arg === undefined || arg === null) {
            continue;
        }
        for (; typeIndex < typeLength; ++typeIndex) {
            if (core_object_typeof(arg) === types[typeIndex]) {
                result[keys[typeIndex]] = arg;
                ++typeIndex;
                break;
            }
        }
        if (typeIndex >= typeLength) {
            break;
        }
    }
    return result;
}
/**
 * 路由变量定义区
 *
 */
//收集用户路由配置信息
var router_base_routerTable = [];
//处理后的路由集合，[{pathRegexp:RegExp, controller:'controllerFn', keys:{}}]
var router_base_routerTableReg = [];
//应用是否支持单页面（跳转与否）
var router_base_singlePage = false;
// @Finrila hash模式处理不可用状态，先下掉
// //项目是否使用hash
// var router_base_useHash = false;
// init/new/forward/bak/refresh/replace
var router_base_routerType = 'init';
var router_base_prevHref;
var router_base_currentHref = location.toString();


/*
 * dom事件绑定
 * @method core_event_addEventListener
 * @private
 * @param {Element} el
 * @param {string} type
 * @param {string} fn
 */
var core_event_addEventListener = isAddEventListener ?
    function( el, type, fn, useCapture) {
        el.addEventListener( type, fn, useCapture === undefined ? false : useCapture);
    }
    :
    function( el, type, fn ) {
        el.attachEvent( 'on' + type, fn );
    };
/*
 * dom ready
 * @method core_dom_ready
 * @private
 * @param {Function} handler
 */
function core_dom_ready( handler ) {
    function DOMReady() {
        if ( DOMReady !== emptyFunction ) {
            DOMReady = emptyFunction;
            handler();
        }
    }
    if ( /complete/.test( document.readyState ) ) {
        handler();
    } else {
        if ( isAddEventListener ) {
            core_event_addEventListener( document, 'DOMContentLoaded', DOMReady );
        } else {
            core_event_addEventListener( document, 'onreadystatechange', DOMReady );
            //在跨域嵌套iframe时 IE8- 浏览器获取window.frameElement 会出现权限问题
            try {
                var _frameElement = window.frameElement;
            } catch (e) {}
            if ( _frameElement == null && docElem.doScroll ) {
                (function doScrollCheck() {
                    try {
                        docElem.doScroll( 'left' );
                    } catch ( e ) {
                        return setTimeout( doScrollCheck, 25 );
                    }
                    DOMReady();
                })();
            }
        }
        core_event_addEventListener( window, 'load', DOMReady );
    }
}

/*
 * preventDefault
 * @method core_event_preventDefault
 * @private
 * @return {Event} e
 */
function core_event_preventDefault( event ) {
    if ( event.preventDefault ) {
        event.preventDefault();
    } else {
        event.returnValue = false;
    }
}


function router_parseURL(url) {
    url = url || location.toString();
    var result = core_parseURL(url);
    // @Finrila hash模式处理不可用状态，先下掉
    // var hash = result.hash;
    // if (router_base_useHash && hash) {
    //     //获取当前 hash后的 path
    //     result = core_parseURL(core_fixUrl(url, hash));
    // }
    return result;
}
function router_match(url) {
    var routerUrl = core_object_isObject(url) ? url : router_parseURL(url);
    var path = routerUrl.path;// store values
    for (var i = 0, len = router_base_routerTableReg.length; i < len; i++) {
        var obj = router_base_routerTableReg[i];
        var pathMatchResult;//正则校验结果；
        if (pathMatchResult = obj['pathRegexp'].exec(path)) {
            var keys = obj['keys'];
            var param = {};
            var prop;
            var n = 0;
            var key;
            var val;
            for (var j = 1, len = pathMatchResult.length; j < len; ++j) {
                key = keys[j - 1];
                prop = key ? key.name : n++;
                val = decodeURIComponent(pathMatchResult[j]);
                param[prop] = val;
            }
            return {
                config: obj['config'],
                param: param
            };
        }
    }
}

/**
 * 地址管理，负责管理state的数据和当面页面在state历史中的索引位置
 */
// 当前页面在整个单页面跳转中的索引位置
var router_history_stateIndex_key = '--steel-stateIndex';
var router_history_state_data;
var router_history_state_dataForPush;
router_history_state_init();
core_notice_on('popstate', router_history_state_init);
//history pushState 及一些处理
function router_history_pushState(url) {
    router_history_state_setPush(router_history_stateIndex_key, router_history_getStateIndex() + 1);
    history.pushState(router_history_stateForPush(), undefined, url);
    router_history_state_init();
}
//history repaceState 及一些处理
function router_history_replaceState(url) {
    history.replaceState(router_history_state_data, undefined, url);
}
//获取当前页面在整个单页面跳转中的索引位置
function router_history_getStateIndex() {
    return router_history_state_get(router_history_stateIndex_key, 0);
}
//初始化state数据
function router_history_state_init() {
    router_history_state_dataForPush = {};
    router_history_state_data = router_history_state();
}
//获取当前的state
function router_history_state() {
    return history.state || {};
}
//获取下一个将要push页面的state数据
function router_history_stateForPush() {
    return router_history_state_dataForPush;
}
//获取当前state上的某值
function router_history_state_get(key, defaultValue) {
    router_history_state_data = router_history_state();
    if (key in router_history_state_data) {
        return router_history_state_data[key];
    } else if (defaultValue !== undefined) {
        router_history_state_set(key, defaultValue);
        return defaultValue;
    }
}
//设置值到缓存中，并更改history.state的值
function router_history_state_set(key, value) {
    router_history_state_data = {};
    var state = history.state;
    if (state) {
        for (var state_key in state) {
            router_history_state_data[state_key] = state[state_key];
        }
    }
    core_object_extend(router_history_state_data, key, value);
    router_history_replaceState(location.href);
}
//向下一个state的缓存区域添加数据项 并返回新的数据
function router_history_state_setPush(key, value) {
    core_object_extend(router_history_state_dataForPush, key, value);
}

/**
 * 公共对象方法定义文件
 */



//control容器
var render_base_controlCache = {};
//controllerNs容器
var render_base_controllerNs = {};
//资源容器
var render_base_resContainer = {};
//渲染相关通知事件的前缀
var render_base_notice_prefix = '-steel-render-';
//sessionStorage级别 是否使用state缓存模块的数据内容
var render_base_dataCache_usable = false;
//场景相关配置
//场景最大个数
var render_base_stage_maxLength = 10;
//是否启用场景管理
var render_base_stage_usable = false;
//内存级：是否在浏览器中内存缓存启用了场景的页面内容，缓存后页面将由开发者主动刷新
var render_base_stageCache_usable = false;
//是否支持场景切换
var render_base_stageChange_usable = false;
//场景默认显示内容
var render_base_stageDefaultHTML = '';
////
//是否添加模块父样式
var render_base_useCssPrefix_usable = false;
//是否启用进度条
var render_base_loadingBar_usable = false;
//boxid生成器 当参数为true时要求：1.必须唯一 2.同一页面同一模块的id必须是固定的
function render_base_idMaker(supId) {
    return core_uniqueKey();
}



function render_error() {
    var args = core_array_makeArray(arguments);
    core_notice_trigger.apply(undefined, ['renderError'].concat(args));
}
/*
 * control核心逻辑
 *//*
 * 给节点设置属性
 * @method core_dom_getAttribute
 * @private
 * @param {string} name
 */
function core_dom_getAttribute( el, name ) {
    return el.getAttribute( name );
}
/*
 * 对象克隆
 * @method core_object_clone
 */
function core_object_clone( obj ) {
    var ret = obj;
    if ( core_object_typeof( obj ) === 'array' ) {
        ret = [];
        var i = obj.length;
        while ( i-- ) {
            ret[ i ] = core_object_clone( obj[ i ] );
        }
    } else if ( core_object_typeof( obj ) === 'object' ) {
        ret = {};
        for ( var k in obj ) {
            ret[ k ] = core_object_clone( obj[ k ] );
        }
    }
    return ret;
}
/*
 * 返回指定ID或者DOM的节点句柄
 * @method core_dom_removeNode
 * @private
 * @param {Element} node 节点对象
 * @example
 * core_dom_removeNode( node );
 */
function core_dom_removeNode( node ) {
    node && node.parentNode && node.parentNode.removeChild( node );
}






/**
 * 模块渲染和运行时的错误触发
 * @param  {object} resContainer 资源容器
 * @param  {string} type         错误类型
 * @param  {any} ...         错误信息
 * @return {undefined}
 */
function render_control_triggerError(resContainer, type) {
    var args = core_array_makeArray(arguments).slice(1);
    log.apply(undefined, ['Error: render'].concat(args));
    core_notice_trigger.apply(undefined, [resContainer.boxId + 'error'].concat(args))
}
function render_control_setLogic(resContainer) {
    var controllerNs = render_base_controllerNs[resContainer.boxId];
    var logic = resContainer.logic;
    var startTime = null;
    var endTime = null;
    var logicCallbackFn;
    resContainer.logicReady = false;
    resContainer.logicFn = null;
    resContainer.logicRunned = false;
    if(logic){
        if(core_object_typeof(logic) === 'function'){
            resContainer.logicFn = logic;
            render_control_toStartLogic(resContainer);
        } else {
            var cb = logicCallbackFn = function(fn) {
                if(cb === logicCallbackFn){
                    endTime = now();
                    core_notice_trigger('logicTime', {
                        startTime: startTime,
                        logicTime: endTime - startTime || 0,
                        ctrlNS: controllerNs
                    });
                    fn && (resContainer.logicFn = fn);
                    render_control_toStartLogic(resContainer);
                }
                //抛出js加载完成事件
            };
            startTime = now();
            require_global(logic, cb, function() {
                render_error();
                render_control_triggerError(resContainer, 'logic', logic);
            }, controllerNs);
        }
    }
}
function render_control_toStartLogic(resContainer) {
    resContainer.logicReady = true;
    render_control_startLogic(resContainer);
}
function render_control_startLogic(resContainer) {
    var boxId = resContainer.boxId;
    var box = getElementById(boxId);
    var control = render_base_controlCache[boxId];
    var logicResult;
    var real_data = resContainer.real_data || {};
    if (!resContainer.logicRunned && resContainer.logicFn && resContainer.logicReady && resContainer.rendered) {
        if (isDebug) {
            logicResult = resContainer.logicFn(box, real_data, control) || {};
        } else {
            try {
                logicResult = resContainer.logicFn(box, real_data, control) || {};
            } catch(e) {
                render_control_triggerError(resContainer, 'runLogic', resContainer.logic, e);
            }
        }
        resContainer.logicResult = logicResult;
        resContainer.logicRunned = true;
    }
}
/*
 * 销毁logic
*/
function render_control_destroyLogic(resContainer) {
    resContainer.logicRunned = false;
    var logicResult = resContainer.logicResult;
    if (logicResult) {
        if (isDebug) {
            logicResult.destroy && logicResult.destroy();
        } else {
            try {
                logicResult.destroy && logicResult.destroy();
            } catch(e) {
                log('Error: destroy logic error:', resContainer.logic, e);
            }
        }
      resContainer.logicResult = undefined;
    }
}

/**
 * @param {Object} o
 * @param {boolean} isprototype 继承的属性是否也在检查之列
 * @example
 * core_object_isEmpty({}) === true;
 * core_object_isEmpty({'test':'test'}) === false;
 */
function core_object_isEmpty(o,isprototype){
    for(var k in o){
        if(isprototype || o.hasOwnProperty(k)){
            return false;
        }
    }
    return true;
}




function core_array_inArray(oElement, aSource){
    return core_array_indexOf(oElement, aSource) > -1;
}/**
 * 场景管理
 * 第一版本实现目标：
 *//*
 * 创建节点
 * @method core_dom_createElement
 * @private
 * @param {string} tagName
 */
function core_dom_createElement( tagName ) {
    return document.createElement( tagName );
}
/*
 * 给节点设置属性
 * @method core_dom_setAttribute
 * @private
 * @param {string} name
 * @param {string} value
 */
function core_dom_setAttribute( el, name, value ) {
    return el.setAttribute( name, value );
}/**
 * 销毁一个模块，样式，逻辑，节点
 *//**
 * s-data属性的特殊处理，当子模块节点中s-data的值为#sdata-开头时 从缓存中获取模块数据
 */
var render_control_sData_preFix = '#sdata-';
var render_control_sData_current_boxId;
var render_control_sData_s_data_index;
var render_control_sData_dataMap = {};
function render_control_sData(data) {
    var dataId = render_control_sData_preFix + render_control_sData_current_boxId + '-' + (render_control_sData_s_data_index++);
    render_control_sData_dataMap[render_control_sData_current_boxId][dataId] = data || {};
    return dataId;
}
function render_control_sData_setBoxId(boxId) {
    render_control_sData_current_boxId = boxId;
    render_control_sData_s_data_index = 0;
    render_control_sData_dataMap[boxId] = {};
}
function render_control_sData_getData(dataId) {
    var idMatch = dataId.match(RegExp('^' + render_control_sData_preFix + '(.*)-\\d+$'));
    if (idMatch) {
        return render_control_sData_dataMap[idMatch[1]][dataId];
    }
}
function render_control_sData_delData(boxId) {
    delete render_control_sData_dataMap[boxId];
}
function render_control_destroy(idMap, onlyRes) {
  idMap = idMap || {};
  if (typeof idMap === 'string') {
    var _idMap = {};
    _idMap[idMap] = true;
    idMap = _idMap;
  }
  for (var id in idMap) {
    render_control_destroy_one(id, onlyRes);
  }
}
function render_control_destroy_one(id, onlyRes) {
  var resContainer = render_base_resContainer[id];
  var childControl = render_base_controlCache[id];
  var childControllerNs = render_base_controllerNs[id];
  if (!onlyRes) {
    if (childControl) {
      childControl._destroy();
      delete render_base_controlCache[id];
    }
    if (childControllerNs) {
      delete render_base_controllerNs[id];
    }
  }
  if (resContainer) {
    render_control_destroyLogic(resContainer);
    render_control_setCss_destroyCss(resContainer);
    render_control_destroy(resContainer.childrenid);
    render_control_sData_delData(id);
    delete render_base_resContainer[id];
  }
}/**
 * 得到节点的计算样式
 */
var core_dom_getComputedStyle = window.getComputedStyle ? function(node, property) {
    return getComputedStyle(node, '')[property];
} : function(node, property) {
    return node.currentStyle && node.currentStyle[property];
};/**
 * querySelectorAll
 * 在非h5下目前只支持标签名和属性选择如div[id=fsd],属性值不支持通配符
 */
var core_dom_querySelectorAll_REG1 = /([^\[]*)(?:\[([^\]=]*)=?['"]?([^\]]*?)['"]?\])?/;
function core_dom_querySelectorAll(dom, select) {
    var result;
    var matchResult;
    var matchTag;
    var matchAttrName;
    var matchAttrValue;
    var elements;
    var elementAttrValue;
    if (dom.querySelectorAll) {
        result = dom.querySelectorAll(select);
    } else {
        if (matchResult = select.match(core_dom_querySelectorAll_REG1)) {
            matchTag = matchResult[1];
            matchAttrName = matchResult[2];
            matchAttrValue = matchResult[3];
            result = getElementsByTagName(matchTag || '*', dom);
            if (matchAttrName) {
                elements = result;
                result = [];
                for (var i = 0, l = elements.length; i < l; ++i) {
                    elementAttrValue = elements[i].getAttribute(matchAttrName);
                    if (elementAttrValue !== null && (!matchAttrValue || elementAttrValue === matchAttrValue)) {
                        result.push(elements[i])
                    }
                }
            }
        }
    }
    return result || [];
}/*
 * dom事件解绑定
 * @method core_event_removeEventListener
 * @private
 * @param {Element} el
 * @param {string} type
 * @param {string} fn
 */
var core_event_removeEventListener = isAddEventListener ?
    function( el, type, fn ) {
        el.removeEventListener( type, fn, false );
    }
    :
    function( el, type, fn ) {
        el.detachEvent( 'on' + type, fn );
    };/**
 * event对象属性适配
 */
function core_event_eventFix(e) {
    e.target = e.target || e.srcElement;
}/**
 * 两点之间的距离
 */
function core_math_distance(point1, point2) {
    return Math.sqrt(Math.pow((point2[0] - point1[0]), 2) + Math.pow((point2[1] - point1[1]), 2));
}
var core_dom_className_blankReg = / +/g;
/**
 * classname编辑工具
 */
function core_dom_className(node, addNames, delNames) {
    var oldClassName = ' ' + (node.className || '').replace(core_dom_className_blankReg, '  ') + ' ';
    addNames = addNames || '';
    delNames = (addNames + ' ' +(delNames || '')).replace(core_dom_className_blankReg, '|').replace(/^\||\|$/, '');
    node.className = oldClassName.replace(RegExp(' (' + delNames + ') ', 'ig'), ' ') + ' ' + addNames;
}
var render_stage_data = {}; //stageBoxId -> {curr:index, last:index, subs:[]}
var render_stage_anidata = {};
var render_stage_style_mainId = 'steel-style-main';
var render_stage_style_rewriteId = 'steel-style-rewrite';
var render_stage_ani_transition_class = 'steel-stage-transform';
var render_stage_scroll_class = 'steel-render-stage-scroll';
var render_stage_fixed_class = 'steel-render-stage-fixed';
var render_stage_subNode_class = 'steel-stage-sub';
//状态变量区域
var render_stage_webkitTransitionDestroyFn;
var render_stage_ani_doing;
var render_stage_input_focused;
var render_stage_boxId;
var render_stage_touch_status_started;
var render_stage_touch_status_start_time;
var render_stage_touch_status_moved;
var render_stage_touch_status_move_time;
var render_stage_touch_status_ended;
var render_stage_touch_status_end_time;
////
var inputReg = /input|textarea/i;
/**
 * 获取当前渲染的stageBoxId
 */
function render_stage_getBox() {
    return getElementById(render_stage_boxId || mainBox && mainBox.id);
}
/**
 * 获取当前支持滚动的节点的id  这个方法只在启用了并支持场景切换功能时有效，
 */
function render_stage_getScrollBox() {
    var boxId = render_stage_boxId || mainBox && mainBox.id;
    var stageScrollId;
    if (render_base_stageChange_usable) {
        stageScrollId = render_base_resContainer[boxId] && render_base_resContainer[boxId].stageScrollId;
        if (stageScrollId) {
            return getElementById(stageScrollId);
        }
    }
}
function render_stage_init() {
    render_stage_style_init();
    render_stage_change_init();
}
//场景切换功能初始化
function render_stage_change_init() {
    if (!render_base_stageChange_usable) {
        return;
    }
    var touchDataStartX, touchDataStartY, touchDataLastX, touchDataLastY, touchDataX, touchDataY;
    // var touchDirection, touchMoved;
    // var touchStartTime;
    // var isPreventDefaulted;
    var isInputTouched;
    var lastTouchendTime;
    core_event_addEventListener(docElem, 'touchstart', function(e) {
        core_event_eventFix(e);
        checkStopEvent(e);
        render_stage_touch_status_started = true;
        render_stage_touch_status_moved = false;
        render_stage_touch_status_start_time = now();
        render_stage_touch_status_ended = undefined;
        render_stage_touch_status_end_time = undefined;
        if (render_stage_webkitTransitionDestroyFn) {
            e.preventDefault();
        }
        readTouchData(e);
        touchDataStartX = touchDataX;
        touchDataStartY = touchDataY;
        // touchStartTime = now();
        // 针对iphone下文本框输入时样式错乱问题的方法解决
        if (iphone) {
            isInputTouched = inputReg.test(e.target.tagName);
            if (!isInputTouched) {
                render_stage_input_focused = false;
                render_stage_style_rewrite();
            }
        }
    });
    // core_event_addEventListener(docElem, 'touchmove', function(e) {
    //     if (e._7) {
    //         return;
    //     }
    //     e._7 = true;
    //     var oldPreventDefault = e.preventDefault;
    //     isPreventDefaulted = false;
    //     e.preventDefault = function() {
    //         isPreventDefaulted = true;
    //         oldPreventDefault.call(e);
    //     };
    // }, true);
    var count = 0;
    core_event_addEventListener(docElem, 'touchmove', function(e) {
        readTouchData(e);
        render_stage_touch_status_moved = true;
        // if (core_math_distance([touchDataX, touchDataY], [touchDataLastX, touchDataLastY]) > 15) {
        //     render_stage_touch_status_moved = true;
        // }
        render_stage_touch_status_move_time = now();
        if (render_stage_webkitTransitionDestroyFn) {
            e.preventDefault();
        }
        // if (!touchDirection) {
        //     touchDirection = (Math.abs(touchDataY - touchDataLastY) > Math.abs(touchDataX - touchDataLastX)) ? 'Y' : 'X';
        // }
        // touchMoved = true;
        // if (isPreventDefaulted) {
        //     return;
        // }
        // if (touchDirection === 'X') {
        //     // e.preventDefault();
        // } else {
        // }
    });
    core_event_addEventListener(docElem, 'touchend', function(e) {
        core_event_eventFix(e);
        checkStopEvent(e);
        //阻止dblclick的默认行为
        if (lastTouchendTime && now() - lastTouchendTime < 300 || render_stage_webkitTransitionDestroyFn) {
            e.preventDefault();
        }
        render_stage_touch_status_ended = true;
        render_stage_touch_status_end_time = lastTouchendTime = now();
        // readTouchData(e);
        // touchDirection = touchMoved = undefined;
        // 针对iphone下文本框输入时样式错乱问题的方法解决
        if (iphone) {
            if (isInputTouched && inputReg.test(e.target.tagName)) {
                render_stage_input_focused = true;
                render_stage_style_rewrite();
            }
        }
    });
    //动画期间阻止一切事件的触发
    core_event_addEventListener(docElem, 'click', checkStopEvent);
    function readTouchData(e) {
        var touch = e.changedTouches[0];
        touchDataLastX = touchDataX;
        touchDataLastY = touchDataY;
        touchDataX = touch.clientX;
        touchDataY = touch.clientY;
    }
    //动画期间阻止一切事件的触发
    function checkStopEvent(e) {
        if (render_stage_ani_doing) {
            e.preventDefault();
            e.stopPropagation();
        }
    }
}
function render_stage_change_check_host_behaviour_onStageChangeBack() {
    if (iphone && render_stage_touch_status_started && render_stage_touch_status_moved) {
        if (!render_stage_touch_status_ended) {
            return true;
        } else if (now() - render_stage_touch_status_end_time < 377) {
            return true;
        }
    }
}
/**
 * 根据路由类型在维护当前场景并返回当前路由与该场景对应的渲染节点
 * @param  {string} stageBoxId  场景主节点
 * @param  {string} routerType init/new/forward/bak/refresh/replace
 */
function render_stage(stageBoxId, routerType) {
    var stateIndex = router_history_getStateIndex();
    var data = render_stage_data_get(stageBoxId, stateIndex);
    var node = getElementById(stageBoxId);
    core_dom_setAttribute(node, 's-stage-sup', 'true');
    if (!data.subs[stateIndex]) {
        render_stage_data_newsub(node, data, stateIndex);
    }
    var subData = data.subs[stateIndex];
    data.last = data.curr;
    data.curr = stateIndex;
    return (render_stage_boxId = subData.id);
}
function render_stage_ani(stageBoxId, aniType, aniEnd) {
    render_stage_ani_doing = true;
    var node = getElementById(stageBoxId);
    var data = render_stage_data_get(stageBoxId);
    var subs = data.subs;
    var last = data.last;
    var curr = data.curr;
    var lastSub = subs[last];
    var currSub = subs[curr];
    var goForward = curr > last;
    var renderFromStage = false;
    var lastNode = getElementById(lastSub.id);
    var currNode = getElementById(currSub.id);
    if (lastSub !== currSub) {
        renderFromStage = currSub.inStage && render_base_stageCache_usable;
        //在iphone下判断web宿主容器的行为，如果发现是宿主切换的页面就不做动画，原因是宿主的行为不能被阻止，
        var is_host_behaviour = curr < last && render_stage_change_check_host_behaviour_onStageChangeBack();
        // window._setTitle && _setTitle(is_host_behaviour ? '1111' : '000000');
        if (render_base_stageChange_usable && !is_host_behaviour) {
            var winWidth = docElem.clientWidth;
            var winHeight = docElem.clientHeight;
            var bodyBackgroundColor = core_dom_getComputedStyle(document.body, 'backgroundColor');
            render_stage_webkitTransitionDestroyFn && render_stage_webkitTransitionDestroyFn();
            var currLeft = (goForward ? winWidth : -winWidth/3);
            currNode.style.top = 0;
            currNode.style.left = currLeft + 'px';
            if (goForward) {
                lastNode.style.zIndex = 99;
                currNode.style.zIndex = 100;
                currNode.style.boxShadow = '0 0 20px 0 rgba(0,0,0,0.40)';
                currNode.style.backgroundColor = bodyBackgroundColor;
            } else {
                currNode.style.zIndex = 99;
                lastNode.style.zIndex = 100;
                lastNode.style.boxShadow = '0 0 20px 0 rgba(0,0,0,0.40)';
                lastNode.style.backgroundColor = bodyBackgroundColor;
            }
            currNode.style.display = '';
            render_stage_input_focused = false;
            render_stage_webkitTransitionDestroyFn = node_webkitTransitionDestroy;
            render_stage_style_rewrite();
            setTimeout(function() {
                currNode.style.WebkitTransform = 'translate3d(' + (-currLeft) + 'px, 0, 0)';
                lastNode.style.WebkitTransform = 'translate3d(' + (goForward ? -winWidth/3 : winWidth) + 'px, 0, 0)';
                core_dom_className(currNode, render_stage_ani_transition_class);
                core_dom_className(lastNode, render_stage_ani_transition_class);
                core_event_addEventListener(node, 'webkitTransitionEnd', node_webkitTransitionEnd);
            }, 199);
            function node_webkitTransitionEnd(e) {
                var target = (e.target || e.srcElement);
                if (target !== currNode && target !== lastNode) {
                    return;
                }
                node_webkitTransitionDestroy();
            }
            function node_webkitTransitionDestroy() {
                if (!render_stage_webkitTransitionDestroyFn) {
                    return;
                }
                render_stage_webkitTransitionDestroyFn = false;
                core_event_removeEventListener(node, 'webkitTransitionEnd', node_webkitTransitionEnd);
                core_dom_className(currNode, undefined, render_stage_ani_transition_class);
                core_dom_className(lastNode, undefined, render_stage_ani_transition_class);
                currNode.style.cssText = '';
                lastNode.style.cssText = 'display:none';
                render_stage_style_rewrite();
                doDestroy();
                callAniEnd();
            }
        } else {
            if (render_base_stageChange_usable && is_host_behaviour) {
                lastNode.style.display = 'none';
                currNode.style.display = '';
                doDestroy();
                callAniEnd();
            } else {//当不是系统切换页面行为时使用等待的方式解决透传问题
                setTimeout(function() {
                    lastNode.style.display = 'none';
                    currNode.style.display = '';
                    doDestroy();
                    callAniEnd();
                }, 366);
            }
        }
    } else {
        currNode.style.display = '';
        callAniEnd();
    }
    if (currSub) {
        currSub.inStage = true;
    }
    return renderFromStage;
    function doDestroy() {
        var index = router_history_getStateIndex();
        render_stage_destroy(data, index + 1);
        if (!render_base_stageCache_usable) {
            render_stage_destroy(data, 0, index - 1);
        }
    }
    function callAniEnd() {
        if (aniEnd) {
            aniEnd(currSub.id, lastSub.id, renderFromStage);
        }
        render_stage_touch_status_started = false;
        setTimeout(function() {
            render_stage_ani_doing = false;
        }, 377);
    }
}
/**
 * 销毁场景下无用的子
 */
function render_stage_destroy(data, fromIndex, toIndex) {
    var subs = data.subs;
    var destroySubs = [];
    toIndex = toIndex === undefined ? (subs.length - 1) : toIndex;
    for (var i = fromIndex; i <= toIndex; ++i) {
        destroySubs.push(subs[i]);
        subs[i] = undefined;
    }
    setTimeout(function() {
        for (var i = 0, l = destroySubs.length; i < l; ++i) {
            if (destroySubs[i]) {
                var subId = destroySubs[i].id;
                !function(subId) {
                    setTimeout(function() {
                        try{
                            render_control_destroy(subId);
                        } catch(e) {
                            log('Error: destroy subId(' + subId + ') error in stage!');
                        } finally {
                            core_dom_removeNode(getElementById(subId));
                        }
                    });
                }(subId);
            }
        }
    }, 377);
}
/**
 * 新建子数据和节点 step 步数
 */
function render_stage_data_newsub(node, data, stateIndex) {
    var subId = render_base_idMaker();
    var subNode = core_dom_createElement('div');
    subNode.id = subId;
    core_dom_className(subNode, render_stage_subNode_class);
    core_dom_setAttribute(subNode, 's-stage-sub', 'true');
    subNode.innHTML = render_base_stageDefaultHTML;
    subNode.style.display = 'none';
    node.appendChild(subNode);
    var subs = data.subs;
    subs[stateIndex] = {
        id: subId
    };
    if (stateIndex >= render_base_stage_maxLength) {
        render_stage_destroy(data, 0, stateIndex - render_base_stage_maxLength + 1);
        return true;
    }
}
/**
 * 产生并获取数据结构
 */
function render_stage_data_get(stageBoxId, stateIndex) {
    if (!render_stage_data[stageBoxId]) {
        render_stage_data[stageBoxId] = {
            last: stateIndex,
            curr: stateIndex,
            subs: []
        };
    }
    return render_stage_data[stageBoxId];
}
//fixed元素处理 解决动画时和动画后fixed节点抖动的问题
function render_stage_style_init() {
    if (!render_base_stage_usable) {
        return;
    }
    var styleTextArray = [];
    if (render_base_stageChange_usable) {
        styleTextArray.push('body{overflow:hidden;-webkit-overflow-scrolling : touch;}');//
        styleTextArray.push('.' + render_stage_ani_transition_class + '{-webkit-transition: -webkit-transform 0.4s ease-out;transition: transform 0.4s ease-out;}');
        styleTextArray.push('.' + render_stage_subNode_class + '{position:absolute;top:0;left:0;width:100%;height:100%;overflow:hidden;}');
        styleTextArray.push('.' + render_stage_scroll_class + '{position:absolute;top:0;left:0;width:100%;height:100%;overflow-x:hidden;overflow-y:auto;-webkit-overflow-scrolling:touch;-webkit-box-sizing : border-box;}');
    }
    styleTextArray.push('.' + render_stage_fixed_class + '{position:fixed!important;}');
    var styleEl = core_dom_createElement('style');
    core_dom_setAttribute(styleEl, 'type', 'text/css');
    styleEl.id = render_stage_style_mainId;
    styleEl.innerHTML = styleTextArray.join('');
    head.appendChild(styleEl);
}
/**
 * Steel自带样式重写方法，当处于动画中时fixed节点使用abosolute，当input得到焦点时scroll节点删除overflow-y：auto，解决input聚焦时业务样式丢失的问题
 */
function render_stage_style_rewrite() {
    var styleTextArray = [];
    if (render_stage_webkitTransitionDestroyFn) {
        styleTextArray.push('.' + render_stage_fixed_class + '{position:absolute!important;}');
    }
    if (render_stage_input_focused) {
        styleTextArray.push('.' + render_stage_scroll_class + '{overflow-y: visible!important;}');
    }
    var styleEl = getElementById(render_stage_style_rewriteId);
    if (!styleEl) {
        styleEl = core_dom_createElement('style');
        core_dom_setAttribute(styleEl, 'type', 'text/css');
        styleEl.id = render_stage_style_rewriteId;
        styleEl.innerHTML = styleTextArray.join('');
        head.appendChild(styleEl);
    } else {
        styleEl.innerHTML = styleTextArray.join('');
    }
}
//解析jade fun
function render_parse(jadeFunStr) {
    var g;
    var result = [];
    var ret = [];
    var reg = /<[a-z]+([^>]*?s-(child)[^>]*?)>/g;//|tpl|data|css|logic
    while (g = reg.exec(jadeFunStr)) {
        var ele = g[1].replace(/\\\"/g, '"');
        var oEle = ele.replace(/\"/g, '').replace(/ /g, '&');
        var eleObj = core_queryToJson(oEle);
        var id = render_base_idMaker();
        eleObj['s-id'] = id;
        eleObj['s-all'] = ele;
        result.push(eleObj);
    }
    reg = RegExp('(class=\"[^\]*?' + render_stage_scroll_class + '[^\]*?\")');
    if (g = reg.exec(jadeFunStr)) {
        result.push({
            's-stage-scroll': true,
            's-all': g[1].replace(/\\\"/g, '"'),
            's-id': render_base_idMaker()
        });
    }
    return result;
}/*
 * 处理子模块
*/


function render_control_handleChild(boxId, tplParseResult) {
    var resContainer = render_base_resContainer[boxId];
    var s_controller, s_child, s_id;
    var parseResultEle;
    var childResContainer = {};
    for (var i = 0, len = tplParseResult.length; i < len; i++) {
        parseResultEle = tplParseResult[i];
        if (parseResultEle['s-stage-scroll']) {
            continue;
        }
        s_id = parseResultEle['s-id'];
        childResContainer = render_base_resContainer[s_id] = render_base_resContainer[s_id] || {
            boxId: s_id,
            childrenid: {},
            s_childMap: {},
            needToTriggerChildren: false,
            toDestroyChildrenid: null,
            forceRender: false,
            lastRes:{},
            fromParent: true
        };
        resContainer.childrenid[s_id] = true;
        childResContainer.parentId = boxId;
        childResContainer.tpl = parseResultEle['s-tpl'];
        childResContainer.css = parseResultEle['s-css'];
        childResContainer.data = parseResultEle['s-data'];
        childResContainer.logic = parseResultEle['s-logic'];
        if(s_child = parseResultEle['s-child']) {
            s_child = (s_child === 's-child' ? '' : s_child);
            if(s_child) {
                s_controller = resContainer.children && resContainer.children[s_child];
                resContainer.s_childMap[s_child] = s_id;
            } else {
                s_controller = parseResultEle['s-controller']
            }
            render_run(s_id, s_controller);//渲染提前
        }
    }
}

//用户扩展类
function render_control_setExtTplData_F() {}
render_control_setExtTplData_F.prototype.constructor = render_control_setExtTplData_F;
//用于帮助用户设置子模块数据的方法：steel_s_data(data) data为要设置的对象，设置后
render_control_setExtTplData_F.prototype.steel_s_data = render_control_sData;
//用户扩展全局功能方法
function render_control_setExtTplData(obj) {
    if (!core_object_isObject(obj)) {
        log('Error:The method "steel.setExtTplData(obj)" used in your app need an object as the param.');
        return;
    }
    render_control_setExtTplData_F.prototype = obj;
}
/**
 * 触发rendered事件
 */
function render_control_triggerRendered(boxId) {
    core_notice_trigger('rendered', {
        boxId: boxId,
        controller: render_base_controllerNs[boxId]
    });
}
var render_control_render_moduleAttrName = 's-module';
var render_control_render_moduleAttrValue = 'ismodule';
function render_control_render(resContainer) {
    //如果是react组件，执行react_render逻辑
    if(resContainer.component){
        log("render_control_component_render",resContainer);
        render_control_component_render(resContainer);
    }
    var boxId = resContainer.boxId;
    if ( !resContainer.dataReady || !resContainer.tplReady || resContainer.rendered) {
        return;
    }
    var tplFn = resContainer.tplFn;
    var real_data = resContainer.real_data;
    if (!tplFn || !real_data) {
        return;
    }
    var html = resContainer.html;
    if (!html) {
        render_control_sData_setBoxId(boxId);
        var parseResultEle = null;
        var extTplData = new render_control_setExtTplData_F();
        var retData = extTplData;
        for (var key in real_data) {
            retData[key] = real_data[key];
        }
        try {
            html = tplFn(retData);
        } catch (e) {
            render_error(e);
            render_control_triggerError(resContainer, 'render', e);
            return;
        }
        resContainer.html = html;
    }
    if (!resContainer.cssReady) {
        return;
    }
    //子模块分析
    resContainer.childrenid = {};
    var tplParseResult = render_parse(html);
    resContainer.stageScrollId = undefined;
    //去掉节点上的资源信息
    for (var i = 0, len = tplParseResult.length; i < len; i++) {
        parseResultEle = tplParseResult[i];
        if (parseResultEle['s-stage-scroll']) {
            resContainer.stageScrollId = parseResultEle['s-id'];
            html = html.replace(parseResultEle['s-all'], parseResultEle['s-all'] + ' id=' + parseResultEle['s-id']);
        } else {
            html = html.replace(parseResultEle['s-all'], ' ' + render_control_render_moduleAttrName + '=' + render_control_render_moduleAttrValue + ' ' + parseResultEle['s-all'] + ' id=' + parseResultEle['s-id']);
        }
    }
    resContainer.html = html;
    ////@finrila 由于做场景管理时需要BOX是存在的，所以调整渲染子模块流程到写入HTML后再处理子模块，那么每个模块的box在页面上是一定存在的了
    var box = getElementById(boxId);
    render_control_destroyLogic(resContainer);
    render_control_destroy(resContainer.toDestroyChildrenid, false);
    box.innerHTML = html;
    resContainer.rendered = true;
    render_control_startLogic(resContainer);
    render_control_handleChild(boxId, tplParseResult);
    render_control_setCss_destroyCss(resContainer, true);
    render_control_triggerRendered(boxId);
}
function render_control_component_render(resContainer) {
    console.log(resContainer);
    if(!resContainer.componentReady || !resContainer.cssReady || resContainer.rendered){
        return;
    }
    var boxId = resContainer.boxId;
    var real_data = resContainer.real_data;
    var virtualDom = resContainer.virtualDom;
    if (!virtualDom) {
        try {
            resContainer.virtualDom = ReactDOM.render(
                React.createElement(resContainer.component, {data:real_data}, null),
                getElementById(boxId)
            );
        } catch (e) {
            render_error(e);
            render_control_triggerError(resContainer, 'render', e);
            return;
        }
    }
    resContainer.rendered = true;
    render_control_setCss_destroyCss(resContainer, true);
    render_control_triggerRendered(boxId);
}

/**
 * 获取 url 的目录地址
 */
function core_urlFolder(url){
    return url.substr(0, url.lastIndexOf('/') + 1);
}
/**
 * 命名空间的适应
 */
function core_nameSpaceFix(id, basePath) {
    basePath = basePath && core_urlFolder(basePath);
    if (id) {
        if (id.indexOf('.') === 0) {
            id = basePath ? (basePath + id).replace(/\/\.\//, '/') : id.replace(/^\.\//, '');
        }
        while (id.indexOf('../') !== -1) {
            id = id.replace(/\w+\/\.\.\//, '');
        }
    }
    return id;
}


var render_control_setCss_cssCache = {};//css容器
var render_control_setCss_cssCallbackFn;
function render_control_setCss(resContainer) {
    var cssCallbackFn;
    var startTime = null;
    var endTime = null;
    var css = resContainer.css;
    if (!css) {
        cssReady();
        return;
    }
    var boxId = resContainer.boxId;
    var box;
    var cssId;
    var controllerNs = render_base_controllerNs[boxId];
    var css = core_nameSpaceFix(resContainer.css, controllerNs);
    //给模块添加css前缀
    if (render_base_useCssPrefix_usable && (box = getElementById(boxId)) && (cssId = resource_res_getCssId(css))) {
        core_dom_className(box, cssId);
    }
    if (render_control_setCss_cssCache[css]) {
        render_control_setCss_cssCache[css][boxId] = true;
        cssReady();
        return;
    }
    render_control_setCss_cssCache[css] = {};
    render_control_setCss_cssCache[css][boxId] = true;
    var cb = render_control_setCss_cssCallbackFn = function(){
        if(cb === render_control_setCss_cssCallbackFn) {
            endTime = now();
            core_notice_trigger('cssTime', {
                startTime: startTime,
                cssTime: (endTime - startTime) || 0,
                ctrlNS: controllerNs
            });
            cssReady();
            //抛出css加载完成事件
        }
    };
    startTime = now();
    resource_res.css(css, cb, function() {
        cssReady();
        render_control_triggerError(resContainer, 'css', css);
    });
    function cssReady() {
        resContainer.cssReady = true;
        render_control_render(resContainer);
    }
}
function render_control_setCss_destroyCss(resContainer, excludeSelf) {
    var boxId = resContainer.boxId;
    var controllerNs = render_base_controllerNs[boxId];
    var excludeCss = excludeSelf && core_nameSpaceFix(resContainer.css, controllerNs);
    for(var css in render_control_setCss_cssCache) {
        if (excludeCss === css) {
            continue;
        }
        var cssCache = render_control_setCss_cssCache[css];
        if (cssCache[boxId]) {
            delete cssCache[boxId];
            !function() {
                for (var _boxId in cssCache) {
                    return;
                }
                resource_res.removeCss(css);
                delete render_control_setCss_cssCache[css];
            }();
        }
    }
}

function render_control_setChildren(resContainer) {
    var children = resContainer.children || {};
    for (var key in children) {
        //如果存在，相应的key则运行
        if (resContainer.s_childMap[key]) {
            render_run(resContainer.s_childMap[key], children[key]);
        }
    }
}
function render_control_destroyChildren(childrenid) {
    render_control_destroy(childrenid);
}






function render_control_setTpl(resContainer) {
    var controllerNs = render_base_controllerNs[resContainer.boxId];
    var tplCallbackFn;
    var startTime = null;
    var endTime = null;
    var tpl = resContainer.tpl;
    resContainer.tplFn = null;
    if(tpl){
        if(core_object_typeof(tpl) === 'function'){
            resContainer.tplFn = tpl;
            render_control_setTpl_toRender(resContainer);
            return;
        }
        var cb = tplCallbackFn = function(jadefn){
            if(cb === tplCallbackFn){
                endTime = now();
                core_notice_trigger('tplTime', {
                    startTime: startTime,
                    tplTime: endTime - startTime || 0,
                    ctrlNS: controllerNs
                });
                resContainer.tplFn = jadefn;
                render_control_setTpl_toRender(resContainer);
            }
        };
        startTime = now();
        require_global(tpl, cb, function() {
            render_error();
            render_control_triggerError(resContainer, 'tpl', tpl);
        }, controllerNs);
    }
}
function render_control_setTpl_toRender(resContainer) {
    resContainer.tplReady = true;
    render_control_render(resContainer);
}



//http://www.sharejs.com/codes/javascript/1985
function core_object_equals(x, y){
    // If both x and y are null or undefined and exactly the same
    if ( x === y ) {
        return true;
    }
    // If they are not strictly equal, they both need to be Objects
    if ( ! ( x instanceof Object ) || ! ( y instanceof Object ) ) {
        return false;
    }
    // They must have the exact same prototype chain, the closest we can do is
    // test the constructor.
    if ( x.constructor !== y.constructor ) {
        return false;
    }
    for ( var p in x ) {
        // Inherited properties were tested using x.constructor === y.constructor
        if ( x.hasOwnProperty( p ) ) {
            // Allows comparing x[ p ] and y[ p ] when set to undefined
            if ( ! y.hasOwnProperty( p ) ) {
                return false;
            }
            // If they have the same strict value or identity then they are equal
            if ( x[ p ] === y[ p ] ) {
                continue;
            }
            // Numbers, Strings, Functions, Booleans must be strictly equal
            if ( typeof( x[ p ] ) !== "object" ) {
                return false;
            }
            // Objects and Arrays must be tested recursively
            if ( ! core_object_equals( x[ p ],  y[ p ] ) ) {
                return false;
            }
        }
    }
    for ( p in y ) {
        // allows x[ p ] to be set to undefined
        if ( y.hasOwnProperty( p ) && ! x.hasOwnProperty( p ) ) {
            return false;
        }
    }
    return true;
};
function render_contorl_toTiggerChildren(resContainer) {
    if (resContainer.needToTriggerChildren) {
        var s_childIdMap = {};
        if (resContainer.childrenChanged) {
            for (var s_child in resContainer.s_childMap) {
                s_childIdMap[resContainer.s_childMap[s_child]] = true;
            }
        }
        for (var id in resContainer.childrenid) {
            if (s_childIdMap[id]) {
                continue;
            }
            var childControl = render_base_controlCache[id];
            if (childControl) {
                render_run(id, childControl._controller);
            }
        }
    }
    resContainer.needToTriggerChildren = false;
}





var render_control_setData_dataCallbackFn;
function render_control_setData(resContainer, tplChanged) {
    var data = resContainer.data;
    // var isMain = getElementById(resContainer.boxId) === mainBox;
    var controllerNs = render_base_controllerNs[resContainer.boxId];
    var startTime = null;
    var endTime = null;
    var real_data;
    // var ajaxRunTime = 10;//计算ajax时间时，运行时间假定需要10ms（实际在10ms内）
    if (data === null || data === 'null') {
        render_control_setData_toRender({}, resContainer, tplChanged);
        return;
    }
    if (!data) {
        return;
    }
    var dataType = core_object_typeof(data);
    if (dataType === 'object') {
        render_control_setData_toRender(data, resContainer, tplChanged);
    } else if (dataType === 'string') {
        real_data = render_control_sData_getData(data);
        if (real_data) {
            render_control_setData_toRender(real_data, resContainer, tplChanged);
            return;
        }
        var cb = render_control_setData_dataCallbackFn = function(ret) {
            if (cb === render_control_setData_dataCallbackFn) {
                //拿到ajax数据
                endTime = now();
                core_notice_trigger('ajaxTime', {
                    startTime: startTime,
                    ajaxTime: (endTime - startTime) || 0,
                    ctrlNS: controllerNs
                });
                render_control_setData_toRender(ret.data, resContainer, tplChanged);
            }
        };
        //开始拿模块数据
        startTime = now();
        resource_res.get(data, cb, function(ret){
            resContainer.real_data = null;
            render_error(ret);
            render_control_triggerError(resContainer, 'data', data, ret);
        });
    }
}
function render_control_setData_toRender(data, resContainer, tplChanged) {
    resContainer.dataReady = true;
    if (resContainer.forceRender || tplChanged || !core_object_equals(data, resContainer.real_data)) {
        resContainer.real_data = data;
        render_control_render(resContainer);
    } else {
        render_control_triggerRendered(resContainer.boxId);
        render_contorl_toTiggerChildren(resContainer);
    }
}
function render_control_setComponent(resContainer) {
    var controllerNs = render_base_controllerNs[resContainer.boxId];
    var componentCallbackFn;
    var startTime = null;
    var endTime = null;
    var component = resContainer.component;
    resContainer.componentReady = false;
    resContainer.componentFn = null;
    if(component){
        if(core_object_typeof(component) === 'function'){
            resContainer.componentFn = component;
            render_control_setComponent_toRender(resContainer);
            return;
        }
        var cb = componentCallbackFn = function(component){
            if(cb === componentCallbackFn){
                endTime = now();
                core_notice_trigger('componentTime', {
                    startTime: startTime,
                    componentTime: endTime - startTime || 0,
                    ctrlNS: controllerNs
                });
                resContainer.component = component;
                render_control_setComponent_toRender(resContainer);
            }
        };
        startTime = now();
        require_global(component, cb, function() {
            render_error();
            render_control_triggerError(resContainer, 'component', component);
        }, controllerNs);
    }
}
function render_control_setComponent_toRender(resContainer) {
    resContainer.componentReady = true;
    render_control_render(resContainer);
}

//检查资源是否改变
function render_control_checkResChanged(resContainer, type, value) {
    var valueType = core_object_typeof(value);
    var res = resContainer[type];
    var resFun = resContainer[type+ 'Fn'];
    if (resContainer.lastRes && type in resContainer.lastRes) {
        return resContainer.lastRes[type] !== value;
        // return render_control_checkResChanged(resContainer.lastRes, type, value);
    }
    if (type === 'data') {
        return true;
    }
    if (valueType === 'function') {
        return !resFun || resFun.toString() !== value.toString();
    }
    /*if (type === 'tpl' || type === 'logic') {
        return !(resContainer[type + 'Fn'] && resContainer[type + 'Fn'] === require_runner(value)[0]);
    }*/
    if (type === 'children') {
        return !core_object_equals(res, value);
    }
    return res !== value;
}
var render_control_main_types = ['css', 'tpl', 'data', 'logic', 'component'];
var render_control_main_realTypeMap = {
    tpl: 'tplFn',
    data: 'real_data',
    logic: 'logicFn',
    component: 'compositeFn'
};
var render_control_main_eventList = [
  'init',//模块初始化
  'enter',//模块从其他模块切换进入（不一定只发生在初始化时）
  'leave',//模块离开（不一定销毁）
  'error',//模块运行时错误，类型资源错误（data,tpl,css,logic）、渲染错误(render)、逻辑运行错误(run,runLogic)
  'destroy'//模块销毁事件
  ];
function render_control_main(boxId) {
    //资源容器
    var resContainer = render_base_resContainer[boxId] = render_base_resContainer[boxId] || {
        boxId: boxId,
        childrenid: {},
        s_childMap: {},
        needToTriggerChildren: false,
        toDestroyChildrenid: null,
        forceRender: false
    };
    var box = getElementById(boxId);
    var dealCalledByUser;
    //状态类型 newset|loading|ready
    //tpl,css,data,logic,children,render,
    //tplReady,cssReady,dataReady,logicReady,rendered,logicRunned
    var changeResList = {};
    var control = {
        id: boxId,
        setForceRender: function(_forceRender) {
            resContainer.forceRender = _forceRender;
        },
        get: function(type) {
            return resContainer && resContainer[type];
        },
        set: function(type, value, toDeal) {
            if (!boxId) {
                return;
            }
            if (core_object_typeof(type) === 'object') {
                toDeal = value;
                for (var key in type) {
                    control.set(key, type[key]);
                }
                if (toDeal) {
                    deal();
                }
                return;
            }
            changeResList[type] = render_control_checkResChanged(resContainer, type, value);
            resContainer[type] = value;
            if (changeResList[type] && toDeal) {
                deal();
            }
        },
        /**
         * 控制器事件
         */
        on: function(type, fn) {
            if (render_control_main_eventList.indexOf(type) > -1) {
                core_notice_on(boxId + type, fn);
            }
        },
        off: function(type, fn) {
            if (render_control_main_eventList.indexOf(type) > -1 && fn) {
                core_notice_off(boxId + type, fn);
            }
        },
        refresh: function(forceRender) {
            resContainer.needToTriggerChildren = true;
            if (forceRender) {
                resContainer.real_data = undefined;
            }
            changeResList['data'] = true;
            deal();
        },
        /**
         * 资源处理接口,用户可以使用这个接口主动让框架去分析资源进行处理
         * @type {undefined}
         */
        deal: deal,
        _destroy: function() {
            for (var i = render_control_main_eventList.length - 1; i >= 0; i--) {
                core_notice_off(boxId + render_control_main_eventList[i]);
            }
            boxId = control._controller = resContainer = box = undefined;
        }
    };
    init();
    return control;
    function init() {
        resContainer.needToTriggerChildren = true;
        //状态
        resContainer.cssReady = true;
        resContainer.dataReady = true;
        resContainer.tplReady = true;
        resContainer.logicReady = true;
        resContainer.rendered = true;
        resContainer.logicRunned = false;
        //react 组件加载状态
        resContainer.componentReady = true;
        //第一层不能使用s-child与s-controller，只能通过render_run执行controller
        var type, attrValue;
        resContainer.lastRes = {};
        changeResList = {};
        for (var i = 0, l = render_control_main_types.length; i < l; ++i) {
            type = render_control_main_types[i];
            type !== 'data' && (resContainer.lastRes[type] = resContainer[type]);
            if (box) {
                attrValue = core_dom_getAttribute(box, 's-' + type);
                if (attrValue) {
                    if (render_control_checkResChanged(resContainer, type, attrValue)) {
                        changeResList[type] = true;
                        resContainer[type] = attrValue;
                    }
                } else {
                    if (type in resContainer) {
                        delete resContainer[type];
                    }
                }
            }
            if (resContainer.fromParent) {
                if (resContainer[type]) {
                    changeResList[type] = true;
                }
            }
        }
        resContainer.fromParent = false;
    }
    function deal(isSelfCall) {
        if (isSelfCall) {
            if (dealCalledByUser) {
                return;
            }
        } else {
            dealCalledByUser = true;
        }
        resContainer.lastRes = null;
        var tplChanged = changeResList['tpl'];
        var dataChanged = changeResList['data'];
        var cssChanged = changeResList['css'];
        var logicChanged = changeResList['logic'];
        var componentChanged = changeResList['component'];
        resContainer.childrenChanged = changeResList['children'];
        changeResList = {};
        if(componentChanged){
            resContainer.rendered = false;
            resContainer.virtualDom = '';
        }
        else if (tplChanged || dataChanged) {
            resContainer.rendered = false;
            resContainer.html = '';
            resContainer.toDestroyChildrenid = core_object_clone(resContainer.childrenid);
        } else {
            render_contorl_toTiggerChildren(resContainer);
        }
        if (componentChanged) {
            resContainer.componentReady = false;
        }
        if (tplChanged) {
            resContainer.tplReady = false;
        }
        if (dataChanged) {
            resContainer.dataReady = false;
        }
        if (cssChanged) {
            resContainer.cssReady = false;
        }
        if (logicChanged) {
            resContainer.logicReady = false;
        }
        !resContainer.tpl && delete resContainer.tplFn;
        !resContainer.logic && delete resContainer.logicFn;
        componentChanged && render_control_setComponent(resContainer);
        tplChanged && render_control_setTpl(resContainer);
        dataChanged && render_control_setData(resContainer, tplChanged);
        cssChanged && render_control_setCss(resContainer);
        logicChanged && render_control_setLogic(resContainer);
        resContainer.childrenChanged && render_control_setChildren(resContainer);
    }
}
var render_run_controllerLoadFn = {};
var render_run_rootScope = {};
var render_run_renderingMap = {};
var render_run_renderedTimer;
core_notice_on('stageChange', function() {
    render_run_renderingMap = {};
});
core_notice_on('rendered', function(module) {
    delete render_run_renderingMap[module.boxId];
    if (render_run_renderedTimer) {
        clearTimeout(render_run_renderedTimer);
    }
    // render_run_renderedTimer = setTimeout(function() {
        if (core_object_isEmpty(render_run_renderingMap)) {
            core_notice_trigger('allRendered');
            core_notice_trigger('allDomReady');
        }
    // }, 44);
});
//controller的boot方法
function render_run(stageBox, controller) {
    var stageBoxId, boxId, control, controllerLoadFn, controllerNs;
    var startTime = null;
    var endTime = null;
    var routerType = router_router_get().type;
    var isMain = stageBox === mainBox;
    var renderFromStage;
    var lastBoxId;
    if (typeof stageBox === 'string') {
        stageBoxId = stageBox;
        stageBox = getElementById(stageBoxId);
    } else {
        stageBoxId = stageBox.id;
        if (!stageBoxId) {
            stageBox.id = stageBoxId = render_base_idMaker();
        }
    }
    boxId = stageBoxId;
    if (render_base_stage_usable && isMain) {
        boxId = render_stage(stageBoxId, routerType);
        renderFromStage = render_stage_ani(stageBoxId, '', function(currId, lastId, renderFromStage) {
            if (currId !== lastId) {
                lastBoxId = lastId;
                core_notice_trigger(lastId + 'leave', function(transferData) {
                    if (transferData) {
                        router_history_state_set(router_router_transferData_key, transferData);
                    }
                });
                if (renderFromStage && routerType.indexOf('refresh') === -1) {
                    triggerEnter(false);
                }
            }
        });
        core_notice_trigger('stageChange', getElementById(boxId), renderFromStage);
        render_run_renderingMap[boxId] = true;
        if (!renderFromStage || routerType.indexOf('refresh') > -1) {
            async_controller();
        } else {
            render_control_triggerRendered(boxId);
        }
    } else {
        if (isMain) {
            core_notice_trigger('stageChange', getElementById(boxId), false);
        }
        render_run_renderingMap[boxId] = true;
        async_controller();
    }
    function async_controller() {
        //处理异步的controller
        render_run_controllerLoadFn[boxId] = undefined;
        if (core_object_isString(controller)) {
            render_base_controllerNs[boxId] = controller;
            controllerLoadFn = render_run_controllerLoadFn[boxId] = function(controller){
                if (controllerLoadFn === render_run_controllerLoadFn[boxId] && controller) {
                    endTime = now();
                    core_notice_trigger('ctrlTime', {
                        startTime: startTime,
                        ctrlTime: (endTime - startTime) || 0,
                        ctrlNS: controllerNs
                    });
                    render_run_controllerLoadFn[boxId] = undefined;
                    run_with_controllerobj(controller);
                }
            };
            startTime = now();
            require_global(controller, controllerLoadFn, render_error);
            return;
        } else {
            run_with_controllerobj();
        }
        ////
    }
    function run_with_controllerobj(controllerobj) {
        controller = controllerobj || controller;
        if (stageBox !== document.body) {
            //找到它的父亲
            var parentNode = stageBox.parentNode;
            var parentResContainer;
            while(parentNode && parentNode !== docElem && (!parentNode.id || !(parentResContainer = render_base_resContainer[parentNode.id]))) {
                parentNode = parentNode.parentNode;
            }
            if (parentResContainer) {
                parentResContainer.childrenid[boxId] = true;
            }
        }
        control = render_base_controlCache[boxId];
        if (control) {
            if (control._controller === controller) {
                control.refresh();
                triggerEnter(false);
                return;
            }
            if (control._controller) {
                control._destroy();
            }
        }
        render_base_controlCache[boxId] = control = render_control_main(boxId);
        if (controller) {
            control._controller = controller;
            controller(control, render_run_rootScope);
        }
        control.deal(true);
        triggerEnter(true);
    }
    function triggerEnter(isInit) {
        var transferData = router_history_state_get(router_router_transferData_key);
        if (isInit) {
            core_notice_trigger(boxId + 'init', transferData);
        }
        core_notice_trigger(boxId + 'enter', transferData, isInit);
    }
}



//@Finrila 未处理hashchange事件
var router_listen_queryTime = 5;
var router_listen_count;
var router_listen_lastStateIndex = undefined;
function router_listen() {
    router_listen_lastStateIndex = router_history_getStateIndex();
    //绑定link
    core_event_addEventListener(document, 'click', function(e) {
        //e.target 是a 有.href　下一步，或者不是a e.target.parentNode
        //向上查找三层，找到带href属性的节点，如果没有找到放弃，找到后继续
        var el = e.target;
        router_listen_count = 1;
        var hrefNode = router_listen_getHrefNode(el);
        var href = hrefNode && hrefNode.href;
        //如果A连接有target=_blank或者用户同时按下command(新tab打开)、ctrl(新tab打开)、alt(下载)、shift(新窗口打开)键时，直接跳链。
        //@shaobo3  （此处可以优化性能@Finrila）
        if (!href || href.indexOf('javascript:') === 0 || hrefNode.getAttribute("target") === "_blank" || e.metaKey || e.ctrlKey || e.altKey || e.shiftKey) {
            return;
        }
        core_event_preventDefault(e);
        router_router_set(href);
    });
    var popstateTime = 0;
    core_event_addEventListener(window, 'popstate', function() {
        core_notice_trigger('popstate');
        var currentStateIndex = router_history_getStateIndex();
        if (router_listen_lastStateIndex === currentStateIndex || router_base_currentHref === href) {
            return;
        }
        var href = location.href;
        if (router_listen_lastStateIndex > currentStateIndex) {
            if (router_base_routerType === 'refresh') {
                router_base_routerType = 'back-refresh';
            } else {
                router_base_routerType = 'back';
            }
        } else {
            router_base_routerType = 'forward';
        }
        router_listen_lastStateIndex = currentStateIndex;
        router_listen_handleHrefChenged(href);
    });
}
function router_listen_getHrefNode(el) {
    if (el && router_listen_count < router_listen_queryTime) {
        router_listen_count++;
        if (el.tagName && el.tagName.toLowerCase() === 'a') {
            return el;
        }
        return router_listen_getHrefNode(el.parentNode);
    }
}
function router_listen_handleHrefChenged(url) {
    router_base_prevHref = router_base_currentHref;
    router_history_state_set(router_router_prevHref_key, router_base_prevHref);
    router_base_currentHref = url;
    router_listen_lastStateIndex = router_history_getStateIndex();
    if (router_router_get(true).config) {
        router_listen_fireRouterChange();
    } else {
        location.reload();
    }
}
//派发routerChange事件，返回router变化数据 @shaobo3
function router_listen_fireRouterChange() {
    core_notice_trigger('routerChange', router_router_get());
}



//当前访问path的变量集合,以及location相关的解析结果
var router_router_value;
var router_router_transferData;
var router_router_isRouterAPICalled;
var router_router_transferData_key = '-steel-router-transferData';
var router_router_backNum_key = '-steel-router-backNum';
var router_router_prevHref_key = '-steel-router-prevHref';
var router_router = {
    fix: function(url) {
        return core_fixUrl(router_router_get().url, url);
    },
    get: router_router_get,
    push: router_router_push,
    replace: router_router_replace,
    set: router_router_set,
    back: router_router_back,
    refresh: router_router_refresh,
    clearTransferData: router_router_clearTransferData
};
core_notice_on('popstate', router_router_onpopstate);
function router_router_onpopstate() {
    if (router_router_isRouterAPICalled) {
        router_router_isRouterAPICalled = undefined;
        router_history_state_set(router_router_transferData_key, router_router_transferData);
    } else {
        router_router_clearTransferData();
    }
    router_router_refreshValue();
}
/**
 * 获取当前路由信息
 * @return {object} 路由信息对象
 */
function router_router_get(refreshRouterValue) {
    if (refreshRouterValue || !router_router_value) {
        router_router_refreshValue();
    }
    return router_router_value;
}
/**
 * 路由前进到某个地址
 * @param  {string} url 页面地址
 * @param  {Object} data 想要传递到新页面的对象
 * @return {undefined}
 */
function router_router_push(url, data) {
    router_router_set(url, data);
}
/**
 * 将路由替换成某个地址
 * @param  {string} url 页面地址
 * @param  {Object} data 想要传递到新页面的对象
 * @return {undefined}
 */
function router_router_replace(url, data) {
    router_router_set(url, true, data);
}
/**
 * 设置路由
 * @param  {string} url     地址 必添
 * @param  {boolean} replace 是否替换当前页面 不产生历史
 * @param  {Object} data 想要传递到新页面的对象
 * @return {undefined}
 */
function router_router_set(url, replace, data) {
    //多态
    if (core_object_isObject(replace)) {
        data = replace;
        replace = false;
    }
    router_router_transferData = data;
    url = core_fixUrl(router_router_get().url, url || '');
    if (!router_base_singlePage || !core_crossDomainCheck(url)) {// || (android && history.length === 1)
        if (replace) {
            location.replace(url);
        } else {
            location.href = url;
        }
    } else {
        if (replace) {
            router_base_routerType = 'replace';
            router_history_replaceState(url);
        } else {
            if (router_base_currentHref !== url) {
                router_base_routerType = 'new';
                router_history_pushState(url);
            } else {
                router_base_routerType = 'refresh';
            }
        }
        router_router_isRouterAPICalled = true;
        router_router_onpopstate();
        router_listen_handleHrefChenged(url);
    }
}
/**
 * 单页面刷新
 * @return {undefined}
 */
function router_router_refresh() {
    if (router_base_singlePage) {
        router_router_set(router_router_get().url);
    } else {
        location.reload();
    }
}
/**
 * 路由后退
 * @param  {string} url 后退后替换的地址 可以为空
 * @param  {number} num 后退的步数 默认为1步 必须为大于0的正整数
 * @param  {Object} data 想要传递到新页面的对象
 * @param  {boolean} refresh 是否在后退后刷新页面
 * @return {undefined}
 */
function router_router_back(url, num, data, refresh) {
    var options = core_argsPolymorphism(arguments, ['url', 'num', 'data', 'refresh'], ['string', 'number', 'object', 'boolean']);
    url = options.url;
    num = options.num;
    data = options.data;
    refresh = options.refresh;
    router_router_transferData = data;
    num = (core_object_isNumber(num) && num > 0) ? num : 1;
    if (router_base_singlePage) {
        if (router_history_getStateIndex() < num) {
            url && location.replace(core_fixUrl(router_router_get().url, url));
            return false;
        }
        core_notice_on('popstate', function popstate() {
            core_notice_off('popstate', popstate);
            var currentUrl = router_router_get().url;
            url = url && core_fixUrl(currentUrl, url);
            if (url && url !== currentUrl) {
                if (core_crossDomainCheck(url)) {
                    router_base_routerType = 'refresh';
                    router_history_replaceState(url);
                    router_router_refreshValue();
                } else {
                    location.replace(url);
                }
            } else if (refresh) {
                router_base_routerType = 'refresh';
            }
        });
        router_router_isRouterAPICalled = true;
        history.go(-num);
        return true;
    } else {
        if (url) {
            location.href = core_fixUrl(router_router_get().url, url);
        } else {
            history.go(-num);
        }
        return true;
    }
}
function router_router_clearTransferData() {
    if (router_base_singlePage) {
        router_history_state_set(router_router_transferData_key, undefined);
    }
}
/**
 * 内部使用的路由信息刷新方法
 * @return {object} 路由信息对象
 */
function router_router_refreshValue() {
    var lastRouterValue = router_router_value;
    var index = 0;
    if (router_base_singlePage) {
        index = router_history_getStateIndex()
    }
    router_router_value = router_parseURL();
    var path = router_router_value.path;
    router_router_value.path = isDebug ? path.replace(/\.(jade)$/g, '') : path;
    router_router_value.search = router_router_value.query;
    router_router_value.query = core_queryToJson(router_router_value.query);
    router_router_value.type = router_base_routerType;
    router_router_value.prev = router_base_prevHref || router_history_state_get(router_router_prevHref_key);
    router_router_value.transferData = router_history_state_get(router_router_transferData_key);
    router_router_value.state = router_history_state();
    router_router_value.index = index;
    router_router_value.lastIndex = lastRouterValue ? lastRouterValue.index : index;
    var matchResult = router_match(router_router_value);
    if (matchResult) {
        router_router_value.config = matchResult.config;
        router_router_value.param = matchResult.param;
    }
    return router_router_value;
}
function resource_fixUrl(url, type) {
    switch(type) {
        case 'js':
            path = resource_jsPath;
            break;
        case 'css':
            path = resource_cssPath;
            break;
        case 'ajax':
            path = resource_ajaxPath;
    }
    var currentRouter = router_router_get();
    //匹配参数{id} -> ?id=2
    // var urlMatch = url.match(/\{(.*?)\}/g);
    if (type === 'ajax') {
        var urlParams = {};
        var hrefParams = currentRouter.query;
        url = url.replace(/\{(.*?)\}/g, function(_, name) {
            if (hrefParams[name]) {
                urlParams[name] = hrefParams[name];
            }
            return '';
        });
        url = core_URL(url).setParams(urlParams).toString();
    }
    var result = resource_fixUrl_handle(path, url, resource_basePath, currentRouter.url.replace(/\/([^\/]+)$/, '/'));
    if ((type === 'js' || type === 'css') && !RegExp('(\\.' + type + ')$').test(url)) {
        result += '.' + type;
    }
    return result;
}
function resource_fixUrl_handle(path, url, basePath, hrefPath) {
    return core_fixUrl(path || basePath || hrefPath, url);
}

/**
 * 异步调用方法
 */
function core_asyncCall(fn, args) {
    setTimeout(function() {
        fn.apply(undefined, args);
    });
}
/**
 * 资源队列管理
 * @params
 * url 请求资源地址
 * succ
 * err
 * access 是否成功
 * data 资源数据
 */
function resource_queue_create(url){
    resource_queue_list[url] = [];
}
function resource_queue_push(url, succ, err){
    resource_queue_list[url].push([succ, err]);
}
function resource_queue_run(url, access, data){
    access = access ? 0 : 1;
    for(var i = 0, len = resource_queue_list[url].length; i < len; i++) {
        var item = resource_queue_list[url][i];
        item[access](data, url);
    }
}
function resource_queue_del(url) {
    url in resource_queue_list && (delete resource_queue_list[url]);
}

/**
 * make an ajax request
 * @alias loader_ajax
 * @param {Object}  {
        'url': '',
        'charset': 'UTF-8',
        'timeout': 30 * 1000,
        'args': {},
        'onComplete': null,
        'onTimeout': null,
        'onFail': null,
        'method': 'get', // post or get
        'asynchronous': true,
        'contentType': 'application/x-www-form-urlencoded',
        'responseType': 'text'// xml or text or json
    };
 * @return {Void}
 * @example
 * loader_ajax(url, {//'url':'/ajax.php',
    'args':{'id':123,'test':'true'},
    });
 */
function loader_ajax(url, onComplete, onFail){//(url, callback)
    var opts = {
        'charset': 'UTF-8',
        'timeout': 30 * 1000,
        'args': {},
        'onComplete': onComplete || emptyFunction,
        'onTimeout': onFail || emptyFunction,
        'uniqueID': null,
        'onFail': onFail || emptyFunction,
        'method': 'get', // post or get
        'asynchronous': true,
        'header' : {},
        'isEncode' : false,
        'responseType': 'json'// xml or text or json
    };
    if (url == '') {
        log('Error: ajax need url in parameters object');
        return;
    }
    var tm;
    var trans = getXHR();
    var cback = function(){
        if (trans.readyState == 4) {
            clearTimeout(tm);
            var data = '';
            if (opts['responseType'] === 'xml') {
                    data = trans.responseXML;
            }else if(opts['responseType'] === 'text'){
                    data = trans.responseText;
            }else {
                try{
                    if(trans.responseText && typeof trans.responseText === 'string'){
                        // data = $.core.json.strToJson(trans.responseText);
                        data = window['eval']('(' + trans.responseText + ')');
                    }else{
                        data = {};
                    }
                }catch(exp){
                    data = url + 'return error : data error';
                    // throw opts['url'] + 'return error : syntax error';
                }
            }
            if (trans.status == 200) {
                if (opts.onComplete != null) {
                    opts.onComplete(data);
                }
            }else if(trans.status == 0){
                //for abort;
            } else {
                if (opts.onFail != null) {
                    opts.onFail(data, trans);
                }
            }
        }
        /*else {
            if (opts['onTraning'] != null) {
                opts['onTraning'](trans);
            }
        }*/
    };
    trans.onreadystatechange = cback;
    if(!opts['header']['Content-Type']){
        opts['header']['Content-Type'] = 'application/x-www-form-urlencoded';
    }
    if(!opts['header']['X-Requested-With']){
        opts['header']['X-Requested-With'] = 'XMLHttpRequest';
    }
    if (opts['method'].toLocaleLowerCase() == 'get') {
        var url = core_URL(url, {
            'isEncodeQuery' : opts['isEncode']
        });
        url.setParams(opts['args']);
        url.setParam('__rnd', new Date().valueOf());
        trans.open(opts['method'], url.toString(), opts['asynchronous']);
        try{
            for(var k in opts['header']){
                trans.setRequestHeader(k, opts['header'][k]);
            }
        }catch(exp){
        }
        trans.send('');
    }
    else {
        trans.open(opts['method'], url, opts['asynchronous']);
        try{
            for(var k in opts['header']){
                trans.setRequestHeader(k, opts['header'][k]);
            }
        }catch(exp){
        }
        trans.send(core_jsonToQuery(opts['args'],opts['isEncode']));
    }
    if(opts['timeout']){
        tm = setTimeout(function(){
            try{
                trans.abort();
                opts['onTimeout']({}, trans);
                callback(false, {}, trans);
            }catch(exp){
            }
        }, opts['timeout']);
    }
    function getXHR(){
        var _XHR = false;
        try {
            _XHR = new XMLHttpRequest();
        }
        catch (try_MS) {
            try {
                _XHR = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch (other_MS) {
                try {
                    _XHR = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (failed) {
                    _XHR = false;
                }
            }
        }
        return _XHR;
    }
    return trans;
}
function resource_request(url, callback) {
    return loader_ajax(url, function(response, params) {
        resource_request_apiRule(url, response, params, callback);
    }, function(response) {
        callback(false, response);
    });
}
function resource_request_apiRule(url, response, params, callback) {
    if (resource_base_apiRule) {
        resource_base_apiRule(response, params, callback);
    } else {
        if (response && response.code == '100000') {
            callback(true, response);
        } else {
            log('Error: response data url("' + url + '") : The api error code is ' + (response && response.code) + '. The error reason is ' + (response && response.msg));
            callback(false, response, params);
        }
    }
}
var resource_preLoad_resMap = {};
/**
 * 支持两种资源的预加载
 * css: link节点 s-preload-css="name1|name2|name3"
 *     例如：<link s-preload-css="page/index" href="http://a.com/css/page/index.css?version=x" type="text/css" rel="stylesheet">
 * data: script节点 s-preload-data="name1|name2|name3"
 *     例：
 *        1. jsonp方式
 *           <script s-preload-data="/aj/index?page=2" s-preload-data-property="index_data" type="text/javascript">
 *               function index_callback(data) {
 *                   index_data = data;
 *               }
 *           </script>
 *           <script type="text/javascript" src="http://a.com/aj/index?page=2&callback=index_callback" async="async"></script>
 *        2. ajax方式
 *           <script s-preload-data="/aj/index?page=2" s-preload-data-property="index_data" type="text/javascript">
 *               //ajax方法定义
 *               ajax('/aj/index?page=2', function(data) {
 *                   index_data = data;
 *               }, function() {
 *                   index_data = false;
 *               })
 *           </script>
 */
function resource_preLoad_bootLoad() {
    var links = getElementsByTagName('link');
    for (var i = links.length - 1; i >= 0; i--) {
        var preloadCss = links[i].getAttribute('s-preload-css');
        if (preloadCss) {
            preloadCss = preloadCss.replace(/&amp;/gi, '&');
            var cssUrls = preloadCss.split('|');
            for (var j = cssUrls.length - 1; j >= 0; j--) {
                resource_preLoad_setRes(cssUrls[j], 'css', true, true);
            }
        }
    }
    var scripts = getElementsByTagName('script');
    for (var i = scripts.length - 1; i >= 0; i--) {
        var script = scripts[i];
        var preloadData = script.getAttribute('s-preload-data');
        var preloadDataProperty = script.getAttribute('s-preload-data-property');
        if (preloadData) {
            preloadData = preloadData.replace(/&amp;/gi, '&');
            resource_preLoad_bootLoad_data(preloadData, preloadDataProperty);
        }
    }
}
function resource_preLoad_bootLoad_data(url, property) {
    resource_preLoad_setRes(url, 'ajax', false);
    var checkTime = 250;//250*19 超时时间
    var resource = resource_preLoad_resMap[url];
    check();
    function check() {
        if (!resource || resource.complete) {
            return;
        }
        if (property in window) {
            resource.complete = true;
            var response = window[property];
            if (response === 'error') {
                callback(false, null);
            } else {
                resource_request_apiRule(url, response, {}, function(success, response) {
                    callback(success, response);
                });
            }
        } else {
            if (checkTime > 0) {
                setTimeout(check, 19);
            } else {
                resource.complete = true;
                callback(false, null);
            }
        }
        checkTime--;
    }
    function callback(success, response) {
        var callbackList = resource[success ? 'onsuccess' : 'onfail'];
        resource[success ? 'success' : 'fail'] = response;
        for (var i = 0, l = callbackList.length; i < l; i++) {
            if (callbackList[i]) {
                callbackList[i](response);
            }
        }
    }
}
function resource_preLoad_setRes(url, type, complete, success, fail) {
    resource_preLoad_resMap[url] = {
        type: type,
        complete: complete,
        success: success,
        fail: fail,
        onsuccess: [],
        onfail: []
    };
}
/**
 * 得到预加载的资源
 * @param  {string} url
 */
function resource_preLoad_get(url) {
    return resource_preLoad_resMap[url];
}
function loader_js(url, callback){
    var entityList = {};
    var opts = {
        'charset': 'UTF-8',
        'timeout': 30 * 1000,
        'args': {},
        'isEncode' : false
    };
    var js, requestTimeout;
    var uniqueID = core_uniqueKey();
    js = entityList[uniqueID];
    if (js != null && !IE) {
        core_dom_removeNode(js);
        js = null;
    }
    if (js == null) {
        js = entityList[uniqueID] = core_dom_createElement('script');
    }
    js.charset = opts.charset;
    js.id = 'scriptRequest_script_' + uniqueID;
    js.type = 'text/javascript';
    if (callback != null) {
        if (IE) {
            js['onreadystatechange'] = function(){
                if (js.readyState.toLowerCase() == 'loaded' || js.readyState.toLowerCase() == 'complete') {
                    try{
                        clearTimeout(requestTimeout);
                        head.removeChild(js);
                        js['onreadystatechange'] = null;
                    }catch(exp){
                    }
                    callback(true);
                }
            };
        }
        else {
            js['onload'] = function(){
                try{
                    clearTimeout(requestTimeout);
                    core_dom_removeNode(js);
                }catch(exp){}
                callback(true);
            };
        }
    }
    js.src = core_URL(url,{
        'isEncodeQuery' : opts['isEncode']
    }).setParams(opts.args).toString();
    head.appendChild(js);
    if (opts.timeout > 0) {
        requestTimeout = setTimeout(function(){
            try{
                head.removeChild(js);
            }catch(exp){
            }
            callback(false);
        }, opts.timeout);
    }
    return js;
}





var core_hideDiv_hideDiv;
/*
 * 向隐藏容器添加节点
 * @method core_hideDiv_appendChild
 * @private
 * @param {Element} el 节点
 */
function core_hideDiv_appendChild( el ) {
    if ( !core_hideDiv_hideDiv ) {
        ( core_hideDiv_hideDiv = core_dom_createElement( 'div' ) ).style.cssText = 'position:absolute;top:-9999px;';
        head.appendChild( core_hideDiv_hideDiv );
    }
    core_hideDiv_hideDiv.appendChild( el );
}
/*
 * 向隐藏容器添加节点
 * @method core_hideDiv_removeChild
 * @private
 * @param {Element} el 节点
 */
function core_hideDiv_removeChild( el ) {
    core_hideDiv_hideDiv && core_hideDiv_hideDiv.removeChild( el );
}

function loader_css(url, callback, load_ID) {
    var load_div = null;
    var domID = core_uniqueKey();
    var timer = null;
    var _rTime = 500;//5000毫秒
    load_div = core_dom_createElement('div');
    core_dom_setAttribute(load_div, 'id', load_ID);
    core_hideDiv_appendChild(load_div);
    if (check()) {
        return;
    }
    var link = core_dom_createElement('link');
    core_dom_setAttribute(link, 'rel', 'Stylesheet');
    core_dom_setAttribute(link, 'type', 'text/css');
    core_dom_setAttribute(link, 'charset', 'utf-8');
    core_dom_setAttribute(link, 'id', 'link_' + load_ID);
    core_dom_setAttribute(link, 'href', url);
    head.appendChild(link);
    timer = function() {
        if (check()) {
            return;
        }
        if (--_rTime > 0) {
            setTimeout(timer, 10);
        } else {
            log('Error: css("' + url + '" timeout!');
            core_hideDiv_removeChild(load_div);
            callback(false);
        }
    };
    setTimeout(timer, 50);
    function check() {
        var result = parseInt(window.getComputedStyle ? getComputedStyle(load_div, null)['height'] : load_div.currentStyle && load_div.currentStyle['height']) === 42;
        if (result) {
            load_div && core_hideDiv_removeChild(load_div);
            callback(true);
        }
        return result;
    }
}
function loader_css_remove(load_ID) {
    var linkDom = getElementById('link_' + load_ID);
    if (linkDom) {
        core_dom_removeNode(linkDom);
        return true;
    }
    return false;
}

var resource_res_cssPrefix = 'S_CSS_';
var resource_res = {
    js: function(name, succ, err) {
        resource_res_handle('js', name, succ, err);
    },
    css: function(name, succ, err) {
        resource_res_handle('css', name, succ, err);
    },
    get: function(name, succ, err) {
        resource_res_handle('ajax', name, succ, err);
    },
    removeCss: function(name) {
        return loader_css_remove(resource_res_getCssId(name));
    }
};
function resource_res_handle(type, name, succ, err) {
    var nameObj = resource_preLoad_get(name);
    if (router_router_get().type === 'init' && nameObj) {
        if (nameObj.complete) {
            if (nameObj.success) {
                succ && succ.apply(undefined, [].concat(nameObj.success));
            } else {
                err && err.apply(undefined, [].concat(nameObj.fail));
            }
        } else {
            nameObj.onsuccess.push(succ);
            nameObj.onfail.push(err);
        }
    } else {
        resource_res_do(type, name, succ, err);
    }
}
function resource_res_do(type, name, succ, err) {
    var cssId;
    if (type === 'css') {
        cssId = resource_res_getCssId(name);
    }
    var hasProtocol = core_hasProtocol(name);
    var url = name, loader;
    if (!hasProtocol) {
        url = resource_fixUrl(name, type);
        if (type !== 'ajax' && resource_base_version) {
            url += '?version=' + resource_base_version;
        }
    }
    if(resource_queue_list[url]) {
        resource_queue_push(url, succ, err);
    } else {
        resource_queue_create(url);
        resource_queue_push(url, succ, err);
        switch(type) {
            case 'js':
                loader_js(url, callback);
                break;
            case 'css':
                loader_css(url, callback, cssId);
                break;
            case 'ajax':
                resource_request(url, callback);
                break;
        }
    }
    function callback(access, data) {
        resource_queue_run(url, access, data);
        resource_queue_del(url);
    }
}
function resource_res_getCssId(path) {
    return path && resource_res_cssPrefix + path.replace(/(\.css)$/i, '').replace(/\//g, '_');
}





//外部异步调用require方法
function require_global(deps, complete, errcb, currNs, runDeps) {
    var depNs;
    var depDefined = 0;
    var errored = 0;
    var baseModulePath = currNs && core_urlFolder(currNs);
    deps = [].concat(deps);
    for (var i = 0, len = deps.length; i < len; i++) {
        depNs = deps[i] = core_nameSpaceFix(deps[i], baseModulePath);
        if (require_base_module_loaded[depNs]) {
            checkDepDefined(depNs);
        } else {
            ! function(depNs) {
                resource_res.js(depNs, function() {
                    if (core_hasProtocol(depNs)) {
                        require_base_module_defined[depNs] = true;
                        require_base_module_loaded[depNs] = true;
                    }
                    checkDepDefined(depNs);
                }, function() {
                    errored++;
                });
            }(depNs);
        }
    }
    function check() {
        if (deps.length <= depDefined) {
            if (errored) {
                errcb();
            } else {
                var runner_result = [];
                if (runDeps === undefined || runDeps === true) {
                    runner_result = require_runner(deps);
                }
                complete && complete.apply(window, runner_result);
            }
        }
    }
    function checkDepDefined(depNs) {
        if (require_base_module_defined[depNs]) {
            depDefined++;
            check();
        } else {
            core_notice_on(require_base_event_defined, function definedFn(ns) {
                if (depNs === ns) {
                    core_notice_off(require_base_event_defined, definedFn);
                    depDefined++;
                    check();
                }
            });
        }
    }
}





//内部同步调用require方法
function require_runner_makeRequire(currNs) {
    var basePath = core_urlFolder(currNs);
    return require;
    function require(ns) {
        if (core_object_typeof(ns) === 'array') {
            var paramList = core_array_makeArray(arguments);
            paramList[3] = paramList[3] || currNs;
            return require_global.apply(window, paramList);
        }
        ns = core_nameSpaceFix(ns, basePath);
        if (!require_base_module_defined[ns]) {
            log('Error: ns("' + ns + '") is undefined!');
            return;
        }
        if (!(ns in require_base_module_runed)) {
            require_runner(ns);
        }
        return require_base_module_runed[ns];
    }
}
//运行define列表，并返回实例集
function require_runner(pkg, basePath) {
    pkg = [].concat(pkg);
    var i, len;
    var ns, nsConstructor, module;
    var resultList = [];
    for (i = 0, len = pkg.length; i < len; i++) {
        ns = core_nameSpaceFix(pkg[i], basePath);
        nsConstructor = require_base_module_fn[ns];
        if (!nsConstructor) {
            log('Warning: ns("' + ns + '") has not constructor!');
            resultList.push(undefined);
        } else {
            if (!require_base_module_runed[ns]) {
                if (require_base_module_deps[ns]) {
                    require_runner(require_base_module_deps[ns], core_urlFolder(ns));
                }
                module = {
                    exports: {}
                };
                require_base_module_runed[ns] = nsConstructor.apply(window, [require_runner_makeRequire(ns), module.exports, module]) || module.exports;
            }
            resultList.push(require_base_module_runed[ns]);
        }
    }
    return resultList;
}


//全局define
function require_define(ns, deps, construtor) {
    if (require_base_module_defined[ns]) {
        return;
    }
    require_base_module_loaded[ns] = true;
    require_base_module_deps[ns] = construtor ? (deps || []) : [];
    require_base_module_fn[ns] = construtor || deps;
    deps = require_base_module_deps[ns];
    if (deps.length > 0) {
        require_global(deps, doDefine, function() {
            log('Error: ns("' + ns + '") deps loaded error!', '');
        }, ns, false);
    } else {
        doDefine();
    }
    function doDefine() {
        require_base_module_defined[ns] = true;
        core_notice_trigger(require_base_event_defined, ns);
        log('Debug: define ns("' + ns + '")');
    }
}





//暂不做
var resource_config_slash = '/';
config_push(function (parseParamFn) {
    resource_jsPath = parseParamFn('jsPath', resource_jsPath);
    resource_cssPath = parseParamFn('cssPath', resource_cssPath);
    resource_ajaxPath = parseParamFn('ajaxPath', resource_ajaxPath);
    resource_basePath = parseParamFn('basePath', resource_config_slash);
    resource_base_apiRule = parseParamFn('defApiRule', resource_base_apiRule);
    resource_base_version = parseParamFn('version', resource_base_version);
});
function resource_boot() {
    resource_preLoad_bootLoad();
}

/**
 * 渲染管理器的主页面
 */
var render_render_stage = {
    getBox: render_stage_getBox,
    getScrollBox: render_stage_getScrollBox
};

config_push(function(parseParamFn) {
    if (isHTML5) {
        render_base_dataCache_usable = parseParamFn('dataCache', render_base_dataCache_usable);
        if ((iphone && iphoneVersion >= 8.0 && webkit) || (android && androidVersion >= 4.4 && webkit)) {
            // return;
            //目前限制使用这个功能，这个限制会优先于用户的配置
            render_base_stage_usable = parseParamFn('stage', render_base_stage_usable);
            if (render_base_stage_usable) {
                render_base_stageCache_usable = parseParamFn('stageCache', render_base_stageCache_usable);
                render_base_stageChange_usable = parseParamFn('stageChange', render_base_stageChange_usable);
                render_base_stageDefaultHTML = parseParamFn('stageDefaultHTML', render_base_stageDefaultHTML);
                render_base_stage_maxLength = parseParamFn('stageMaxLength', render_base_stage_maxLength);
            }
        }
    }
    render_base_useCssPrefix_usable = parseParamFn('useCssPrefix', render_base_useCssPrefix_usable);
});
/**
 * 渲染的启动入口
 */
function render_boot() {
    render_stage_init();
}

/**
 * 路由配置
 */


config_push(router_config);
function router_config(parseParamFn, config) {
  router_base_routerTable = parseParamFn('router', router_base_routerTable);
  // @Finrila hash模式处理不可用状态，先下掉
  // router_base_useHash = parseParamFn('useHash', router_base_useHash);
  router_base_singlePage = isHTML5 ? parseParamFn('singlePage', router_base_singlePage) : false;
}


/**
 * 路由启动接口
 * 1、设置侦听
 * 2、主动响应第一次的url(第一次是由后端渲染的，如果没有真实文件，无法启动页面)
 *
 */



/**
 * router.use
 * 设置单条路由规则
 * 路由语法说明：
 * 1、path中的变量定义参考express
 * 2、支持query和hash
 * 3、低版浏览器支持用hash模式来设置路由
 */


/**
 * Turn an Express-style path string such as /user/:name into a regular expression.
 *
 */
/**
 * 判断对象是否为数组
 * @param {Array} o
 * @return {Boolean}
 * @example
 * var li1 = [1,2,3]
 * var bl2 = core_array_isArray(li1);
 * bl2 === TRUE
 */
var core_array_isArray = Array.isArray ? function(arr) {
    return Array.isArray(arr);
} : function(arr){
    return 'array' === core_object_typeof(arr);
};
/**
 * The main path matching regexp utility.
 *
 * @type {RegExp}
 */
var router_pathToRegexp_PATH_REGEXP = RegExp([
    // Match escaped characters that would otherwise appear in future matches.
    // This allows the user to escape special characters that won't transform.
    '(\\\\.)',
    // Match Express-style parameters and un-named parameters with a prefix
    // and optional suffixes. Matches appear as:
    //
    // "/:test(\\d+)?" => ["/", "test", "\d+", undefined, "?"]
    // "/route(\\d+)" => [undefined, undefined, undefined, "\d+", undefined]
    '([\\/.])?(?:\\:(\\w+)(?:\\(((?:\\\\.|[^)])*)\\))?|\\(((?:\\\\.|[^)])*)\\))([+*?])?',
    // Match regexp special characters that are always escaped.
    '([.+*?=^!:${}()[\\]|\\/])'
].join('|'), 'g');
/**
 * Escape the capturing group by escaping special characters and meaning.
 *
 * @param  {String} group
 * @return {String}
 */
function router_pathToRegexp_escapeGroup(group) {
    return group.replace(/([=!:$\/()])/g, '\\$1');
}
/**
 * Attach the keys as a property of the regexp.
 *
 * @param  {RegExp} re
 * @param  {Array}  keys
 * @return {RegExp}
 */
function router_pathToRegexp_attachKeys(re, keys) {
    re.keys = keys;
    return re;
}
/**
 * Get the router_pathToRegexp_flags for a regexp from the options.
 *
 * @param  {Object} options
 * @return {String}
 */
function router_pathToRegexp_flags(options) {
    return options.sensitive ? '' : 'i';
}
/**
 * Pull out keys from a regexp.
 *
 * @param  {RegExp} path
 * @param  {Array}  keys
 * @return {RegExp}
 */
function router_pathToRegexp_regexpToRegexp(path, keys) {
    // Use a negative lookahead to match only capturing groups.
    var groups = path.source.match(/\((?!\?)/g);
    if (groups) {
        for (var i = 0; i < groups.length; i++) {
            keys.push({
                name: i,
                delimiter: null,
                optional: false,
                repeat: false
            });
        }
    }
    return router_pathToRegexp_attachKeys(path, keys);
}
/**
 * Transform an array into a regexp.
 *
 * @param  {Array}  path
 * @param  {Array}  keys
 * @param  {Object} options
 * @return {RegExp}
 */
function router_pathToRegexp_arrayToRegexp(path, keys, options) {
    var parts = [];
    for (var i = 0; i < path.length; i++) {
        parts.push(router_pathToRegexp(path[i], keys, options).source);
    }
    var regexp = RegExp('(?:' + parts.join('|') + ')', router_pathToRegexp_flags(options));
    return router_pathToRegexp_attachKeys(regexp, keys);
}
/**
 * Replace the specific tags with regexp strings.
 *
 * @param  {String} path
 * @param  {Array}  keys
 * @return {String}
 */
function router_pathToRegexp_replacePath(path, keys) {
    var index = 0;
    function replace(_, escaped, prefix, key, capture, group, suffix, escape) {
        if (escaped) {
            return escaped;
        }
        if (escape) {
            return '\\' + escape;
        }
        var repeat = suffix === '+' || suffix === '*';
        var optional = suffix === '?' || suffix === '*';
        keys.push({
            name: key || index++,
            delimiter: prefix || '/',
            optional: optional,
            repeat: repeat
        });
        prefix = prefix ? ('\\' + prefix) : '';
        capture = router_pathToRegexp_escapeGroup(capture || group || '[^' + (prefix || '\\/') + ']+?');
        if (repeat) {
            capture = capture + '(?:' + prefix + capture + ')*';
        }
        if (optional) {
            return '(?:' + prefix + '(' + capture + '))?';
        }
        // Basic parameter support.
        return prefix + '(' + capture + ')';
    }
    return path.replace(router_pathToRegexp_PATH_REGEXP, replace);
}
/**
 * Normalize the given path string, returning a regular expression.
 *
 * An empty array can be passed in for the keys, which will hold the
 * placeholder key descriptions. For example, using `/user/:id`, `keys` will
 * contain `[{ name: 'id', delimiter: '/', optional: false, repeat: false }]`.
 *
 * @param  {(String|RegExp|Array)} path
 * @param  {Array}                 [keys]
 * @param  {Object}                [options]
 * @return {RegExp}
 */
function router_pathToRegexp(path, keys, options) {
    keys = keys || [];
    if (!core_array_isArray(keys)) {
        options = keys;
        keys = [];
    } else if (!options) {
        options = {};
    }
    if (path instanceof window.RegExp) {
        return router_pathToRegexp_regexpToRegexp(path, keys, options);
    }
    if (core_array_isArray(path)) {
        return router_pathToRegexp_arrayToRegexp(path, keys, options);
    }
    var strict = options.strict;
    var end = options.end !== false;
    var route = router_pathToRegexp_replacePath(path, keys);
    var endsWithSlash = path.charAt(path.length - 1) === '/';
    // In non-strict mode we allow a slash at the end of match. If the path to
    // match already ends with a slash, we remove it for consistency. The slash
    // is valid at the end of a path match, not in the middle. This is important
    // in non-ending mode, where "/test/" shouldn't match "/test//route".
    if (!strict) {
        route = (endsWithSlash ? route.slice(0, -2) : route) + '(?:\\/(?=$))?';
    }
    if (end) {
        route += '$';
    } else {
        // In non-ending mode, we need the capturing groups to match as much as
        // possible by using a positive lookahead to the end or next path segment.
        route += strict && endsWithSlash ? '' : '(?=\\/|$)';
    }
    return router_pathToRegexp_attachKeys(RegExp('^' + route, router_pathToRegexp_flags(options)), keys);
}
function router_use(path, config) {
    var key, value, _results;
    if (typeof path === 'object' && !(path instanceof window.RegExp)) {
        //批量设置
        _results = [];
        for (key in path) {
            value = path[key];
            _results.push(router_use(key, value));
        }
        return _results;
    } else {
        //单条设置
        var keys = [];
        var pathRegexp = router_pathToRegexp(path, keys);
        return router_base_routerTableReg.push({
            pathRegexp: pathRegexp,
            config: config,
            keys: keys
        });
    }
}
function router_boot() {
    for (var i = 0, len = router_base_routerTable.length; i < len; i++) {
        var items = router_base_routerTable[i];
        router_use(items[0], items);
    }
    router_router_clearTransferData();
    if (router_router_get(true).config) {
        router_listen_fireRouterChange();
    }
    //浏览器支持HTML5，且应用设置为单页面应用时，绑定路由侦听； @shaobo3
    isHTML5 && router_base_singlePage && router_listen();
}
  config_push(function(parseParamFn, config) {
    isDebug = parseParamFn('debug', isDebug);
    logLevel = parseParamFn('logLevel', logLevel);
    if (!config.logLevel && !isDebug) {
      logLevel = 'Error';
    }
    mainBox = parseParamFn('mainBox', mainBox);
    if (core_object_isString(mainBox)) {
      mainBox = getElementById(mainBox);
    }
  });
  steel.d = require_define;
  steel.res = resource_res;
  steel.run = render_run;
  steel.stage = render_render_stage;
  steel.router = router_router;
  steel.on = core_notice_on;
  steel.off = core_notice_off;
  steel.setExtTplData = render_control_setExtTplData;
  steel.require = require_global;
  steel.config = config;
  steel.boot = function(ns) {
    steel.isDebug = isDebug;
    require_global(ns, function() {
      resource_boot();
      render_boot();
      router_boot();
    });
  };
  steel._destroyByNode = function(node) {
    var id = node && node.id;
    var resContainer;
    if (id && (resContainer = render_base_resContainer[id])) {
      render_control_destroyLogic(resContainer);
      render_control_destroyChildren(resContainer.toDestroyChildrenid);
    }
  };
  core_notice_on('routerChange', function(routerValue) {
    var config = routerValue.config;
    var controller = config[1];
    render_run(mainBox, controller);
    log("Info: routerChange", mainBox, controller, routerValue.type);
  });
  window.steel = steel;
}(window);
/*!
 * react-lite.js v0.15.9
 * (c) 2016 Jade Gu
 * Released under the MIT License.
 */
(function (global, factory) {
  typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
  typeof define === 'function' && define.amd ? define(factory) :
  global.React = factory();
}(this, function () { 'use strict';
  var SVGNamespaceURI = 'http://www.w3.org/2000/svg';
  var COMPONENT_ID = 'liteid';
  var VELEMENT = 2;
  var VSTATELESS = 3;
  var VCOMPONENT = 4;
  var VCOMMENT = 5;
  var refs = null;
  function createVelem(type, props) {
      return {
          vtype: VELEMENT,
          type: type,
          props: props,
          refs: refs
      };
  }
  function createVstateless(type, props) {
      return {
          vtype: VSTATELESS,
          id: getUid(),
          type: type,
          props: props
      };
  }
  function createVcomponent(type, props) {
      return {
          vtype: VCOMPONENT,
          id: getUid(),
          type: type,
          props: props,
          refs: refs
      };
  }
  function createVcomment(comment) {
      return {
          vtype: VCOMMENT,
          comment: comment
      };
  }
  function initVnode(vnode, parentContext, namespaceURI) {
      var vtype = vnode.vtype;
      var node = null;
      if (!vtype) {
          node = document.createTextNode(vnode);
      } else if (vtype === VELEMENT) {
          node = initVelem(vnode, parentContext, namespaceURI);
      } else if (vtype === VCOMPONENT) {
          node = initVcomponent(vnode, parentContext, namespaceURI);
      } else if (vtype === VSTATELESS) {
          node = initVstateless(vnode, parentContext, namespaceURI);
      } else if (vtype === VCOMMENT) {
          node = document.createComment(vnode.comment);
      }
      return node;
  }
  function destroyVnode(vnode, node) {
      var vtype = vnode.vtype;
      if (vtype === VELEMENT) {
          destroyVelem(vnode, node);
      } else if (vtype === VCOMPONENT) {
          destroyVcomponent(vnode, node);
      } else if (vtype === VSTATELESS) {
          destroyVstateless(vnode, node);
      }
  }
  function initVelem(velem, parentContext, namespaceURI) {
      var type = velem.type;
      var props = velem.props;
      var node = null;
      if (type === 'svg' || namespaceURI === SVGNamespaceURI) {
          node = document.createElementNS(SVGNamespaceURI, type);
          namespaceURI = SVGNamespaceURI;
      } else {
          node = document.createElement(type);
      }
      var children = props.children;
      var vchildren = node.vchildren = [];
      if (isArr(children)) {
          flattenChildren(children, collectChild, vchildren);
      } else {
          collectChild(children, vchildren);
      }
      for (var i = 0, len = vchildren.length; i < len; i++) {
          node.appendChild(initVnode(vchildren[i], parentContext, namespaceURI));
      }
      var isCustomComponent = type.indexOf('-') >= 0 || props.is != null;
      setProps(node, props, isCustomComponent);
      attachRef(velem.refs, velem.ref, node);
      return node;
  }
  function collectChild(child, children) {
      if (child != null && typeof child !== 'boolean') {
          children[children.length] = child.vtype ? child : '' + child;
      }
  }
  function updateVelem(velem, newVelem, node, parentContext) {
      var props = velem.props;
      var type = velem.type;
      var newProps = newVelem.props;
      var oldHtml = props.dangerouslySetInnerHTML && props.dangerouslySetInnerHTML.__html;
      var newChildren = newProps.children;
      var vchildren = node.vchildren;
      var childNodes = node.childNodes;
      var namespaceURI = node.namespaceURI;
      var isCustomComponent = type.indexOf('-') >= 0 || props.is != null;
      var vchildrenLen = vchildren.length;
      var newVchildren = node.vchildren = [];
      if (isArr(newChildren)) {
          flattenChildren(newChildren, collectChild, newVchildren);
      } else {
          collectChild(newChildren, newVchildren);
      }
      var newVchildrenLen = newVchildren.length;
      if (oldHtml == null && vchildrenLen) {
          var shouldRemove = null;
          var patches = Array(newVchildrenLen);
          for (var i = 0; i < vchildrenLen; i++) {
              var vnode = vchildren[i];
              for (var j = 0; j < newVchildrenLen; j++) {
                  if (patches[j]) {
                      continue;
                  }
                  var newVnode = newVchildren[j];
                  if (vnode === newVnode) {
                      patches[j] = {
                          vnode: vnode,
                          node: childNodes[i]
                      };
                      vchildren[i] = null;
                      break;
                  }
              }
          }
          outer: for (var i = 0; i < vchildrenLen; i++) {
              var vnode = vchildren[i];
              if (vnode === null) {
                  continue;
              }
              var _type = vnode.type;
              var key = vnode.key;
              var _refs = vnode.refs;
              var childNode = childNodes[i];
              for (var j = 0; j < newVchildrenLen; j++) {
                  if (patches[j]) {
                      continue;
                  }
                  var newVnode = newVchildren[j];
                  if (newVnode.type === _type && newVnode.key === key && newVnode.refs === _refs) {
                      patches[j] = {
                          vnode: vnode,
                          node: childNode
                      };
                      continue outer;
                  }
              }
              if (!shouldRemove) {
                  shouldRemove = [];
              }
              shouldRemove[shouldRemove.length] = childNode;
              // shouldRemove.push(childNode)
              destroyVnode(vnode, childNode);
          }
          if (shouldRemove) {
              for (var i = 0, len = shouldRemove.length; i < len; i++) {
                  node.removeChild(shouldRemove[i]);
              }
          }
          for (var i = 0; i < newVchildrenLen; i++) {
              var newVnode = newVchildren[i];
              var patchItem = patches[i];
              if (patchItem) {
                  var vnode = patchItem.vnode;
                  var newChildNode = patchItem.node;
                  if (newVnode !== vnode) {
                      var vtype = newVnode.vtype;
                      if (!vtype) {
                          // textNode
                          newChildNode.newText = newVnode;
                          pendingTextUpdater[pendingTextUpdater.length] = newChildNode;
                          // newChildNode.nodeValue = newVnode
                          // newChildNode.replaceData(0, vnode.length, newVnode)
                      } else if (vtype === VELEMENT) {
                              newChildNode = updateVelem(vnode, newVnode, newChildNode, parentContext);
                          } else if (vtype === VCOMPONENT) {
                              newChildNode = updateVcomponent(vnode, newVnode, newChildNode, parentContext);
                          } else if (vtype === VSTATELESS) {
                              newChildNode = updateVstateless(vnode, newVnode, newChildNode, parentContext);
                          }
                  }
                  var currentNode = childNodes[i];
                  if (currentNode !== newChildNode) {
                      node.insertBefore(newChildNode, currentNode || null);
                  }
              } else {
                  var newChildNode = initVnode(newVnode, parentContext, namespaceURI);
                  node.insertBefore(newChildNode, childNodes[i] || null);
              }
          }
          node.props = props;
          node.newProps = newProps;
          node.isCustomComponent = isCustomComponent;
          pendingPropsUpdater[pendingPropsUpdater.length] = node;
      } else {
          // should patch props first, make sure innerHTML was cleared
          patchProps(node, props, newProps, isCustomComponent);
          for (var i = 0; i < newVchildrenLen; i++) {
              node.appendChild(initVnode(newVchildren[i], parentContext, namespaceURI));
          }
      }
      if (velem.ref !== newVelem.ref) {
          detachRef(velem.refs, velem.ref);
          attachRef(newVelem.refs, newVelem.ref, node);
      }
      return node;
  }
  function destroyVelem(velem, node) {
      var props = velem.props;
      var vchildren = node.vchildren;
      var childNodes = node.childNodes;
      for (var i = 0, len = vchildren.length; i < len; i++) {
          destroyVnode(vchildren[i], childNodes[i]);
      }
      detachRef(velem.refs, velem.ref);
      node.eventStore = node.vchildren = null;
      for (var key in props) {
          if (props.hasOwnProperty(key) && EVENT_KEYS.test(key)) {
              key = getEventName(key);
              if (notBubbleEvents[key] === true) {
                  node[key] = null;
              }
          }
      }
  }
  function initVstateless(vstateless, parentContext, namespaceURI) {
      var vnode = renderVstateless(vstateless, parentContext);
      var node = initVnode(vnode, parentContext, namespaceURI);
      node.cache = node.cache || {};
      node.cache[vstateless.id] = vnode;
      return node;
  }
  function updateVstateless(vstateless, newVstateless, node, parentContext) {
      var id = vstateless.id;
      var vnode = node.cache[id];
      delete node.cache[id];
      var newVnode = renderVstateless(newVstateless, parentContext);
      var newNode = compareTwoVnodes(vnode, newVnode, node, parentContext);
      newNode.cache = newNode.cache || {};
      newNode.cache[newVstateless.id] = newVnode;
      if (newNode !== node) {
          syncCache(newNode.cache, node.cache, newNode);
      }
      return newNode;
  }
  function destroyVstateless(vstateless, node) {
      var id = vstateless.id;
      var vnode = node.cache[id];
      delete node.cache[id];
      destroyVnode(vnode, node);
  }
  function renderVstateless(vstateless, parentContext) {
      var factory = vstateless.type;
      var props = vstateless.props;
      var componentContext = getContextByTypes(parentContext, factory.contextTypes);
      var vnode = factory(props, componentContext);
      if (vnode && vnode.render) {
          vnode = vnode.render();
      }
      if (vnode === null || vnode === false) {
          vnode = createVcomment('react-empty: ' + getUid());
      } else if (!vnode || !vnode.vtype) {
          throw new Error('@' + factory.name + '#render:You may have returned undefined, an array or some other invalid object');
      }
      return vnode;
  }
  function initVcomponent(vcomponent, parentContext, namespaceURI) {
      var Component = vcomponent.type;
      var props = vcomponent.props;
      var id = vcomponent.id;
      var componentContext = getContextByTypes(parentContext, Component.contextTypes);
      var component = new Component(props, componentContext);
      var updater = component.$updater;
      var cache = component.$cache;
      cache.parentContext = parentContext;
      updater.isPending = true;
      component.props = component.props || props;
      component.context = component.context || componentContext;
      if (component.componentWillMount) {
          component.componentWillMount();
          component.state = updater.getState();
      }
      var vnode = renderComponent(component, parentContext);
      var node = initVnode(vnode, vnode.context, namespaceURI);
      node.cache = node.cache || {};
      node.cache[id] = component;
      cache.vnode = vnode;
      cache.node = node;
      cache.isMounted = true;
      pendingComponents.push(component);
      attachRef(vcomponent.refs, vcomponent.ref, component);
      return node;
  }
  function updateVcomponent(vcomponent, newVcomponent, node, parentContext) {
      var id = vcomponent.id;
      var component = node.cache[id];
      var updater = component.$updater;
      var cache = component.$cache;
      var Component = newVcomponent.type;
      var nextProps = newVcomponent.props;
      var componentContext = getContextByTypes(parentContext, Component.contextTypes);
      delete node.cache[id];
      node.cache[newVcomponent.id] = component;
      cache.parentContext = parentContext;
      if (component.componentWillReceiveProps) {
          updater.isPending = true;
          component.componentWillReceiveProps(nextProps, componentContext);
          updater.isPending = false;
      }
      updater.emitUpdate(nextProps, componentContext);
      if (vcomponent.ref !== newVcomponent.ref) {
          detachRef(vcomponent.refs, vcomponent.ref);
          attachRef(newVcomponent.refs, newVcomponent.ref, component);
      }
      return cache.node;
  }
  function destroyVcomponent(vcomponent, node) {
      var id = vcomponent.id;
      var component = node.cache[id];
      var cache = component.$cache;
      delete node.cache[id];
      detachRef(vcomponent.refs, vcomponent.ref);
      component.setState = component.forceUpdate = noop;
      if (component.componentWillUnmount) {
          component.componentWillUnmount();
      }
      destroyVnode(cache.vnode, node);
      delete component.setState;
      cache.isMounted = false;
      cache.node = cache.parentContext = cache.vnode = component.refs = component.context = null;
  }
  function getContextByTypes(curContext, contextTypes) {
      var context = {};
      if (!contextTypes || !curContext) {
          return context;
      }
      for (var key in contextTypes) {
          if (contextTypes.hasOwnProperty(key)) {
              context[key] = curContext[key];
          }
      }
      return context;
  }
  function renderComponent(component, parentContext) {
      refs = component.refs;
      var vnode = component.render();
      if (vnode === null || vnode === false) {
          vnode = createVcomment('react-empty: ' + getUid());
      } else if (!vnode || !vnode.vtype) {
          throw new Error('@' + component.constructor.name + '#render:You may have returned undefined, an array or some other invalid object');
      }
      var curContext = refs = null;
      if (component.getChildContext) {
          curContext = component.getChildContext();
      }
      if (curContext) {
          curContext = extend(extend({}, parentContext), curContext);
      } else {
          curContext = parentContext;
      }
      vnode.context = curContext;
      return vnode;
  }
  function batchUpdateDOM() {
      clearPendingPropsUpdater();
      clearPendingTextUpdater();
      clearPendingComponents();
  }
  var pendingComponents = [];
  function clearPendingComponents() {
      var len = pendingComponents.length;
      if (!len) {
          return;
      }
      var components = pendingComponents;
      pendingComponents = [];
      var i = -1;
      while (len--) {
          var component = components[++i];
          var updater = component.$updater;
          if (component.componentDidMount) {
              component.componentDidMount();
          }
          updater.isPending = false;
          updater.emitUpdate();
      }
  }
  var pendingTextUpdater = [];
  var clearPendingTextUpdater = function clearPendingTextUpdater() {
      var len = pendingTextUpdater.length;
      if (!len) {
          return;
      }
      var list = pendingTextUpdater;
      pendingTextUpdater = [];
      for (var i = 0; i < len; i++) {
          var node = list[i];
          node.nodeValue = node.newText;
      }
  };
  var pendingPropsUpdater = [];
  var clearPendingPropsUpdater = function clearPendingPropsUpdater() {
      var len = pendingPropsUpdater.length;
      if (!len) {
          return;
      }
      var list = pendingPropsUpdater;
      pendingPropsUpdater = [];
      for (var i = 0; i < len; i++) {
          var node = list[i];
          patchProps(node, node.props, node.newProps, node.isCustomComponent);
          node.props = node.newProps = null;
      }
  };
  function compareTwoVnodes(vnode, newVnode, node, parentContext) {
      var newNode = node;
      if (newVnode == null) {
          // remove
          destroyVnode(vnode, node);
          node.parentNode.removeChild(node);
      } else if (vnode.type !== newVnode.type || newVnode.key !== vnode.key) {
          // replace
          destroyVnode(vnode, node);
          newNode = initVnode(newVnode, parentContext, node.namespaceURI);
          node.parentNode.replaceChild(newNode, node);
      } else if (vnode !== newVnode) {
          // same type and same key -> update
          var vtype = vnode.vtype;
          if (vtype === VELEMENT) {
              newNode = updateVelem(vnode, newVnode, node, parentContext);
          } else if (vtype === VCOMPONENT) {
              newNode = updateVcomponent(vnode, newVnode, node, parentContext);
          } else if (vtype === VSTATELESS) {
              newNode = updateVstateless(vnode, newVnode, node, parentContext);
          }
      }
      return newNode;
  }
  function getDOMNode() {
      return this;
  }
  function attachRef(refs, refKey, refValue) {
      if (!refs || refKey == null || !refValue) {
          return;
      }
      if (refValue.nodeName && !refValue.getDOMNode) {
          // support react v0.13 style: this.refs.myInput.getDOMNode()
          refValue.getDOMNode = getDOMNode;
      }
      if (isFn(refKey)) {
          refKey(refValue);
      } else {
          refs[refKey] = refValue;
      }
  }
  function detachRef(refs, refKey) {
      if (!refs || refKey == null) {
          return;
      }
      if (isFn(refKey)) {
          refKey(null);
      } else {
          delete refs[refKey];
      }
  }
  function syncCache(cache, oldCache, node) {
      for (var key in oldCache) {
          if (!oldCache.hasOwnProperty(key)) {
              continue;
          }
          var value = oldCache[key];
          cache[key] = value;
          // is component, update component.$cache.node
          if (value.forceUpdate) {
              value.$cache.node = node;
          }
      }
  }
  var updateQueue = {
    updaters: [],
    isPending: false,
    add: function add(updater) {
        this.updaters.push(updater);
    },
    batchUpdate: function batchUpdate() {
        if (this.isPending) {
            return;
        }
        this.isPending = true;
        /*
     each updater.update may add new updater to updateQueue
     clear them with a loop
     event bubbles from bottom-level to top-level
     reverse the updater order can merge some props and state and reduce the refresh times
     see Updater.update method below to know why
    */
        var updaters = this.updaters;
        var updater = undefined;
        while (updater = updaters.pop()) {
            updater.updateComponent();
        }
        this.isPending = false;
    }
  };
  function Updater(instance) {
    this.instance = instance;
    this.pendingStates = [];
    this.pendingCallbacks = [];
    this.isPending = false;
    this.nextProps = this.nextContext = null;
    this.clearCallbacks = this.clearCallbacks.bind(this);
  }
  Updater.prototype = {
    emitUpdate: function emitUpdate(nextProps, nextContext) {
        this.nextProps = nextProps;
        this.nextContext = nextContext;
        // receive nextProps!! should update immediately
        nextProps || !updateQueue.isPending ? this.updateComponent() : updateQueue.add(this);
    },
    updateComponent: function updateComponent() {
        var instance = this.instance;
        var pendingStates = this.pendingStates;
        var nextProps = this.nextProps;
        var nextContext = this.nextContext;
        if (nextProps || pendingStates.length > 0) {
            nextProps = nextProps || instance.props;
            nextContext = nextContext || instance.context;
            this.nextProps = this.nextContext = null;
            // merge the nextProps and nextState and update by one time
            shouldUpdate(instance, nextProps, this.getState(), nextContext, this.clearCallbacks);
        }
    },
    addState: function addState(nextState) {
        if (nextState) {
            this.pendingStates.push(nextState);
            if (!this.isPending) {
                this.emitUpdate();
            }
        }
    },
    replaceState: function replaceState(nextState) {
        var pendingStates = this.pendingStates;
        pendingStates.pop();
        // push special params to point out should replace state
        pendingStates.push([nextState]);
    },
    getState: function getState() {
        var instance = this.instance;
        var pendingStates = this.pendingStates;
        var state = instance.state;
        var props = instance.props;
        if (pendingStates.length) {
            state = extend({}, state);
            eachItem(pendingStates, function (nextState) {
                // replace state
                if (isArr(nextState)) {
                    state = extend({}, nextState[0]);
                    return;
                }
                if (isFn(nextState)) {
                    nextState = nextState.call(instance, state, props);
                }
                extend(state, nextState);
            });
            pendingStates.length = 0;
        }
        return state;
    },
    clearCallbacks: function clearCallbacks() {
        var pendingCallbacks = this.pendingCallbacks;
        var instance = this.instance;
        if (pendingCallbacks.length > 0) {
            this.pendingCallbacks = [];
            eachItem(pendingCallbacks, function (callback) {
                return callback.call(instance);
            });
        }
    },
    addCallback: function addCallback(callback) {
        if (isFn(callback)) {
            this.pendingCallbacks.push(callback);
        }
    }
  };
  function Component(props, context) {
    this.$updater = new Updater(this);
    this.$cache = { isMounted: false };
    this.props = props;
    this.state = {};
    this.refs = {};
    this.context = context;
  }
  Component.prototype = {
    constructor: Component,
    // getChildContext: _.noop,
    // componentWillUpdate: _.noop,
    // componentDidUpdate: _.noop,
    // componentWillReceiveProps: _.noop,
    // componentWillMount: _.noop,
    // componentDidMount: _.noop,
    // componentWillUnmount: _.noop,
    // shouldComponentUpdate(nextProps, nextState) {
    //  return true
    // },
    forceUpdate: function forceUpdate(callback) {
        var $updater = this.$updater;
        var $cache = this.$cache;
        var props = this.props;
        var state = this.state;
        var context = this.context;
        if ($updater.isPending || !$cache.isMounted) {
            return;
        }
        var nextProps = $cache.props || props;
        var nextState = $cache.state || state;
        var nextContext = $cache.context || {};
        var parentContext = $cache.parentContext;
        var node = $cache.node;
        var vnode = $cache.vnode;
        $cache.props = $cache.state = $cache.context = null;
        $updater.isPending = true;
        if (this.componentWillUpdate) {
            this.componentWillUpdate(nextProps, nextState, nextContext);
        }
        this.state = nextState;
        this.props = nextProps;
        this.context = nextContext;
        var newVnode = renderComponent(this, parentContext);
        var newNode = compareTwoVnodes(vnode, newVnode, node, newVnode.context);
        if (newNode !== node) {
            newNode.cache = newNode.cache || {};
            syncCache(newNode.cache, node.cache, newNode);
        }
        $cache.vnode = newVnode;
        $cache.node = newNode;
        batchUpdateDOM();
        if (this.componentDidUpdate) {
            this.componentDidUpdate(props, state, context);
        }
        if (callback) {
            callback.call(this);
        }
        $updater.isPending = false;
        $updater.emitUpdate();
    },
    setState: function setState(nextState, callback) {
        var $updater = this.$updater;
        $updater.addCallback(callback);
        $updater.addState(nextState);
    },
    replaceState: function replaceState(nextState, callback) {
        var $updater = this.$updater;
        $updater.addCallback(callback);
        $updater.replaceState(nextState);
    },
    getDOMNode: function getDOMNode() {
        var node = this.$cache.node;
        return node && node.nodeName === '#comment' ? null : node;
    },
    isMounted: function isMounted() {
        return this.$cache.isMounted;
    }
  };
  function shouldUpdate(component, nextProps, nextState, nextContext, callback) {
    var shouldComponentUpdate = true;
    if (component.shouldComponentUpdate) {
        shouldComponentUpdate = component.shouldComponentUpdate(nextProps, nextState, nextContext);
    }
    if (shouldComponentUpdate === false) {
        component.props = nextProps;
        component.state = nextState;
        component.context = nextContext || {};
        return;
    }
    var cache = component.$cache;
    cache.props = nextProps;
    cache.state = nextState;
    cache.context = nextContext || {};
    component.forceUpdate(callback);
  }
  // event config
  var notBubbleEvents = {
    onmouseleave: 1,
    onmouseenter: 1,
    onload: 1,
    onunload: 1,
    onscroll: 1,
    onfocus: 1,
    onblur: 1,
    onrowexit: 1,
    onbeforeunload: 1,
    onstop: 1,
    ondragdrop: 1,
    ondragenter: 1,
    ondragexit: 1,
    ondraggesture: 1,
    ondragover: 1,
    oncontextmenu: 1
  };
  function getEventName(key) {
    key = key === 'onDoubleClick' ? 'ondblclick' : key;
    return key.toLowerCase();
  }
  // Mobile Safari does not fire properly bubble click events on
  // non-interactive elements, which means delegated click listeners do not
  // fire. The workaround for this bug involves attaching an empty click
  // listener on the target node.
  var inMobile = ('ontouchstart' in document);
  var emptyFunction = function emptyFunction() {};
  var ON_CLICK_KEY = 'onclick';
  var eventTypes = {};
  function addEvent(elem, eventType, listener) {
    eventType = getEventName(eventType);
    if (notBubbleEvents[eventType] === 1) {
        elem[eventType] = listener;
        return;
    }
    var eventStore = elem.eventStore || (elem.eventStore = {});
    eventStore[eventType] = listener;
    if (!eventTypes[eventType]) {
        // onclick -> click
        document.addEventListener(eventType.substr(2), dispatchEvent, false);
        eventTypes[eventType] = true;
    }
    if (inMobile && eventType === ON_CLICK_KEY) {
        elem.addEventListener('click', emptyFunction, false);
    }
    var nodeName = elem.nodeName;
    if (eventType === 'onchange' && (nodeName === 'INPUT' || nodeName === 'TEXTAREA')) {
        addEvent(elem, 'oninput', listener);
    }
  }
  function removeEvent(elem, eventType) {
    eventType = getEventName(eventType);
    if (notBubbleEvents[eventType] === 1) {
        elem[eventType] = null;
        return;
    }
    var eventStore = elem.eventStore || (elem.eventStore = {});
    delete eventStore[eventType];
    if (inMobile && eventType === ON_CLICK_KEY) {
        elem.removeEventListener('click', emptyFunction, false);
    }
    var nodeName = elem.nodeName;
    if (eventType === 'onchange' && (nodeName === 'INPUT' || nodeName === 'TEXTAREA')) {
        delete eventStore['oninput'];
    }
  }
  function dispatchEvent(event) {
    var target = event.target;
    var type = event.type;
    var eventType = 'on' + type;
    var syntheticEvent = undefined;
    updateQueue.isPending = true;
    while (target) {
        var _target = target;
        var eventStore = _target.eventStore;
        var listener = eventStore && eventStore[eventType];
        if (!listener) {
            target = target.parentNode;
            continue;
        }
        if (!syntheticEvent) {
            syntheticEvent = createSyntheticEvent(event);
        }
        syntheticEvent.currentTarget = target;
        listener.call(target, syntheticEvent);
        if (syntheticEvent.$cancalBubble) {
            break;
        }
        target = target.parentNode;
    }
    updateQueue.isPending = false;
    updateQueue.batchUpdate();
  }
  function createSyntheticEvent(nativeEvent) {
    var syntheticEvent = {};
    var cancalBubble = function cancalBubble() {
        return syntheticEvent.$cancalBubble = true;
    };
    syntheticEvent.nativeEvent = nativeEvent;
    for (var key in nativeEvent) {
        if (typeof nativeEvent[key] !== 'function') {
            syntheticEvent[key] = nativeEvent[key];
        } else if (key === 'stopPropagation' || key === 'stopImmediatePropagation') {
            syntheticEvent[key] = cancalBubble;
        } else {
            syntheticEvent[key] = nativeEvent[key].bind(nativeEvent);
        }
    }
    return syntheticEvent;
  }
  function setStyle(elemStyle, styles) {
      for (var styleName in styles) {
          if (styles.hasOwnProperty(styleName)) {
              setStyleValue(elemStyle, styleName, styles[styleName]);
          }
      }
  }
  function removeStyle(elemStyle, styles) {
      for (var styleName in styles) {
          if (styles.hasOwnProperty(styleName)) {
              elemStyle[styleName] = '';
          }
      }
  }
  function patchStyle(elemStyle, style, newStyle) {
      if (style === newStyle) {
          return;
      }
      if (!newStyle && style) {
          removeStyle(elemStyle, style);
          return;
      } else if (newStyle && !style) {
          setStyle(elemStyle, newStyle);
          return;
      }
      var keyMap = {};
      for (var key in style) {
          if (style.hasOwnProperty(key)) {
              keyMap[key] = true;
              if (style[key] !== newStyle[key]) {
                  setStyleValue(elemStyle, key, newStyle[key]);
              }
          }
      }
      for (var key in newStyle) {
          if (newStyle.hasOwnProperty(key) && keyMap[key] !== true) {
              if (style[key] !== newStyle[key]) {
                  setStyleValue(elemStyle, key, newStyle[key]);
              }
          }
      }
  }
  /**
   * CSS properties which accept numbers but are not in units of "px".
   */
  var isUnitlessNumber = {
      animationIterationCount: 1,
      borderImageOutset: 1,
      borderImageSlice: 1,
      borderImageWidth: 1,
      boxFlex: 1,
      boxFlexGroup: 1,
      boxOrdinalGroup: 1,
      columnCount: 1,
      flex: 1,
      flexGrow: 1,
      flexPositive: 1,
      flexShrink: 1,
      flexNegative: 1,
      flexOrder: 1,
      gridRow: 1,
      gridColumn: 1,
      fontWeight: 1,
      lineClamp: 1,
      lineHeight: 1,
      opacity: 1,
      order: 1,
      orphans: 1,
      tabSize: 1,
      widows: 1,
      zIndex: 1,
      zoom: 1,
      // SVG-related properties
      fillOpacity: 1,
      floodOpacity: 1,
      stopOpacity: 1,
      strokeDasharray: 1,
      strokeDashoffset: 1,
      strokeMiterlimit: 1,
      strokeOpacity: 1,
      strokeWidth: 1
  };
  function prefixKey(prefix, key) {
      return prefix + key.charAt(0).toUpperCase() + key.substring(1);
  }
  var prefixes = ['Webkit', 'ms', 'Moz', 'O'];
  Object.keys(isUnitlessNumber).forEach(function (prop) {
      prefixes.forEach(function (prefix) {
          isUnitlessNumber[prefixKey(prefix, prop)] = 1;
      });
  });
  var RE_NUMBER = /^-?\d+(\.\d+)?$/;
  function setStyleValue(elemStyle, styleName, styleValue) {
      if (!isUnitlessNumber[styleName] && RE_NUMBER.test(styleValue)) {
          elemStyle[styleName] = styleValue + 'px';
          return;
      }
      if (styleName === 'float') {
          styleName = 'cssFloat';
      }
      if (styleValue == null || typeof styleValue === 'boolean') {
          styleValue = '';
      }
      elemStyle[styleName] = styleValue;
  }
  var ATTRIBUTE_NAME_START_CHAR = ':A-Z_a-z\\u00C0-\\u00D6\\u00D8-\\u00F6\\u00F8-\\u02FF\\u0370-\\u037D\\u037F-\\u1FFF\\u200C-\\u200D\\u2070-\\u218F\\u2C00-\\u2FEF\\u3001-\\uD7FF\\uF900-\\uFDCF\\uFDF0-\\uFFFD';
  var ATTRIBUTE_NAME_CHAR = ATTRIBUTE_NAME_START_CHAR + '\\-.0-9\\uB7\\u0300-\\u036F\\u203F-\\u2040';
  var VALID_ATTRIBUTE_NAME_REGEX = new RegExp('^[' + ATTRIBUTE_NAME_START_CHAR + '][' + ATTRIBUTE_NAME_CHAR + ']*$');
  var isCustomAttribute = RegExp.prototype.test.bind(new RegExp('^(data|aria)-[' + ATTRIBUTE_NAME_CHAR + ']*$'));
  // will merge some data in properties below
  var properties = {};
  /**
   * Mapping from normalized, camelcased property names to a configuration that
   * specifies how the associated DOM property should be accessed or rendered.
   */
  var MUST_USE_PROPERTY = 0x1;
  var HAS_BOOLEAN_VALUE = 0x4;
  var HAS_NUMERIC_VALUE = 0x8;
  var HAS_POSITIVE_NUMERIC_VALUE = 0x10 | 0x8;
  var HAS_OVERLOADED_BOOLEAN_VALUE = 0x20;
  // html config
  var HTMLDOMPropertyConfig = {
      props: {
          /**
           * Standard Properties
           */
          accept: 0,
          acceptCharset: 0,
          accessKey: 0,
          action: 0,
          allowFullScreen: HAS_BOOLEAN_VALUE,
          allowTransparency: 0,
          alt: 0,
          async: HAS_BOOLEAN_VALUE,
          autoComplete: 0,
          autoFocus: HAS_BOOLEAN_VALUE,
          autoPlay: HAS_BOOLEAN_VALUE,
          capture: HAS_BOOLEAN_VALUE,
          cellPadding: 0,
          cellSpacing: 0,
          charSet: 0,
          challenge: 0,
          checked: MUST_USE_PROPERTY | HAS_BOOLEAN_VALUE,
          cite: 0,
          classID: 0,
          className: 0,
          cols: HAS_POSITIVE_NUMERIC_VALUE,
          colSpan: 0,
          content: 0,
          contentEditable: 0,
          contextMenu: 0,
          controls: HAS_BOOLEAN_VALUE,
          coords: 0,
          crossOrigin: 0,
          data: 0, // For `<object />` acts as `src`.
          dateTime: 0,
          'default': HAS_BOOLEAN_VALUE,
          // not in regular react, they did it in other way
          defaultValue: MUST_USE_PROPERTY,
          // not in regular react, they did it in other way
          defaultChecked: MUST_USE_PROPERTY | HAS_BOOLEAN_VALUE,
          defer: HAS_BOOLEAN_VALUE,
          dir: 0,
          disabled: HAS_BOOLEAN_VALUE,
          download: HAS_OVERLOADED_BOOLEAN_VALUE,
          draggable: 0,
          encType: 0,
          form: 0,
          formAction: 0,
          formEncType: 0,
          formMethod: 0,
          formNoValidate: HAS_BOOLEAN_VALUE,
          formTarget: 0,
          frameBorder: 0,
          headers: 0,
          height: 0,
          hidden: HAS_BOOLEAN_VALUE,
          high: 0,
          href: 0,
          hrefLang: 0,
          htmlFor: 0,
          httpEquiv: 0,
          icon: 0,
          id: 0,
          inputMode: 0,
          integrity: 0,
          is: 0,
          keyParams: 0,
          keyType: 0,
          kind: 0,
          label: 0,
          lang: 0,
          list: 0,
          loop: HAS_BOOLEAN_VALUE,
          low: 0,
          manifest: 0,
          marginHeight: 0,
          marginWidth: 0,
          max: 0,
          maxLength: 0,
          media: 0,
          mediaGroup: 0,
          method: 0,
          min: 0,
          minLength: 0,
          // Caution; `option.selected` is not updated if `select.multiple` is
          // disabled with `removeAttribute`.
          multiple: MUST_USE_PROPERTY | HAS_BOOLEAN_VALUE,
          muted: MUST_USE_PROPERTY | HAS_BOOLEAN_VALUE,
          name: 0,
          nonce: 0,
          noValidate: HAS_BOOLEAN_VALUE,
          open: HAS_BOOLEAN_VALUE,
          optimum: 0,
          pattern: 0,
          placeholder: 0,
          poster: 0,
          preload: 0,
          profile: 0,
          radioGroup: 0,
          readOnly: HAS_BOOLEAN_VALUE,
          rel: 0,
          required: HAS_BOOLEAN_VALUE,
          reversed: HAS_BOOLEAN_VALUE,
          role: 0,
          rows: HAS_POSITIVE_NUMERIC_VALUE,
          rowSpan: HAS_NUMERIC_VALUE,
          sandbox: 0,
          scope: 0,
          scoped: HAS_BOOLEAN_VALUE,
          scrolling: 0,
          seamless: HAS_BOOLEAN_VALUE,
          selected: MUST_USE_PROPERTY | HAS_BOOLEAN_VALUE,
          shape: 0,
          size: HAS_POSITIVE_NUMERIC_VALUE,
          sizes: 0,
          span: HAS_POSITIVE_NUMERIC_VALUE,
          spellCheck: 0,
          src: 0,
          srcDoc: 0,
          srcLang: 0,
          srcSet: 0,
          start: HAS_NUMERIC_VALUE,
          step: 0,
          style: 0,
          summary: 0,
          tabIndex: 0,
          target: 0,
          title: 0,
          // Setting .type throws on non-<input> tags
          type: 0,
          useMap: 0,
          value: MUST_USE_PROPERTY,
          width: 0,
          wmode: 0,
          wrap: 0,
          /**
           * RDFa Properties
           */
          about: 0,
          datatype: 0,
          inlist: 0,
          prefix: 0,
          // property is also supported for OpenGraph in meta tags.
          property: 0,
          resource: 0,
          'typeof': 0,
          vocab: 0,
          /**
           * Non-standard Properties
           */
          // autoCapitalize and autoCorrect are supported in Mobile Safari for
          // keyboard hints.
          autoCapitalize: 0,
          autoCorrect: 0,
          // autoSave allows WebKit/Blink to persist values of input fields on page reloads
          autoSave: 0,
          // color is for Safari mask-icon link
          color: 0,
          // itemProp, itemScope, itemType are for
          // Microdata support. See http://schema.org/docs/gs.html
          itemProp: 0,
          itemScope: HAS_BOOLEAN_VALUE,
          itemType: 0,
          // itemID and itemRef are for Microdata support as well but
          // only specified in the WHATWG spec document. See
          // https://html.spec.whatwg.org/multipage/microdata.html#microdata-dom-api
          itemID: 0,
          itemRef: 0,
          // results show looking glass icon and recent searches on input
          // search fields in WebKit/Blink
          results: 0,
          // IE-only attribute that specifies security restrictions on an iframe
          // as an alternative to the sandbox attribute on IE<10
          security: 0,
          // IE-only attribute that controls focus behavior
          unselectable: 0
      },
      attrNS: {},
      domAttrs: {
          acceptCharset: 'accept-charset',
          className: 'class',
          htmlFor: 'for',
          httpEquiv: 'http-equiv'
      },
      domProps: {}
  };
  // svg config
  var xlink = 'http://www.w3.org/1999/xlink';
  var xml = 'http://www.w3.org/XML/1998/namespace';
  // We use attributes for everything SVG so let's avoid some duplication and run
  // code instead.
  // The following are all specified in the HTML config already so we exclude here.
  // - class (as className)
  // - color
  // - height
  // - id
  // - lang
  // - max
  // - media
  // - method
  // - min
  // - name
  // - style
  // - target
  // - type
  // - width
  var ATTRS = {
      accentHeight: 'accent-height',
      accumulate: 0,
      additive: 0,
      alignmentBaseline: 'alignment-baseline',
      allowReorder: 'allowReorder',
      alphabetic: 0,
      amplitude: 0,
      arabicForm: 'arabic-form',
      ascent: 0,
      attributeName: 'attributeName',
      attributeType: 'attributeType',
      autoReverse: 'autoReverse',
      azimuth: 0,
      baseFrequency: 'baseFrequency',
      baseProfile: 'baseProfile',
      baselineShift: 'baseline-shift',
      bbox: 0,
      begin: 0,
      bias: 0,
      by: 0,
      calcMode: 'calcMode',
      capHeight: 'cap-height',
      clip: 0,
      clipPath: 'clip-path',
      clipRule: 'clip-rule',
      clipPathUnits: 'clipPathUnits',
      colorInterpolation: 'color-interpolation',
      colorInterpolationFilters: 'color-interpolation-filters',
      colorProfile: 'color-profile',
      colorRendering: 'color-rendering',
      contentScriptType: 'contentScriptType',
      contentStyleType: 'contentStyleType',
      cursor: 0,
      cx: 0,
      cy: 0,
      d: 0,
      decelerate: 0,
      descent: 0,
      diffuseConstant: 'diffuseConstant',
      direction: 0,
      display: 0,
      divisor: 0,
      dominantBaseline: 'dominant-baseline',
      dur: 0,
      dx: 0,
      dy: 0,
      edgeMode: 'edgeMode',
      elevation: 0,
      enableBackground: 'enable-background',
      end: 0,
      exponent: 0,
      externalResourcesRequired: 'externalResourcesRequired',
      fill: 0,
      fillOpacity: 'fill-opacity',
      fillRule: 'fill-rule',
      filter: 0,
      filterRes: 'filterRes',
      filterUnits: 'filterUnits',
      floodColor: 'flood-color',
      floodOpacity: 'flood-opacity',
      focusable: 0,
      fontFamily: 'font-family',
      fontSize: 'font-size',
      fontSizeAdjust: 'font-size-adjust',
      fontStretch: 'font-stretch',
      fontStyle: 'font-style',
      fontVariant: 'font-variant',
      fontWeight: 'font-weight',
      format: 0,
      from: 0,
      fx: 0,
      fy: 0,
      g1: 0,
      g2: 0,
      glyphName: 'glyph-name',
      glyphOrientationHorizontal: 'glyph-orientation-horizontal',
      glyphOrientationVertical: 'glyph-orientation-vertical',
      glyphRef: 'glyphRef',
      gradientTransform: 'gradientTransform',
      gradientUnits: 'gradientUnits',
      hanging: 0,
      horizAdvX: 'horiz-adv-x',
      horizOriginX: 'horiz-origin-x',
      ideographic: 0,
      imageRendering: 'image-rendering',
      'in': 0,
      in2: 0,
      intercept: 0,
      k: 0,
      k1: 0,
      k2: 0,
      k3: 0,
      k4: 0,
      kernelMatrix: 'kernelMatrix',
      kernelUnitLength: 'kernelUnitLength',
      kerning: 0,
      keyPoints: 'keyPoints',
      keySplines: 'keySplines',
      keyTimes: 'keyTimes',
      lengthAdjust: 'lengthAdjust',
      letterSpacing: 'letter-spacing',
      lightingColor: 'lighting-color',
      limitingConeAngle: 'limitingConeAngle',
      local: 0,
      markerEnd: 'marker-end',
      markerMid: 'marker-mid',
      markerStart: 'marker-start',
      markerHeight: 'markerHeight',
      markerUnits: 'markerUnits',
      markerWidth: 'markerWidth',
      mask: 0,
      maskContentUnits: 'maskContentUnits',
      maskUnits: 'maskUnits',
      mathematical: 0,
      mode: 0,
      numOctaves: 'numOctaves',
      offset: 0,
      opacity: 0,
      operator: 0,
      order: 0,
      orient: 0,
      orientation: 0,
      origin: 0,
      overflow: 0,
      overlinePosition: 'overline-position',
      overlineThickness: 'overline-thickness',
      paintOrder: 'paint-order',
      panose1: 'panose-1',
      pathLength: 'pathLength',
      patternContentUnits: 'patternContentUnits',
      patternTransform: 'patternTransform',
      patternUnits: 'patternUnits',
      pointerEvents: 'pointer-events',
      points: 0,
      pointsAtX: 'pointsAtX',
      pointsAtY: 'pointsAtY',
      pointsAtZ: 'pointsAtZ',
      preserveAlpha: 'preserveAlpha',
      preserveAspectRatio: 'preserveAspectRatio',
      primitiveUnits: 'primitiveUnits',
      r: 0,
      radius: 0,
      refX: 'refX',
      refY: 'refY',
      renderingIntent: 'rendering-intent',
      repeatCount: 'repeatCount',
      repeatDur: 'repeatDur',
      requiredExtensions: 'requiredExtensions',
      requiredFeatures: 'requiredFeatures',
      restart: 0,
      result: 0,
      rotate: 0,
      rx: 0,
      ry: 0,
      scale: 0,
      seed: 0,
      shapeRendering: 'shape-rendering',
      slope: 0,
      spacing: 0,
      specularConstant: 'specularConstant',
      specularExponent: 'specularExponent',
      speed: 0,
      spreadMethod: 'spreadMethod',
      startOffset: 'startOffset',
      stdDeviation: 'stdDeviation',
      stemh: 0,
      stemv: 0,
      stitchTiles: 'stitchTiles',
      stopColor: 'stop-color',
      stopOpacity: 'stop-opacity',
      strikethroughPosition: 'strikethrough-position',
      strikethroughThickness: 'strikethrough-thickness',
      string: 0,
      stroke: 0,
      strokeDasharray: 'stroke-dasharray',
      strokeDashoffset: 'stroke-dashoffset',
      strokeLinecap: 'stroke-linecap',
      strokeLinejoin: 'stroke-linejoin',
      strokeMiterlimit: 'stroke-miterlimit',
      strokeOpacity: 'stroke-opacity',
      strokeWidth: 'stroke-width',
      surfaceScale: 'surfaceScale',
      systemLanguage: 'systemLanguage',
      tableValues: 'tableValues',
      targetX: 'targetX',
      targetY: 'targetY',
      textAnchor: 'text-anchor',
      textDecoration: 'text-decoration',
      textRendering: 'text-rendering',
      textLength: 'textLength',
      to: 0,
      transform: 0,
      u1: 0,
      u2: 0,
      underlinePosition: 'underline-position',
      underlineThickness: 'underline-thickness',
      unicode: 0,
      unicodeBidi: 'unicode-bidi',
      unicodeRange: 'unicode-range',
      unitsPerEm: 'units-per-em',
      vAlphabetic: 'v-alphabetic',
      vHanging: 'v-hanging',
      vIdeographic: 'v-ideographic',
      vMathematical: 'v-mathematical',
      values: 0,
      vectorEffect: 'vector-effect',
      version: 0,
      vertAdvY: 'vert-adv-y',
      vertOriginX: 'vert-origin-x',
      vertOriginY: 'vert-origin-y',
      viewBox: 'viewBox',
      viewTarget: 'viewTarget',
      visibility: 0,
      widths: 0,
      wordSpacing: 'word-spacing',
      writingMode: 'writing-mode',
      x: 0,
      xHeight: 'x-height',
      x1: 0,
      x2: 0,
      xChannelSelector: 'xChannelSelector',
      xlinkActuate: 'xlink:actuate',
      xlinkArcrole: 'xlink:arcrole',
      xlinkHref: 'xlink:href',
      xlinkRole: 'xlink:role',
      xlinkShow: 'xlink:show',
      xlinkTitle: 'xlink:title',
      xlinkType: 'xlink:type',
      xmlBase: 'xml:base',
      xmlLang: 'xml:lang',
      xmlSpace: 'xml:space',
      y: 0,
      y1: 0,
      y2: 0,
      yChannelSelector: 'yChannelSelector',
      z: 0,
      zoomAndPan: 'zoomAndPan'
  };
  var SVGDOMPropertyConfig = {
      props: {},
      attrNS: {
          xlinkActuate: xlink,
          xlinkArcrole: xlink,
          xlinkHref: xlink,
          xlinkRole: xlink,
          xlinkShow: xlink,
          xlinkTitle: xlink,
          xlinkType: xlink,
          xmlBase: xml,
          xmlLang: xml,
          xmlSpace: xml
      },
      domAttrs: {},
      domProps: {}
  };
  Object.keys(ATTRS).map(function (key) {
      SVGDOMPropertyConfig.props[key] = 0;
      if (ATTRS[key]) {
          SVGDOMPropertyConfig.domAttrs[key] = ATTRS[key];
      }
  });
  // merge html and svg config into properties
  mergeConfigToProperties(HTMLDOMPropertyConfig);
  mergeConfigToProperties(SVGDOMPropertyConfig);
  function mergeConfigToProperties(config) {
      var
      // all react/react-lite supporting property names in here
      props = config.props;
      var
      // attributes namespace in here
      attrNS = config.attrNS;
      var
      // propName in props which should use to be dom-attribute in here
      domAttrs = config.domAttrs;
      var
      // propName in props which should use to be dom-property in here
      domProps = config.domProps;
      for (var propName in props) {
          if (!props.hasOwnProperty(propName)) {
              continue;
          }
          var propConfig = props[propName];
          properties[propName] = {
              attributeName: domAttrs.hasOwnProperty(propName) ? domAttrs[propName] : propName.toLowerCase(),
              propertyName: domProps.hasOwnProperty(propName) ? domProps[propName] : propName,
              attributeNamespace: attrNS.hasOwnProperty(propName) ? attrNS[propName] : null,
              mustUseProperty: checkMask(propConfig, MUST_USE_PROPERTY),
              hasBooleanValue: checkMask(propConfig, HAS_BOOLEAN_VALUE),
              hasNumericValue: checkMask(propConfig, HAS_NUMERIC_VALUE),
              hasPositiveNumericValue: checkMask(propConfig, HAS_POSITIVE_NUMERIC_VALUE),
              hasOverloadedBooleanValue: checkMask(propConfig, HAS_OVERLOADED_BOOLEAN_VALUE)
          };
      }
  }
  function checkMask(value, bitmask) {
      return (value & bitmask) === bitmask;
  }
  /**
   * Sets the value for a property on a node.
   *
   * @param {DOMElement} node
   * @param {string} name
   * @param {*} value
   */
  function setPropValue(node, name, value) {
      var propInfo = properties.hasOwnProperty(name) && properties[name];
      if (propInfo) {
          // should delete value from dom
          if (value == null || propInfo.hasBooleanValue && !value || propInfo.hasNumericValue && isNaN(value) || propInfo.hasPositiveNumericValue && value < 1 || propInfo.hasOverloadedBooleanValue && value === false) {
              removePropValue(node, name);
          } else if (propInfo.mustUseProperty) {
              var propName = propInfo.propertyName;
              // dom.value has side effect
              if (propName !== 'value' || '' + node[propName] !== '' + value) {
                  node[propName] = value;
              }
          } else {
              var attributeName = propInfo.attributeName;
              var namespace = propInfo.attributeNamespace;
              // `setAttribute` with objects becomes only `[object]` in IE8/9,
              // ('' + value) makes it output the correct toString()-value.
              if (namespace) {
                  node.setAttributeNS(namespace, attributeName, '' + value);
              } else if (propInfo.hasBooleanValue || propInfo.hasOverloadedBooleanValue && value === true) {
                  node.setAttribute(attributeName, '');
              } else {
                  node.setAttribute(attributeName, '' + value);
              }
          }
      } else if (isCustomAttribute(name) && VALID_ATTRIBUTE_NAME_REGEX.test(name)) {
          if (value == null) {
              node.removeAttribute(name);
          } else {
              node.setAttribute(name, '' + value);
          }
      }
  }
  /**
   * Deletes the value for a property on a node.
   *
   * @param {DOMElement} node
   * @param {string} name
   */
  function removePropValue(node, name) {
      var propInfo = properties.hasOwnProperty(name) && properties[name];
      if (propInfo) {
          if (propInfo.mustUseProperty) {
              var propName = propInfo.propertyName;
              if (propInfo.hasBooleanValue) {
                  node[propName] = false;
              } else {
                  // dom.value accept string value has side effect
                  if (propName !== 'value' || '' + node[propName] !== '') {
                      node[propName] = '';
                  }
              }
          } else {
              node.removeAttribute(propInfo.attributeName);
          }
      } else if (isCustomAttribute(name)) {
          node.removeAttribute(name);
      }
  }
  function isFn(obj) {
      return typeof obj === 'function';
  }
  var isArr = Array.isArray;
  function noop() {}
  function identity(obj) {
      return obj;
  }
  function pipe(fn1, fn2) {
      return function () {
          fn1.apply(this, arguments);
          return fn2.apply(this, arguments);
      };
  }
  function flattenChildren(list, iteratee, a) {
      var len = list.length;
      var i = -1;
      while (len--) {
          var item = list[++i];
          if (isArr(item)) {
              flattenChildren(item, iteratee, a);
          } else {
              iteratee(item, a);
          }
      }
  }
  function eachItem(list, iteratee) {
      for (var i = 0, len = list.length; i < len; i++) {
          iteratee(list[i], i);
      }
  }
  function extend(to, from) {
      if (!from) {
          return to;
      }
      var keys = Object.keys(from);
      var i = keys.length;
      while (i--) {
          to[keys[i]] = from[keys[i]];
      }
      return to;
  }
  var uid = 0;
  function getUid() {
      return ++uid;
  }
  var EVENT_KEYS = /^on/i;
  function setProps(elem, props, isCustomComponent) {
      for (var key in props) {
          if (!props.hasOwnProperty(key) || key === 'children') {
              continue;
          }
          var value = props[key];
          if (EVENT_KEYS.test(key)) {
              addEvent(elem, key, value);
          } else if (key === 'style') {
              setStyle(elem.style, value);
          } else if (key === 'dangerouslySetInnerHTML') {
              value && value.__html != null && (elem.innerHTML = value.__html);
          } else if (isCustomComponent) {
              if (value == null) {
                  elem.removeAttribute(key);
              } else {
                  elem.setAttribute(key, '' + value);
              }
          } else {
              setPropValue(elem, key, value);
          }
      }
  }
  function patchProp(key, oldValue, value, elem, isCustomComponent) {
      if (key === 'value' || key === 'checked') {
          oldValue = elem[key];
      }
      if (value === oldValue) {
          return;
      }
      if (value === undefined) {
          if (EVENT_KEYS.test(key)) {
              removeEvent(elem, key);
          } else if (key === 'style') {
              removeStyle(elem.style, oldValue);
          } else if (key === 'dangerouslySetInnerHTML') {
              elem.innerHTML = '';
          } else if (isCustomComponent) {
              elem.removeAttribute(key);
          } else {
              removePropValue(elem, key);
          }
          return;
      }
      if (EVENT_KEYS.test(key)) {
          // addEvent will replace the oldValue
          addEvent(elem, key, value);
      } else if (key === 'style') {
          patchStyle(elem.style, oldValue, value);
      } else if (key === 'dangerouslySetInnerHTML') {
          var oldHtml = oldValue && oldValue.__html;
          var html = value && value.__html;
          if (html != null && html !== oldHtml) {
              elem.innerHTML = html;
          }
      } else if (isCustomComponent) {
          if (value == null) {
              elem.removeAttribute(key);
          } else {
              elem.setAttribute(key, '' + value);
          }
      } else {
          setPropValue(elem, key, value);
      }
  }
  function patchProps(elem, props, newProps, isCustomComponent) {
      var keyMap = { children: true };
      for (var key in props) {
          if (props.hasOwnProperty(key) && key !== 'children') {
              keyMap[key] = true;
              patchProp(key, props[key], newProps[key], elem, isCustomComponent);
          }
      }
      for (var key in newProps) {
          if (newProps.hasOwnProperty(key) && keyMap[key] !== true) {
              patchProp(key, props[key], newProps[key], elem, isCustomComponent);
          }
      }
  }
  if (!Object.freeze) {
      Object.freeze = identity;
  }
  var pendingRendering = {};
  var vnodeStore = {};
  function renderTreeIntoContainer(vnode, container, callback, parentContext) {
    if (!vnode.vtype) {
        throw new Error('cannot render ' + vnode + ' to container');
    }
    var id = container[COMPONENT_ID] || (container[COMPONENT_ID] = getUid());
    var argsCache = pendingRendering[id];
    // component lify cycle method maybe call root rendering
    // should bundle them and render by only one time
    if (argsCache) {
        if (argsCache === true) {
            pendingRendering[id] = argsCache = [vnode, callback, parentContext];
        } else {
            argsCache[0] = vnode;
            argsCache[2] = parentContext;
            if (callback) {
                argsCache[1] = argsCache[1] ? pipe(argsCache[1], callback) : callback;
            }
        }
        return;
    }
    pendingRendering[id] = true;
    var oldVnode = null;
    var rootNode = null;
    if (oldVnode = vnodeStore[id]) {
        rootNode = compareTwoVnodes(oldVnode, vnode, container.firstChild, parentContext);
    } else {
        rootNode = initVnode(vnode, parentContext, container.namespaceURI);
        var childNode = null;
        while (childNode = container.lastChild) {
            container.removeChild(childNode);
        }
        container.appendChild(rootNode);
    }
    vnodeStore[id] = vnode;
    var isPending = updateQueue.isPending;
    updateQueue.isPending = true;
    batchUpdateDOM();
    argsCache = pendingRendering[id];
    delete pendingRendering[id];
    var result = null;
    if (isArr(argsCache)) {
        result = renderTreeIntoContainer(argsCache[0], container, argsCache[1], argsCache[2]);
    } else if (vnode.vtype === VELEMENT) {
        result = rootNode;
    } else if (vnode.vtype === VCOMPONENT) {
        result = rootNode.cache[vnode.id];
    }
    if (!isPending) {
        updateQueue.isPending = false;
        updateQueue.batchUpdate();
    }
    if (callback) {
        callback.call(result);
    }
    return result;
  }
  function render(vnode, container, callback) {
    return renderTreeIntoContainer(vnode, container, callback);
  }
  function unstable_renderSubtreeIntoContainer(parentComponent, subVnode, container, callback) {
    var context = parentComponent.vnode ? parentComponent.vnode.context : parentComponent.$cache.parentContext;
    return renderTreeIntoContainer(subVnode, container, callback, context);
  }
  function unmountComponentAtNode(container) {
    if (!container.nodeName) {
        throw new Error('expect node');
    }
    var id = container[COMPONENT_ID];
    var vnode = null;
    if (vnode = vnodeStore[id]) {
        destroyVnode(vnode, container.firstChild);
        container.removeChild(container.firstChild);
        delete vnodeStore[id];
        return true;
    }
    return false;
  }
  function findDOMNode(node) {
    if (node == null) {
        return null;
    }
    if (node.nodeName) {
        return node;
    }
    var component = node;
    // if component.node equal to false, component must be unmounted
    if (component.getDOMNode && component.$cache.isMounted) {
        return component.getDOMNode();
    }
    throw new Error('findDOMNode can not find Node');
  }
  var ReactDOM = Object.freeze({
    render: render,
    unstable_renderSubtreeIntoContainer: unstable_renderSubtreeIntoContainer,
    unmountComponentAtNode: unmountComponentAtNode,
    findDOMNode: findDOMNode
  });
  function isValidElement(obj) {
    return obj != null && !!obj.vtype;
  }
  function cloneElement(originElem, props) {
    var type = originElem.type;
    var key = originElem.key;
    var ref = originElem.ref;
    var newProps = extend(extend({ key: key, ref: ref }, originElem.props), props);
    for (var _len = arguments.length, children = Array(_len > 2 ? _len - 2 : 0), _key = 2; _key < _len; _key++) {
        children[_key - 2] = arguments[_key];
    }
    var vnode = createElement.apply(undefined, [type, newProps].concat(children));
    if (vnode.ref === originElem.ref) {
        vnode.refs = originElem.refs;
    }
    return vnode;
  }
  function createFactory(type) {
    var factory = function factory() {
        for (var _len2 = arguments.length, args = Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
            args[_key2] = arguments[_key2];
        }
        return createElement.apply(undefined, [type].concat(args));
    };
    factory.type = type;
    return factory;
  }
  function createElement(type, props, children) {
    var createVnode = null;
    var varType = typeof type;
    if (varType === 'string') {
        createVnode = createVelem;
    } else if (varType === 'function') {
        if (type.prototype && typeof type.prototype.forceUpdate === 'function') {
            createVnode = createVcomponent;
        } else {
            createVnode = createVstateless;
        }
    } else {
        throw new Error('React.createElement: unexpect type [ ' + type + ' ]');
    }
    var key = null;
    var ref = null;
    var finalProps = {};
    if (props != null) {
        for (var propKey in props) {
            if (!props.hasOwnProperty(propKey)) {
                continue;
            }
            if (propKey === 'key') {
                if (props.key !== undefined) {
                    key = '' + props.key;
                }
            } else if (propKey === 'ref') {
                if (props.ref !== undefined) {
                    ref = props.ref;
                }
            } else {
                finalProps[propKey] = props[propKey];
            }
        }
    }
    var defaultProps = type.defaultProps;
    if (defaultProps) {
        for (var propKey in defaultProps) {
            if (finalProps[propKey] === undefined) {
                finalProps[propKey] = defaultProps[propKey];
            }
        }
    }
    var argsLen = arguments.length;
    var finalChildren = children;
    if (argsLen > 3) {
        finalChildren = Array(argsLen - 2);
        for (var i = 2; i < argsLen; i++) {
            finalChildren[i - 2] = arguments[i];
        }
    }
    if (finalChildren !== undefined) {
        finalProps.children = finalChildren;
    }
    var vnode = createVnode(type, finalProps);
    vnode.key = key;
    vnode.ref = ref;
    return vnode;
  }
  var tagNames = 'a|abbr|address|area|article|aside|audio|b|base|bdi|bdo|big|blockquote|body|br|button|canvas|caption|cite|code|col|colgroup|data|datalist|dd|del|details|dfn|dialog|div|dl|dt|em|embed|fieldset|figcaption|figure|footer|form|h1|h2|h3|h4|h5|h6|head|header|hgroup|hr|html|i|iframe|img|input|ins|kbd|keygen|label|legend|li|link|main|map|mark|menu|menuitem|meta|meter|nav|noscript|object|ol|optgroup|option|output|p|param|picture|pre|progress|q|rp|rt|ruby|s|samp|script|section|select|small|source|span|strong|style|sub|summary|sup|table|tbody|td|textarea|tfoot|th|thead|time|title|tr|track|u|ul|var|video|wbr|circle|clipPath|defs|ellipse|g|image|line|linearGradient|mask|path|pattern|polygon|polyline|radialGradient|rect|stop|svg|text|tspan';
  var DOM = {};
  eachItem(tagNames.split('|'), function (tagName) {
    DOM[tagName] = createFactory(tagName);
  });
  var check = function check() {
      return check;
  };
  check.isRequired = check;
  var PropTypes = {
      "array": check,
      "bool": check,
      "func": check,
      "number": check,
      "object": check,
      "string": check,
      "any": check,
      "arrayOf": check,
      "element": check,
      "instanceOf": check,
      "node": check,
      "objectOf": check,
      "oneOf": check,
      "oneOfType": check,
      "shape": check
  };
  function only(children) {
    if (isValidElement(children)) {
        return children;
    }
    throw new Error('expect only one child');
  }
  function forEach(children, iteratee, context) {
    if (children == null) {
        return children;
    }
    var index = 0;
    if (isArr(children)) {
        flattenChildren(children, function (child) {
            iteratee.call(context, child, index++);
        });
    } else {
        iteratee.call(context, children, index);
    }
  }
  function map(children, iteratee, context) {
    if (children == null) {
        return children;
    }
    var store = [];
    var keyMap = {};
    forEach(children, function (child, index) {
        var data = {};
        data.child = iteratee.call(context, child, index) || child;
        data.isEqual = data.child === child;
        var key = data.key = getKey(child, index);
        if (keyMap.hasOwnProperty(key)) {
            keyMap[key] += 1;
        } else {
            keyMap[key] = 0;
        }
        data.index = keyMap[key];
        store.push(data);
    });
    var result = [];
    eachItem(store, function (_ref) {
        var child = _ref.child;
        var key = _ref.key;
        var index = _ref.index;
        var isEqual = _ref.isEqual;
        if (child == null || typeof child === 'boolean') {
            return;
        }
        if (!isValidElement(child) || key == null) {
            result.push(child);
            return;
        }
        if (keyMap[key] !== 0) {
            key += ':' + index;
        }
        if (!isEqual) {
            key = escapeUserProvidedKey(child.key || '') + '/' + key;
        }
        child = cloneElement(child, { key: key });
        result.push(child);
    });
    return result;
  }
  function count(children) {
    var count = 0;
    forEach(children, function () {
        count++;
    });
    return count;
  }
  function toArray(children) {
    return map(children, identity) || [];
  }
  function getKey(child, index) {
    var key = undefined;
    if (isValidElement(child) && typeof child.key === 'string') {
        key = '.$' + child.key;
    } else {
        key = '.' + index.toString(36);
    }
    return key;
  }
  var userProvidedKeyEscapeRegex = /\/(?!\/)/g;
  function escapeUserProvidedKey(text) {
    return ('' + text).replace(userProvidedKeyEscapeRegex, '//');
  }
  var Children = Object.freeze({
    only: only,
    forEach: forEach,
    map: map,
    count: count,
    toArray: toArray
  });
  function eachMixin(mixins, iteratee) {
    eachItem(mixins, function (mixin) {
        if (mixin) {
            if (isArr(mixin.mixins)) {
                eachMixin(mixin.mixins, iteratee);
            }
            iteratee(mixin);
        }
    });
  }
  function combineMixinToProto(proto, mixin) {
    for (var key in mixin) {
        if (!mixin.hasOwnProperty(key)) {
            continue;
        }
        var value = mixin[key];
        if (key === 'getInitialState') {
            proto.$getInitialStates.push(value);
            continue;
        }
        var curValue = proto[key];
        if (isFn(curValue) && isFn(value)) {
            proto[key] = pipe(curValue, value);
        } else {
            proto[key] = value;
        }
    }
  }
  function combineMixinToClass(Component, mixin) {
    extend(Component.propTypes, mixin.propTypes);
    extend(Component.contextTypes, mixin.contextTypes);
    extend(Component, mixin.statics);
    if (isFn(mixin.getDefaultProps)) {
        extend(Component.defaultProps, mixin.getDefaultProps());
    }
  }
  function bindContext(obj, source) {
    for (var key in source) {
        if (source.hasOwnProperty(key)) {
            if (isFn(source[key])) {
                obj[key] = source[key].bind(obj);
            }
        }
    }
  }
  var Facade = function Facade() {};
  Facade.prototype = Component.prototype;
  function getInitialState() {
    var _this = this;
    var state = {};
    var setState = this.setState;
    this.setState = Facade;
    eachItem(this.$getInitialStates, function (getInitialState) {
        if (isFn(getInitialState)) {
            extend(state, getInitialState.call(_this));
        }
    });
    this.setState = setState;
    return state;
  }
  function createClass(spec) {
    if (!isFn(spec.render)) {
        throw new Error('createClass: spec.render is not function');
    }
    var specMixins = spec.mixins || [];
    var mixins = specMixins.concat(spec);
    spec.mixins = null;
    function Klass(props, context) {
        Component.call(this, props, context);
        this.constructor = Klass;
        spec.autobind !== false && bindContext(this, Klass.prototype);
        this.state = this.getInitialState() || this.state;
    }
    Klass.displayName = spec.displayName;
    Klass.contextTypes = {};
    Klass.propTypes = {};
    Klass.defaultProps = {};
    var proto = Klass.prototype = new Facade();
    proto.$getInitialStates = [];
    eachMixin(mixins, function (mixin) {
        combineMixinToProto(proto, mixin);
        combineMixinToClass(Klass, mixin);
    });
    proto.getInitialState = getInitialState;
    spec.mixins = specMixins;
    return Klass;
  }
  var React = extend({
      version: '0.15.1',
      cloneElement: cloneElement,
      isValidElement: isValidElement,
      createElement: createElement,
      createFactory: createFactory,
      Component: Component,
      createClass: createClass,
      Children: Children,
      PropTypes: PropTypes,
      DOM: DOM
  }, ReactDOM);
  React.__SECRET_DOM_DO_NOT_USE_OR_YOU_WILL_BE_FIRED = ReactDOM;
  return React;
}));
;
 (function(f) {
         var g;
         if (typeof window !== "undefined") {
             g = window;
         } else if (typeof global !== "undefined") {
             g = global;
         } else if (typeof self !== "undefined") {
             g = self;
         } else {
             // works providing we're not in "use strict";
             // needed for Java 8 Nashorn
             // see https://github.com/facebook/react/issues/3037
             g = this;
         }
         g.ReactDOM = f(g.React);
 })(function(React) {
     return React.__SECRET_DOM_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
 });/**
  * ReactDOM v15.0.0-rc.2
  *
  * Copyright 2013-present, Facebook, Inc.
  * All rights reserved.
  *
  * This source code is licensed under the BSD-style license found in the
  * LICENSE file in the root directory of this source tree. An additional grant
  * of patent rights can be found in the PATENTS file in the same directory.
  *
  */
 // Based off https://github.com/ForbesLindesay/umd/blob/master/template.js
 ;
 (function(f) {
         var g;
         if (typeof window !== "undefined") {
             g = window;
         } else if (typeof global !== "undefined") {
             g = global;
         } else if (typeof self !== "undefined") {
             g = self;
         } else {
             // works providing we're not in "use strict";
             // needed for Java 8 Nashorn
             // see https://github.com/facebook/react/issues/3037
             g = this;
         }
         g.ReactDOM = f(g.React);
 })(function(React) {
     return React.__SECRET_DOM_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;
 });

/* Zepto v1.1.6 - zepto event ajax form ie - zeptojs.com/license */

var Zepto = (function() {
  var undefined, key, $, classList, emptyArray = [], slice = emptyArray.slice, filter = emptyArray.filter,
    document = window.document,
    elementDisplay = {}, classCache = {},
    cssNumber = { 'column-count': 1, 'columns': 1, 'font-weight': 1, 'line-height': 1,'opacity': 1, 'z-index': 1, 'zoom': 1 },
    fragmentRE = /^\s*<(\w+|!)[^>]*>/,
    singleTagRE = /^<(\w+)\s*\/?>(?:<\/\1>|)$/,
    tagExpanderRE = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,
    rootNodeRE = /^(?:body|html)$/i,
    capitalRE = /([A-Z])/g,

    // special attributes that should be get/set via method calls
    methodAttributes = ['val', 'css', 'html', 'text', 'data', 'width', 'height', 'offset'],

    adjacencyOperators = [ 'after', 'prepend', 'before', 'append' ],
    table = document.createElement('table'),
    tableRow = document.createElement('tr'),
    containers = {
      'tr': document.createElement('tbody'),
      'tbody': table, 'thead': table, 'tfoot': table,
      'td': tableRow, 'th': tableRow,
      '*': document.createElement('div')
    },
    readyRE = /complete|loaded|interactive/,
    simpleSelectorRE = /^[\w-]*$/,
    class2type = {},
    toString = class2type.toString,
    zepto = {},
    camelize, uniq,
    tempParent = document.createElement('div'),
    propMap = {
      'tabindex': 'tabIndex',
      'readonly': 'readOnly',
      'for': 'htmlFor',
      'class': 'className',
      'maxlength': 'maxLength',
      'cellspacing': 'cellSpacing',
      'cellpadding': 'cellPadding',
      'rowspan': 'rowSpan',
      'colspan': 'colSpan',
      'usemap': 'useMap',
      'frameborder': 'frameBorder',
      'contenteditable': 'contentEditable'
    },
    isArray = Array.isArray ||
      function(object){ return object instanceof Array }

  zepto.matches = function(element, selector) {
    if (!selector || !element || element.nodeType !== 1) return false
    var matchesSelector = element.webkitMatchesSelector || element.mozMatchesSelector ||
                          element.oMatchesSelector || element.matchesSelector
    if (matchesSelector) return matchesSelector.call(element, selector)
    // fall back to performing a selector:
    var match, parent = element.parentNode, temp = !parent
    if (temp) (parent = tempParent).appendChild(element)
    match = ~zepto.qsa(parent, selector).indexOf(element)
    temp && tempParent.removeChild(element)
    return match
  }

  function type(obj) {
    return obj == null ? String(obj) :
      class2type[toString.call(obj)] || "object"
  }

  function isFunction(value) { return type(value) == "function" }
  function isWindow(obj)     { return obj != null && obj == obj.window }
  function isDocument(obj)   { return obj != null && obj.nodeType == obj.DOCUMENT_NODE }
  function isObject(obj)     { return type(obj) == "object" }
  function isPlainObject(obj) {
    return isObject(obj) && !isWindow(obj) && Object.getPrototypeOf(obj) == Object.prototype
  }
  function likeArray(obj) { return typeof obj.length == 'number' }

  function compact(array) { return filter.call(array, function(item){ return item != null }) }
  function flatten(array) { return array.length > 0 ? $.fn.concat.apply([], array) : array }
  camelize = function(str){ return str.replace(/-+(.)?/g, function(match, chr){ return chr ? chr.toUpperCase() : '' }) }
  function dasherize(str) {
    return str.replace(/::/g, '/')
           .replace(/([A-Z]+)([A-Z][a-z])/g, '$1_$2')
           .replace(/([a-z\d])([A-Z])/g, '$1_$2')
           .replace(/_/g, '-')
           .toLowerCase()
  }
  uniq = function(array){ return filter.call(array, function(item, idx){ return array.indexOf(item) == idx }) }

  function classRE(name) {
    return name in classCache ?
      classCache[name] : (classCache[name] = new RegExp('(^|\\s)' + name + '(\\s|$)'))
  }

  function maybeAddPx(name, value) {
    return (typeof value == "number" && !cssNumber[dasherize(name)]) ? value + "px" : value
  }

  function defaultDisplay(nodeName) {
    var element, display
    if (!elementDisplay[nodeName]) {
      element = document.createElement(nodeName)
      document.body.appendChild(element)
      display = getComputedStyle(element, '').getPropertyValue("display")
      element.parentNode.removeChild(element)
      display == "none" && (display = "block")
      elementDisplay[nodeName] = display
    }
    return elementDisplay[nodeName]
  }

  function children(element) {
    return 'children' in element ?
      slice.call(element.children) :
      $.map(element.childNodes, function(node){ if (node.nodeType == 1) return node })
  }

  // `$.zepto.fragment` takes a html string and an optional tag name
  // to generate DOM nodes nodes from the given html string.
  // The generated DOM nodes are returned as an array.
  // This function can be overriden in plugins for example to make
  // it compatible with browsers that don't support the DOM fully.
  zepto.fragment = function(html, name, properties) {
    var dom, nodes, container

    // A special case optimization for a single tag
    if (singleTagRE.test(html)) dom = $(document.createElement(RegExp.$1))

    if (!dom) {
      if (html.replace) html = html.replace(tagExpanderRE, "<$1></$2>")
      if (name === undefined) name = fragmentRE.test(html) && RegExp.$1
      if (!(name in containers)) name = '*'

      container = containers[name]
      container.innerHTML = '' + html
      dom = $.each(slice.call(container.childNodes), function(){
        container.removeChild(this)
      })
    }

    if (isPlainObject(properties)) {
      nodes = $(dom)
      $.each(properties, function(key, value) {
        if (methodAttributes.indexOf(key) > -1) nodes[key](value)
        else nodes.attr(key, value)
      })
    }

    return dom
  }

  // `$.zepto.Z` swaps out the prototype of the given `dom` array
  // of nodes with `$.fn` and thus supplying all the Zepto functions
  // to the array. Note that `__proto__` is not supported on Internet
  // Explorer. This method can be overriden in plugins.
  zepto.Z = function(dom, selector) {
    dom = dom || []
    dom.__proto__ = $.fn
    dom.selector = selector || ''
    return dom
  }

  // `$.zepto.isZ` should return `true` if the given object is a Zepto
  // collection. This method can be overriden in plugins.
  zepto.isZ = function(object) {
    return object instanceof zepto.Z
  }

  // `$.zepto.init` is Zepto's counterpart to jQuery's `$.fn.init` and
  // takes a CSS selector and an optional context (and handles various
  // special cases).
  // This method can be overriden in plugins.
  zepto.init = function(selector, context) {
    var dom
    // If nothing given, return an empty Zepto collection
    if (!selector) return zepto.Z()
    // Optimize for string selectors
    else if (typeof selector == 'string') {
      selector = selector.trim()
      // If it's a html fragment, create nodes from it
      // Note: In both Chrome 21 and Firefox 15, DOM error 12
      // is thrown if the fragment doesn't begin with <
      if (selector[0] == '<' && fragmentRE.test(selector))
        dom = zepto.fragment(selector, RegExp.$1, context), selector = null
      // If there's a context, create a collection on that context first, and select
      // nodes from there
      else if (context !== undefined) return $(context).find(selector)
      // If it's a CSS selector, use it to select nodes.
      else dom = zepto.qsa(document, selector)
    }
    // If a function is given, call it when the DOM is ready
    else if (isFunction(selector)) return $(document).ready(selector)
    // If a Zepto collection is given, just return it
    else if (zepto.isZ(selector)) return selector
    else {
      // normalize array if an array of nodes is given
      if (isArray(selector)) dom = compact(selector)
      // Wrap DOM nodes.
      else if (isObject(selector))
        dom = [selector], selector = null
      // If it's a html fragment, create nodes from it
      else if (fragmentRE.test(selector))
        dom = zepto.fragment(selector.trim(), RegExp.$1, context), selector = null
      // If there's a context, create a collection on that context first, and select
      // nodes from there
      else if (context !== undefined) return $(context).find(selector)
      // And last but no least, if it's a CSS selector, use it to select nodes.
      else dom = zepto.qsa(document, selector)
    }
    // create a new Zepto collection from the nodes found
    return zepto.Z(dom, selector)
  }

  // `$` will be the base `Zepto` object. When calling this
  // function just call `$.zepto.init, which makes the implementation
  // details of selecting nodes and creating Zepto collections
  // patchable in plugins.
  $ = function(selector, context){
    return zepto.init(selector, context)
  }

  function extend(target, source, deep) {
    for (key in source)
      if (deep && (isPlainObject(source[key]) || isArray(source[key]))) {
        if (isPlainObject(source[key]) && !isPlainObject(target[key]))
          target[key] = {}
        if (isArray(source[key]) && !isArray(target[key]))
          target[key] = []
        extend(target[key], source[key], deep)
      }
      else if (source[key] !== undefined) target[key] = source[key]
  }

  // Copy all but undefined properties from one or more
  // objects to the `target` object.
  $.extend = function(target){
    var deep, args = slice.call(arguments, 1)
    if (typeof target == 'boolean') {
      deep = target
      target = args.shift()
    }
    args.forEach(function(arg){ extend(target, arg, deep) })
    return target
  }

  // `$.zepto.qsa` is Zepto's CSS selector implementation which
  // uses `document.querySelectorAll` and optimizes for some special cases, like `#id`.
  // This method can be overriden in plugins.
  zepto.qsa = function(element, selector){
    var found,
        maybeID = selector[0] == '#',
        maybeClass = !maybeID && selector[0] == '.',
        nameOnly = maybeID || maybeClass ? selector.slice(1) : selector, // Ensure that a 1 char tag name still gets checked
        isSimple = simpleSelectorRE.test(nameOnly)
    return (isDocument(element) && isSimple && maybeID) ?
      ( (found = element.getElementById(nameOnly)) ? [found] : [] ) :
      (element.nodeType !== 1 && element.nodeType !== 9) ? [] :
      slice.call(
        isSimple && !maybeID ?
          maybeClass ? element.getElementsByClassName(nameOnly) : // If it's simple, it could be a class
          element.getElementsByTagName(selector) : // Or a tag
          element.querySelectorAll(selector) // Or it's not simple, and we need to query all
      )
  }

  function filtered(nodes, selector) {
    return selector == null ? $(nodes) : $(nodes).filter(selector)
  }

  $.contains = document.documentElement.contains ?
    function(parent, node) {
      return parent !== node && parent.contains(node)
    } :
    function(parent, node) {
      while (node && (node = node.parentNode))
        if (node === parent) return true
      return false
    }

  function funcArg(context, arg, idx, payload) {
    return isFunction(arg) ? arg.call(context, idx, payload) : arg
  }

  function setAttribute(node, name, value) {
    value == null ? node.removeAttribute(name) : node.setAttribute(name, value)
  }

  // access className property while respecting SVGAnimatedString
  function className(node, value){
    var klass = node.className || '',
        svg   = klass && klass.baseVal !== undefined

    if (value === undefined) return svg ? klass.baseVal : klass
    svg ? (klass.baseVal = value) : (node.className = value)
  }

  // "true"  => true
  // "false" => false
  // "null"  => null
  // "42"    => 42
  // "42.5"  => 42.5
  // "08"    => "08"
  // JSON    => parse if valid
  // String  => self
  function deserializeValue(value) {
    try {
      return value ?
        value == "true" ||
        ( value == "false" ? false :
          value == "null" ? null :
          +value + "" == value ? +value :
          /^[\[\{]/.test(value) ? $.parseJSON(value) :
          value )
        : value
    } catch(e) {
      return value
    }
  }

  $.type = type
  $.isFunction = isFunction
  $.isWindow = isWindow
  $.isArray = isArray
  $.isPlainObject = isPlainObject

  $.isEmptyObject = function(obj) {
    var name
    for (name in obj) return false
    return true
  }

  $.inArray = function(elem, array, i){
    return emptyArray.indexOf.call(array, elem, i)
  }

  $.camelCase = camelize
  $.trim = function(str) {
    return str == null ? "" : String.prototype.trim.call(str)
  }

  // plugin compatibility
  $.uuid = 0
  $.support = { }
  $.expr = { }

  $.map = function(elements, callback){
    var value, values = [], i, key
    if (likeArray(elements))
      for (i = 0; i < elements.length; i++) {
        value = callback(elements[i], i)
        if (value != null) values.push(value)
      }
    else
      for (key in elements) {
        value = callback(elements[key], key)
        if (value != null) values.push(value)
      }
    return flatten(values)
  }

  $.each = function(elements, callback){
    var i, key
    if (likeArray(elements)) {
      for (i = 0; i < elements.length; i++)
        if (callback.call(elements[i], i, elements[i]) === false) return elements
    } else {
      for (key in elements)
        if (callback.call(elements[key], key, elements[key]) === false) return elements
    }

    return elements
  }

  $.grep = function(elements, callback){
    return filter.call(elements, callback)
  }

  if (window.JSON) $.parseJSON = JSON.parse

  // Populate the class2type map
  $.each("Boolean Number String Function Array Date RegExp Object Error".split(" "), function(i, name) {
    class2type[ "[object " + name + "]" ] = name.toLowerCase()
  })

  // Define methods that will be available on all
  // Zepto collections
  $.fn = {
    // Because a collection acts like an array
    // copy over these useful array functions.
    forEach: emptyArray.forEach,
    reduce: emptyArray.reduce,
    push: emptyArray.push,
    sort: emptyArray.sort,
    indexOf: emptyArray.indexOf,
    concat: emptyArray.concat,

    // `map` and `slice` in the jQuery API work differently
    // from their array counterparts
    map: function(fn){
      return $($.map(this, function(el, i){ return fn.call(el, i, el) }))
    },
    slice: function(){
      return $(slice.apply(this, arguments))
    },

    ready: function(callback){
      // need to check if document.body exists for IE as that browser reports
      // document ready when it hasn't yet created the body element
      if (readyRE.test(document.readyState) && document.body) callback($)
      else document.addEventListener('DOMContentLoaded', function(){ callback($) }, false)
      return this
    },
    get: function(idx){
      return idx === undefined ? slice.call(this) : this[idx >= 0 ? idx : idx + this.length]
    },
    toArray: function(){ return this.get() },
    size: function(){
      return this.length
    },
    remove: function(){
      return this.each(function(){
        if (this.parentNode != null)
          this.parentNode.removeChild(this)
      })
    },
    each: function(callback){
      emptyArray.every.call(this, function(el, idx){
        return callback.call(el, idx, el) !== false
      })
      return this
    },
    filter: function(selector){
      if (isFunction(selector)) return this.not(this.not(selector))
      return $(filter.call(this, function(element){
        return zepto.matches(element, selector)
      }))
    },
    add: function(selector,context){
      return $(uniq(this.concat($(selector,context))))
    },
    is: function(selector){
      return this.length > 0 && zepto.matches(this[0], selector)
    },
    not: function(selector){
      var nodes=[]
      if (isFunction(selector) && selector.call !== undefined)
        this.each(function(idx){
          if (!selector.call(this,idx)) nodes.push(this)
        })
      else {
        var excludes = typeof selector == 'string' ? this.filter(selector) :
          (likeArray(selector) && isFunction(selector.item)) ? slice.call(selector) : $(selector)
        this.forEach(function(el){
          if (excludes.indexOf(el) < 0) nodes.push(el)
        })
      }
      return $(nodes)
    },
    has: function(selector){
      return this.filter(function(){
        return isObject(selector) ?
          $.contains(this, selector) :
          $(this).find(selector).size()
      })
    },
    eq: function(idx){
      return idx === -1 ? this.slice(idx) : this.slice(idx, + idx + 1)
    },
    first: function(){
      var el = this[0]
      return el && !isObject(el) ? el : $(el)
    },
    last: function(){
      var el = this[this.length - 1]
      return el && !isObject(el) ? el : $(el)
    },
    find: function(selector){
      var result, $this = this
      if (!selector) result = $()
      else if (typeof selector == 'object')
        result = $(selector).filter(function(){
          var node = this
          return emptyArray.some.call($this, function(parent){
            return $.contains(parent, node)
          })
        })
      else if (this.length == 1) result = $(zepto.qsa(this[0], selector))
      else result = this.map(function(){ return zepto.qsa(this, selector) })
      return result
    },
    closest: function(selector, context){
      var node = this[0], collection = false
      if (typeof selector == 'object') collection = $(selector)
      while (node && !(collection ? collection.indexOf(node) >= 0 : zepto.matches(node, selector)))
        node = node !== context && !isDocument(node) && node.parentNode
      return $(node)
    },
    parents: function(selector){
      var ancestors = [], nodes = this
      while (nodes.length > 0)
        nodes = $.map(nodes, function(node){
          if ((node = node.parentNode) && !isDocument(node) && ancestors.indexOf(node) < 0) {
            ancestors.push(node)
            return node
          }
        })
      return filtered(ancestors, selector)
    },
    parent: function(selector){
      return filtered(uniq(this.pluck('parentNode')), selector)
    },
    children: function(selector){
      return filtered(this.map(function(){ return children(this) }), selector)
    },
    contents: function() {
      return this.map(function() { return slice.call(this.childNodes) })
    },
    siblings: function(selector){
      return filtered(this.map(function(i, el){
        return filter.call(children(el.parentNode), function(child){ return child!==el })
      }), selector)
    },
    empty: function(){
      return this.each(function(){ this.innerHTML = '' })
    },
    // `pluck` is borrowed from Prototype.js
    pluck: function(property){
      return $.map(this, function(el){ return el[property] })
    },
    show: function(){
      return this.each(function(){
        this.style.display == "none" && (this.style.display = '')
        if (getComputedStyle(this, '').getPropertyValue("display") == "none")
          this.style.display = defaultDisplay(this.nodeName)
      })
    },
    replaceWith: function(newContent){
      return this.before(newContent).remove()
    },
    wrap: function(structure){
      var func = isFunction(structure)
      if (this[0] && !func)
        var dom   = $(structure).get(0),
            clone = dom.parentNode || this.length > 1

      return this.each(function(index){
        $(this).wrapAll(
          func ? structure.call(this, index) :
            clone ? dom.cloneNode(true) : dom
        )
      })
    },
    wrapAll: function(structure){
      if (this[0]) {
        $(this[0]).before(structure = $(structure))
        var children
        // drill down to the inmost element
        while ((children = structure.children()).length) structure = children.first()
        $(structure).append(this)
      }
      return this
    },
    wrapInner: function(structure){
      var func = isFunction(structure)
      return this.each(function(index){
        var self = $(this), contents = self.contents(),
            dom  = func ? structure.call(this, index) : structure
        contents.length ? contents.wrapAll(dom) : self.append(dom)
      })
    },
    unwrap: function(){
      this.parent().each(function(){
        $(this).replaceWith($(this).children())
      })
      return this
    },
    clone: function(){
      return this.map(function(){ return this.cloneNode(true) })
    },
    hide: function(){
      return this.css("display", "none")
    },
    toggle: function(setting){
      return this.each(function(){
        var el = $(this)
        ;(setting === undefined ? el.css("display") == "none" : setting) ? el.show() : el.hide()
      })
    },
    prev: function(selector){ return $(this.pluck('previousElementSibling')).filter(selector || '*') },
    next: function(selector){ return $(this.pluck('nextElementSibling')).filter(selector || '*') },
    html: function(html){
      return 0 in arguments ?
        this.each(function(idx){
          var originHtml = this.innerHTML
          $(this).empty().append( funcArg(this, html, idx, originHtml) )
        }) :
        (0 in this ? this[0].innerHTML : null)
    },
    text: function(text){
      return 0 in arguments ?
        this.each(function(idx){
          var newText = funcArg(this, text, idx, this.textContent)
          this.textContent = newText == null ? '' : ''+newText
        }) :
        (0 in this ? this[0].textContent : null)
    },
    attr: function(name, value){
      var result
      return (typeof name == 'string' && !(1 in arguments)) ?
        (!this.length || this[0].nodeType !== 1 ? undefined :
          (!(result = this[0].getAttribute(name)) && name in this[0]) ? this[0][name] : result
        ) :
        this.each(function(idx){
          if (this.nodeType !== 1) return
          if (isObject(name)) for (key in name) setAttribute(this, key, name[key])
          else setAttribute(this, name, funcArg(this, value, idx, this.getAttribute(name)))
        })
    },
    removeAttr: function(name){
      return this.each(function(){ this.nodeType === 1 && name.split(' ').forEach(function(attribute){
        setAttribute(this, attribute)
      }, this)})
    },
    prop: function(name, value){
      name = propMap[name] || name
      return (1 in arguments) ?
        this.each(function(idx){
          this[name] = funcArg(this, value, idx, this[name])
        }) :
        (this[0] && this[0][name])
    },
    data: function(name, value){
      var attrName = 'data-' + name.replace(capitalRE, '-$1').toLowerCase()

      var data = (1 in arguments) ?
        this.attr(attrName, value) :
        this.attr(attrName)

      return data !== null ? deserializeValue(data) : undefined
    },
    val: function(value){
      return 0 in arguments ?
        this.each(function(idx){
          this.value = funcArg(this, value, idx, this.value)
        }) :
        (this[0] && (this[0].multiple ?
           $(this[0]).find('option').filter(function(){ return this.selected }).pluck('value') :
           this[0].value)
        )
    },
    offset: function(coordinates){
      if (coordinates) return this.each(function(index){
        var $this = $(this),
            coords = funcArg(this, coordinates, index, $this.offset()),
            parentOffset = $this.offsetParent().offset(),
            props = {
              top:  coords.top  - parentOffset.top,
              left: coords.left - parentOffset.left
            }

        if ($this.css('position') == 'static') props['position'] = 'relative'
        $this.css(props)
      })
      if (!this.length) return null
      var obj = this[0].getBoundingClientRect()
      return {
        left: obj.left + window.pageXOffset,
        top: obj.top + window.pageYOffset,
        width: Math.round(obj.width),
        height: Math.round(obj.height)
      }
    },
    css: function(property, value){
      if (arguments.length < 2) {
        var computedStyle, element = this[0]
        if(!element) return
        computedStyle = getComputedStyle(element, '')
        if (typeof property == 'string')
          return element.style[camelize(property)] || computedStyle.getPropertyValue(property)
        else if (isArray(property)) {
          var props = {}
          $.each(property, function(_, prop){
            props[prop] = (element.style[camelize(prop)] || computedStyle.getPropertyValue(prop))
          })
          return props
        }
      }

      var css = ''
      if (type(property) == 'string') {
        if (!value && value !== 0)
          this.each(function(){ this.style.removeProperty(dasherize(property)) })
        else
          css = dasherize(property) + ":" + maybeAddPx(property, value)
      } else {
        for (key in property)
          if (!property[key] && property[key] !== 0)
            this.each(function(){ this.style.removeProperty(dasherize(key)) })
          else
            css += dasherize(key) + ':' + maybeAddPx(key, property[key]) + ';'
      }

      return this.each(function(){ this.style.cssText += ';' + css })
    },
    index: function(element){
      return element ? this.indexOf($(element)[0]) : this.parent().children().indexOf(this[0])
    },
    hasClass: function(name){
      if (!name) return false
      return emptyArray.some.call(this, function(el){
        return this.test(className(el))
      }, classRE(name))
    },
    addClass: function(name){
      if (!name) return this
      return this.each(function(idx){
        if (!('className' in this)) return
        classList = []
        var cls = className(this), newName = funcArg(this, name, idx, cls)
        newName.split(/\s+/g).forEach(function(klass){
          if (!$(this).hasClass(klass)) classList.push(klass)
        }, this)
        classList.length && className(this, cls + (cls ? " " : "") + classList.join(" "))
      })
    },
    removeClass: function(name){
      return this.each(function(idx){
        if (!('className' in this)) return
        if (name === undefined) return className(this, '')
        classList = className(this)
        funcArg(this, name, idx, classList).split(/\s+/g).forEach(function(klass){
          classList = classList.replace(classRE(klass), " ")
        })
        className(this, classList.trim())
      })
    },
    toggleClass: function(name, when){
      if (!name) return this
      return this.each(function(idx){
        var $this = $(this), names = funcArg(this, name, idx, className(this))
        names.split(/\s+/g).forEach(function(klass){
          (when === undefined ? !$this.hasClass(klass) : when) ?
            $this.addClass(klass) : $this.removeClass(klass)
        })
      })
    },
    scrollTop: function(value){
      if (!this.length) return
      var hasScrollTop = 'scrollTop' in this[0]
      if (value === undefined) return hasScrollTop ? this[0].scrollTop : this[0].pageYOffset
      return this.each(hasScrollTop ?
        function(){ this.scrollTop = value } :
        function(){ this.scrollTo(this.scrollX, value) })
    },
    scrollLeft: function(value){
      if (!this.length) return
      var hasScrollLeft = 'scrollLeft' in this[0]
      if (value === undefined) return hasScrollLeft ? this[0].scrollLeft : this[0].pageXOffset
      return this.each(hasScrollLeft ?
        function(){ this.scrollLeft = value } :
        function(){ this.scrollTo(value, this.scrollY) })
    },
    position: function() {
      if (!this.length) return

      var elem = this[0],
        // Get *real* offsetParent
        offsetParent = this.offsetParent(),
        // Get correct offsets
        offset       = this.offset(),
        parentOffset = rootNodeRE.test(offsetParent[0].nodeName) ? { top: 0, left: 0 } : offsetParent.offset()

      // Subtract element margins
      // note: when an element has margin: auto the offsetLeft and marginLeft
      // are the same in Safari causing offset.left to incorrectly be 0
      offset.top  -= parseFloat( $(elem).css('margin-top') ) || 0
      offset.left -= parseFloat( $(elem).css('margin-left') ) || 0

      // Add offsetParent borders
      parentOffset.top  += parseFloat( $(offsetParent[0]).css('border-top-width') ) || 0
      parentOffset.left += parseFloat( $(offsetParent[0]).css('border-left-width') ) || 0

      // Subtract the two offsets
      return {
        top:  offset.top  - parentOffset.top,
        left: offset.left - parentOffset.left
      }
    },
    offsetParent: function() {
      return this.map(function(){
        var parent = this.offsetParent || document.body
        while (parent && !rootNodeRE.test(parent.nodeName) && $(parent).css("position") == "static")
          parent = parent.offsetParent
        return parent
      })
    }
  }

  // for now
  $.fn.detach = $.fn.remove

  // Generate the `width` and `height` functions
  ;['width', 'height'].forEach(function(dimension){
    var dimensionProperty =
      dimension.replace(/./, function(m){ return m[0].toUpperCase() })

    $.fn[dimension] = function(value){
      var offset, el = this[0]
      if (value === undefined) return isWindow(el) ? el['inner' + dimensionProperty] :
        isDocument(el) ? el.documentElement['scroll' + dimensionProperty] :
        (offset = this.offset()) && offset[dimension]
      else return this.each(function(idx){
        el = $(this)
        el.css(dimension, funcArg(this, value, idx, el[dimension]()))
      })
    }
  })

  function traverseNode(node, fun) {
    fun(node)
    for (var i = 0, len = node.childNodes.length; i < len; i++)
      traverseNode(node.childNodes[i], fun)
  }

  // Generate the `after`, `prepend`, `before`, `append`,
  // `insertAfter`, `insertBefore`, `appendTo`, and `prependTo` methods.
  adjacencyOperators.forEach(function(operator, operatorIndex) {
    var inside = operatorIndex % 2 //=> prepend, append

    $.fn[operator] = function(){
      // arguments can be nodes, arrays of nodes, Zepto objects and HTML strings
      var argType, nodes = $.map(arguments, function(arg) {
            argType = type(arg)
            return argType == "object" || argType == "array" || arg == null ?
              arg : zepto.fragment(arg)
          }),
          parent, copyByClone = this.length > 1
      if (nodes.length < 1) return this

      return this.each(function(_, target){
        parent = inside ? target : target.parentNode

        // convert all methods to a "before" operation
        target = operatorIndex == 0 ? target.nextSibling :
                 operatorIndex == 1 ? target.firstChild :
                 operatorIndex == 2 ? target :
                 null

        var parentInDocument = $.contains(document.documentElement, parent)

        nodes.forEach(function(node){
          if (copyByClone) node = node.cloneNode(true)
          else if (!parent) return $(node).remove()

          parent.insertBefore(node, target)
          if (parentInDocument) traverseNode(node, function(el){
            if (el.nodeName != null && el.nodeName.toUpperCase() === 'SCRIPT' &&
               (!el.type || el.type === 'text/javascript') && !el.src)
              window['eval'].call(window, el.innerHTML)
          })
        })
      })
    }

    // after    => insertAfter
    // prepend  => prependTo
    // before   => insertBefore
    // append   => appendTo
    $.fn[inside ? operator+'To' : 'insert'+(operatorIndex ? 'Before' : 'After')] = function(html){
      $(html)[operator](this)
      return this
    }
  })

  zepto.Z.prototype = $.fn

  // Export internal API functions in the `$.zepto` namespace
  zepto.uniq = uniq
  zepto.deserializeValue = deserializeValue
  $.zepto = zepto

  return $
})()

window.Zepto = Zepto
window.$ === undefined && (window.$ = Zepto)

;(function($){
  var _zid = 1, undefined,
      slice = Array.prototype.slice,
      isFunction = $.isFunction,
      isString = function(obj){ return typeof obj == 'string' },
      handlers = {},
      specialEvents={},
      focusinSupported = 'onfocusin' in window,
      focus = { focus: 'focusin', blur: 'focusout' },
      hover = { mouseenter: 'mouseover', mouseleave: 'mouseout' }

  specialEvents.click = specialEvents.mousedown = specialEvents.mouseup = specialEvents.mousemove = 'MouseEvents'

  function zid(element) {
    return element._zid || (element._zid = _zid++)
  }
  function findHandlers(element, event, fn, selector) {
    event = parse(event)
    if (event.ns) var matcher = matcherFor(event.ns)
    return (handlers[zid(element)] || []).filter(function(handler) {
      return handler
        && (!event.e  || handler.e == event.e)
        && (!event.ns || matcher.test(handler.ns))
        && (!fn       || zid(handler.fn) === zid(fn))
        && (!selector || handler.sel == selector)
    })
  }
  function parse(event) {
    var parts = ('' + event).split('.')
    return {e: parts[0], ns: parts.slice(1).sort().join(' ')}
  }
  function matcherFor(ns) {
    return new RegExp('(?:^| )' + ns.replace(' ', ' .* ?') + '(?: |$)')
  }

  function eventCapture(handler, captureSetting) {
    return handler.del &&
      (!focusinSupported && (handler.e in focus)) ||
      !!captureSetting
  }

  function realEvent(type) {
    return hover[type] || (focusinSupported && focus[type]) || type
  }

  function add(element, events, fn, data, selector, delegator, capture){
    var id = zid(element), set = (handlers[id] || (handlers[id] = []))
    events.split(/\s/).forEach(function(event){
      if (event == 'ready') return $(document).ready(fn)
      var handler   = parse(event)
      handler.fn    = fn
      handler.sel   = selector
      // emulate mouseenter, mouseleave
      if (handler.e in hover) fn = function(e){
        var related = e.relatedTarget
        if (!related || (related !== this && !$.contains(this, related)))
          return handler.fn.apply(this, arguments)
      }
      handler.del   = delegator
      var callback  = delegator || fn
      handler.proxy = function(e){
        e = compatible(e)
        if (e.isImmediatePropagationStopped()) return
        e.data = data
        var result = callback.apply(element, e._args == undefined ? [e] : [e].concat(e._args))
        if (result === false) e.preventDefault(), e.stopPropagation()
        return result
      }
      handler.i = set.length
      set.push(handler)
      if ('addEventListener' in element)
        element.addEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
    })
  }
  function remove(element, events, fn, selector, capture){
    var id = zid(element)
    ;(events || '').split(/\s/).forEach(function(event){
      findHandlers(element, event, fn, selector).forEach(function(handler){
        delete handlers[id][handler.i]
      if ('removeEventListener' in element)
        element.removeEventListener(realEvent(handler.e), handler.proxy, eventCapture(handler, capture))
      })
    })
  }

  $.event = { add: add, remove: remove }

  $.proxy = function(fn, context) {
    var args = (2 in arguments) && slice.call(arguments, 2)
    if (isFunction(fn)) {
      var proxyFn = function(){ return fn.apply(context, args ? args.concat(slice.call(arguments)) : arguments) }
      proxyFn._zid = zid(fn)
      return proxyFn
    } else if (isString(context)) {
      if (args) {
        args.unshift(fn[context], fn)
        return $.proxy.apply(null, args)
      } else {
        return $.proxy(fn[context], fn)
      }
    } else {
      throw new TypeError("expected function")
    }
  }

  $.fn.bind = function(event, data, callback){
    return this.on(event, data, callback)
  }
  $.fn.unbind = function(event, callback){
    return this.off(event, callback)
  }
  $.fn.one = function(event, selector, data, callback){
    return this.on(event, selector, data, callback, 1)
  }

  var returnTrue = function(){return true},
      returnFalse = function(){return false},
      ignoreProperties = /^([A-Z]|returnValue$|layer[XY]$)/,
      eventMethods = {
        preventDefault: 'isDefaultPrevented',
        stopImmediatePropagation: 'isImmediatePropagationStopped',
        stopPropagation: 'isPropagationStopped'
      }

  function compatible(event, source) {
    if (source || !event.isDefaultPrevented) {
      source || (source = event)

      $.each(eventMethods, function(name, predicate) {
        var sourceMethod = source[name]
        event[name] = function(){
          this[predicate] = returnTrue
          return sourceMethod && sourceMethod.apply(source, arguments)
        }
        event[predicate] = returnFalse
      })

      if (source.defaultPrevented !== undefined ? source.defaultPrevented :
          'returnValue' in source ? source.returnValue === false :
          source.getPreventDefault && source.getPreventDefault())
        event.isDefaultPrevented = returnTrue
    }
    return event
  }

  function createProxy(event) {
    var key, proxy = { originalEvent: event }
    for (key in event)
      if (!ignoreProperties.test(key) && event[key] !== undefined) proxy[key] = event[key]

    return compatible(proxy, event)
  }

  $.fn.delegate = function(selector, event, callback){
    return this.on(event, selector, callback)
  }
  $.fn.undelegate = function(selector, event, callback){
    return this.off(event, selector, callback)
  }

  $.fn.live = function(event, callback){
    $(document.body).delegate(this.selector, event, callback)
    return this
  }
  $.fn.die = function(event, callback){
    $(document.body).undelegate(this.selector, event, callback)
    return this
  }

  $.fn.on = function(event, selector, data, callback, one){
    var autoRemove, delegator, $this = this
    if (event && !isString(event)) {
      $.each(event, function(type, fn){
        $this.on(type, selector, data, fn, one)
      })
      return $this
    }

    if (!isString(selector) && !isFunction(callback) && callback !== false)
      callback = data, data = selector, selector = undefined
    if (isFunction(data) || data === false)
      callback = data, data = undefined

    if (callback === false) callback = returnFalse

    return $this.each(function(_, element){
      if (one) autoRemove = function(e){
        remove(element, e.type, callback)
        return callback.apply(this, arguments)
      }

      if (selector) delegator = function(e){
        var evt, match = $(e.target).closest(selector, element).get(0)
        if (match && match !== element) {
          evt = $.extend(createProxy(e), {currentTarget: match, liveFired: element})
          return (autoRemove || callback).apply(match, [evt].concat(slice.call(arguments, 1)))
        }
      }

      add(element, event, callback, data, selector, delegator || autoRemove)
    })
  }
  $.fn.off = function(event, selector, callback){
    var $this = this
    if (event && !isString(event)) {
      $.each(event, function(type, fn){
        $this.off(type, selector, fn)
      })
      return $this
    }

    if (!isString(selector) && !isFunction(callback) && callback !== false)
      callback = selector, selector = undefined

    if (callback === false) callback = returnFalse

    return $this.each(function(){
      remove(this, event, callback, selector)
    })
  }

  $.fn.trigger = function(event, args){
    event = (isString(event) || $.isPlainObject(event)) ? $.Event(event) : compatible(event)
    event._args = args
    return this.each(function(){
      // handle focus(), blur() by calling them directly
      if (event.type in focus && typeof this[event.type] == "function") this[event.type]()
      // items in the collection might not be DOM elements
      else if ('dispatchEvent' in this) this.dispatchEvent(event)
      else $(this).triggerHandler(event, args)
    })
  }

  // triggers event handlers on current element just as if an event occurred,
  // doesn't trigger an actual event, doesn't bubble
  $.fn.triggerHandler = function(event, args){
    var e, result
    this.each(function(i, element){
      e = createProxy(isString(event) ? $.Event(event) : event)
      e._args = args
      e.target = element
      $.each(findHandlers(element, event.type || event), function(i, handler){
        result = handler.proxy(e)
        if (e.isImmediatePropagationStopped()) return false
      })
    })
    return result
  }

  // shortcut methods for `.bind(event, fn)` for each event type
  ;('focusin focusout focus blur load resize scroll unload click dblclick '+
  'mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave '+
  'change select keydown keypress keyup error').split(' ').forEach(function(event) {
    $.fn[event] = function(callback) {
      return (0 in arguments) ?
        this.bind(event, callback) :
        this.trigger(event)
    }
  })

  $.Event = function(type, props) {
    if (!isString(type)) props = type, type = props.type
    var event = document.createEvent(specialEvents[type] || 'Events'), bubbles = true
    if (props) for (var name in props) (name == 'bubbles') ? (bubbles = !!props[name]) : (event[name] = props[name])
    event.initEvent(type, bubbles, true)
    return compatible(event)
  }

})(Zepto)

;(function($){
  var jsonpID = 0,
      document = window.document,
      key,
      name,
      rscript = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
      scriptTypeRE = /^(?:text|application)\/javascript/i,
      xmlTypeRE = /^(?:text|application)\/xml/i,
      jsonType = 'application/json',
      htmlType = 'text/html',
      blankRE = /^\s*$/,
      originAnchor = document.createElement('a')

  originAnchor.href = window.location.href

  // trigger a custom event and return false if it was cancelled
  function triggerAndReturn(context, eventName, data) {
    var event = $.Event(eventName)
    $(context).trigger(event, data)
    return !event.isDefaultPrevented()
  }

  // trigger an Ajax "global" event
  function triggerGlobal(settings, context, eventName, data) {
    if (settings.global) return triggerAndReturn(context || document, eventName, data)
  }

  // Number of active Ajax requests
  $.active = 0

  function ajaxStart(settings) {
    if (settings.global && $.active++ === 0) triggerGlobal(settings, null, 'ajaxStart')
  }
  function ajaxStop(settings) {
    if (settings.global && !(--$.active)) triggerGlobal(settings, null, 'ajaxStop')
  }

  // triggers an extra global event "ajaxBeforeSend" that's like "ajaxSend" but cancelable
  function ajaxBeforeSend(xhr, settings) {
    var context = settings.context
    if (settings.beforeSend.call(context, xhr, settings) === false ||
        triggerGlobal(settings, context, 'ajaxBeforeSend', [xhr, settings]) === false)
      return false

    triggerGlobal(settings, context, 'ajaxSend', [xhr, settings])
  }
  function ajaxSuccess(data, xhr, settings, deferred) {
    var context = settings.context, status = 'success'
    settings.success.call(context, data, status, xhr)
    if (deferred) deferred.resolveWith(context, [data, status, xhr])
    triggerGlobal(settings, context, 'ajaxSuccess', [xhr, settings, data])
    ajaxComplete(status, xhr, settings)
  }
  // type: "timeout", "error", "abort", "parsererror"
  function ajaxError(error, type, xhr, settings, deferred) {
    var context = settings.context
    settings.error.call(context, xhr, type, error)
    if (deferred) deferred.rejectWith(context, [xhr, type, error])
    triggerGlobal(settings, context, 'ajaxError', [xhr, settings, error || type])
    ajaxComplete(type, xhr, settings)
  }
  // status: "success", "notmodified", "error", "timeout", "abort", "parsererror"
  function ajaxComplete(status, xhr, settings) {
    var context = settings.context
    settings.complete.call(context, xhr, status)
    triggerGlobal(settings, context, 'ajaxComplete', [xhr, settings])
    ajaxStop(settings)
  }

  // Empty function, used as default callback
  function empty() {}

  $.ajaxJSONP = function(options, deferred){
    if (!('type' in options)) return $.ajax(options)

    var _callbackName = options.jsonpCallback,
      callbackName = ($.isFunction(_callbackName) ?
        _callbackName() : _callbackName) || ('jsonp' + (++jsonpID)),
      script = document.createElement('script'),
      originalCallback = window[callbackName],
      responseData,
      abort = function(errorType) {
        $(script).triggerHandler('error', errorType || 'abort')
      },
      xhr = { abort: abort }, abortTimeout

    if (deferred) deferred.promise(xhr)

    $(script).on('load error', function(e, errorType){
      clearTimeout(abortTimeout)
      $(script).off().remove()

      if (e.type == 'error' || !responseData) {
        ajaxError(null, errorType || 'error', xhr, options, deferred)
      } else {
        ajaxSuccess(responseData[0], xhr, options, deferred)
      }

      window[callbackName] = originalCallback
      if (responseData && $.isFunction(originalCallback))
        originalCallback(responseData[0])

      originalCallback = responseData = undefined
    })

    if (ajaxBeforeSend(xhr, options) === false) {
      abort('abort')
      return xhr
    }

    window[callbackName] = function(){
      responseData = arguments
    }

    script.src = options.url.replace(/\?(.+)=\?/, '?$1=' + callbackName)
    document.head.appendChild(script)

    if (options.timeout > 0) abortTimeout = setTimeout(function(){
      abort('timeout')
    }, options.timeout)

    return xhr
  }

  $.ajaxSettings = {
    // Default type of request
    type: 'GET',
    // Callback that is executed before request
    beforeSend: empty,
    // Callback that is executed if the request succeeds
    success: empty,
    // Callback that is executed the the server drops error
    error: empty,
    // Callback that is executed on request complete (both: error and success)
    complete: empty,
    // The context for the callbacks
    context: null,
    // Whether to trigger "global" Ajax events
    global: true,
    // Transport
    xhr: function () {
      return new window.XMLHttpRequest()
    },
    // MIME types mapping
    // IIS returns Javascript as "application/x-javascript"
    accepts: {
      script: 'text/javascript, application/javascript, application/x-javascript',
      json:   jsonType,
      xml:    'application/xml, text/xml',
      html:   htmlType,
      text:   'text/plain'
    },
    // Whether the request is to another domain
    crossDomain: false,
    // Default timeout
    timeout: 0,
    // Whether data should be serialized to string
    processData: true,
    // Whether the browser should be allowed to cache GET responses
    cache: true
  }

  function mimeToDataType(mime) {
    if (mime) mime = mime.split(';', 2)[0]
    return mime && ( mime == htmlType ? 'html' :
      mime == jsonType ? 'json' :
      scriptTypeRE.test(mime) ? 'script' :
      xmlTypeRE.test(mime) && 'xml' ) || 'text'
  }

  function appendQuery(url, query) {
    if (query == '') return url
    return (url + '&' + query).replace(/[&?]{1,2}/, '?')
  }

  // serialize payload and append it to the URL for GET requests
  function serializeData(options) {
    if (options.processData && options.data && $.type(options.data) != "string")
      options.data = $.param(options.data, options.traditional)
    if (options.data && (!options.type || options.type.toUpperCase() == 'GET'))
      options.url = appendQuery(options.url, options.data), options.data = undefined
  }

  $.ajax = function(options){
    var settings = $.extend({}, options || {}),
        deferred = $.Deferred && $.Deferred(),
        urlAnchor
    for (key in $.ajaxSettings) if (settings[key] === undefined) settings[key] = $.ajaxSettings[key]

    ajaxStart(settings)

    if (!settings.crossDomain) {
      urlAnchor = document.createElement('a')
      urlAnchor.href = settings.url
      urlAnchor.href = urlAnchor.href
      settings.crossDomain = (originAnchor.protocol + '//' + originAnchor.host) !== (urlAnchor.protocol + '//' + urlAnchor.host)
    }

    if (!settings.url) settings.url = window.location.toString()
    serializeData(settings)

    var dataType = settings.dataType, hasPlaceholder = /\?.+=\?/.test(settings.url)
    if (hasPlaceholder) dataType = 'jsonp'

    if (settings.cache === false || (
         (!options || options.cache !== true) &&
         ('script' == dataType || 'jsonp' == dataType)
        ))
      settings.url = appendQuery(settings.url, '_=' + Date.now())

    if ('jsonp' == dataType) {
      if (!hasPlaceholder)
        settings.url = appendQuery(settings.url,
          settings.jsonp ? (settings.jsonp + '=?') : settings.jsonp === false ? '' : 'callback=?')
      return $.ajaxJSONP(settings, deferred)
    }

    var mime = settings.accepts[dataType],
        headers = { },
        setHeader = function(name, value) { headers[name.toLowerCase()] = [name, value] },
        protocol = /^([\w-]+:)\/\//.test(settings.url) ? RegExp.$1 : window.location.protocol,
        xhr = settings.xhr(),
        nativeSetHeader = xhr.setRequestHeader,
        abortTimeout

    if (deferred) deferred.promise(xhr)

    if (!settings.crossDomain) setHeader('X-Requested-With', 'XMLHttpRequest')
    setHeader('Accept', mime || '*/*')
    if (mime = settings.mimeType || mime) {
      if (mime.indexOf(',') > -1) mime = mime.split(',', 2)[0]
      xhr.overrideMimeType && xhr.overrideMimeType(mime)
    }
    if (settings.contentType || (settings.contentType !== false && settings.data && settings.type.toUpperCase() != 'GET'))
      setHeader('Content-Type', settings.contentType || 'application/x-www-form-urlencoded')

    if (settings.headers) for (name in settings.headers) setHeader(name, settings.headers[name])
    xhr.setRequestHeader = setHeader

    xhr.onreadystatechange = function(){
      if (xhr.readyState == 4) {
        xhr.onreadystatechange = empty
        clearTimeout(abortTimeout)
        var result, error = false
        if ((xhr.status >= 200 && xhr.status < 300) || xhr.status == 304 || (xhr.status == 0 && protocol == 'file:')) {
          dataType = dataType || mimeToDataType(settings.mimeType || xhr.getResponseHeader('content-type'))
          result = xhr.responseText

          try {
            // http://perfectionkills.com/global-eval-what-are-the-options/
            if (dataType == 'script')    (1,eval)(result)
            else if (dataType == 'xml')  result = xhr.responseXML
            else if (dataType == 'json') result = blankRE.test(result) ? null : $.parseJSON(result)
          } catch (e) { error = e }

          if (error) ajaxError(error, 'parsererror', xhr, settings, deferred)
          else ajaxSuccess(result, xhr, settings, deferred)
        } else {
          ajaxError(xhr.statusText || null, xhr.status ? 'error' : 'abort', xhr, settings, deferred)
        }
      }
    }

    if (ajaxBeforeSend(xhr, settings) === false) {
      xhr.abort()
      ajaxError(null, 'abort', xhr, settings, deferred)
      return xhr
    }

    if (settings.xhrFields) for (name in settings.xhrFields) xhr[name] = settings.xhrFields[name]

    var async = 'async' in settings ? settings.async : true
    xhr.open(settings.type, settings.url, async, settings.username, settings.password)

    for (name in headers) nativeSetHeader.apply(xhr, headers[name])

    if (settings.timeout > 0) abortTimeout = setTimeout(function(){
        xhr.onreadystatechange = empty
        xhr.abort()
        ajaxError(null, 'timeout', xhr, settings, deferred)
      }, settings.timeout)

    // avoid sending empty string (#319)
    xhr.send(settings.data ? settings.data : null)
    return xhr
  }

  // handle optional data/success arguments
  function parseArguments(url, data, success, dataType) {
    if ($.isFunction(data)) dataType = success, success = data, data = undefined
    if (!$.isFunction(success)) dataType = success, success = undefined
    return {
      url: url
    , data: data
    , success: success
    , dataType: dataType
    }
  }

  $.get = function(/* url, data, success, dataType */){
    return $.ajax(parseArguments.apply(null, arguments))
  }

  $.post = function(/* url, data, success, dataType */){
    var options = parseArguments.apply(null, arguments)
    options.type = 'POST'
    return $.ajax(options)
  }

  $.getJSON = function(/* url, data, success */){
    var options = parseArguments.apply(null, arguments)
    options.dataType = 'json'
    return $.ajax(options)
  }

  $.fn.load = function(url, data, success){
    if (!this.length) return this
    var self = this, parts = url.split(/\s/), selector,
        options = parseArguments(url, data, success),
        callback = options.success
    if (parts.length > 1) options.url = parts[0], selector = parts[1]
    options.success = function(response){
      self.html(selector ?
        $('<div>').html(response.replace(rscript, "")).find(selector)
        : response)
      callback && callback.apply(self, arguments)
    }
    $.ajax(options)
    return this
  }

  var escape = encodeURIComponent

  function serialize(params, obj, traditional, scope){
    var type, array = $.isArray(obj), hash = $.isPlainObject(obj)
    $.each(obj, function(key, value) {
      type = $.type(value)
      if (scope) key = traditional ? scope :
        scope + '[' + (hash || type == 'object' || type == 'array' ? key : '') + ']'
      // handle data in serializeArray() format
      if (!scope && array) params.add(value.name, value.value)
      // recurse into nested objects
      else if (type == "array" || (!traditional && type == "object"))
        serialize(params, value, traditional, key)
      else params.add(key, value)
    })
  }

  $.param = function(obj, traditional){
    var params = []
    params.add = function(key, value) {
      if ($.isFunction(value)) value = value()
      if (value == null) value = ""
      this.push(escape(key) + '=' + escape(value))
    }
    serialize(params, obj, traditional)
    return params.join('&').replace(/%20/g, '+')
  }
})(Zepto)

;(function($){
  $.fn.serializeArray = function() {
    var name, type, result = [],
      add = function(value) {
        if (value.forEach) return value.forEach(add)
        result.push({ name: name, value: value })
      }
    if (this[0]) $.each(this[0].elements, function(_, field){
      type = field.type, name = field.name
      if (name && field.nodeName.toLowerCase() != 'fieldset' &&
        !field.disabled && type != 'submit' && type != 'reset' && type != 'button' && type != 'file' &&
        ((type != 'radio' && type != 'checkbox') || field.checked))
          add($(field).val())
    })
    return result
  }

  $.fn.serialize = function(){
    var result = []
    this.serializeArray().forEach(function(elm){
      result.push(encodeURIComponent(elm.name) + '=' + encodeURIComponent(elm.value))
    })
    return result.join('&')
  }

  $.fn.submit = function(callback) {
    if (0 in arguments) this.bind('submit', callback)
    else if (this.length) {
      var event = $.Event('submit')
      this.eq(0).trigger(event)
      if (!event.isDefaultPrevented()) this.get(0).submit()
    }
    return this
  }

})(Zepto)

;(function($){
  // __proto__ doesn't exist on IE<11, so redefine
  // the Z function to use object extension instead
  if (!('__proto__' in {})) {
    $.extend($.zepto, {
      Z: function(dom, selector){
        dom = dom || []
        $.extend(dom, $.fn)
        dom.selector = selector || ''
        dom.__Z = true
        return dom
      },
      // this is a kludge but works
      isZ: function(object){
        return $.type(object) === 'array' && '__Z' in object
      }
    })
  }

  // getComputedStyle shouldn't freak out when called
  // without a valid element as argument
  try {
    getComputedStyle(undefined)
  } catch(e) {
    var nativeGetComputedStyle = getComputedStyle;
    window.getComputedStyle = function(element){
      try {
        return nativeGetComputedStyle(element)
      } catch(e) {
        return null
      }
    }
  }
})(Zepto)

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  function detect(ua, platform){
    var os = this.os = {}, browser = this.browser = {},
      webkit = ua.match(/Web[kK]it[\/]{0,1}([\d.]+)/),
      android = ua.match(/(Android);?[\s\/]+([\d.]+)?/),
      osx = !!ua.match(/\(Macintosh\; Intel /),
      ipad = ua.match(/(iPad).*OS\s([\d_]+)/),
      ipod = ua.match(/(iPod)(.*OS\s([\d_]+))?/),
      iphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/),
      webos = ua.match(/(webOS|hpwOS)[\s\/]([\d.]+)/),
      win = /Win\d{2}|Windows/.test(platform),
      wp = ua.match(/Windows Phone ([\d.]+)/),
      touchpad = webos && ua.match(/TouchPad/),
      kindle = ua.match(/Kindle\/([\d.]+)/),
      silk = ua.match(/Silk\/([\d._]+)/),
      blackberry = ua.match(/(BlackBerry).*Version\/([\d.]+)/),
      bb10 = ua.match(/(BB10).*Version\/([\d.]+)/),
      rimtabletos = ua.match(/(RIM\sTablet\sOS)\s([\d.]+)/),
      playbook = ua.match(/PlayBook/),
      chrome = ua.match(/Chrome\/([\d.]+)/) || ua.match(/CriOS\/([\d.]+)/),
      firefox = ua.match(/Firefox\/([\d.]+)/),
      firefoxos = ua.match(/\((?:Mobile|Tablet); rv:([\d.]+)\).*Firefox\/[\d.]+/),
      ie = ua.match(/MSIE\s([\d.]+)/) || ua.match(/Trident\/[\d](?=[^\?]+).*rv:([0-9.].)/),
      webview = !chrome && ua.match(/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/),
      safari = webview || ua.match(/Version\/([\d.]+)([^S](Safari)|[^M]*(Mobile)[^S]*(Safari))/)

    // Todo: clean this up with a better OS/browser seperation:
    // - discern (more) between multiple browsers on android
    // - decide if kindle fire in silk mode is android or not
    // - Firefox on Android doesn't specify the Android version
    // - possibly devide in os, device and browser hashes

    if (browser.webkit = !!webkit) browser.version = webkit[1]

    if (android) os.android = true, os.version = android[2]
    if (iphone && !ipod) os.ios = os.iphone = true, os.version = iphone[2].replace(/_/g, '.')
    if (ipad) os.ios = os.ipad = true, os.version = ipad[2].replace(/_/g, '.')
    if (ipod) os.ios = os.ipod = true, os.version = ipod[3] ? ipod[3].replace(/_/g, '.') : null
    if (wp) os.wp = true, os.version = wp[1]
    if (webos) os.webos = true, os.version = webos[2]
    if (touchpad) os.touchpad = true
    if (blackberry) os.blackberry = true, os.version = blackberry[2]
    if (bb10) os.bb10 = true, os.version = bb10[2]
    if (rimtabletos) os.rimtabletos = true, os.version = rimtabletos[2]
    if (playbook) browser.playbook = true
    if (kindle) os.kindle = true, os.version = kindle[1]
    if (silk) browser.silk = true, browser.version = silk[1]
    if (!silk && os.android && ua.match(/Kindle Fire/)) browser.silk = true
    if (chrome) browser.chrome = true, browser.version = chrome[1]
    if (firefox) browser.firefox = true, browser.version = firefox[1]
    if (firefoxos) os.firefoxos = true, os.version = firefoxos[1]
    if (ie) browser.ie = true, browser.version = ie[1]
    if (safari && (osx || os.ios || win)) {
      browser.safari = true
      if (!os.ios) browser.version = safari[1]
    }
    if (webview) browser.webview = true

    os.tablet = !!(ipad || playbook || (android && !ua.match(/Mobile/)) ||
      (firefox && ua.match(/Tablet/)) || (ie && !ua.match(/Phone/) && ua.match(/Touch/)))
    os.phone  = !!(!os.tablet && !os.ipod && (android || iphone || webos || blackberry || bb10 ||
      (chrome && ua.match(/Android/)) || (chrome && ua.match(/CriOS\/([\d.]+)/)) ||
      (firefox && ua.match(/Mobile/)) || (ie && ua.match(/Touch/))))
  }

  detect.call($, navigator.userAgent, navigator.platform)
  // make available to unit tests
  $.__detect = detect

})(Zepto);

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($, undefined){
  var prefix = '', eventPrefix,
    vendors = { Webkit: 'webkit', Moz: '', O: 'o' },
    testEl = document.createElement('div'),
    supportedTransforms = /^((translate|rotate|scale)(X|Y|Z|3d)?|matrix(3d)?|perspective|skew(X|Y)?)$/i,
    transform,
    transitionProperty, transitionDuration, transitionTiming, transitionDelay,
    animationName, animationDuration, animationTiming, animationDelay,
    cssReset = {}

  function dasherize(str) { return str.replace(/([a-z])([A-Z])/, '$1-$2').toLowerCase() }
  function normalizeEvent(name) { return eventPrefix ? eventPrefix + name : name.toLowerCase() }

  $.each(vendors, function(vendor, event){
    if (testEl.style[vendor + 'TransitionProperty'] !== undefined) {
      prefix = '-' + vendor.toLowerCase() + '-'
      eventPrefix = event
      return false
    }
  })

  transform = prefix + 'transform'
  cssReset[transitionProperty = prefix + 'transition-property'] =
  cssReset[transitionDuration = prefix + 'transition-duration'] =
  cssReset[transitionDelay    = prefix + 'transition-delay'] =
  cssReset[transitionTiming   = prefix + 'transition-timing-function'] =
  cssReset[animationName      = prefix + 'animation-name'] =
  cssReset[animationDuration  = prefix + 'animation-duration'] =
  cssReset[animationDelay     = prefix + 'animation-delay'] =
  cssReset[animationTiming    = prefix + 'animation-timing-function'] = ''

  $.fx = {
    off: (eventPrefix === undefined && testEl.style.transitionProperty === undefined),
    speeds: { _default: 400, fast: 200, slow: 600 },
    cssPrefix: prefix,
    transitionEnd: normalizeEvent('TransitionEnd'),
    animationEnd: normalizeEvent('AnimationEnd')
  }

  $.fn.animate = function(properties, duration, ease, callback, delay){
    if ($.isFunction(duration))
      callback = duration, ease = undefined, duration = undefined
    if ($.isFunction(ease))
      callback = ease, ease = undefined
    if ($.isPlainObject(duration))
      ease = duration.easing, callback = duration.complete, delay = duration.delay, duration = duration.duration
    if (duration) duration = (typeof duration == 'number' ? duration :
                    ($.fx.speeds[duration] || $.fx.speeds._default)) / 1000
    if (delay) delay = parseFloat(delay) / 1000
    return this.anim(properties, duration, ease, callback, delay)
  }

  $.fn.anim = function(properties, duration, ease, callback, delay){
    var key, cssValues = {}, cssProperties, transforms = '',
        that = this, wrappedCallback, endEvent = $.fx.transitionEnd,
        fired = false

    if (duration === undefined) duration = $.fx.speeds._default / 1000
    if (delay === undefined) delay = 0
    if ($.fx.off) duration = 0

    if (typeof properties == 'string') {
      // keyframe animation
      cssValues[animationName] = properties
      cssValues[animationDuration] = duration + 's'
      cssValues[animationDelay] = delay + 's'
      cssValues[animationTiming] = (ease || 'linear')
      endEvent = $.fx.animationEnd
    } else {
      cssProperties = []
      // CSS transitions
      for (key in properties)
        if (supportedTransforms.test(key)) transforms += key + '(' + properties[key] + ') '
        else cssValues[key] = properties[key], cssProperties.push(dasherize(key))

      if (transforms) cssValues[transform] = transforms, cssProperties.push(transform)
      if (duration > 0 && typeof properties === 'object') {
        cssValues[transitionProperty] = cssProperties.join(', ')
        cssValues[transitionDuration] = duration + 's'
        cssValues[transitionDelay] = delay + 's'
        cssValues[transitionTiming] = (ease || 'linear')
      }
    }

    wrappedCallback = function(event){
      if (typeof event !== 'undefined') {
        if (event.target !== event.currentTarget) return // makes sure the event didn't bubble from "below"
        $(event.target).unbind(endEvent, wrappedCallback)
      } else
        $(this).unbind(endEvent, wrappedCallback) // triggered by setTimeout

      fired = true
      $(this).css(cssReset)
      callback && callback.call(this)
    }
    if (duration > 0){
      this.bind(endEvent, wrappedCallback)
      // transitionEnd is not always firing on older Android phones
      // so make sure it gets fired
      setTimeout(function(){
        if (fired) return
        wrappedCallback.call(that)
      }, ((duration + delay) * 1000) + 25)
    }

    // trigger page reflow so new elements can animate
    this.size() && this.get(0).clientLeft

    this.css(cssValues)

    if (duration <= 0) setTimeout(function() {
      that.each(function(){ wrappedCallback.call(this) })
    }, 0)

    return this
  }

  testEl = null
})(Zepto);

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($, undefined){
  var document = window.document, docElem = document.documentElement,
    origShow = $.fn.show, origHide = $.fn.hide, origToggle = $.fn.toggle

  function anim(el, speed, opacity, scale, callback) {
    if (typeof speed == 'function' && !callback) callback = speed, speed = undefined
    var props = { opacity: opacity }
    if (scale) {
      props.scale = scale
      el.css($.fx.cssPrefix + 'transform-origin', '0 0')
    }
    return el.animate(props, speed, null, callback)
  }

  function hide(el, speed, scale, callback) {
    return anim(el, speed, 0, scale, function(){
      origHide.call($(this))
      callback && callback.call(this)
    })
  }

  $.fn.show = function(speed, callback) {
    origShow.call(this)
    if (speed === undefined) speed = 0
    else this.css('opacity', 0)
    return anim(this, speed, 1, '1,1', callback)
  }

  $.fn.hide = function(speed, callback) {
    if (speed === undefined) return origHide.call(this)
    else return hide(this, speed, '0,0', callback)
  }

  $.fn.toggle = function(speed, callback) {
    if (speed === undefined || typeof speed == 'boolean')
      return origToggle.call(this, speed)
    else return this.each(function(){
      var el = $(this)
      el[el.css('display') == 'none' ? 'show' : 'hide'](speed, callback)
    })
  }

  $.fn.fadeTo = function(speed, opacity, callback) {
    return anim(this, speed, opacity, null, callback)
  }

  $.fn.fadeIn = function(speed, callback) {
    var target = this.css('opacity')
    if (target > 0) this.css('opacity', 0)
    else target = 1
    return origShow.call(this).fadeTo(speed, target, callback)
  }

  $.fn.fadeOut = function(speed, callback) {
    return hide(this, speed, null, callback)
  }

  $.fn.fadeToggle = function(speed, callback) {
    return this.each(function(){
      var el = $(this)
      el[
        (el.css('opacity') == 0 || el.css('display') == 'none') ? 'fadeIn' : 'fadeOut'
      ](speed, callback)
    })
  }

})(Zepto);

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  // Create a collection of callbacks to be fired in a sequence, with configurable behaviour
  // Option flags:
  //   - once: Callbacks fired at most one time.
  //   - memory: Remember the most recent context and arguments
  //   - stopOnFalse: Cease iterating over callback list
  //   - unique: Permit adding at most one instance of the same callback
  $.Callbacks = function(options) {
    options = $.extend({}, options)

    var memory, // Last fire value (for non-forgettable lists)
        fired,  // Flag to know if list was already fired
        firing, // Flag to know if list is currently firing
        firingStart, // First callback to fire (used internally by add and fireWith)
        firingLength, // End of the loop when firing
        firingIndex, // Index of currently firing callback (modified by remove if needed)
        list = [], // Actual callback list
        stack = !options.once && [], // Stack of fire calls for repeatable lists
        fire = function(data) {
          memory = options.memory && data
          fired = true
          firingIndex = firingStart || 0
          firingStart = 0
          firingLength = list.length
          firing = true
          for ( ; list && firingIndex < firingLength ; ++firingIndex ) {
            if (list[firingIndex].apply(data[0], data[1]) === false && options.stopOnFalse) {
              memory = false
              break
            }
          }
          firing = false
          if (list) {
            if (stack) stack.length && fire(stack.shift())
            else if (memory) list.length = 0
            else Callbacks.disable()
          }
        },

        Callbacks = {
          add: function() {
            if (list) {
              var start = list.length,
                  add = function(args) {
                    $.each(args, function(_, arg){
                      if (typeof arg === "function") {
                        if (!options.unique || !Callbacks.has(arg)) list.push(arg)
                      }
                      else if (arg && arg.length && typeof arg !== 'string') add(arg)
                    })
                  }
              add(arguments)
              if (firing) firingLength = list.length
              else if (memory) {
                firingStart = start
                fire(memory)
              }
            }
            return this
          },
          remove: function() {
            if (list) {
              $.each(arguments, function(_, arg){
                var index
                while ((index = $.inArray(arg, list, index)) > -1) {
                  list.splice(index, 1)
                  // Handle firing indexes
                  if (firing) {
                    if (index <= firingLength) --firingLength
                    if (index <= firingIndex) --firingIndex
                  }
                }
              })
            }
            return this
          },
          has: function(fn) {
            return !!(list && (fn ? $.inArray(fn, list) > -1 : list.length))
          },
          empty: function() {
            firingLength = list.length = 0
            return this
          },
          disable: function() {
            list = stack = memory = undefined
            return this
          },
          disabled: function() {
            return !list
          },
          lock: function() {
            stack = undefined;
            if (!memory) Callbacks.disable()
            return this
          },
          locked: function() {
            return !stack
          },
          fireWith: function(context, args) {
            if (list && (!fired || stack)) {
              args = args || []
              args = [context, args.slice ? args.slice() : args]
              if (firing) stack.push(args)
              else fire(args)
            }
            return this
          },
          fire: function() {
            return Callbacks.fireWith(this, arguments)
          },
          fired: function() {
            return !!fired
          }
        }

    return Callbacks
  }
})(Zepto);

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.
//
//     Some code (c) 2005, 2013 jQuery Foundation, Inc. and other contributors

;(function($){
  var slice = Array.prototype.slice

  function Deferred(func) {
    var tuples = [
          // action, add listener, listener list, final state
          [ "resolve", "done", $.Callbacks({once:1, memory:1}), "resolved" ],
          [ "reject", "fail", $.Callbacks({once:1, memory:1}), "rejected" ],
          [ "notify", "progress", $.Callbacks({memory:1}) ]
        ],
        state = "pending",
        promise = {
          state: function() {
            return state
          },
          always: function() {
            deferred.done(arguments).fail(arguments)
            return this
          },
          then: function(/* fnDone [, fnFailed [, fnProgress]] */) {
            var fns = arguments
            return Deferred(function(defer){
              $.each(tuples, function(i, tuple){
                var fn = $.isFunction(fns[i]) && fns[i]
                deferred[tuple[1]](function(){
                  var returned = fn && fn.apply(this, arguments)
                  if (returned && $.isFunction(returned.promise)) {
                    returned.promise()
                      .done(defer.resolve)
                      .fail(defer.reject)
                      .progress(defer.notify)
                  } else {
                    var context = this === promise ? defer.promise() : this,
                        values = fn ? [returned] : arguments
                    defer[tuple[0] + "With"](context, values)
                  }
                })
              })
              fns = null
            }).promise()
          },

          promise: function(obj) {
            return obj != null ? $.extend( obj, promise ) : promise
          }
        },
        deferred = {}

    $.each(tuples, function(i, tuple){
      var list = tuple[2],
          stateString = tuple[3]

      promise[tuple[1]] = list.add

      if (stateString) {
        list.add(function(){
          state = stateString
        }, tuples[i^1][2].disable, tuples[2][2].lock)
      }

      deferred[tuple[0]] = function(){
        deferred[tuple[0] + "With"](this === deferred ? promise : this, arguments)
        return this
      }
      deferred[tuple[0] + "With"] = list.fireWith
    })

    promise.promise(deferred)
    if (func) func.call(deferred, deferred)
    return deferred
  }

  $.when = function(sub) {
    var resolveValues = slice.call(arguments),
        len = resolveValues.length,
        i = 0,
        remain = len !== 1 || (sub && $.isFunction(sub.promise)) ? len : 0,
        deferred = remain === 1 ? sub : Deferred(),
        progressValues, progressContexts, resolveContexts,
        updateFn = function(i, ctx, val){
          return function(value){
            ctx[i] = this
            val[i] = arguments.length > 1 ? slice.call(arguments) : value
            if (val === progressValues) {
              deferred.notifyWith(ctx, val)
            } else if (!(--remain)) {
              deferred.resolveWith(ctx, val)
            }
          }
        }

    if (len > 1) {
      progressValues = new Array(len)
      progressContexts = new Array(len)
      resolveContexts = new Array(len)
      for ( ; i < len; ++i ) {
        if (resolveValues[i] && $.isFunction(resolveValues[i].promise)) {
          resolveValues[i].promise()
            .done(updateFn(i, resolveContexts, resolveValues))
            .fail(deferred.reject)
            .progress(updateFn(i, progressContexts, progressValues))
        } else {
          --remain
        }
      }
    }
    if (!remain) deferred.resolveWith(resolveContexts, resolveValues)
    return deferred.promise()
  }

  $.Deferred = Deferred
})(Zepto);

//     Zepto.js
//     (c) 2010-2015 Thomas Fuchs
//     Zepto.js may be freely distributed under the MIT license.

;(function($){
  var touch = {},
    touchTimeout, tapTimeout, swipeTimeout, longTapTimeout,
    longTapDelay = 750,
    gesture

  function swipeDirection(x1, x2, y1, y2) {
    return Math.abs(x1 - x2) >=
      Math.abs(y1 - y2) ? (x1 - x2 > 0 ? 'Left' : 'Right') : (y1 - y2 > 0 ? 'Up' : 'Down')
  }

  function longTap() {
    longTapTimeout = null
    if (touch.last) {
      touch.el.trigger('longTap')
      touch = {}
    }
  }

  function cancelLongTap() {
    if (longTapTimeout) clearTimeout(longTapTimeout)
    longTapTimeout = null
  }

  function cancelAll() {
    if (touchTimeout) clearTimeout(touchTimeout)
    if (tapTimeout) clearTimeout(tapTimeout)
    if (swipeTimeout) clearTimeout(swipeTimeout)
    if (longTapTimeout) clearTimeout(longTapTimeout)
    touchTimeout = tapTimeout = swipeTimeout = longTapTimeout = null
    touch = {}
  }

  function isPrimaryTouch(event){
    return (event.pointerType == 'touch' ||
      event.pointerType == event.MSPOINTER_TYPE_TOUCH)
      && event.isPrimary
  }

  function isPointerEventType(e, type){
    return (e.type == 'pointer'+type ||
      e.type.toLowerCase() == 'mspointer'+type)
  }

  $(document).ready(function(){
    var now, delta, deltaX = 0, deltaY = 0, firstTouch, _isPointerType

    if ('MSGesture' in window) {
      gesture = new MSGesture()
      gesture.target = document.body
    }

    $(document)
      .bind('MSGestureEnd', function(e){
        var swipeDirectionFromVelocity =
          e.velocityX > 1 ? 'Right' : e.velocityX < -1 ? 'Left' : e.velocityY > 1 ? 'Down' : e.velocityY < -1 ? 'Up' : null;
        if (swipeDirectionFromVelocity) {
          touch.el.trigger('swipe')
          touch.el.trigger('swipe'+ swipeDirectionFromVelocity)
        }
      })
      .on('touchstart MSPointerDown pointerdown', function(e){
        if((_isPointerType = isPointerEventType(e, 'down')) &&
          !isPrimaryTouch(e)) return
        firstTouch = _isPointerType ? e : e.touches[0]
        if (e.touches && e.touches.length === 1 && touch.x2) {
          // Clear out touch movement data if we have it sticking around
          // This can occur if touchcancel doesn't fire due to preventDefault, etc.
          touch.x2 = undefined
          touch.y2 = undefined
        }
        now = Date.now()
        delta = now - (touch.last || now)
        touch.el = $('tagName' in firstTouch.target ?
          firstTouch.target : firstTouch.target.parentNode)
        touchTimeout && clearTimeout(touchTimeout)
        touch.x1 = firstTouch.pageX
        touch.y1 = firstTouch.pageY
        if (delta > 0 && delta <= 250) touch.isDoubleTap = true
        touch.last = now
        longTapTimeout = setTimeout(longTap, longTapDelay)
        // adds the current touch contact for IE gesture recognition
        if (gesture && _isPointerType) gesture.addPointer(e.pointerId);
      })
      .on('touchmove MSPointerMove pointermove', function(e){
        if((_isPointerType = isPointerEventType(e, 'move')) &&
          !isPrimaryTouch(e)) return
        firstTouch = _isPointerType ? e : e.touches[0]
        cancelLongTap()
        touch.x2 = firstTouch.pageX
        touch.y2 = firstTouch.pageY

        deltaX += Math.abs(touch.x1 - touch.x2)
        deltaY += Math.abs(touch.y1 - touch.y2)
      })
      .on('touchend MSPointerUp pointerup', function(e){
        if((_isPointerType = isPointerEventType(e, 'up')) &&
          !isPrimaryTouch(e)) return
        cancelLongTap()

        // swipe
        if ((touch.x2 && Math.abs(touch.x1 - touch.x2) > 30) ||
            (touch.y2 && Math.abs(touch.y1 - touch.y2) > 30))

          swipeTimeout = setTimeout(function() {
            touch.el.trigger('swipe')
            touch.el.trigger('swipe' + (swipeDirection(touch.x1, touch.x2, touch.y1, touch.y2)))
            touch = {}
          }, 0)

        // normal tap
        else if ('last' in touch)
          // don't fire tap when delta position changed by more than 30 pixels,
          // for instance when moving to a point and back to origin
          if (deltaX < 30 && deltaY < 30) {
            // delay by one tick so we can cancel the 'tap' event if 'scroll' fires
            // ('tap' fires before 'scroll')
            tapTimeout = setTimeout(function() {

              // trigger universal 'tap' with the option to cancelTouch()
              // (cancelTouch cancels processing of single vs double taps for faster 'tap' response)
              var event = $.Event('tap')
              event.cancelTouch = cancelAll
              touch.el.trigger(event)

              // trigger double tap immediately
              if (touch.isDoubleTap) {
                if (touch.el) touch.el.trigger('doubleTap')
                touch = {}
              }

              // trigger single tap after 250ms of inactivity
              else {
                touchTimeout = setTimeout(function(){
                  touchTimeout = null
                  if (touch.el) touch.el.trigger('singleTap')
                  touch = {}
                }, 250)
              }
            }, 0)
          } else {
            touch = {}
          }
          deltaX = deltaY = 0

      })
      // when the browser window loses focus,
      // for example when a modal dialog is shown,
      // cancel all ongoing events
      .on('touchcancel MSPointerCancel pointercancel', cancelAll)

    // scrolling the window indicates intention of the user
    // to scroll, not tap or swipe, so cancel all ongoing events
    $(window).on('scroll', cancelAll)
  })

  ;['swipe', 'swipeLeft', 'swipeRight', 'swipeUp', 'swipeDown',
    'doubleTap', 'tap', 'singleTap', 'longTap'].forEach(function(eventName){
    $.fn[eventName] = function(callback){ return this.on(eventName, callback) }
  })
})(Zepto);
;
steel.config({
  debug: true
});
var STK = (function() {
	var a = {};
	var b = [];
	a.inc = function(d, c) {
		return true
	};
	a.register = function(e, c) {
		var g = e.split(".");
		var f = a;
		var d = null;
		while (d = g.shift()) {
			if (g.length) {
				if (f[d] === undefined) {
					f[d] = {}
				}
				f = f[d]
			} else {
				if (f[d] === undefined) {
					try {
						f[d] = c(a)
					} catch (h) {
						b.push(h)
					}
				}
			}
		}
	};
	a.regShort = function(c, d) {
		if (a[c] !== undefined) {
			throw "[" + c + "] : short : has been register"
		}
		a[c] = d
	};
	a.IE = /msie/i.test(navigator.userAgent);
	a.E = function(c) {
		if (typeof c === "string") {
			return document.getElementById(c)
		} else {
			return c
		}
	};
	a.C = function(c) {
		var d;
		c = c.toUpperCase();
		if (c == "TEXT") {
			d = document.createTextNode("")
		} else {
			if (c == "BUFFER") {
				d = document.createDocumentFragment()
			} else {
				d = document.createElement(c)
			}
		}
		return d
	};
	a.log = function(c) {
		b.push("[" + ((new Date()).getTime() % 100000) + "]: " + c)
	};
	a.getErrorLogInformationList = function(c) {
		return b.splice(0, c || b.length)
	};
	return a
})();
$Import = STK.inc;
STK.register("core.ani.algorithm", function(b) {
	var a = {
		linear: function(f, e, j, h, g) {
			return j * f / h + e
		},
		easeincubic: function(f, e, j, h, g) {
			return j * (f /= h) * f * f + e
		},
		easeoutcubic: function(f, e, j, h, g) {
			if ((f /= h / 2) < 1) {
				return j / 2 * f * f * f + e
			}
			return j / 2 * ((f -= 2) * f * f + 2) + e
		},
		easeinoutcubic: function(f, e, j, h, g) {
			if (g == undefined) {
				g = 1.70158
			}
			return j * (f /= h) * f * ((g + 1) * f - g) + e
		},
		easeinback: function(f, e, j, h, g) {
			if (g == undefined) {
				g = 1.70158
			}
			return j * ((f = f / h - 1) * f * ((g + 1) * f + g) + 1) + e
		},
		easeoutback: function(f, e, j, h, g) {
			if (g == undefined) {
				g = 1.70158
			}
			return j * ((f = f / h - 1) * f * ((g + 1) * f + g) + 1) + e
		},
		easeinoutback: function(f, e, j, h, g) {
			if (g == undefined) {
				g = 1.70158
			}
			if ((f /= h / 2) < 1) {
				return j / 2 * (f * f * (((g *= (1.525)) + 1) * f - g)) + e
			}
			return j / 2 * ((f -= 2) * f * (((g *= (1.525)) + 1) * f + g) + 2) + e
		}
	};
	return {
		addAlgorithm: function(c, d) {
			if (a[c]) {
				throw "[core.ani.tweenValue] this algorithm :" + c + "already exist"
			}
			a[c] = d
		},
		compute: function(h, e, d, f, g, c, j) {
			if (typeof a[h] !== "function") {
				throw "[core.ani.tweenValue] this algorithm :" + h + "do not exist"
			}
			return a[h](f, e, d, g, c, j)
		}
	}
});
STK.register("core.func.empty", function() {
	return function() {}
});
STK.register("core.obj.parseParam", function(a) {
	return function(d, c, b) {
		var e, f = {};
		c = c || {};
		for (e in d) {
			f[e] = d[e];
			if (c[e] != null) {
				if (b) {
					if (d.hasOwnProperty[e]) {
						f[e] = c[e]
					}
				} else {
					f[e] = c[e]
				}
			}
		}
		return f
	}
});
STK.register("core.ani.tweenArche", function(a) {
	return function(n, o) {
		var h, g, f, c, d, b, j, e;
		g = {};
		h = a.core.obj.parseParam({
			animationType: "linear",
			distance: 1,
			duration: 500,
			callback: a.core.func.empty,
			algorithmParams: {},
			extra: 5,
			delay: 25
		}, o);
		var m = function() {
			f = (+new Date() - c);
			if (f < h.duration) {
				d = a.core.ani.algorithm.compute(h.animationType, 0, h.distance, f, h.duration, h.extra, h.algorithmParams);
				n(d);
				b = setTimeout(m, h.delay)
			} else {
				e = "stop";
				h.callback()
			}
		};
		e = "stop";
		g.getStatus = function() {
			return e
		};
		g.play = function() {
			c = +new Date();
			d = null;
			m();
			e = "play";
			return g
		};
		g.stop = function() {
			clearTimeout(b);
			e = "stop";
			return g
		};
		g.resume = function() {
			if (j) {
				c += (+new Date() - j);
				m()
			}
			return g
		};
		g.pause = function() {
			clearTimeout(b);
			j = +new Date();
			e = "pause";
			return g
		};
		g.destroy = function() {
			clearTimeout(b);
			j = 0;
			e = "stop"
		};
		return g
	}
});
STK.register("core.dom.getStyle", function(a) {
	return function(c, f) {
		if (a.IE) {
			switch (f) {
				case "opacity":
					var h = 100;
					try {
						h = c.filters["DXImageTransform.Microsoft.Alpha"].opacity
					} catch (g) {
						try {
							h = c.filters("alpha").opacity
						} catch (g) {}
					}
					return h / 100;
				case "float":
					f = "styleFloat";
				default:
					var d = c.currentStyle ? c.currentStyle[f] : null;
					return (c.style[f] || d)
			}
		} else {
			if (f == "float") {
				f = "cssFloat"
			}
			try {
				var b = document.defaultView.getComputedStyle(c, "")
			} catch (g) {}
			return c.style[f] || b ? b[f] : null
		}
	}
});
STK.register("core.dom.cssText", function(a) {
	return function(e) {
		e = (e || "").replace(/(^[^\:]*?;)|(;[^\:]*?$)/g, "").split(";");
		var g = {},
			c;
		for (var b = 0; b < e.length; b++) {
			c = e[b].split(":");
			g[c[0].toLowerCase()] = c[1]
		}
		var f = [],
			d = {
				push: function(j, h) {
					g[j.toLowerCase()] = h;
					return d
				},
				remove: function(h) {
					h = h.toLowerCase();
					g[h] && delete g[h];
					return d
				},
				getCss: function() {
					var j = [];
					for (var h in g) {
						j.push(h + ":" + g[h])
					}
					return j.join(";")
				}
			};
		return d
	}
});
STK.register("core.func.getType", function(a) {
	return function(b) {
		var c;
		return ((c = typeof(b)) == "object" ? b == null && "null" || Object.prototype.toString.call(b).slice(8, -1) : c).toLowerCase()
	}
});
STK.register("core.arr.isArray", function(a) {
	return function(b) {
		return Object.prototype.toString.call(b) === "[object Array]"
	}
});
STK.register("core.arr.foreach", function(c) {
	var a = function(j, f) {
		var h = [];
		for (var g = 0, e = j.length; g < e; g += 1) {
			var d = f(j[g], g);
			if (d === false) {
				break
			} else {
				if (d !== null) {
					h[g] = d
				}
			}
		}
		return h
	};
	var b = function(h, e) {
		var g = {};
		for (var f in h) {
			var d = e(h[f], f);
			if (d === false) {
				break
			} else {
				if (d !== null) {
					g[f] = d
				}
			}
		}
		return g
	};
	return function(e, d) {
		if (c.core.arr.isArray(e) || (e.length && e[0] !== undefined)) {
			return a(e, d)
		} else {
			if (typeof e === "object") {
				return b(e, d)
			}
		}
		return null
	}
});
STK.register("core.arr.indexOf", function(a) {
	return function(d, e) {
		if (e.indexOf) {
			return e.indexOf(d)
		}
		for (var c = 0, b = e.length; c < b; c++) {
			if (e[c] === d) {
				return c
			}
		}
		return -1
	}
});
STK.register("core.arr.inArray", function(a) {
	return function(b, c) {
		return a.core.arr.indexOf(b, c) > -1
	}
});
STK.register("core.dom.isNode", function(a) {
	return function(b) {
		return (b != undefined) && Boolean(b.nodeName) && Boolean(b.nodeType)
	}
});
STK.register("core.json.merge", function(b) {
	var a = function(d) {
		if (d === undefined) {
			return true
		}
		if (d === null) {
			return true
		}
		if (b.core.arr.inArray(["number", "string", "function"], (typeof d))) {
			return true
		}
		if (b.core.arr.isArray(d)) {
			return true
		}
		if (b.core.dom.isNode(d)) {
			return true
		}
		return false
	};
	var c = function(g, j, f) {
		var h = {};
		for (var e in g) {
			if (j[e] === undefined) {
				h[e] = g[e]
			} else {
				if (!a(g[e]) && !a(j[e]) && f) {
					h[e] = arguments.callee(g[e], j[e])
				} else {
					h[e] = j[e]
				}
			}
		}
		for (var d in j) {
			if (h[d] === undefined) {
				h[d] = j[d]
			}
		}
		return h
	};
	return function(d, g, f) {
		var e = b.core.obj.parseParam({
			isDeep: false
		}, f);
		return c(d, g, e.isDeep)
	}
});
STK.register("core.util.color", function(f) {
	var c = /^#([a-fA-F0-9]{3,8})$/;
	var e = /^rgb[a]?\s*\((\s*([0-9]{1,3})\s*,){2,3}(\s*([0-9]{1,3})\s*)\)$/;
	var d = /([0-9]{1,3})/ig;
	var a = /([a-fA-F0-9]{2})/ig;
	var b = f.core.arr.foreach;
	var g = function(m) {
		var h = [];
		var j = [];
		if (c.test(m)) {
			j = m.match(c);
			if (j[1].length <= 4) {
				h = b(j[1].split(""), function(o, n) {
					return parseInt(o + o, 16)
				})
			} else {
				if (j[1].length <= 8) {
					h = b(j[1].match(a), function(o, n) {
						return parseInt(o, 16)
					})
				}
			}
			return h
		}
		if (e.test(m)) {
			j = m.match(d);
			h = b(j, function(o, n) {
				return parseInt(o, 10)
			});
			return h
		}
		return false
	};
	return function(m, h) {
		var j = g(m);
		if (!j) {
			return false
		}
		var n = {};
		n.getR = function() {
			return j[0]
		};
		n.getG = function() {
			return j[1]
		};
		n.getB = function() {
			return j[2]
		};
		n.getA = function() {
			return j[3]
		};
		return n
	}
});
STK.register("core.ani.tween", function(d) {
	var a = d.core.ani.tweenArche;
	var b = d.core.arr.foreach;
	var g = d.core.dom.getStyle;
	var h = d.core.func.getType;
	var n = d.core.obj.parseParam;
	var m = d.core.json.merge;
	var c = d.core.util.color;
	var f = function(r) {
		var q = /(-?\d\.?\d*)([a-z%]*)/i.exec(r);
		var p = [0, "px"];
		if (q) {
			if (q[1]) {
				p[0] = q[1] - 0
			}
			if (q[2]) {
				p[1] = q[2]
			}
		}
		return p
	};
	var o = function(t) {
		for (var r = 0, p = t.length; r < p; r += 1) {
			var q = t.charCodeAt(r);
			if (q > 64 && q < 90) {
				var u = t.substr(0, r);
				var w = t.substr(r, 1);
				var v = t.slice(r + 1);
				return u + "-" + w.toLowerCase() + v
			}
		}
		return t
	};
	var j = function(u, w, r) {
		var v = g(u, r);
		if (h(v) === "undefined" || v === "auto") {
			if (r === "height") {
				v = u.offsetHeight
			}
			if (r === "width") {
				v = u.offsetWidth
			}
		}
		var q = {
			start: v,
			end: w,
			unit: "",
			key: r,
			defaultColor: false
		};
		if (h(w) === "number") {
			var s = [0, "px"];
			if (h(v) === "number") {
				s[0] = v
			} else {
				s = f(v)
			}
			q.start = s[0];
			q.unit = s[1]
		}
		if (h(w) === "string") {
			var p, t;
			p = c(w);
			if (p) {
				t = c(v);
				if (!t) {
					t = c("#fff")
				}
				q.start = t;
				q.end = p;
				q.defaultColor = true
			}
		}
		u = null;
		return q
	};
	var e = {
		opacity: function(q, t, p, r) {
			var s = (q * (p - t) + t);
			return {
				filter: "alpha(opacity=" + s * 100 + ")",
				opacity: Math.max(Math.min(1, s), 0),
				zoom: "1"
			}
		},
		defaultColor: function(v, q, s, x, y) {
			var p = Math.max(0, Math.min(255, Math.ceil((v * (s.getR() - q.getR()) + q.getR()))));
			var t = Math.max(0, Math.min(255, Math.ceil((v * (s.getG() - q.getG()) + q.getG()))));
			var w = Math.max(0, Math.min(255, Math.ceil((v * (s.getB() - q.getB()) + q.getB()))));
			var u = {};
			u[o(y)] = "#" + (p < 16 ? "0" : "") + p.toString(16) + (t < 16 ? "0" : "") + t.toString(16) + (w < 16 ? "0" : "") + w.toString(16);
			return u
		},
		"default": function(s, v, p, t, r) {
			var u = (s * (p - v) + v);
			var q = {};
			q[o(r)] = u + t;
			return q
		}
	};
	return function(r, A) {
		var u, v, p, B, C, z, D, s, t, x;
		A = A || {};
		v = n({
			animationType: "linear",
			duration: 500,
			algorithmParams: {},
			extra: 5,
			delay: 25
		}, A);
		v.distance = 1;
		v.callback = (function() {
			var E = A.end || d.core.func.empty;
			return function() {
				B(1);
				D();
				E(r)
			}
		})();
		p = m(e, A.propertys || {});
		z = null;
		C = {};
		t = [];
		B = function(E) {
			var G = [];
			var F = b(C, function(L, J) {
				var K;
				if (p[J]) {
					K = p[J]
				} else {
					if (L.defaultColor) {
						K = p.defaultColor
					} else {
						K = p["default"]
					}
				}
				var I = K(E, L.start, L.end, L.unit, L.key);
				for (var H in I) {
					z.push(H, I[H])
				}
			});
			r.style.cssText = z.getCss()
		};
		D = function() {
			var E;
			while (E = t.shift()) {
				try {
					E.fn();
					if (E.type === "play") {
						break
					}
					if (E.type === "destroy") {
						break
					}
				} catch (F) {}
			}
		};
		x = a(B, v);
		var w = function() {
			if (x.getStatus() !== "play") {
				r = el
			} else {
				t.push({
					fn: w,
					type: "setNode"
				})
			}
		};
		var q = function(E) {
			if (x.getStatus() !== "play") {
				C = b(E, function(G, F) {
					return j(r, G, F)
				});
				z = d.core.dom.cssText(r.style.cssText + (A.staticStyle || ""));
				x.play()
			} else {
				t.push({
					fn: function() {
						q(E)
					},
					type: "play"
				})
			}
		};
		var y = function() {
			if (x.getStatus() !== "play") {
				x.destroy();
				r = null;
				u = null;
				v = null;
				p = null;
				B = null;
				C = null;
				z = null;
				D = null;
				s = null;
				t = null
			} else {
				t.push({
					fn: y,
					type: "destroy"
				})
			}
		};
		u = {};
		u.play = function(E) {
			q(E);
			return u
		};
		u.stop = function() {
			x.stop();
			return u
		};
		u.pause = function() {
			x.pause();
			return u
		};
		u.resume = function() {
			x.resume();
			return u
		};
		u.finish = function(E) {
			q(E);
			y();
			return u
		};
		u.setNode = function(E) {
			w();
			return u
		};
		u.destroy = function() {
			y();
			return u
		};
		return u
	}
});
STK.register("core.arr.findout", function(a) {
	return function(f, e) {
		if (!a.core.arr.isArray(f)) {
			throw "the findout function needs an array as first parameter"
		}
		var c = [];
		for (var d = 0, b = f.length; d < b; d += 1) {
			if (f[d] === e) {
				c.push(d)
			}
		}
		return c
	}
});
STK.register("core.arr.clear", function(a) {
	return function(e) {
		if (!a.core.arr.isArray(e)) {
			throw "the clear function needs an array as first parameter"
		}
		var c = [];
		for (var d = 0, b = e.length; d < b; d += 1) {
			if (!(a.core.arr.findout([undefined, null, ""], e[d]).length)) {
				c.push(e[d])
			}
		}
		return c
	}
});
STK.register("core.arr.copy", function(a) {
	return function(b) {
		if (!a.core.arr.isArray(b)) {
			throw "the copy function needs an array as first parameter"
		}
		return b.slice(0)
	}
});
STK.register("core.arr.hasby", function(a) {
	return function(f, c) {
		if (!a.core.arr.isArray(f)) {
			throw "the hasBy function needs an array as first parameter"
		}
		var d = [];
		for (var e = 0, b = f.length; e < b; e += 1) {
			if (c(f[e], e)) {
				d.push(e)
			}
		}
		return d
	}
});
STK.register("core.arr.unique", function(a) {
	return function(e) {
		if (!a.core.arr.isArray(e)) {
			throw "the unique function needs an array as first parameter"
		}
		var c = [];
		for (var d = 0, b = e.length; d < b; d += 1) {
			if (a.core.arr.indexOf(e[d], c) === -1) {
				c.push(e[d])
			}
		}
		return c
	}
});
STK.register("core.dom.hasClassName", function(a) {
	return function(c, b) {
		return (new RegExp("\\b" + b + "\\b").test(c.className))
	}
});
STK.register("core.dom.addClassName", function(a) {
	return function(c, b) {
		if (c.nodeType === 1) {
			if (!a.core.dom.hasClassName(c, b)) {
				c.className += (" " + b)
			}
		}
	}
});
STK.register("core.dom.addHTML", function(a) {
	return function(d, c) {
		if (a.IE) {
			d.insertAdjacentHTML("BeforeEnd", c)
		} else {
			var e = d.ownerDocument.createRange();
			e.setStartBefore(d);
			var b = e.createContextualFragment(c);
			d.appendChild(b)
		}
	}
});
STK.register("core.dom.sizzle", function(n) {
	var t = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,
		m = 0,
		d = Object.prototype.toString,
		s = false,
		j = true;
	[0, 0].sort(function() {
		j = false;
		return 0
	});
	var b = function(z, e, C, D) {
		C = C || [];
		e = e || document;
		var F = e;
		if (e.nodeType !== 1 && e.nodeType !== 9) {
			return []
		}
		if (!z || typeof z !== "string") {
			return C
		}
		var A = [],
			w, H, K, v, y = true,
			x = b.isXML(e),
			E = z,
			G, J, I, B;
		do {
			t.exec("");
			w = t.exec(E);
			if (w) {
				E = w[3];
				A.push(w[1]);
				if (w[2]) {
					v = w[3];
					break
				}
			}
		} while (w);
		if (A.length > 1 && o.exec(z)) {
			if (A.length === 2 && f.relative[A[0]]) {
				H = h(A[0] + A[1], e)
			} else {
				H = f.relative[A[0]] ? [e] : b(A.shift(), e);
				while (A.length) {
					z = A.shift();
					if (f.relative[z]) {
						z += A.shift()
					}
					H = h(z, H)
				}
			}
		} else {
			if (!D && A.length > 1 && e.nodeType === 9 && !x && f.match.ID.test(A[0]) && !f.match.ID.test(A[A.length - 1])) {
				G = b.find(A.shift(), e, x);
				e = G.expr ? b.filter(G.expr, G.set)[0] : G.set[0]
			}
			if (e) {
				G = D ? {
					expr: A.pop(),
					set: a(D)
				} : b.find(A.pop(), A.length === 1 && (A[0] === "~" || A[0] === "+") && e.parentNode ? e.parentNode : e, x);
				H = G.expr ? b.filter(G.expr, G.set) : G.set;
				if (A.length > 0) {
					K = a(H)
				} else {
					y = false
				}
				while (A.length) {
					J = A.pop();
					I = J;
					if (!f.relative[J]) {
						J = ""
					} else {
						I = A.pop()
					}
					if (I == null) {
						I = e
					}
					f.relative[J](K, I, x)
				}
			} else {
				K = A = []
			}
		}
		if (!K) {
			K = H
		}
		if (!K) {
			b.error(J || z)
		}
		if (d.call(K) === "[object Array]") {
			if (!y) {
				C.push.apply(C, K)
			} else {
				if (e && e.nodeType === 1) {
					for (B = 0; K[B] != null; B++) {
						if (K[B] && (K[B] === true || K[B].nodeType === 1 && b.contains(e, K[B]))) {
							C.push(H[B])
						}
					}
				} else {
					for (B = 0; K[B] != null; B++) {
						if (K[B] && K[B].nodeType === 1) {
							C.push(H[B])
						}
					}
				}
			}
		} else {
			a(K, C)
		}
		if (v) {
			b(v, F, C, D);
			b.uniqueSort(C)
		}
		return C
	};
	b.uniqueSort = function(v) {
		if (c) {
			s = j;
			v.sort(c);
			if (s) {
				for (var e = 1; e < v.length; e++) {
					if (v[e] === v[e - 1]) {
						v.splice(e--, 1)
					}
				}
			}
		}
		return v
	};
	b.matches = function(e, v) {
		return b(e, null, null, v)
	};
	b.find = function(B, e, C) {
		var A;
		if (!B) {
			return []
		}
		for (var x = 0, w = f.order.length; x < w; x++) {
			var z = f.order[x],
				y;
			if ((y = f.leftMatch[z].exec(B))) {
				var v = y[1];
				y.splice(1, 1);
				if (v.substr(v.length - 1) !== "\\") {
					y[1] = (y[1] || "").replace(/\\/g, "");
					A = f.find[z](y, e, C);
					if (A != null) {
						B = B.replace(f.match[z], "");
						break
					}
				}
			}
		}
		if (!A) {
			A = e.getElementsByTagName("*")
		}
		return {
			set: A,
			expr: B
		}
	};
	b.filter = function(F, E, I, y) {
		var w = F,
			K = [],
			C = E,
			A, e, B = E && E[0] && b.isXML(E[0]);
		while (F && E.length) {
			for (var D in f.filter) {
				if ((A = f.leftMatch[D].exec(F)) != null && A[2]) {
					var v = f.filter[D],
						J, H, x = A[1];
					e = false;
					A.splice(1, 1);
					if (x.substr(x.length - 1) === "\\") {
						continue
					}
					if (C === K) {
						K = []
					}
					if (f.preFilter[D]) {
						A = f.preFilter[D](A, C, I, K, y, B);
						if (!A) {
							e = J = true
						} else {
							if (A === true) {
								continue
							}
						}
					}
					if (A) {
						for (var z = 0;
							(H = C[z]) != null; z++) {
							if (H) {
								J = v(H, A, z, C);
								var G = y ^ !!J;
								if (I && J != null) {
									if (G) {
										e = true
									} else {
										C[z] = false
									}
								} else {
									if (G) {
										K.push(H);
										e = true
									}
								}
							}
						}
					}
					if (J !== undefined) {
						if (!I) {
							C = K
						}
						F = F.replace(f.match[D], "");
						if (!e) {
							return []
						}
						break
					}
				}
			}
			if (F === w) {
				if (e == null) {
					b.error(F)
				} else {
					break
				}
			}
			w = F
		}
		return C
	};
	b.error = function(e) {
		throw "Syntax error, unrecognized expression: " + e
	};
	var f = {
		order: ["ID", "NAME", "TAG"],
		match: {
			ID: /#((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
			CLASS: /\.((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
			NAME: /\[name=['"]*((?:[\w\u00c0-\uFFFF\-]|\\.)+)['"]*\]/,
			ATTR: /\[\s*((?:[\w\u00c0-\uFFFF\-]|\\.)+)\s*(?:(\S?=)\s*(['"]*)(.*?)\3|)\s*\]/,
			TAG: /^((?:[\w\u00c0-\uFFFF\*\-]|\\.)+)/,
			CHILD: /:(only|nth|last|first)-child(?:\((even|odd|[\dn+\-]*)\))?/,
			POS: /:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^\-]|$)/,
			PSEUDO: /:((?:[\w\u00c0-\uFFFF\-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/
		},
		leftMatch: {},
		attrMap: {
			"class": "className",
			"for": "htmlFor"
		},
		attrHandle: {
			href: function(e) {
				return e.getAttribute("href")
			}
		},
		relative: {
			"+": function(A, v) {
				var x = typeof v === "string",
					z = x && !/\W/.test(v),
					B = x && !z;
				if (z) {
					v = v.toLowerCase()
				}
				for (var w = 0, e = A.length, y; w < e; w++) {
					if ((y = A[w])) {
						while ((y = y.previousSibling) && y.nodeType !== 1) {}
						A[w] = B || y && y.nodeName.toLowerCase() === v ? y || false : y === v
					}
				}
				if (B) {
					b.filter(v, A, true)
				}
			},
			">": function(A, v) {
				var y = typeof v === "string",
					z, w = 0,
					e = A.length;
				if (y && !/\W/.test(v)) {
					v = v.toLowerCase();
					for (; w < e; w++) {
						z = A[w];
						if (z) {
							var x = z.parentNode;
							A[w] = x.nodeName.toLowerCase() === v ? x : false
						}
					}
				} else {
					for (; w < e; w++) {
						z = A[w];
						if (z) {
							A[w] = y ? z.parentNode : z.parentNode === v
						}
					}
					if (y) {
						b.filter(v, A, true)
					}
				}
			},
			"": function(x, v, z) {
				var w = m++,
					e = u,
					y;
				if (typeof v === "string" && !/\W/.test(v)) {
					v = v.toLowerCase();
					y = v;
					e = r
				}
				e("parentNode", v, w, x, y, z)
			},
			"~": function(x, v, z) {
				var w = m++,
					e = u,
					y;
				if (typeof v === "string" && !/\W/.test(v)) {
					v = v.toLowerCase();
					y = v;
					e = r
				}
				e("previousSibling", v, w, x, y, z)
			}
		},
		find: {
			ID: function(v, w, x) {
				if (typeof w.getElementById !== "undefined" && !x) {
					var e = w.getElementById(v[1]);
					return e ? [e] : []
				}
			},
			NAME: function(w, z) {
				if (typeof z.getElementsByName !== "undefined") {
					var v = [],
						y = z.getElementsByName(w[1]);
					for (var x = 0, e = y.length; x < e; x++) {
						if (y[x].getAttribute("name") === w[1]) {
							v.push(y[x])
						}
					}
					return v.length === 0 ? null : v
				}
			},
			TAG: function(e, v) {
				return v.getElementsByTagName(e[1])
			}
		},
		preFilter: {
			CLASS: function(x, v, w, e, A, B) {
				x = " " + x[1].replace(/\\/g, "") + " ";
				if (B) {
					return x
				}
				for (var y = 0, z;
					(z = v[y]) != null; y++) {
					if (z) {
						if (A ^ (z.className && (" " + z.className + " ").replace(/[\t\n]/g, " ").indexOf(x) >= 0)) {
							if (!w) {
								e.push(z)
							}
						} else {
							if (w) {
								v[y] = false
							}
						}
					}
				}
				return false
			},
			ID: function(e) {
				return e[1].replace(/\\/g, "")
			},
			TAG: function(v, e) {
				return v[1].toLowerCase()
			},
			CHILD: function(e) {
				if (e[1] === "nth") {
					var v = /(-?)(\d*)n((?:\+|-)?\d*)/.exec(e[2] === "even" && "2n" || e[2] === "odd" && "2n+1" || !/\D/.test(e[2]) && "0n+" + e[2] || e[2]);
					e[2] = (v[1] + (v[2] || 1)) - 0;
					e[3] = v[3] - 0
				}
				e[0] = m++;
				return e
			},
			ATTR: function(y, v, w, e, z, A) {
				var x = y[1].replace(/\\/g, "");
				if (!A && f.attrMap[x]) {
					y[1] = f.attrMap[x]
				}
				if (y[2] === "~=") {
					y[4] = " " + y[4] + " "
				}
				return y
			},
			PSEUDO: function(y, v, w, e, z) {
				if (y[1] === "not") {
					if ((t.exec(y[3]) || "").length > 1 || /^\w/.test(y[3])) {
						y[3] = b(y[3], null, null, v)
					} else {
						var x = b.filter(y[3], v, w, true ^ z);
						if (!w) {
							e.push.apply(e, x)
						}
						return false
					}
				} else {
					if (f.match.POS.test(y[0]) || f.match.CHILD.test(y[0])) {
						return true
					}
				}
				return y
			},
			POS: function(e) {
				e.unshift(true);
				return e
			}
		},
		filters: {
			enabled: function(e) {
				return e.disabled === false && e.type !== "hidden"
			},
			disabled: function(e) {
				return e.disabled === true
			},
			checked: function(e) {
				return e.checked === true
			},
			selected: function(e) {
				e.parentNode.selectedIndex;
				return e.selected === true
			},
			parent: function(e) {
				return !!e.firstChild
			},
			empty: function(e) {
				return !e.firstChild
			},
			has: function(w, v, e) {
				return !!b(e[3], w).length
			},
			header: function(e) {
				return (/h\d/i).test(e.nodeName)
			},
			text: function(e) {
				return "text" === e.type
			},
			radio: function(e) {
				return "radio" === e.type
			},
			checkbox: function(e) {
				return "checkbox" === e.type
			},
			file: function(e) {
				return "file" === e.type
			},
			password: function(e) {
				return "password" === e.type
			},
			submit: function(e) {
				return "submit" === e.type
			},
			image: function(e) {
				return "image" === e.type
			},
			reset: function(e) {
				return "reset" === e.type
			},
			button: function(e) {
				return "button" === e.type || e.nodeName.toLowerCase() === "button"
			},
			input: function(e) {
				return (/input|select|textarea|button/i).test(e.nodeName)
			}
		},
		setFilters: {
			first: function(v, e) {
				return e === 0
			},
			last: function(w, v, e, x) {
				return v === x.length - 1
			},
			even: function(v, e) {
				return e % 2 === 0
			},
			odd: function(v, e) {
				return e % 2 === 1
			},
			lt: function(w, v, e) {
				return v < e[3] - 0
			},
			gt: function(w, v, e) {
				return v > e[3] - 0
			},
			nth: function(w, v, e) {
				return e[3] - 0 === v
			},
			eq: function(w, v, e) {
				return e[3] - 0 === v
			}
		},
		filter: {
			PSEUDO: function(w, B, A, C) {
				var e = B[1],
					v = f.filters[e];
				if (v) {
					return v(w, A, B, C)
				} else {
					if (e === "contains") {
						return (w.textContent || w.innerText || b.getText([w]) || "").indexOf(B[3]) >= 0
					} else {
						if (e === "not") {
							var x = B[3];
							for (var z = 0, y = x.length; z < y; z++) {
								if (x[z] === w) {
									return false
								}
							}
							return true
						} else {
							b.error("Syntax error, unrecognized expression: " + e)
						}
					}
				}
			},
			CHILD: function(e, x) {
				var A = x[1],
					v = e;
				switch (A) {
					case "only":
					case "first":
						while ((v = v.previousSibling)) {
							if (v.nodeType === 1) {
								return false
							}
						}
						if (A === "first") {
							return true
						}
						v = e;
					case "last":
						while ((v = v.nextSibling)) {
							if (v.nodeType === 1) {
								return false
							}
						}
						return true;
					case "nth":
						var w = x[2],
							D = x[3];
						if (w === 1 && D === 0) {
							return true
						}
						var z = x[0],
							C = e.parentNode;
						if (C && (C.sizcache !== z || !e.nodeIndex)) {
							var y = 0;
							for (v = C.firstChild; v; v = v.nextSibling) {
								if (v.nodeType === 1) {
									v.nodeIndex = ++y
								}
							}
							C.sizcache = z
						}
						var B = e.nodeIndex - D;
						if (w === 0) {
							return B === 0
						} else {
							return (B % w === 0 && B / w >= 0)
						}
				}
			},
			ID: function(v, e) {
				return v.nodeType === 1 && v.getAttribute("id") === e
			},
			TAG: function(v, e) {
				return (e === "*" && v.nodeType === 1) || v.nodeName.toLowerCase() === e
			},
			CLASS: function(v, e) {
				return (" " + (v.className || v.getAttribute("class")) + " ").indexOf(e) > -1
			},
			ATTR: function(z, x) {
				var w = x[1],
					e = f.attrHandle[w] ? f.attrHandle[w](z) : z[w] != null ? z[w] : z.getAttribute(w),
					A = e + "",
					y = x[2],
					v = x[4];
				return e == null ? y === "!=" : y === "=" ? A === v : y === "*=" ? A.indexOf(v) >= 0 : y === "~=" ? (" " + A + " ").indexOf(v) >= 0 : !v ? A && e !== false : y === "!=" ? A !== v : y === "^=" ? A.indexOf(v) === 0 : y === "$=" ? A.substr(A.length - v.length) === v : y === "|=" ? A === v || A.substr(0, v.length + 1) === v + "-" : false
			},
			POS: function(y, v, w, z) {
				var e = v[2],
					x = f.setFilters[e];
				if (x) {
					return x(y, w, v, z)
				}
			}
		}
	};
	b.selectors = f;
	var o = f.match.POS,
		g = function(v, e) {
			return "\\" + (e - 0 + 1)
		};
	for (var q in f.match) {
		f.match[q] = new RegExp(f.match[q].source + (/(?![^\[]*\])(?![^\(]*\))/.source));
		f.leftMatch[q] = new RegExp(/(^(?:.|\r|\n)*?)/.source + f.match[q].source.replace(/\\(\d+)/g, g))
	}
	var a = function(v, e) {
		v = Array.prototype.slice.call(v, 0);
		if (e) {
			e.push.apply(e, v);
			return e
		}
		return v
	};
	try {
		Array.prototype.slice.call(document.documentElement.childNodes, 0)[0].nodeType
	} catch (p) {
		a = function(y, x) {
			var v = x || [],
				w = 0;
			if (d.call(y) === "[object Array]") {
				Array.prototype.push.apply(v, y)
			} else {
				if (typeof y.length === "number") {
					for (var e = y.length; w < e; w++) {
						v.push(y[w])
					}
				} else {
					for (; y[w]; w++) {
						v.push(y[w])
					}
				}
			}
			return v
		}
	}
	var c;
	if (document.documentElement.compareDocumentPosition) {
		c = function(v, e) {
			if (!v.compareDocumentPosition || !e.compareDocumentPosition) {
				if (v == e) {
					s = true
				}
				return v.compareDocumentPosition ? -1 : 1
			}
			var w = v.compareDocumentPosition(e) & 4 ? -1 : v === e ? 0 : 1;
			if (w === 0) {
				s = true
			}
			return w
		}
	} else {
		if ("sourceIndex" in document.documentElement) {
			c = function(v, e) {
				if (!v.sourceIndex || !e.sourceIndex) {
					if (v == e) {
						s = true
					}
					return v.sourceIndex ? -1 : 1
				}
				var w = v.sourceIndex - e.sourceIndex;
				if (w === 0) {
					s = true
				}
				return w
			}
		} else {
			if (document.createRange) {
				c = function(x, v) {
					if (!x.ownerDocument || !v.ownerDocument) {
						if (x == v) {
							s = true
						}
						return x.ownerDocument ? -1 : 1
					}
					var w = x.ownerDocument.createRange(),
						e = v.ownerDocument.createRange();
					w.setStart(x, 0);
					w.setEnd(x, 0);
					e.setStart(v, 0);
					e.setEnd(v, 0);
					var y = w.compareBoundaryPoints(Range.START_TO_END, e);
					if (y === 0) {
						s = true
					}
					return y
				}
			}
		}
	}
	b.getText = function(e) {
		var v = "",
			x;
		for (var w = 0; e[w]; w++) {
			x = e[w];
			if (x.nodeType === 3 || x.nodeType === 4) {
				v += x.nodeValue
			} else {
				if (x.nodeType !== 8) {
					v += b.getText(x.childNodes)
				}
			}
		}
		return v
	};
	(function() {
		var v = document.createElement("div"),
			w = "script" + (new Date()).getTime();
		v.innerHTML = "<a name='" + w + "'/>";
		var e = document.documentElement;
		e.insertBefore(v, e.firstChild);
		if (document.getElementById(w)) {
			f.find.ID = function(y, z, A) {
				if (typeof z.getElementById !== "undefined" && !A) {
					var x = z.getElementById(y[1]);
					return x ? x.id === y[1] || typeof x.getAttributeNode !== "undefined" && x.getAttributeNode("id").nodeValue === y[1] ? [x] : undefined : []
				}
			};
			f.filter.ID = function(z, x) {
				var y = typeof z.getAttributeNode !== "undefined" && z.getAttributeNode("id");
				return z.nodeType === 1 && y && y.nodeValue === x
			}
		}
		e.removeChild(v);
		e = v = null
	})();
	(function() {
		var e = document.createElement("div");
		e.appendChild(document.createComment(""));
		if (e.getElementsByTagName("*").length > 0) {
			f.find.TAG = function(v, z) {
				var y = z.getElementsByTagName(v[1]);
				if (v[1] === "*") {
					var x = [];
					for (var w = 0; y[w]; w++) {
						if (y[w].nodeType === 1) {
							x.push(y[w])
						}
					}
					y = x
				}
				return y
			}
		}
		e.innerHTML = "<a href='#'></a>";
		if (e.firstChild && typeof e.firstChild.getAttribute !== "undefined" && e.firstChild.getAttribute("href") !== "#") {
			f.attrHandle.href = function(v) {
				return v.getAttribute("href", 2)
			}
		}
		e = null
	})();
	if (document.querySelectorAll) {
		(function() {
			var e = b,
				w = document.createElement("div");
			w.innerHTML = "<p class='TEST'></p>";
			if (w.querySelectorAll && w.querySelectorAll(".TEST").length === 0) {
				return
			}
			b = function(A, z, x, y) {
				z = z || document;
				if (!y && z.nodeType === 9 && !b.isXML(z)) {
					try {
						return a(z.querySelectorAll(A), x)
					} catch (B) {}
				}
				return e(A, z, x, y)
			};
			for (var v in e) {
				b[v] = e[v]
			}
			w = null
		})()
	}(function() {
		var e = document.createElement("div");
		e.innerHTML = "<div class='test e'></div><div class='test'></div>";
		if (!e.getElementsByClassName || e.getElementsByClassName("e").length === 0) {
			return
		}
		e.lastChild.className = "e";
		if (e.getElementsByClassName("e").length === 1) {
			return
		}
		f.order.splice(1, 0, "CLASS");
		f.find.CLASS = function(v, w, x) {
			if (typeof w.getElementsByClassName !== "undefined" && !x) {
				return w.getElementsByClassName(v[1])
			}
		};
		e = null
	})();

	function r(v, A, z, D, B, C) {
		for (var x = 0, w = D.length; x < w; x++) {
			var e = D[x];
			if (e) {
				e = e[v];
				var y = false;
				while (e) {
					if (e.sizcache === z) {
						y = D[e.sizset];
						break
					}
					if (e.nodeType === 1 && !C) {
						e.sizcache = z;
						e.sizset = x
					}
					if (e.nodeName.toLowerCase() === A) {
						y = e;
						break
					}
					e = e[v]
				}
				D[x] = y
			}
		}
	}

	function u(v, A, z, D, B, C) {
		for (var x = 0, w = D.length; x < w; x++) {
			var e = D[x];
			if (e) {
				e = e[v];
				var y = false;
				while (e) {
					if (e.sizcache === z) {
						y = D[e.sizset];
						break
					}
					if (e.nodeType === 1) {
						if (!C) {
							e.sizcache = z;
							e.sizset = x
						}
						if (typeof A !== "string") {
							if (e === A) {
								y = true;
								break
							}
						} else {
							if (b.filter(A, [e]).length > 0) {
								y = e;
								break
							}
						}
					}
					e = e[v]
				}
				D[x] = y
			}
		}
	}
	b.contains = document.compareDocumentPosition ? function(v, e) {
		return !!(v.compareDocumentPosition(e) & 16)
	} : function(v, e) {
		return v !== e && (v.contains ? v.contains(e) : true)
	};
	b.isXML = function(e) {
		var v = (e ? e.ownerDocument || e : 0).documentElement;
		return v ? v.nodeName !== "HTML" : false
	};
	var h = function(e, B) {
		var x = [],
			y = "",
			z, w = B.nodeType ? [B] : B;
		while ((z = f.match.PSEUDO.exec(e))) {
			y += z[0];
			e = e.replace(f.match.PSEUDO, "")
		}
		e = f.relative[e] ? e + "*" : e;
		for (var A = 0, v = w.length; A < v; A++) {
			b(e, w[A], x)
		}
		return b.filter(y, x)
	};
	return b
});
STK.register("core.dom.builder", function(a) {
	function b(m, f) {
		if (f) {
			return f
		}
		var e, h = /\<(\w+)[^>]*\s+node-type\s*=\s*([\'\"])?(\w+)\2.*?>/g;
		var g = {};
		var j, d, c;
		while ((e = h.exec(m))) {
			d = e[1];
			j = e[3];
			c = d + "[node-type=" + j + "]";
			g[j] = g[j] == null ? [] : g[j];
			if (!a.core.arr.inArray(c, g[j])) {
				g[j].push(d + "[node-type=" + j + "]")
			}
		}
		return g
	}
	return function(g, f) {
		var c = a.core.func.getType(g) == "string";
		var m = b(c ? g : g.innerHTML, f);
		var d = g;
		if (c) {
			d = a.C("div");
			d.innerHTML = g
		}
		var n, j, h;
		h = a.core.dom.sizzle("[node-type]", d);
		j = {};
		for (n in m) {
			j[n] = a.core.dom.sizzle.matches(m[n].toString(), h)
		}
		var e = g;
		if (c) {
			e = a.C("buffer");
			while (d.children[0]) {
				e.appendChild(d.children[0])
			}
		}
		return {
			box: e,
			list: j
		}
	}
});
STK.register("core.obj.beget", function(b) {
	var a = function() {};
	return function(c) {
		a.prototype = c;
		return new a()
	}
});
STK.register("core.dom.setStyle", function(a) {
	return function(b, c, d) {
		if (a.IE) {
			switch (c) {
				case "opacity":
					b.style.filter = "alpha(opacity=" + (d * 100) + ")";
					if (!b.currentStyle || !b.currentStyle.hasLayout) {
						b.style.zoom = 1
					}
					break;
				case "float":
					c = "styleFloat";
				default:
					b.style[c] = d
			}
		} else {
			if (c == "float") {
				c = "cssFloat"
			}
			b.style[c] = d
		}
	}
});
STK.register("core.dom.insertAfter", function(a) {
	return function(c, d) {
		var b = d.parentNode;
		if (b.lastChild == d) {
			b.appendChild(c)
		} else {
			b.insertBefore(c, d.nextSibling)
		}
	}
});
STK.register("core.dom.insertBefore", function(a) {
	return function(c, d) {
		var b = d.parentNode;
		b.insertBefore(c, d)
	}
});
STK.register("core.dom.removeClassName", function(a) {
	return function(c, b) {
		if (c.nodeType === 1) {
			if (a.core.dom.hasClassName(c, b)) {
				c.className = c.className.replace(new RegExp("\\b" + b + "\\b"), " ")
			}
		}
	}
});
STK.register("core.dom.trimNode", function(a) {
	return function(c) {
		var d = c.childNodes;
		for (var b = 0; b < d.length; b++) {
			if (d[b].nodeType == 3 || d[b].nodeType == 8) {
				c.removeChild(d[b])
			}
		}
	}
});
STK.register("core.dom.removeNode", function(a) {
	return function(b) {
		b = a.E(b) || b;
		try {
			b.parentNode.removeChild(b)
		} catch (c) {}
	}
});
STK.register("core.evt.addEvent", function(a) {
	return function(b, e, d) {
		var c = a.E(b);
		if (c == null) {
			return false
		}
		e = e || "click";
		if ((typeof d).toLowerCase() != "function") {
			return
		}
		if (c.addEventListener) {
			c.addEventListener(e, d, false)
		} else {
			if (c.attachEvent) {
				c.attachEvent("on" + e, d)
			} else {
				c["on" + e] = d
			}
		}
		return true
	}
});
STK.register("core.evt.removeEvent", function(a) {
	return function(c, e, d, b) {
		var f = a.E(c);
		if (f == null) {
			return false
		}
		if (typeof d != "function") {
			return false
		}
		if (f.removeEventListener) {
			f.removeEventListener(e, d, b)
		} else {
			if (f.detachEvent) {
				f.detachEvent("on" + e, d)
			} else {
				f["on" + e] = null
			}
		}
		return true
	}
});
STK.register("core.evt.fireEvent", function(a) {
	return function(c, d) {
		_el = a.E(c);
		if (a.IE) {
			_el.fireEvent("on" + d)
		} else {
			var b = document.createEvent("HTMLEvents");
			b.initEvent(d, true, true);
			_el.dispatchEvent(b)
		}
	}
});
STK.register("core.util.scrollPos", function(a) {
	return function(d) {
		d = d || document;
		var b = d.documentElement;
		var c = d.body;
		return {
			top: Math.max(window.pageYOffset || 0, b.scrollTop, c.scrollTop),
			left: Math.max(window.pageXOffset || 0, b.scrollLeft, c.scrollLeft)
		}
	}
});
STK.register("core.util.browser", function(h) {
	var a = navigator.userAgent.toLowerCase();
	var o = window.external || "";
	var c, d, f, p, g;
	var b = function(e) {
		var m = 0;
		return parseFloat(e.replace(/\./g, function() {
			return (m++ == 1) ? "" : "."
		}))
	};
	try {
		if ((/windows|win32/i).test(a)) {
			g = "windows"
		} else {
			if ((/macintosh/i).test(a)) {
				g = "macintosh"
			} else {
				if ((/rhino/i).test(a)) {
					g = "rhino"
				}
			}
		}
		if ((d = a.match(/applewebkit\/([^\s]*)/)) && d[1]) {
			c = "webkit";
			p = b(d[1])
		} else {
			if ((d = a.match(/presto\/([\d.]*)/)) && d[1]) {
				c = "presto";
				p = b(d[1])
			} else {
				if (d = a.match(/msie\s([^;]*)/)) {
					c = "trident";
					p = 1;
					if ((d = a.match(/trident\/([\d.]*)/)) && d[1]) {
						p = b(d[1])
					}
				} else {
					if (/gecko/.test(a)) {
						c = "gecko";
						p = 1;
						if ((d = a.match(/rv:([\d.]*)/)) && d[1]) {
							p = b(d[1])
						}
					}
				}
			}
		}
		if (/world/.test(a)) {
			f = "world"
		} else {
			if (/360se/.test(a)) {
				f = "360"
			} else {
				if ((/maxthon/.test(a)) || typeof o.max_version == "number") {
					f = "maxthon"
				} else {
					if (/tencenttraveler\s([\d.]*)/.test(a)) {
						f = "tt"
					} else {
						if (/se\s([\d.]*)/.test(a)) {
							f = "sogou"
						}
					}
				}
			}
		}
	} catch (n) {}
	var j = {
		OS: g,
		CORE: c,
		Version: p,
		EXTRA: (f ? f : false),
		IE: /msie/.test(a),
		OPERA: /opera/.test(a),
		MOZ: /gecko/.test(a) && !/(compatible|webkit)/.test(a),
		IE5: /msie 5 /.test(a),
		IE55: /msie 5.5/.test(a),
		IE6: /msie 6/.test(a),
		IE7: /msie 7/.test(a),
		IE8: /msie 8/.test(a),
		IE9: /msie 9/.test(a),
		SAFARI: !/chrome\/([\d.]*)/.test(a) && /\/([\d.]*) safari/.test(a),
		CHROME: /chrome\/([\d.]*)/.test(a),
		IPAD: /\(ipad/i.test(a),
		IPHONE: /\(iphone/i.test(a),
		ITOUCH: /\(itouch/i.test(a),
		MOBILE: /mobile/i.test(a)
	};
	return j
});
STK.register("core.dom.position", function(c) {
	var a = function(g) {
		var h, f, e, d, m, j;
		h = g.getBoundingClientRect();
		f = c.core.util.scrollPos();
		e = g.ownerDocument.body;
		d = g.ownerDocument.documentElement;
		m = d.clientTop || e.clientTop || 0;
		j = d.clientLeft || e.clientLeft || 0;
		return {
			l: parseInt(h.left + f.left - j, 10) || 0,
			t: parseInt(h.top + f.top - m, 10) || 0
		}
	};
	var b = function(e, d) {
		var f;
		f = [e.offsetLeft, e.offsetTop];
		parent = e.offsetParent;
		if (parent !== e && parent !== d) {
			while (parent) {
				f[0] += parent.offsetLeft;
				f[1] += parent.offsetTop;
				parent = parent.offsetParent
			}
		}
		if (c.core.util.browser.OPERA != -1 || (c.core.util.browser.SAFARI != -1 && e.style.position == "absolute")) {
			f[0] -= document.body.offsetLeft;
			f[1] -= document.body.offsetTop
		}
		if (e.parentNode) {
			parent = e.parentNode
		} else {
			parent = null
		}
		while (parent && !/^body|html$/i.test(parent.tagName) && parent !== d) {
			if (parent.style.display.search(/^inline|table-row.*$/i)) {
				f[0] -= parent.scrollLeft;
				f[1] -= parent.scrollTop
			}
			parent = parent.parentNode
		}
		return {
			l: parseInt(f[0], 10),
			t: parseInt(f[1], 10)
		}
	};
	return function(f, d) {
		if (f == document.body) {
			return false
		}
		if (f.parentNode == null) {
			return false
		}
		if (f.style.display == "none") {
			return false
		}
		var e = c.core.obj.parseParam({
			parent: null
		}, d);
		if (f.getBoundingClientRect) {
			if (e.parent) {
				var h = a(f);
				var g = a(e.parent);
				return {
					l: h.l - g.l,
					t: h.t - g.t
				}
			} else {
				return a(f)
			}
		} else {
			return b(f, e.parent || document.body)
		}
	}
});
STK.register("core.dom.setXY", function(a) {
	return function(b, f) {
		var c = a.core.dom.getStyle(b, "position");
		if (c == "static") {
			a.core.dom.setStyle(b, "position", "relative");
			c = "relative"
		}
		var e = a.core.dom.position(b);
		if (e == false) {
			return
		}
		var d = {
			l: parseInt(a.core.dom.getStyle(b, "left"), 10),
			t: parseInt(a.core.dom.getStyle(b, "top"), 10)
		};
		if (isNaN(d.l)) {
			d.l = (c == "relative") ? 0 : b.offsetLeft
		}
		if (isNaN(d.t)) {
			d.t = (c == "relative") ? 0 : b.offsetTop
		}
		if (f.l != null) {
			b.style.left = f.l - e.l + d.l + "px"
		}
		if (f.t != null) {
			b.style.top = f.t - e.t + d.t + "px"
		}
	}
});
STK.register("core.str.encodeHTML", function(a) {
	return function(b) {
		if (typeof b !== "string") {
			throw "encodeHTML need a string as parameter"
		}
		return b.replace(/\&/g, "&amp;").replace(/"/g, "&quot;").replace(/\</g, "&lt;").replace(/\>/g, "&gt;").replace(/\'/g, "&#39;").replace(/\u00A0/g, "&nbsp;").replace(/(\u0020|\u000B|\u2028|\u2029|\f)/g, "&#32;")
	}
});
STK.register("core.str.decodeHTML", function(a) {
	return function(b) {
		if (typeof b !== "string") {
			throw "decodeHTML need a string as parameter"
		}
		return b.replace(/&quot;/g, '"').replace(/&lt;/g, "<").replace(/&gt;/g, ">").replace(/&#39/g, "'").replace(/&nbsp;/g, "\u00A0").replace(/&#32/g, "\u0020").replace(/&amp;/g, "&")
	}
});
STK.register("core.dom.cascadeNode", function(a) {
	return function(d) {
		var c = {};
		var e = d.style.display || "";
		e = (e === "none" ? "" : e);
		var b = [];
		c.setStyle = function(g, f) {
			a.core.dom.setStyle(d, g, f);
			if (g === "display") {
				e = (f === "none" ? "" : f)
			}
			return c
		};
		c.insertAfter = function(f) {
			a.core.dom.insertAfter(f, d);
			return c
		};
		c.insertBefore = function(f) {
			a.core.dom.insertBefore(f, d);
			return c
		};
		c.addClassName = function(f) {
			a.core.dom.addClassName(d, f);
			return c
		};
		c.removeClassName = function(f) {
			a.core.dom.removeClassName(d, f);
			return c
		};
		c.trimNode = function() {
			a.core.dom.trimNode(d);
			return c
		};
		c.removeNode = function() {
			a.core.dom.removeNode(d);
			return c
		};
		c.on = function(h, j) {
			for (var g = 0, f = b.length; g < f; g += 1) {
				if (b[g]["fn"] === j && b[g]["type"] === h) {
					return c
				}
			}
			b.push({
				fn: j,
				type: h
			});
			a.core.evt.addEvent(d, h, j);
			return c
		};
		c.unon = function(h, j) {
			for (var g = 0, f = b.length; g < f; g += 1) {
				if (b[g]["fn"] === j && b[g]["type"] === h) {
					a.core.evt.removeEvent(d, j, h);
					b.splice(g, 1);
					break
				}
			}
			return c
		};
		c.fire = function(f) {
			a.core.evt.fireEvent(f, d);
			return c
		};
		c.appendChild = function(f) {
			d.appendChild(f);
			return c
		};
		c.removeChild = function(f) {
			d.removeChild(f);
			return c
		};
		c.toggle = function() {
			if (d.style.display === "none") {
				d.style.display = e
			} else {
				d.style.display = "none"
			}
			return c
		};
		c.show = function() {
			if (d.style.display === "none") {
				if (e === "none") {
					d.style.display = ""
				} else {
					d.style.display = e
				}
			}
			return c
		};
		c.hidd = function() {
			if (d.style.display !== "none") {
				d.style.display = "none"
			}
			return c
		};
		c.hide = c.hidd;
		c.scrollTo = function(f, g) {
			if (f === "left") {
				d.scrollLeft = g
			}
			if (f === "top") {
				d.scrollTop = g
			}
			return c
		};
		c.replaceChild = function(f, g) {
			d.replaceChild(f, g);
			return c
		};
		c.position = function(f) {
			if (f !== undefined) {
				a.core.dom.setXY(d, f)
			}
			return a.core.dom.position(d)
		};
		c.setPosition = function(f) {
			if (f !== undefined) {
				a.core.dom.setXY(d, f)
			}
			return c
		};
		c.getPosition = function(f) {
			return a.core.dom.position(d)
		};
		c.html = function(f) {
			if (f !== undefined) {
				d.innerHTML = f
			}
			return d.innerHTML
		};
		c.setHTML = function(f) {
			if (f !== undefined) {
				d.innerHTML = f
			}
			return c
		};
		c.getHTML = function() {
			return d.innerHTML
		};
		c.text = function(f) {
			if (f !== undefined) {
				d.innerHTML = a.core.str.encodeHTML(f)
			}
			return a.core.str.decodeHTML(d.innerHTML)
		};
		c.ttext = c.text;
		c.setText = function(f) {
			if (f !== undefined) {
				d.innerHTML = a.core.str.encodeHTML(f)
			}
			return c
		};
		c.getText = function() {
			return a.core.str.decodeHTML(d.innerHTML)
		};
		c.get = function(f) {
			if (f === "node") {
				return d
			}
			return a.core.dom.getStyle(d, f)
		};
		c.getStyle = function(f) {
			return a.core.dom.getStyle(d, f)
		};
		c.getOriginNode = function() {
			return d
		};
		c.destroy = function() {
			for (var g = 0, f = b; g < f; g += 1) {
				a.core.evt.removeEvent(d, b[g]["fn"], b[g]["type"])
			}
			e = null;
			b = null;
			d = null
		};
		return c
	}
});
STK.register("core.dom.contains", function(a) {
	return function(b, c) {
		if (b === c) {
			return false
		} else {
			if (b.compareDocumentPosition) {
				return ((b.compareDocumentPosition(c) & 16) === 16)
			} else {
				if (b.contains && c.nodeType === 1) {
					return b.contains(c)
				} else {
					while (c = c.parentNode) {
						if (b === c) {
							return true
						}
					}
				}
			}
		}
		return false
	}
});
STK.register("core.util.hideContainer", function(c) {
	var d;
	var a = function() {
		if (d) {
			return
		}
		d = c.C("div");
		d.style.cssText = "position:absolute;top:-9999px;left:-9999px;";
		document.getElementsByTagName("head")[0].appendChild(d)
	};
	var b = {
		appendChild: function(e) {
			if (c.core.dom.isNode(e)) {
				a();
				d.appendChild(e)
			}
		},
		removeChild: function(e) {
			if (c.core.dom.isNode(e)) {
				d && d.removeChild(e)
			}
		}
	};
	return b
});
STK.register("core.dom.getSize", function(b) {
	var a = function(d) {
		if (!b.core.dom.isNode(d)) {
			throw "core.dom.getSize need Element as first parameter"
		}
		return {
			width: d.offsetWidth,
			height: d.offsetHeight
		}
	};
	var c = function(e) {
		var d = null;
		if (e.style.display === "none") {
			e.style.visibility = "hidden";
			e.style.display = "";
			d = a(e);
			e.style.display = "none";
			e.style.visibility = "visible"
		} else {
			d = a(e)
		}
		return d
	};
	return function(e) {
		var d = {};
		if (!e.parentNode) {
			b.core.util.hideContainer.appendChild(e);
			d = c(e);
			b.core.util.hideContainer.removeChild(e)
		} else {
			d = c(e)
		}
		return d
	}
});
STK.register("core.dom.textSelectArea", function(a) {
	return function(b) {
		var e = {
			start: 0,
			len: 0
		};
		if (typeof b.selectionStart === "number") {
			e.start = b.selectionStart;
			e.len = b.selectionEnd - b.selectionStart
		} else {
			if (typeof document.selection !== undefined) {
				var d = document.selection.createRange();
				if (b.tagName === "INPUT") {
					var c = b.createTextRange()
				} else {
					if (b.tagName === "TEXTAREA") {
						var c = d.duplicate();
						c.moveToElementText(b)
					}
				}
				c.setEndPoint("EndToStart", d);
				e.start = c.text.length;
				e.len = d.text.length;
				d = null;
				c = null
			}
		}
		return e
	}
});
STK.register("core.dom.insertHTML", function(a) {
	return function(e, d, c) {
		e = a.E(e) || document.body;
		c = c ? c.toLowerCase() : "beforeend";
		if (e.insertAdjacentHTML) {
			switch (c) {
				case "beforebegin":
					e.insertAdjacentHTML("BeforeBegin", d);
					return e.previousSibling;
				case "afterbegin":
					e.insertAdjacentHTML("AfterBegin", d);
					return e.firstChild;
				case "beforeend":
					e.insertAdjacentHTML("BeforeEnd", d);
					return e.lastChild;
				case "afterend":
					e.insertAdjacentHTML("AfterEnd", d);
					return e.nextSibling
			}
			throw 'Illegal insertion point -> "' + c + '"'
		} else {
			var b = e.ownerDocument.createRange();
			var f;
			switch (c) {
				case "beforebegin":
					b.setStartBefore(e);
					f = b.createContextualFragment(d);
					e.parentNode.insertBefore(f, e);
					return e.previousSibling;
				case "afterbegin":
					if (e.firstChild) {
						b.setStartBefore(e.firstChild);
						f = b.createContextualFragment(d);
						e.insertBefore(f, e.firstChild);
						return e.firstChild
					} else {
						e.innerHTML = d;
						return e.firstChild
					}
					break;
				case "beforeend":
					if (e.lastChild) {
						b.setStartAfter(e.lastChild);
						f = b.createContextualFragment(d);
						e.appendChild(f);
						return e.lastChild
					} else {
						e.innerHTML = d;
						return e.lastChild
					}
					break;
				case "afterend":
					b.setStartAfter(e);
					f = b.createContextualFragment(d);
					e.parentNode.insertBefore(f, e.nextSibling);
					return e.nextSibling
			}
			throw 'Illegal insertion point -> "' + c + '"'
		}
	}
});
STK.register("core.dom.insertElement", function(a) {
	return function(d, c, b) {
		d = a.E(d) || document.body;
		b = b ? b.toLowerCase() : "beforeend";
		switch (b) {
			case "beforebegin":
				d.parentNode.insertBefore(c, d);
				break;
			case "afterbegin":
				d.insertBefore(c, d.firstChild);
				break;
			case "beforeend":
				d.appendChild(c);
				break;
			case "afterend":
				if (d.nextSibling) {
					d.parentNode.insertBefore(c, d.nextSibling)
				} else {
					d.parentNode.appendChild(c)
				}
				break
		}
	}
});
STK.register("core.dom.next", function(a) {
	return function(c) {
		var b = c.nextSibling;
		if (!b) {
			return null
		} else {
			if (b.nodeType !== 1) {
				b = arguments.callee(b)
			}
		}
		return b
	}
});
STK.register("core.dom.prev", function(a) {
	return function(c) {
		var b = c.previousSibling;
		if (!b) {
			return null
		} else {
			if (b.nodeType !== 1) {
				b = arguments.callee(b)
			}
		}
		return b
	}
});
STK.register("core.dom.replaceNode", function(a) {
	return function(c, b) {
		if (c == null || b == null) {
			throw "replaceNode need node as paramster"
		}
		b.parentNode.replaceChild(c, b)
	}
});
STK.register("core.dom.ready", function(g) {
	var c = [];
	var o = false;
	var n = g.core.func.getType;
	var h = g.core.util.browser;
	var f = g.core.evt.addEvent;
	var j = function() {
		if (!o) {
			if (document.readyState === "complete") {
				return true
			}
		}
		return o
	};
	var d = function() {
		if (o == true) {
			return
		}
		o = true;
		for (var q = 0, p = c.length; q < p; q++) {
			if (n(c[q]) === "function") {
				try {
					c[q].call()
				} catch (r) {}
			}
		}
		c = []
	};
	var a = function() {
		if (j()) {
			d();
			return
		}
		try {
			document.documentElement.doScroll("left")
		} catch (p) {
			setTimeout(arguments.callee, 25);
			return
		}
		d()
	};
	var b = function() {
		if (j()) {
			d();
			return
		}
		setTimeout(arguments.callee, 25)
	};
	var e = function() {
		f(document, "DOMContentLoaded", d)
	};
	var m = function() {
		f(window, "load", d)
	};
	if (!j()) {
		if (g.IE && window === window.top) {
			a()
		}
		e();
		b();
		m()
	}
	return function(p) {
		if (j()) {
			if (n(p) === "function") {
				p.call()
			}
		} else {
			c.push(p)
		}
	}
});
STK.register("core.dom.selector", function(a) {
	var b = function(d, j, h, e) {
		var g = [];
		if (typeof d === "string") {
			lis = a.core.dom.sizzle(d, j, h, e);
			for (var f = 0, c = lis.length; f < c; f += 1) {
				g[f] = lis[f]
			}
		} else {
			if (a.core.dom.isNode(d)) {
				if (j) {
					if (a.core.dom.contains(j, d)) {
						g = [d]
					}
				} else {
					g = [d]
				}
			} else {
				if (a.core.arr.isArray(d)) {
					if (j) {
						for (var f = 0, c = d.length; f < c; f += 1) {
							if (a.core.dom.contains(j, d[f])) {
								g.push(d[f])
							}
						}
					} else {
						g = d
					}
				}
			}
		}
		return g
	};
	return function(c, f, e, d) {
		var g = b.apply(window, arguments);
		g.on = function(n, m) {
			for (var j = 0, h = g.length; j < h; j += 1) {
				a.core.evt.addEvent(g[j], n, m)
			}
			return g
		};
		g.css = function(n, j) {
			for (var m = 0, h = g.length; m < h; m += 1) {
				a.core.dom.setStyle(g[m], n, j)
			}
			return g
		};
		g.show = function() {
			for (var j = 0, h = g.length; j < h; j += 1) {
				g[j].style.display = ""
			}
			return g
		};
		g.hidd = function() {
			for (var j = 0, h = g.length; j < h; j += 1) {
				g[j].style.display = "none"
			}
			return g
		};
		g.hide = g.hidd;
		return g
	}
});
STK.register("core.dom.selectText", function() {
	return function(c, d) {
		var e = d.start;
		var a = d.len || 0;
		c.focus();
		if (c.setSelectionRange) {
			c.setSelectionRange(e, e + a)
		} else {
			if (c.createTextRange) {
				var b = c.createTextRange();
				b.collapse(1);
				b.moveStart("character", e);
				b.moveEnd("character", a);
				b.select()
			}
		}
	}
});
STK.register("core.dom.setStyles", function(a) {
	return function(b, c, d) {
		if (!a.core.arr.isArray(b)) {
			var b = [b]
		}
		for (i = 0, l = b.length; i < l; i++) {
			a.core.dom.setStyle(b[i], c, d)
		}
		return b
	}
});
STK.register("core.util.getUniqueKey", function(c) {
	var a = (new Date()).getTime().toString(),
		b = 1;
	return function() {
		return a + (b++)
	}
});
STK.register("core.dom.uniqueID", function(a) {
	return function(b) {
		return b && (b.uniqueID || (b.uniqueID = a.core.util.getUniqueKey()))
	}
});
STK.register("core.evt.custEvent", function(c) {
	var a = "__custEventKey__",
		d = 1,
		e = {},
		b = function(h, g) {
			var f = (typeof h == "number") ? h : h[a];
			return (f && e[f]) && {
				obj: (typeof g == "string" ? e[f][g] : e[f]),
				key: f
			}
		};
	return {
		define: function(m, h) {
			if (m && h) {
				var g = (typeof m == "number") ? m : m[a] || (m[a] = d++),
					j = e[g] || (e[g] = {});
				h = [].concat(h);
				for (var f = 0; f < h.length; f++) {
					j[h[f]] || (j[h[f]] = [])
				}
				return g
			}
		},
		undefine: function(j, h) {
			if (j) {
				var g = (typeof j == "number") ? j : j[a];
				if (g && e[g]) {
					if (h) {
						h = [].concat(h);
						for (var f = 0; f < h.length; f++) {
							if (h[f] in e[g]) {
								delete e[g][h[f]]
							}
						}
					} else {
						delete e[g]
					}
				}
			}
		},
		add: function(m, g, f, h) {
			if (m && typeof g == "string" && f) {
				var j = b(m, g);
				if (!j || !j.obj) {
					throw "custEvent (" + g + ") is undefined !"
				}
				j.obj.push({
					fn: f,
					data: h
				});
				return j.key
			}
		},
		once: function(m, g, f, h) {
			if (m && typeof g == "string" && f) {
				var j = b(m, g);
				if (!j || !j.obj) {
					throw "custEvent (" + g + ") is undefined !"
				}
				j.obj.push({
					fn: f,
					data: h,
					once: true
				});
				return j.key
			}
		},
		remove: function(n, j, h) {
			if (n) {
				var m = b(n, j),
					o, f;
				if (m && (o = m.obj)) {
					if (c.core.arr.isArray(o)) {
						if (h) {
							var g = 0;
							while (o[g]) {
								if (o[g].fn === h) {
									break
								}
								g++
							}
							o.splice(g, 1)
						} else {
							o.splice(0, o.length)
						}
					} else {
						for (var g in o) {
							o[g] = []
						}
					}
					return m.key
				}
			}
		},
		fire: function(g, p, n) {
			if (g && typeof p == "string") {
				var f = b(g, p),
					m;
				if (f && (m = f.obj)) {
					if (!c.core.arr.isArray(n)) {
						n = n != undefined ? [n] : []
					}
					for (var h = m.length - 1; h > -1 && m[h]; h--) {
						var q = m[h].fn;
						var o = m[h].once;
						if (q && q.apply) {
							try {
								q.apply(g, [{
									type: p,
									data: m[h].data
								}].concat(n));
								if (o) {
									m.splice(h, 1)
								}
							} catch (j) {
								c.log("[error][custEvent]" + j.message)
							}
						}
					}
					return f.key
				}
			}
		},
		destroy: function() {
			e = {};
			d = 1
		}
	}
});
STK.register("core.str.trim", function(a) {
	return function(e) {
		if (typeof e !== "string") {
			throw "trim need a string as parameter"
		}
		var b = e.length;
		var d = 0;
		var c = /(\u3000|\s|\t|\u00A0)/;
		while (d < b) {
			if (!c.test(e.charAt(d))) {
				break
			}
			d += 1
		}
		while (b > d) {
			if (!c.test(e.charAt(b - 1))) {
				break
			}
			b -= 1
		}
		return e.slice(d, b)
	}
});
STK.register("core.json.queryToJson", function(a) {
	return function(d, h) {
		var m = a.core.str.trim(d).split("&");
		var j = {};
		var c = function(o) {
			if (h) {
				return decodeURIComponent(o)
			} else {
				return o
			}
		};
		for (var f = 0, g = m.length; f < g; f++) {
			if (m[f]) {
				var e = m[f].split("=");
				var b = e[0];
				var n = e[1];
				if (e.length < 2) {
					n = b;
					b = "$nullName"
				}
				if (!j[b]) {
					j[b] = c(n)
				} else {
					if (a.core.arr.isArray(j[b]) != true) {
						j[b] = [j[b]]
					}
					j[b].push(c(n))
				}
			}
		}
		return j
	}
});
STK.register("core.evt.getEvent", function(a) {
	return function() {
		if (a.IE) {
			return window.event
		} else {
			if (window.event) {
				return window.event
			}
			var c = arguments.callee.caller;
			var b;
			var d = 0;
			while (c != null && d < 40) {
				b = c.arguments[0];
				if (b && (b.constructor == Event || b.constructor == MouseEvent || b.constructor == KeyboardEvent)) {
					return b
				}
				d++;
				c = c.caller
			}
			return b
		}
	}
});
STK.register("core.evt.fixEvent", function(a) {
	return function(b) {
		b = b || a.core.evt.getEvent();
		if (!b.target) {
			b.target = b.srcElement;
			b.pageX = b.x;
			b.pageY = b.y
		}
		if (typeof b.layerX == "undefined") {
			b.layerX = b.offsetX
		}
		if (typeof b.layerY == "undefined") {
			b.layerY = b.offsetY
		}
		return b
	}
});
STK.register("core.obj.isEmpty", function(a) {
	return function(e, d) {
		var c = true;
		for (var b in e) {
			if (d) {
				c = false;
				break
			} else {
				if (e.hasOwnProperty(b)) {
					c = false;
					break
				}
			}
		}
		return c
	}
});
STK.register("core.evt.delegatedEvent", function(b) {
	var a = function(f, e) {
		for (var d = 0, c = f.length; d < c; d += 1) {
			if (b.core.dom.contains(f[d], e)) {
				return true
			}
		}
		return false
	};
	return function(d, g) {
		if (!b.core.dom.isNode(d)) {
			throw "core.evt.delegatedEvent need an Element as first Parameter"
		}
		if (!g) {
			g = []
		}
		if (b.core.arr.isArray(g)) {
			g = [g]
		}
		var c = {};
		var f = function(p) {
			var j = b.core.evt.fixEvent(p);
			var o = j.target;
			var n = p.type;
			var q = function() {
				var t, r, s;
				t = o.getAttribute("action-target");
				if (t) {
					r = b.core.dom.sizzle(t, d);
					if (r.length) {
						s = j.target = r[0]
					}
				}
				q = b.core.func.empty;
				return s
			};
			var h = function() {
				var r = q() || o;
				if (c[n] && c[n][m]) {
					return c[n][m]({
						evt: j,
						el: r,
						box: d,
						data: b.core.json.queryToJson(r.getAttribute("action-data") || "")
					})
				} else {
					return true
				}
			};
			if (a(g, o)) {
				return false
			} else {
				if (!b.core.dom.contains(d, o)) {
					return false
				} else {
					var m = null;
					while (o && o !== d) {
						m = o.getAttribute("action-type");
						if (m && h() === false) {
							break
						}
						o = o.parentNode
					}
				}
			}
		};
		var e = {};
		e.add = function(m, n, j) {
			if (!c[n]) {
				c[n] = {};
				b.core.evt.addEvent(d, n, f)
			}
			var h = c[n];
			h[m] = j
		};
		e.remove = function(h, j) {
			if (c[j]) {
				delete c[j][h];
				if (b.core.obj.isEmpty(c[j])) {
					delete c[j];
					b.core.evt.removeEvent(d, j, f)
				}
			}
		};
		e.pushExcept = function(h) {
			g.push(h)
		};
		e.removeExcept = function(m) {
			if (!m) {
				g = []
			} else {
				for (var j = 0, h = g.length; j < h; j += 1) {
					if (g[j] === m) {
						g.splice(j, 1)
					}
				}
			}
		};
		e.clearExcept = function(h) {
			g = []
		};
		e.destroy = function() {
			for (k in c) {
				for (l in c[k]) {
					delete c[k][l]
				}
				delete c[k];
				b.core.evt.removeEvent(d, k, f)
			}
		};
		return e
	}
});
STK.register("core.evt.getActiveElement", function(a) {
	return function() {
		try {
			var b = a.core.evt.getEvent();
			return document.activeElement ? document.activeElement : b.explicitOriginalTarget
		} catch (c) {
			return document.body
		}
	}
});
STK.register("core.evt.hitTest", function(a) {
	function b(e) {
		var d = STK.E(e);
		var f = a.core.dom.position(d);
		var c = {
			left: f.l,
			top: f.t,
			right: f.l + d.offsetWidth,
			bottom: f.t + d.offsetHeight
		};
		return c
	}
	return function(h, d) {
		var c = b(h);
		if (d == null) {
			d = a.core.evt.getEvent()
		} else {
			if (d.nodeType == 1) {
				var g = b(d);
				if (c.right > g.left && c.left < g.right && c.bottom > g.top && c.top < g.bottom) {
					return true
				}
				return false
			} else {
				if (d.clientX == null) {
					throw "core.evt.hitTest: [" + d + ":oEvent] is not a valid value"
				}
			}
		}
		var j = a.core.util.scrollPos();
		var f = d.clientX + j.left;
		var e = d.clientY + j.top;
		return (f >= c.left && f <= c.right) && (e >= c.top && e <= c.bottom) ? true : false
	}
});
STK.register("core.evt.stopEvent", function(a) {
	return function(c) {
		var b = c ? c : a.core.evt.getEvent();
		if (a.IE) {
			b.cancelBubble = true;
			b.returnValue = false
		} else {
			b.preventDefault();
			b.stopPropagation()
		}
		return false
	}
});
STK.register("core.evt.preventDefault", function(a) {
	return function(c) {
		var b = c ? c : a.core.evt.getEvent();
		if (a.IE) {
			b.returnValue = false
		} else {
			b.preventDefault()
		}
	}
});
STK.register("core.evt.hotKey", function(d) {
	var c = d.core.dom.uniqueID;
	var b = {
		reg1: /^keypress|keydown|keyup$/,
		keyMap: {
			27: "esc",
			9: "tab",
			32: "space",
			13: "enter",
			8: "backspace",
			145: "scrollclock",
			20: "capslock",
			144: "numlock",
			19: "pause",
			45: "insert",
			36: "home",
			46: "delete",
			35: "end",
			33: "pageup",
			34: "pagedown",
			37: "left",
			38: "up",
			39: "right",
			40: "down",
			112: "f1",
			113: "f2",
			114: "f3",
			115: "f4",
			116: "f5",
			117: "f6",
			118: "f7",
			119: "f8",
			120: "f9",
			121: "f10",
			122: "f11",
			123: "f12",
			191: "/",
			17: "ctrl",
			16: "shift",
			109: "-",
			107: "=",
			219: "[",
			221: "]",
			220: "\\",
			222: "'",
			187: "=",
			188: ",",
			189: "-",
			190: ".",
			191: "/",
			96: "0",
			97: "1",
			98: "2",
			99: "3",
			100: "4",
			101: "5",
			102: "6",
			103: "7",
			104: "8",
			105: "9",
			106: "*",
			110: ".",
			111: "/"
		},
		keyEvents: {}
	};
	b.preventDefault = function() {
		this.returnValue = false
	};
	b.handler = function(g) {
		g = g || window.event;
		if (!g.target) {
			g.target = g.srcElement || document
		}
		if (!g.which && ((g.charCode || g.charCode === 0) ? g.charCode : g.keyCode)) {
			g.which = g.charCode || g.keyCode
		}
		if (!g.preventDefault) {
			g.preventDefault = b.preventDefault
		}
		var p = c(this),
			f, j;
		if (p && (f = b.keyEvents[p]) && (j = f[g.type])) {
			var h;
			switch (g.type) {
				case "keypress":
					if (g.ctrlKey || g.altKey) {
						return
					}
					if (g.which == 13) {
						h = b.keyMap[13]
					}
					if (g.which == 32) {
						h = b.keyMap[32]
					}
					if (g.which >= 33 && g.which <= 126) {
						h = String.fromCharCode(g.which)
					}
					break;
				case "keyup":
				case "keydown":
					if (b.keyMap[g.which]) {
						h = b.keyMap[g.which]
					}
					if (!h) {
						if ((g.which >= 48 && g.which <= 57)) {
							h = String.fromCharCode(g.which)
						} else {
							if ((g.which >= 65 && g.which <= 90)) {
								h = String.fromCharCode(g.which + 32)
							}
						}
					}
					if (h && g.type == "keydown") {
						f.linkedKey += f.linkedKey ? (">" + h) : h;
						if (g.altKey) {
							h = "alt+" + h
						}
						if (g.shiftKey) {
							h = "shift+" + h
						}
						if (g.ctrlKey) {
							h = "ctrl+" + h
						}
					}
					break
			}
			var q = /^select|textarea|input$/.test(g.target.nodeName.toLowerCase());
			if (h) {
				var m = [],
					n = false;
				if (f.linkedKey && f.linkKeyStr) {
					if (f.linkKeyStr.indexOf(" " + f.linkedKey) != -1) {
						if (f.linkKeyStr.indexOf(" " + f.linkedKey + " ") != -1) {
							m = m.concat(j[f.linkedKey]);
							f.linkedKey = ""
						}
						n = true
					} else {
						f.linkedKey = ""
					}
				}
				if (!n) {
					m = m.concat(j[h])
				}
				for (var o = 0; o < m.length; o++) {
					if (m[o] && (!m[o].disableInInput || !q)) {
						m[o].fn.apply(this, [g, m[o].key])
					}
				}
			}
		}
	};
	var e = function(n, m, j, h) {
		var f = {};
		if (!d.core.dom.isNode(n) || d.core.func.getType(j) !== "function") {
			return f
		}
		if (typeof m !== "string" || !(m = m.replace(/\s*/g, ""))) {
			return f
		}
		if (!h) {
			h = {}
		}
		if (!h.disableInInput) {
			h.disableInInput = false
		}
		if (!h.type) {
			h.type = "keypress"
		}
		h.type = h.type.replace(/\s*/g, "");
		if (!b.reg1.test(h.type) || (h.disableInInput && /^select|textarea|input$/.test(n.nodeName.toLowerCase()))) {
			return f
		}
		if (m.length > 1 || h.type != "keypress") {
			m = m.toLowerCase()
		}
		if (!/(^(\+|>)$)|(^([^\+>]+)$)/.test(m)) {
			var g = "";
			if (/((ctrl)|(shift)|(alt))\+(\+|([^\+]+))$/.test(m)) {
				if (m.indexOf("ctrl+") != -1) {
					g += "ctr+"
				}
				if (m.indexOf("shift+") != -1) {
					g += "shift+"
				}
				if (m.indexOf("alt+") != -1) {
					g += "alt+"
				}
				g += m.match(/\+(([^\+]+)|(\+))$/)[1]
			} else {
				if (!/(^>)|(>$)|>>/.test(m) && m.length > 2) {
					f.linkFlag = true
				} else {
					return f
				}
			}
			h.type = "keydown"
		}
		f.keys = m;
		f.fn = j;
		f.opt = h;
		return f
	};
	var a = {
		add: function(g, p, n, f) {
			if (d.core.arr.isArray(p)) {
				for (var j = 0; j < p.length; j++) {
					a.add(g, p[j], n, f)
				}
				return
			}
			var o = e(g, p, n, f);
			if (!o.keys) {
				return
			}
			p = o.keys;
			n = o.fn;
			f = o.opt;
			var q = o.linkFlag;
			var m = c(g);
			if (!b.keyEvents[m]) {
				b.keyEvents[m] = {
					linkKeyStr: "",
					linkedKey: ""
				}
			}
			if (!b.keyEvents[m].handler) {
				b.keyEvents[m].handler = function() {
					b.handler.apply(g, arguments)
				}
			}
			if (q && b.keyEvents[m].linkKeyStr.indexOf(" " + p + " ") == -1) {
				b.keyEvents[m].linkKeyStr += " " + p + " "
			}
			var h = f.type;
			if (!b.keyEvents[m][h]) {
				b.keyEvents[m][h] = {};
				d.core.evt.addEvent(g, h, b.keyEvents[m].handler)
			}
			if (!b.keyEvents[m][h][p]) {
				b.keyEvents[m][h][p] = []
			}
			b.keyEvents[m][h][p].push({
				fn: n,
				disableInInput: f.disableInInput,
				key: p
			})
		},
		remove: function(m, u, r, h) {
			if (d.core.arr.isArray(u)) {
				for (var p = 0; p < u.length; p++) {
					a.remove(m, u[p], r, h)
				}
				return
			}
			var t = e(m, u, r, h);
			if (!t.keys) {
				return
			}
			u = t.keys;
			r = t.fn;
			h = t.opt;
			linkFlag = t.linkFlag;
			var q = c(m),
				f, g, j;
			var o = h.type;
			if (q && (f = b.keyEvents[q]) && (g = f[o]) && f.handler && (j = g[u])) {
				for (var p = 0; p < j.length;) {
					if (j[p].fn === r) {
						j.splice(p, 1)
					} else {
						p++
					}
				}
				if (j.length < 1) {
					delete g[u]
				}
				var n = false;
				for (var s in g) {
					n = true;
					break
				}
				if (!n) {
					d.core.evt.removeEvent(m, o, f.handler);
					delete f[o]
				}
				if (linkFlag && f.linkKeyStr) {
					f.linkKeyStr = f.linkKeyStr.replace(" " + u + " ", "")
				}
			}
		}
	};
	return a
});
STK.register("core.func.bind", function(a) {
	return function(d, b, c) {
		c = a.core.arr.isArray(c) ? c : [c];
		return function() {
			return b.apply(d, c)
		}
	}
});
STK.register("core.func.memorize", function(a) {
	return function(b, d) {
		if (typeof b !== "function") {
			throw "core.func.memorize need a function as first parameter"
		}
		d = d || {};
		var c = {};
		if (d.timeout) {
			setInterval(function() {
				c = {}
			}, d.timeout)
		}
		return function() {
			var e = Array.prototype.join.call(arguments, "_");
			if (!(e in c)) {
				c[e] = b.apply((d.context || {}), arguments)
			}
			return c[e]
		}
	}
});
STK.register("core.func.methodBefore", function(a) {
	return function() {
		var b = false;
		var d = [];
		var c = {};
		c.add = function(g, f) {
			var e = a.core.obj.parseParam({
				args: [],
				pointer: window,
				top: false
			}, f);
			if (e.top == true) {
				d.unshift([g, e.args, e.pointer])
			} else {
				d.push([g, e.args, e.pointer])
			}
			return !b
		};
		c.start = function() {
			var g, e, j, f, h;
			if (b == true) {
				return
			}
			b = true;
			for (g = 0, e = d.length; g < e; g++) {
				j = d[g][0];
				f = d[g][1];
				h = d[g][2];
				j.apply(h, f)
			}
		};
		c.reset = function() {
			d = [];
			b = false
		};
		c.getList = function() {
			return d
		};
		return c
	}
});
STK.register("core.func.timedChunk", function(b) {
	var a = {
		process: function(c) {
			if (typeof c === "function") {
				c()
			}
		},
		context: {},
		callback: null,
		delay: 25,
		execTime: 50
	};
	return function(e, g) {
		if (!b.core.arr.isArray(e)) {
			throw "core.func.timedChunk need an array as first parameter"
		}
		var c = e.concat();
		var f = b.core.obj.parseParam(a, g);
		var h = null;
		var d = function() {
			var j = +new Date();
			do {
				f.process.call(f.context, c.shift())
			} while (c.length > 0 && (+new Date() - j < f.execTime));
			if (c.length <= 0) {
				if (f.callback) {
					f.callback(e)
				}
			} else {
				setTimeout(arguments.callee, f.delay)
			}
		};
		h = setTimeout(d, f.delay)
	}
});
STK.register("core.io.getXHR", function(a) {
	return function() {
		var e = false;
		try {
			e = new XMLHttpRequest()
		} catch (d) {
			try {
				e = new ActiveXObject("Msxml2.XMLHTTP")
			} catch (c) {
				try {
					e = new ActiveXObject("Microsoft.XMLHTTP")
				} catch (b) {
					e = false
				}
			}
		}
		return e
	}
});
STK.register("core.str.parseURL", function(a) {
	return function(d) {
		var c = /^(?:([A-Za-z]+):(\/{0,3}))?([0-9.\-A-Za-z]+\.[0-9A-Za-z]+)?(?::(\d+))?(?:\/([^?#]*))?(?:\?([^#]*))?(?:#(.*))?$/;
		var h = ["url", "scheme", "slash", "host", "port", "path", "query", "hash"];
		var f = c.exec(d);
		var g = {};
		for (var e = 0, b = h.length; e < b; e += 1) {
			g[h[e]] = f[e] || ""
		}
		return g
	}
});
STK.register("core.json.jsonToQuery", function(a) {
	var b = function(d, c) {
		d = d == null ? "" : d;
		d = a.core.str.trim(d.toString());
		if (c) {
			return encodeURIComponent(d)
		} else {
			return d
		}
	};
	return function(g, e) {
		var h = [];
		if (typeof g == "object") {
			for (var d in g) {
				if (d === "$nullName") {
					h = h.concat(g[d]);
					continue
				}
				if (g[d] instanceof Array) {
					for (var f = 0, c = g[d].length; f < c; f++) {
						h.push(d + "=" + b(g[d][f], e))
					}
				} else {
					if (typeof g[d] != "function") {
						h.push(d + "=" + b(g[d], e))
					}
				}
			}
		}
		if (h.length) {
			return h.join("&")
		} else {
			return ""
		}
	}
});
STK.register("core.util.URL", function(a) {
	return function(f, c) {
		var e = a.core.obj.parseParam({
			isEncodeQuery: false,
			isEncodeHash: false
		}, c || {});
		var d = {};
		var h = a.core.str.parseURL(f);
		var b = a.core.json.queryToJson(h.query);
		var g = a.core.json.queryToJson(h.hash);
		d.setParam = function(j, m) {
			b[j] = m;
			return this
		};
		d.getParam = function(j) {
			return b[j]
		};
		d.setParams = function(m) {
			for (var j in m) {
				d.setParam(j, m[j])
			}
			return this
		};
		d.setHash = function(j, m) {
			g[j] = m;
			return this
		};
		d.getHash = function(j) {
			return g[j]
		};
		d.valueOf = d.toString = function() {
			var j = [];
			var m = a.core.json.jsonToQuery(b, e.isEncodeQuery);
			var n = a.core.json.jsonToQuery(g, e.isEncodeQuery);
			if (h.scheme != "") {
				j.push(h.scheme + ":");
				j.push(h.slash)
			}
			if (h.host != "") {
				j.push(h.host);
				if (h.port != "") {
					j.push(":");
					j.push(h.port)
				}
			}
			j.push("/");
			j.push(h.path);
			if (m != "") {
				j.push("?" + m)
			}
			if (n != "") {
				j.push("#" + n)
			}
			return j.join("")
		};
		return d
	}
});
STK.register("core.io.ajax", function($) {
	return function(oOpts) {
		var opts = $.core.obj.parseParam({
			url: "",
			charset: "UTF-8",
			timeout: 30 * 1000,
			args: {},
			onComplete: null,
			onTimeout: $.core.func.empty,
			uniqueID: null,
			onFail: $.core.func.empty,
			method: "get",
			asynchronous: true,
			header: {},
			isEncode: false,
			responseType: "json"
		}, oOpts);
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
					if (trans.status == 0) {} else {
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
			var url = $.core.util.URL(opts.url, {
				isEncodeQuery: opts.isEncode
			});
			url.setParams(opts.args);
			url.setParam("__rnd", new Date().valueOf());
			trans.open(opts.method, url, opts.asynchronous);
			try {
				for (var k in opts.header) {
					trans.setRequestHeader(k, opts.header[k])
				}
			} catch (exp) {}
			trans.send("")
		} else {
			trans.open(opts.method, opts.url, opts.asynchronous);
			try {
				for (var k in opts.header) {
					trans.setRequestHeader(k, opts.header[k])
				}
			} catch (exp) {}
			trans.send($.core.json.jsonToQuery(opts.args, opts.isEncode))
		}
		if (opts.timeout) {
			tm = setTimeout(function() {
				try {
					trans.abort()
				} catch (exp) {}
				opts.onTimeout({}, trans);
				opts.onFail(data, trans)
			}, opts.timeout)
		}
		return trans
	}
});
STK.register("core.io.scriptLoader", function(b) {
	var c = {};
	var a = {
		url: "",
		charset: "UTF-8",
		timeout: 30 * 1000,
		args: {},
		onComplete: b.core.func.empty,
		onTimeout: null,
		isEncode: false,
		uniqueID: null
	};
	return function(h) {
		var f, d;
		var e = b.core.obj.parseParam(a, h);
		if (e.url == "") {
			throw "scriptLoader: url is null"
		}
		var g = e.uniqueID || b.core.util.getUniqueKey();
		f = c[g];
		if (f != null && b.IE != true) {
			b.core.dom.removeNode(f);
			f = null
		}
		if (f == null) {
			f = c[g] = b.C("script")
		}
		f.charset = e.charset;
		f.id = "scriptRequest_script_" + g;
		f.type = "text/javascript";
		if (e.onComplete != null) {
			if (b.IE) {
				f.onreadystatechange = function() {
					if (f.readyState.toLowerCase() == "loaded" || f.readyState.toLowerCase() == "complete") {
						try {
							clearTimeout(d);
							document.getElementsByTagName("head")[0].removeChild(f);
							f.onreadystatechange = null
						} catch (j) {}
						e.onComplete()
					}
				}
			} else {
				f.onload = function() {
					try {
						clearTimeout(d);
						b.core.dom.removeNode(f)
					} catch (j) {}
					e.onComplete()
				}
			}
		}
		f.src = STK.core.util.URL(e.url, {
			isEncodeQuery: e.isEncode
		}).setParams(e.args);
		document.getElementsByTagName("head")[0].appendChild(f);
		if (e.timeout > 0 && e.onTimeout != null) {
			d = setTimeout(function() {
				try {
					document.getElementsByTagName("head")[0].removeChild(f)
				} catch (j) {}
				e.onTimeout()
			}, e.timeout)
		}
		return f
	}
});
STK.register("core.io.jsonp", function(a) {
	return function(f) {
		var d = a.core.obj.parseParam({
			url: "",
			charset: "UTF-8",
			timeout: 30 * 1000,
			args: {},
			onComplete: null,
			onTimeout: null,
			responseName: null,
			isEncode: false,
			varkey: "callback"
		}, f);
		var g = -1;
		var e = d.responseName || ("STK_" + a.core.util.getUniqueKey());
		d.args[d.varkey] = e;
		var b = d.onComplete;
		var c = d.onTimeout;
		window[e] = function(h) {
			if (g != 2 && b != null) {
				g = 1;
				b(h)
			}
		};
		d.onComplete = null;
		d.onTimeout = function() {
			if (g != 1 && c != null) {
				g = 2;
				c()
			}
		};
		return a.core.io.scriptLoader(d)
	}
});
STK.register("core.util.templet", function(a) {
	return function(b, c) {
		return b.replace(/#\{(.+?)\}/ig, function() {
			var g = arguments[1].replace(/\s/ig, "");
			var e = arguments[0];
			var h = g.split("||");
			for (var f = 0, d = h.length; f < d; f += 1) {
				if (/^default:.*$/.test(h[f])) {
					e = h[f].replace(/^default:/, "");
					break
				} else {
					if (c[h[f]] !== undefined) {
						e = c[h[f]];
						break
					}
				}
			}
			return e
		})
	}
});
STK.register("core.io.getIframeTrans", function(b) {
	var a = '<iframe id="#{id}" name="#{id}" height="0" width="0" frameborder="no"></iframe>';
	return function(c) {
		var f, d, e;
		d = b.core.obj.parseParam({
			id: "STK_iframe_" + b.core.util.getUniqueKey()
		}, c);
		e = {};
		f = b.C("DIV");
		f.innerHTML = b.core.util.templet(a, d);
		b.core.util.hideContainer.appendChild(f);
		e.getId = function() {
			return d.id
		};
		e.destroy = function() {
			f.innerHTML = "";
			try {
				f.getElementsByTagName("iframe")[0].src = "about:blank"
			} catch (g) {}
			b.core.util.hideContainer.removeChild(f);
			f = null
		};
		return e
	}
});
STK.register("core.io.require", function(d) {
	var c = "http://js.t.sinajs.cn/STK/js/";
	var f = function(n, j) {
		var m = j.split(".");
		var h = n;
		var g = null;
		while (g = m.shift()) {
			h = h[g];
			if (h === undefined) {
				return false
			}
		}
		return true
	};
	var a = [];
	var e = function(g) {
		if (d.core.arr.indexOf(g, a) !== -1) {
			return false
		}
		a.push(g);
		d.core.io.scriptLoader({
			url: g,
			callback: function() {
				d.core.arr.foreach(a, function(j, h) {
					if (j === g) {
						a.splice(h, 1);
						return false
					}
				})
			}
		});
		return false
	};
	var b = function(m, o, q) {
		var j = null;
		for (var n = 0, g = m.length; n < g; n += 1) {
			var p = m[n];
			if (typeof p === "string") {
				if (!f(d, p)) {
					e(c + p.replace(/\./ig, "/") + ".js")
				}
			} else {
				if (!f(window, p.NS)) {
					e(p.url)
				}
			}
		}
		var h = function() {
			for (var s = 0, r = m.length; s < r; s += 1) {
				var t = m[s];
				if (typeof t === "string") {
					if (!f(d, t)) {
						j = setTimeout(h, 25);
						return false
					}
				} else {
					if (!f(window, t.NS)) {
						j = setTimeout(h, 25);
						return false
					}
				}
			}
			clearTimeout(j);
			o.apply({}, [].concat(q))
		};
		j = setTimeout(h, 25)
	};
	b.setBaseURL = function(g) {
		if (typeof g !== "string") {
			throw "[STK.kit.extra.require.setBaseURL] need string as frist parameter"
		}
		c = g
	};
	return b
});
STK.register("core.io.ijax", function(a) {
	return function(c) {
		var d, f, h, j, e, b, g;
		d = a.core.obj.parseParam({
			url: "",
			form: null,
			args: {},
			uniqueID: null,
			timeout: 30 * 1000,
			onComplete: a.core.func.empty,
			onTimeout: a.core.func.empty,
			onFail: a.core.func.empty,
			asynchronous: true,
			isEncode: true,
			abaurl: null,
			responseName: null,
			varkey: "callback",
			abakey: "callback"
		}, c);
		g = {};
		if (d.url == "") {
			throw "ijax need url in parameters object"
		}
		if (!d.form) {
			throw "ijax need form in parameters object"
		}
		f = a.core.io.getIframeTrans();
		h = d.responseName || ("STK_ijax_" + a.core.util.getUniqueKey());
		b = {};
		b[d.varkey] = h;
		if (d.abaurl) {
			d.abaurl = a.core.util.URL(d.abaurl).setParams(b);
			b = {};
			b[d.abakey] = d.abaurl
		}
		d.url = a.core.util.URL(d.url, {
			isEncodeQuery: d.isEncode
		}).setParams(b).setParams(d.args);
		e = function() {
			window[h] = null;
			f.destroy();
			f = null;
			clearTimeout(j)
		};
		j = setTimeout(function() {
			e();
			d.onTimeout();
			d.onFail()
		}, d.timeout);
		window[h] = function(m, n) {
			e();
			d.onComplete(m, n)
		};
		d.form.action = d.url;
		d.form.target = f.getId();
		d.form.submit();
		g.abort = e;
		return g
	}
});
STK.register("core.json.clone", function(a) {
	function b(f) {
		var d;
		if (f instanceof Array) {
			d = [];
			var e = f.length;
			while (e--) {
				d[e] = b(f[e])
			}
			return d
		} else {
			if (f instanceof Object) {
				d = {};
				for (var c in f) {
					d[c] = b(f[c])
				}
				return d
			} else {
				return f
			}
		}
	}
	return b
});
STK.register("core.json.include", function(a) {
	return function(e, f) {
		for (var c in f) {
			if (typeof f[c] === "object") {
				if (f[c] instanceof Array) {
					if (e[c] instanceof Array) {
						if (f[c].length === e[c].length) {
							for (var d = 0, b = f[c].length; d < b; d += 1) {
								if (!arguments.callee(f[c][d], e[c][d])) {
									return false
								}
							}
						} else {
							return false
						}
					} else {
						return false
					}
				} else {
					if (typeof e[c] === "object") {
						if (!arguments.callee(f[c], e[c])) {
							return false
						}
					} else {
						return false
					}
				}
			} else {
				if (typeof f[c] === "number" || typeof f[c] === "string") {
					if (f[c] !== e[c]) {
						return false
					}
				} else {
					if (f[c] !== undefined && f[c] !== null) {
						if (e[c] !== undefined && e[c] !== null) {
							if (!f[c].toString || !e[c].toString) {
								throw "json1[k] or json2[k] do not have toString method"
							}
							if (f[c].toString() !== e[c].toString()) {
								return false
							}
						} else {
							return false
						}
					}
				}
			}
		}
		return true
	}
});
STK.register("core.json.compare", function(a) {
	return function(c, b) {
		if (a.core.json.include(c, b) && a.core.json.include(b, c)) {
			return true
		} else {
			return false
		}
	}
});
STK.register("core.json.jsonToStr", function(d) {
	function e(f) {
		return f < 10 ? "0" + f : f
	}
	if (typeof Date.prototype.toJSON !== "function") {
		Date.prototype.toJSON = function(f) {
			return isFinite(this.valueOf()) ? this.getUTCFullYear() + "-" + e(this.getUTCMonth() + 1) + "-" + e(this.getUTCDate()) + "T" + e(this.getUTCHours()) + ":" + e(this.getUTCMinutes()) + ":" + e(this.getUTCSeconds()) + "Z" : null
		};
		String.prototype.toJSON = Number.prototype.toJSON = Boolean.prototype.toJSON = function(f) {
			return this.valueOf()
		}
	}
	var c = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
		h = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
		j, b, n = {
			"\b": "\\b",
			"\t": "\\t",
			"\n": "\\n",
			"\f": "\\f",
			"\r": "\\r",
			'"': '\\"',
			"\\": "\\\\"
		},
		m;

	function a(f) {
		h.lastIndex = 0;
		return h.test(f) ? '"' + f.replace(h, function(o) {
			var p = n[o];
			return typeof p === "string" ? p : "\\u" + ("0000" + o.charCodeAt(0).toString(16)).slice(-4)
		}) + '"' : '"' + f + '"'
	}

	function g(u, r) {
		var p, o, w, f, s = j,
			q, t = r[u];
		if (t && typeof t === "object" && typeof t.toJSON === "function") {
			t = t.toJSON(u)
		}
		if (typeof m === "function") {
			t = m.call(r, u, t)
		}
		switch (typeof t) {
			case "string":
				return a(t);
			case "number":
				return isFinite(t) ? String(t) : "null";
			case "boolean":
			case "null":
				return String(t);
			case "object":
				if (!t) {
					return "null"
				}
				j += b;
				q = [];
				if (Object.prototype.toString.apply(t) === "[object Array]") {
					f = t.length;
					for (p = 0; p < f; p += 1) {
						q[p] = g(p, t) || "null"
					}
					w = q.length === 0 ? "[]" : j ? "[\n" + j + q.join(",\n" + j) + "\n" + s + "]" : "[" + q.join(",") + "]";
					j = s;
					return w
				}
				if (m && typeof m === "object") {
					f = m.length;
					for (p = 0; p < f; p += 1) {
						o = m[p];
						if (typeof o === "string") {
							w = g(o, t);
							if (w) {
								q.push(a(o) + (j ? ": " : ":") + w)
							}
						}
					}
				} else {
					for (o in t) {
						if (Object.hasOwnProperty.call(t, o)) {
							w = g(o, t);
							if (w) {
								q.push(a(o) + (j ? ": " : ":") + w)
							}
						}
					}
				}
				w = q.length === 0 ? "{}" : j ? "{\n" + j + q.join(",\n" + j) + "\n" + s + "}" : "{" + q.join(",") + "}";
				j = s;
				return w
		}
	}
	return function(q, o, p) {
		var f;
		j = "";
		b = "";
		if (typeof p === "number") {
			for (f = 0; f < p; f += 1) {
				b += " "
			}
		} else {
			if (typeof p === "string") {
				b = p
			}
		}
		m = o;
		if (o && typeof o !== "function" && (typeof o !== "object" || typeof o.length !== "number")) {
			throw new Error("JSON.stringify")
		}
		return g("", {
			"": q
		})
	}
});
STK.register("core.json.strToJson", function(g) {
	var d, b, a = {
			'"': '"',
			"\\": "\\",
			"/": "/",
			b: "\b",
			f: "\f",
			n: "\n",
			r: "\r",
			t: "\t"
		},
		q, o = function(r) {
			throw {
				name: "SyntaxError",
				message: r,
				at: d,
				text: q
			}
		},
		h = function(r) {
			if (r && r !== b) {
				o("Expected '" + r + "' instead of '" + b + "'")
			}
			b = q.charAt(d);
			d += 1;
			return b
		},
		f = function() {
			var s, r = "";
			if (b === "-") {
				r = "-";
				h("-")
			}
			while (b >= "0" && b <= "9") {
				r += b;
				h()
			}
			if (b === ".") {
				r += ".";
				while (h() && b >= "0" && b <= "9") {
					r += b
				}
			}
			if (b === "e" || b === "E") {
				r += b;
				h();
				if (b === "-" || b === "+") {
					r += b;
					h()
				}
				while (b >= "0" && b <= "9") {
					r += b;
					h()
				}
			}
			s = +r;
			if (isNaN(s)) {
				o("Bad number")
			} else {
				return s
			}
		},
		j = function() {
			var u, t, s = "",
				r;
			if (b === '"') {
				while (h()) {
					if (b === '"') {
						h();
						return s
					} else {
						if (b === "\\") {
							h();
							if (b === "u") {
								r = 0;
								for (t = 0; t < 4; t += 1) {
									u = parseInt(h(), 16);
									if (!isFinite(u)) {
										break
									}
									r = r * 16 + u
								}
								s += String.fromCharCode(r)
							} else {
								if (typeof a[b] === "string") {
									s += a[b]
								} else {
									break
								}
							}
						} else {
							s += b
						}
					}
				}
			}
			o("Bad string")
		},
		n = function() {
			while (b && b <= " ") {
				h()
			}
		},
		c = function() {
			switch (b) {
				case "t":
					h("t");
					h("r");
					h("u");
					h("e");
					return true;
				case "f":
					h("f");
					h("a");
					h("l");
					h("s");
					h("e");
					return false;
				case "n":
					h("n");
					h("u");
					h("l");
					h("l");
					return null
			}
			o("Unexpected '" + b + "'")
		},
		p, m = function() {
			var r = [];
			if (b === "[") {
				h("[");
				n();
				if (b === "]") {
					h("]");
					return r
				}
				while (b) {
					r.push(p());
					n();
					if (b === "]") {
						h("]");
						return r
					}
					h(",");
					n()
				}
			}
			o("Bad array")
		},
		e = function() {
			var s, r = {};
			if (b === "{") {
				h("{");
				n();
				if (b === "}") {
					h("}");
					return r
				}
				while (b) {
					s = j();
					n();
					h(":");
					if (Object.hasOwnProperty.call(r, s)) {
						o('Duplicate key "' + s + '"')
					}
					r[s] = p();
					n();
					if (b === "}") {
						h("}");
						return r
					}
					h(",");
					n()
				}
			}
			o("Bad object")
		};
	p = function() {
		n();
		switch (b) {
			case "{":
				return e();
			case "[":
				return m();
			case '"':
				return j();
			case "-":
				return f();
			default:
				return b >= "0" && b <= "9" ? f() : c()
		}
	};
	return function(u, s) {
		var r;
		q = u;
		d = 0;
		b = " ";
		r = p();
		n();
		if (b) {
			o("Syntax error")
		}
		return typeof s === "function" ? (function t(z, y) {
			var x, w, A = z[y];
			if (A && typeof A === "object") {
				for (x in A) {
					if (Object.hasOwnProperty.call(A, x)) {
						w = t(A, x);
						if (w !== undefined) {
							A[x] = w
						} else {
							delete A[x]
						}
					}
				}
			}
			return s.call(z, y, A)
		}({
			"": r
		}, "")) : r
	}
});
STK.register("core.obj.cascade", function(a) {
	return function(e, c) {
		for (var d = 0, b = c.length; d < b; d += 1) {
			if (typeof e[c[d]] !== "function") {
				throw "cascade need function list as the second paramsters"
			}
			e[c[d]] = (function(f) {
				return function() {
					f.apply(e, arguments);
					return e
				}
			})(e[c[d]])
		}
	}
});
STK.register("core.obj.clear", function(a) {
	return function(b) {
		var c, d = {};
		for (c in b) {
			if (b[c] != null) {
				d[c] = b[c]
			}
		}
		return d
	}
});
STK.register("core.obj.cut", function(a) {
	return function(e, d) {
		var c = {};
		if (!a.core.arr.isArray(d)) {
			throw "core.obj.cut need array as second parameter"
		}
		for (var b in e) {
			if (!a.core.arr.inArray(b, d)) {
				c[b] = e[b]
			}
		}
		return c
	}
});
STK.register("core.obj.sup", function(a) {
	return function(f, c) {
		var e = {};
		for (var d = 0, b = c.length; d < b; d += 1) {
			if (typeof f[c[d]] !== "function") {
				throw "super need function list as the second paramsters"
			}
			e[c[d]] = (function(g) {
				return function() {
					return g.apply(f, arguments)
				}
			})(f[c[d]])
		}
		return e
	}
});
STK.register("core.str.bLength", function(a) {
	return function(c) {
		if (!c) {
			return 0
		}
		var b = c.match(/[^\x00-\xff]/g);
		return (c.length + (!b ? 0 : b.length))
	}
});
STK.register("core.str.dbcToSbc", function(a) {
	return function(b) {
		return b.replace(/[\uff01-\uff5e]/g, function(c) {
			return String.fromCharCode(c.charCodeAt(0) - 65248)
		}).replace(/\u3000/g, " ")
	}
});
STK.register("core.str.parseHTML", function(a) {
	return function(f) {
		var d = /[^<>]+|<(\/?)([A-Za-z0-9]+)([^<>]*)>/g;
		var b, e;
		var c = [];
		while ((b = d.exec(f))) {
			var g = [];
			for (e = 0; e < b.length; e += 1) {
				g.push(b[e])
			}
			c.push(g)
		}
		return c
	}
});
STK.register("core.str.leftB", function(a) {
	return function(d, b) {
		var c = d.replace(/\*/g, " ").replace(/[^\x00-\xff]/g, "**");
		d = d.slice(0, c.slice(0, b).replace(/\*\*/g, " ").replace(/\*/g, "").length);
		if (a.core.str.bLength(d) > b && b > 0) {
			d = d.slice(0, d.length - 1)
		}
		return d
	}
});
STK.register("core.str.queryString", function(a) {
	return function(e, f) {
		var d = a.core.obj.parseParam({
			source: window.location.href.toString(),
			split: "&"
		}, f);
		var b = new RegExp("(^|)" + e + "=([^\\" + d.split + "]*)(\\" + d.split + "|$)", "gi").exec(d.source),
			c;
		if (c = b) {
			return c[2]
		}
		return null
	}
});
STK.register("core.util.cookie", function(b) {
	var a = {
		set: function(g, m, j) {
			var c = [];
			var h, f;
			var e = b.core.obj.parseParam({
				expire: null,
				path: "/",
				domain: null,
				secure: null,
				encode: true
			}, j);
			if (e.encode == true) {
				m = escape(m)
			}
			c.push(g + "=" + m);
			if (e.path != null) {
				c.push("path=" + e.path)
			}
			if (e.domain != null) {
				c.push("domain=" + e.domain)
			}
			if (e.secure != null) {
				c.push(e.secure)
			}
			if (e.expire != null) {
				h = new Date();
				f = h.getTime() + e.expire * 3600000;
				h.setTime(f);
				c.push("expires=" + h.toGMTString())
			}
			document.cookie = c.join(";")
		},
		get: function(e) {
			e = e.replace(/([\.\[\]\$])/g, "\\$1");
			var d = new RegExp(e + "=([^;]*)?;", "i");
			var f = document.cookie + ";";
			var c = f.match(d);
			if (c) {
				return c[1] || ""
			} else {
				return ""
			}
		},
		remove: function(c, d) {
			d = d || {};
			d.expire = -10;
			a.set(c, "", d)
		}
	};
	return a
});
STK.register("core.util.drag", function(c) {
	var a = function(d) {
		d.cancelBubble = true;
		return false
	};
	var b = function(e, d) {
		e.clientX = d.clientX;
		e.clientY = d.clientY;
		e.pageX = d.clientX + c.core.util.scrollPos()["left"];
		e.pageY = d.clientY + c.core.util.scrollPos()["top"];
		return e
	};
	return function(e, p) {
		if (!c.core.dom.isNode(e)) {
			throw "core.util.drag need Element as first parameter"
		}
		var o = c.core.obj.parseParam({
			actRect: [],
			actObj: {}
		}, p);
		var j = {};
		var m = c.core.evt.custEvent.define(o.actObj, "dragStart");
		var f = c.core.evt.custEvent.define(o.actObj, "dragEnd");
		var g = c.core.evt.custEvent.define(o.actObj, "draging");
		var n = function(r) {
			var q = b({}, r);
			document.body.onselectstart = function() {
				return false
			};
			c.core.evt.addEvent(document, "mousemove", h);
			c.core.evt.addEvent(document, "mouseup", d);
			c.core.evt.addEvent(document, "click", a, true);
			if (!c.IE) {
				r.preventDefault();
				r.stopPropagation()
			}
			c.core.evt.custEvent.fire(m, "dragStart", q);
			return false
		};
		var h = function(r) {
			var q = b({}, r);
			r.cancelBubble = true;
			c.core.evt.custEvent.fire(m, "draging", q)
		};
		var d = function(r) {
			var q = b({}, r);
			document.body.onselectstart = function() {
				return true
			};
			c.core.evt.removeEvent(document, "mousemove", h);
			c.core.evt.removeEvent(document, "mouseup", d);
			c.core.evt.removeEvent(document, "click", a, true);
			c.core.evt.custEvent.fire(m, "dragEnd", q)
		};
		c.core.evt.addEvent(e, "mousedown", n);
		j.destroy = function() {
			c.core.evt.removeEvent(e, "mousedown", n);
			o = null
		};
		j.getActObj = function() {
			return o.actObj
		};
		return j
	}
});
STK.register("core.util.nameValue", function(a) {
	return function(b) {
		var j = b.getAttribute("name");
		var e = b.getAttribute("type");
		var h = b.tagName;
		var m = {
			name: j,
			value: ""
		};
		var f = function(n) {
			if (n === false) {
				m = false
			} else {
				if (!m.value) {
					m.value = a.core.str.trim((n || ""))
				} else {
					m.value = [a.core.str.trim((n || ""))].concat(m.value)
				}
			}
		};
		if (!b.disabled && j) {
			switch (h) {
				case "INPUT":
					if (e == "radio" || e == "checkbox") {
						if (b.checked) {
							f(b.value)
						} else {
							f(false)
						}
					} else {
						if (e == "reset" || e == "submit" || e == "image") {
							f(false)
						} else {
							f(b.value)
						}
					}
					break;
				case "SELECT":
					if (b.multiple) {
						var c = b.options;
						for (var d = 0, g = c.length; d < g; d++) {
							if (c[d].selected) {
								f(c[d].value)
							}
						}
					} else {
						f(b.value)
					}
					break;
				case "TEXTAREA":
					f(b.value || b.getAttribute("value") || false);
					break;
				case "BUTTON":
				default:
					f(b.value || b.getAttribute("value") || b.innerHTML || false)
			}
		} else {
			return false
		}
		return m
	}
});
STK.register("core.util.htmlToJson", function(a) {
	return function(h, c, e) {
		var o = {};
		c = c || ["INPUT", "TEXTAREA", "BUTTON", "SELECT"];
		if (!h || !c) {
			return false
		}
		var b = a.core.util.nameValue;
		for (var f = 0, g = c.length; f < g; f++) {
			var n = h.getElementsByTagName(c[f]);
			for (var d = 0, m = n.length; d < m; d++) {
				var p = b(n[d]);
				if (!p || (e && (p.value === ""))) {
					continue
				}
				if (o[p.name]) {
					if (o[p.name] instanceof Array) {
						o[p.name] = o[p.name].concat(p.value)
					} else {
						o[p.name] = [o[p.name]].concat(p.value)
					}
				} else {
					o[p.name] = p.value
				}
			}
		}
		return o
	}
});
STK.register("core.util.jobsM", function(a) {
	return (function() {
		var e = [];
		var f = {};
		var d = false;
		var g = {};
		var b = function(m) {
			var o = m.name;
			var h = m.func;
			var j = +new Date();
			if (!f[o]) {
				try {
					h(a);
					h[o] = true
				} catch (n) {
					a.log("[error][jobs]" + o)
				}
			}
		};
		var c = function(h) {
			if (h.length) {
				a.core.func.timedChunk(h, {
					process: b,
					callback: arguments.callee
				});
				h.splice(0, h.length)
			} else {
				d = false
			}
		};
		g.register = function(h, j) {
			e.push({
				name: h,
				func: j
			})
		};
		g.start = function() {
			if (d) {
				return true
			} else {
				d = true
			}
			c(e)
		};
		g.load = function() {};
		a.core.dom.ready(g.start);
		return g
	})()
});
STK.register("core.util.language", function(a) {
	return function(b, c) {
		return b.replace(/#L\{((.*?)(?:[^\\]))\}/ig, function() {
			var e = arguments[1];
			var d;
			if (c && c[e] !== undefined) {
				d = c[e]
			} else {
				d = e
			}
			return d
		})
	}
});
STK.register("core.util.listener", function(a) {
	return (function() {
		var e = {};
		var b;
		var f = [];
		var d;

		function g() {
			if (f.length == 0) {
				return
			}
			clearTimeout(d);
			var h = f.splice(0, 1)[0];
			try {
				h.func.apply(h.func, [].concat(h.data))
			} catch (j) {
				a.log("[error][listener]: One of " + h + "-" + h + " function execute error.")
			}
			d = setTimeout(g, 25)
		}
		var c = {
			conn: function() {
				var h = window;
				while (h != top) {
					h = h.parent;
					if (h.STK && h.STK["core"] && h.STK["core"]["util"] && h.STK["core"]["util"]["listener"] != null) {
						b = h
					}
				}
			},
			register: function(h, m, j) {
				if (b != null) {
					b.STK["core"]["util"]["listener"].register(h, m, j)
				} else {
					e[h] = e[h] || {};
					e[h][m] = e[h][m] || [];
					e[h][m].push(j)
				}
			},
			fire: function(m, o, p) {
				if (b != null) {
					b.listener.fire(m, o, p)
				} else {
					var n;
					var j, h;
					if (e[m] && e[m][o] && e[m][o].length > 0) {
						n = e[m][o];
						n.data_cache = p;
						for (j = 0, h = n.length; j < h; j++) {
							f.push({
								channel: m,
								evt: o,
								func: n[j],
								data: p
							})
						}
						g()
					}
				}
			},
			remove: function(m, o, n) {
				if (b != null) {
					b.STK["core"]["util"]["listener"].remove(m, o, n)
				} else {
					if (e[m]) {
						if (e[m][o]) {
							for (var j = 0, h = e[m][o].length; j < h; j++) {
								if (e[m][o][j] === n) {
									e[m][o].splice(j, 1);
									break
								}
							}
						}
					}
				}
			},
			list: function() {
				return e
			},
			cache: function(h, j) {
				if (b != null) {
					return b.listener.cache(h, j)
				} else {
					if (e[h] && e[h][j]) {
						return e[h][j].data_cache
					}
				}
			}
		};
		return c
	})()
});
STK.register("core.util.winSize", function(a) {
	return function(c) {
		var b, d;
		var e;
		if (c) {
			e = c.document
		} else {
			e = document
		}
		if (e.compatMode === "CSS1Compat") {
			b = e.documentElement.clientWidth;
			d = e.documentElement.clientHeight
		} else {
			if (self.innerHeight) {
				if (c) {
					e = c.self
				} else {
					e = self
				}
				b = e.innerWidth;
				d = e.innerHeight
			} else {
				if (e.documentElement && e.documentElement.clientHeight) {
					b = e.documentElement.clientWidth;
					d = e.documentElement.clientHeight
				} else {
					if (e.body) {
						b = e.body.clientWidth;
						d = e.body.clientHeight
					}
				}
			}
		}
		return {
			width: b,
			height: d
		}
	}
});
STK.register("core.util.pageSize", function(a) {
	return function(d) {
		if (d) {
			target = d.document
		} else {
			target = document
		}
		var h = (target.compatMode == "CSS1Compat" ? target.documentElement : target.body);
		var g, c;
		var f, e;
		if (window.innerHeight && window.scrollMaxY) {
			g = h.scrollWidth;
			c = window.innerHeight + window.scrollMaxY
		} else {
			if (h.scrollHeight > h.offsetHeight) {
				g = h.scrollWidth;
				c = h.scrollHeight
			} else {
				g = h.offsetWidth;
				c = h.offsetHeight
			}
		}
		var b = a.core.util.winSize(d);
		if (c < b.height) {
			f = b.height
		} else {
			f = c
		}
		if (g < b.width) {
			e = b.width
		} else {
			e = g
		}
		return {
			page: {
				width: e,
				height: f
			},
			win: {
				width: b.width,
				height: b.height
			}
		}
	}
});
STK.register("core.util.queue", function(a) {
	return function() {
		var b = {};
		var c = [];
		b.add = function(d) {
			c.push(d);
			return b
		};
		b.get = function() {
			if (c.length > 0) {
				return c.shift()
			} else {
				return false
			}
		};
		return b
	}
});
STK.register("core.util.timer", function(a) {
	return (function() {
		var g = {};
		var h = {};
		var b = 0;
		var e = null;
		var f = false;
		var d = 25;
		var c = function() {
			for (var j in h) {
				if (!h[j]["pause"]) {
					h[j]["fun"]()
				}
			}
			return g
		};
		g.add = function(j) {
			if (typeof j != "function") {
				throw ("The timer needs add a function as a parameters")
			}
			var m = "" + (new Date()).getTime() + (Math.random()) * Math.pow(10, 17);
			h[m] = {
				fun: j,
				pause: false
			};
			if (b <= 0) {
				g.start()
			}
			b++;
			return m
		};
		g.remove = function(j) {
			if (h[j]) {
				delete h[j];
				b--
			}
			if (b <= 0) {
				g.stop()
			}
			return g
		};
		g.pause = function(j) {
			if (h[j]) {
				h[j]["pause"] = true
			}
			return g
		};
		g.play = function(j) {
			if (h[j]) {
				h[j]["pause"] = false
			}
			return g
		};
		g.stop = function() {
			clearInterval(e);
			e = null;
			return g
		};
		g.start = function() {
			e = setInterval(c, d);
			return g
		};
		g.loop = c;
		g.get = function(j) {
			if (j === "delay") {
				return d
			}
			if (j === "functionList") {
				return h
			}
		};
		g.set = function(j, m) {
			if (j === "delay") {
				if (typeof m === "number") {
					d = Math.max(25, Math.min(m, 200))
				}
			}
		};
		return g
	})()
});
STK.register("core.util.scrollTo", function(a) {
	return function(c, m) {
		if (!a.core.dom.isNode(c)) {
			throw "core.dom.isNode need element as the first parameter"
		}
		var d = a.core.obj.parseParam({
			box: document.documentElement,
			top: 0,
			step: 2,
			onMoveStop: null
		}, m);
		d.step = Math.max(2, Math.min(10, d.step));
		var b = [];
		var j = a.core.dom.position(c);
		var h;
		if (d.box == document.documentElement) {
			h = {
				t: 0
			}
		} else {
			h = a.core.dom.position(d.box)
		}
		var e = Math.max(0, (j ? j.t : 0) - (h ? h.t : 0) - d.top);
		var f = d.box === document.documentElement ? (d.box.scrollTop || document.body.scrollTop || window.pageYOffset) : d.box.scrollTop;
		while (Math.abs(f - e) > d.step && f !== 0) {
			b.push(Math.round(f + (e - f) * d.step / 10));
			f = b[b.length - 1]
		}
		if (!b.length) {
			b.push(e)
		}
		var g = a.core.util.timer.add(function() {
			if (b.length) {
				if (d.box === document.documentElement) {
					window.scrollTo(0, b.shift())
				} else {
					d.box.scrollTop = b.shift()
				}
			} else {
				if (d.box === document.documentElement) {
					window.scrollTo(0, e)
				} else {
					d.box.scrollTop = e
				}
				a.core.util.timer.remove(g);
				if (typeof d.onMoveStop === "function") {
					d.onMoveStop()
				}
			}
		})
	}
});
STK.register("core.util.stack", function(a) {
	return function() {
		var c = {};
		var b = [];
		c.add = function(d) {
			b.push(d);
			return c
		};
		c.get = function() {
			if (b.length > 0) {
				return b.pop()
			} else {
				return false
			}
		};
		return c
	}
});
STK.register("core.util.swf", function(c) {
	function a(j, m) {
		var e = c.core.obj.parseParam({
			id: "swf_" + parseInt(Math.random() * 10000, 10),
			width: 1,
			height: 1,
			attrs: {},
			paras: {},
			flashvars: {},
			html: ""
		}, m);
		if (j == null) {
			throw "swf: [sURL] 未定义";
			return
		}
		var h;
		var g = [];
		var f = [];
		for (h in e.attrs) {
			f.push(h + '="' + e.attrs[h] + '" ')
		}
		var d = [];
		for (h in e.flashvars) {
			d.push(h + "=" + e.flashvars[h])
		}
		e.paras.flashvars = d.join("&");
		if (c.IE) {
			g.push('<object width="' + e.width + '" height="' + e.height + '" id="' + e.id + '" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ');
			g.push(f.join(""));
			g.push('><param name="movie" value="' + j + '" />');
			for (h in e.paras) {
				g.push('<param name="' + h + '" value="' + e.paras[h] + '" />')
			}
			g.push("</object>")
		} else {
			g.push('<embed width="' + e.width + '" height="' + e.height + '" id="' + e.id + '" src="' + j + '" type="application/x-shockwave-flash" ');
			g.push(f.join(""));
			for (h in e.paras) {
				g.push(h + '="' + e.paras[h] + '" ')
			}
			g.push(" />")
		}
		e.html = g.join("");
		return e
	}
	var b = {};
	b.create = function(e, g, h) {
		var f = c.E(e);
		if (f == null) {
			throw "swf: [" + e + "] 未找到";
			return
		}
		var d = a(g, h);
		f.innerHTML = d.html;
		return c.E(d.id)
	};
	b.html = function(e, f) {
		var d = a(e, f);
		return d.html
	};
	b.check = function() {
		var e = -1;
		if (c.IE) {
			try {
				var d = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
				e = d.GetVariable("$version")
			} catch (f) {}
		} else {
			if (navigator.plugins["Shockwave Flash"]) {
				e = navigator.plugins["Shockwave Flash"]["description"]
			}
		}
		return e
	};
	return b
});
STK.register("core.util.easyTemplate", function(b) {
	var a = function(e, g) {
		if (!e) {
			return ""
		}
		if (e !== a.template) {
			a.template = e;
			a.aStatement = a.parsing(a.separate(e))
		}
		var c = a.aStatement;
		var f = function(d) {
			if (d) {
				g = d
			}
			return arguments.callee
		};
		f.toString = function() {
			return (new Function(c[0], c[1]))(g)
		};
		return f
	};
	a.separate = function(c) {
		var e = /\\'/g;
		var d = c.replace(/(<(\/?)#(.*?(?:\(.*?\))*)>)|(')|([\r\n\t])|(\$\{([^\}]*?)\})/g, function(m, j, s, r, q, p, o, n) {
			if (j) {
				return "{|}" + (s ? "-" : "+") + r + "{|}"
			}
			if (q) {
				return "\\'"
			}
			if (p) {
				return ""
			}
			if (o) {
				return "'+(" + n.replace(e, "'") + ")+'"
			}
		});
		return d
	};
	a.parsing = function(o) {
		var n, e, h, d, g, f, j, m = ["var aRet = [];"];
		j = o.split(/\{\|\}/);
		var c = /\s/;
		while (j.length) {
			h = j.shift();
			if (!h) {
				continue
			}
			g = h.charAt(0);
			if (g !== "+" && g !== "-") {
				h = "'" + h + "'";
				m.push("aRet.push(" + h + ");");
				continue
			}
			d = h.split(c);
			switch (d[0]) {
				case "+et":
					n = d[1];
					e = d[2];
					m.push('aRet.push("<!--' + n + ' start-->");');
					break;
				case "-et":
					m.push('aRet.push("<!--' + n + ' end-->");');
					break;
				case "+if":
					d.splice(0, 1);
					m.push("if" + d.join(" ") + "{");
					break;
				case "+elseif":
					d.splice(0, 1);
					m.push("}else if" + d.join(" ") + "{");
					break;
				case "-if":
					m.push("}");
					break;
				case "+else":
					m.push("}else{");
					break;
				case "+list":
					m.push("if(" + d[1] + ".constructor === Array){with({i:0,l:" + d[1] + ".length," + d[3] + "_index:0," + d[3] + ":null}){for(i=l;i--;){" + d[3] + "_index=(l-i-1);" + d[3] + "=" + d[1] + "[" + d[3] + "_index];");
					break;
				case "-list":
					m.push("}}}");
					break;
				default:
					break
			}
		}
		m.push('return aRet.join("");');
		return [e, m.join("")]
	};
	return a
});
STK.register("core.util.storage", function(d) {
	var a = window.localStorage;
	if (a) {
		return {
			get: function(e) {
				return unescape(a.getItem(e))
			},
			set: function(e, g, h) {
				a.setItem(e, escape(g))
			},
			del: function(e) {
				a.removeItem(e)
			},
			clear: function() {
				a.clear()
			},
			getAll: function() {
				var e = a.length,
					h = null,
					j = [];
				for (var g = 0; g < e; g++) {
					h = a.key(g), j.push(h + "=" + this.getKey(h))
				}
				return j.join("; ")
			}
		}
	} else {
		if (window.ActiveXObject) {
			var b = document.documentElement;
			var c = "localstorage";
			try {
				b.addBehavior("#default#userdata");
				b.save("localstorage")
			} catch (f) {}
			return {
				set: function(e, g) {
					b.setAttribute(e, g);
					b.save(c)
				},
				get: function(e) {
					b.load(c);
					return b.getAttribute(e)
				},
				del: function(e) {
					b.removeAttribute(e);
					b.save(c)
				}
			}
		} else {
			return {
				get: function(m) {
					var h = document.cookie.split("; "),
						g = h.length,
						e = [];
					for (var j = 0; j < g; j++) {
						e = h[j].split("=");
						if (m === e[0]) {
							return unescape(e[1])
						}
					}
					return null
				},
				set: function(e, g, h) {
					if (!(h && typeof h === date)) {
						h = new Date(), h.setDate(h.getDate() + 1)
					}
					document.cookie = e + "=" + escape(g) + "; expires=" + h.toGMTString()
				},
				del: function(e) {
					document.cookie = e + "=''; expires=Fri, 31 Dec 1999 23:59:59 GMT;"
				},
				clear: function() {
					var h = document.cookie.split("; "),
						g = h.length,
						e = [];
					for (var j = 0; j < g; j++) {
						e = h[j].split("=");
						this.deleteKey(e[0])
					}
				},
				getAll: function() {
					return unescape(document.cookie.toString())
				}
			}
		}
	}
});
STK.register("core.util.pageletM", function(j) {
	var D = "http://js.t.sinajs.cn/t4/";
	var p = "http://img.t.sinajs.cn/t4/";
	if (typeof $CONFIG != "undefined") {
		D = $CONFIG.jsPath || D;
		p = $CONFIG.cssPath || p
	}
	var c = j.core.arr.indexOf;
	var f = {},
		e, y = {},
		A = {},
		m = {},
		g = {};
	var r, s;
	if (j.IE) {
		r = {};
		s = function() {
			var F, G, E;
			for (F in r) {
				if (r[F].length < 31) {
					E = j.E(F);
					break
				}
			}
			if (!E) {
				F = "style_" + j.core.util.getUniqueKey(), E = document.createElement("style");
				E.setAttribute("type", "text/css");
				E.setAttribute("id", F);
				document.getElementsByTagName("head")[0].appendChild(E);
				r[F] = []
			}
			return {
				styleID: F,
				styleSheet: E.styleSheet || E.sheet
			}
		}
	}
	var v = function(H, G) {
		m[H] = {
			cssURL: G
		};
		if (j.IE) {
			var F = s();
			F.styleSheet.addImport(G);
			r[F.styleID].push(H);
			m[H].styleID = F.styleID
		} else {
			var E = j.C("link");
			E.setAttribute("rel", "Stylesheet");
			E.setAttribute("type", "text/css");
			E.setAttribute("charset", "utf-8");
			E.setAttribute("href", G);
			E.setAttribute("id", H);
			document.getElementsByTagName("head")[0].appendChild(E)
		}
	};
	var z = {};
	var B = function(E, G) {
		var H = j.E(E);
		if (H) {
			G(H);
			z[E] && delete z[E];
			for (var F in z) {
				B(F, z[F])
			}
		} else {
			z[E] = G
		}
	};
	var n = function(H) {
		if (j.IE) {
			var F = m[H].styleID;
			var G = r[F];
			var E = j.E(F);
			if ((sheetID = c(H, G)) > -1) {
				(E.styleSheet || E.sheet).removeImport(sheetID);
				G.splice(sheetID, 1)
			}
		} else {
			j.core.dom.removeNode(j.E(H))
		}
		delete f[m[H].cssURL];
		delete m[H]
	};
	var d = function(F, I, H) {
		for (var G in g) {
			if (!j.E(G)) {
				delete g[G]
			}
		}
		g[F] = {
			js: {},
			css: {}
		};
		if (H) {
			for (var G = 0, E = H.length; G < E; ++G) {
				g[F].css[p + H[G]] = 1
			}
		}
	};
	var a = function() {
		for (var H in m) {
			var F = false,
				G = m[H].cssURL;
			for (var E in g) {
				if (g[E].css[G]) {
					F = true;
					break
				}
			}
			if (!F) {
				n(H)
			}
		}
	};
	var x = function(G, F) {
		var E = f[G] || (f[G] = {
			loaded: false,
			list: []
		});
		if (E.loaded) {
			F(G);
			return false
		}
		E.list.push(F);
		if (E.list.length > 1) {
			return false
		}
		return true
	};
	var C = function(F) {
		var E = f[F].list;
		if (E) {
			for (var G = 0; G < E.length; G++) {
				E[G](F)
			}
			f[F].loaded = true;
			delete f[F].list
		}
	};
	var u = function(N) {
		var H = N.url,
			L = N.load_ID,
			J = N.complete,
			K = N.pid,
			F = p + H,
			E = "css_" + j.core.util.getUniqueKey();
		if (!x(F, J)) {
			return
		}
		v(E, F);
		var G = j.C("div");
		G.id = L;
		j.core.util.hideContainer.appendChild(G);
		var M = 3000;
		var I = function() {
			if (parseInt(j.core.dom.getStyle(G, "height")) == 42) {
				j.core.util.hideContainer.removeChild(G);
				C(F);
				return
			}
			if (--M > 0) {
				setTimeout(I, 10)
			} else {
				j.log(F + "timeout!");
				j.core.util.hideContainer.removeChild(G);
				C(F);
				n(E);
				v(E, F)
			}
		};
		setTimeout(I, 50)
	};
	var q = function(G, F) {
		var E = D + G;
		if (!x(E, F)) {
			return
		}
		j.core.io.scriptLoader({
			url: E,
			onComplete: function() {
				C(E)
			},
			onTimeout: function() {
				j.log(E + "timeout!");
				delete f[E]
			}
		})
	};
	var b = function(F, E) {
		if (!y[F]) {
			y[F] = E
		}
	};
	var h = function(E) {
		if (E) {
			if (y[E]) {
				try {
					A[E] || (A[E] = y[E](j))
				} catch (G) {
					j.log(E, G)
				}
			} else {
				j.log("start:ns=" + E + " ,have not been registed")
			}
			return
		}
		var F = [];
		for (E in y) {
			F.push(E)
		}
		j.core.func.timedChunk(F, {
			process: function(H) {
				try {
					A[H] || (A[H] = y[H](j))
				} catch (I) {
					j.log(H, I)
				}
			}
		})
	};
	var t = function(E) {
		var F = 1,
			M, L, K, I, G, O, H;
		E = E || {};
		L = E.pid;
		K = E.html;
		G = E.js ? [].concat(E.js) : [];
		I = E.css ? [].concat(E.css) : [];
		if (L == undefined) {
			j.log("node pid[" + L + "] is undefined");
			return
		}
		d(L, G, I);
		O = function() {
			if (--F > 0) {
				return
			}
			B(L, function(P) {
				(K != undefined) && (P.innerHTML = K);
				if (G.length > 0) {
					H()
				}
				a()
			})
		};
		H = function(P) {
			if (G.length > 0) {
				q(G.shift(), H)
			}
			if (P && P.indexOf("/pl/") != -1) {
				var Q = P.replace(/^.*?\/(pl\/.*)\.js\??.*$/, "$1").replace(/\//g, ".");
				w(Q);
				h(Q)
			}
		};
		if (I.length > 0) {
			F += I.length;
			for (var J = 0, N;
				(N = I[J]); J++) {
				u({
					url: N,
					load_ID: "js_" + N.replace(/^\/?(.*)\.css\??.*$/i, "$1").replace(/\//g, "_"),
					complete: O,
					pid: L
				})
			}
		}
		O()
	};
	var w = function(E) {
		if (E) {
			if (A[E]) {
				j.log("destroy:" + E);
				try {
					A[E].destroy()
				} catch (F) {
					j.log(F)
				}
				delete A[E]
			}
			return
		}
		for (E in A) {
			j.log("destroy:" + E);
			try {
				A[E] && A[E].destroy && A[E].destroy()
			} catch (F) {
				j.log(E, F)
			}
		}
		A = {}
	};
	var o = {
		register: b,
		start: h,
		view: t,
		clear: w,
		destroy: function() {
			o.clear();
			f = {};
			A = {};
			y = {};
			e = undefined
		}
	};
	j.core.dom.ready(function() {
		j.core.evt.addEvent(window, "unload", function() {
			j.core.evt.removeEvent(window, "unload", arguments.callee);
			o.destroy()
		})
	});
	return o
});
(function() {
	var b = STK.core;
	var c = {
		tween: b.ani.tween,
		tweenArche: b.ani.tweenArche,
		arrCopy: b.arr.copy,
		arrClear: b.arr.clear,
		hasby: b.arr.hasby,
		unique: b.arr.unique,
		foreach: b.arr.foreach,
		isArray: b.arr.isArray,
		inArray: b.arr.inArray,
		arrIndexOf: b.arr.indexOf,
		findout: b.arr.findout,
		domNext: b.dom.next,
		domPrev: b.dom.prev,
		isNode: b.dom.isNode,
		addHTML: b.dom.addHTML,
		insertHTML: b.dom.insertHTML,
		setXY: b.dom.setXY,
		contains: b.dom.contains,
		position: b.dom.position,
		trimNode: b.dom.trimNode,
		insertAfter: b.dom.insertAfter,
		insertBefore: b.dom.insertBefore,
		removeNode: b.dom.removeNode,
		replaceNode: b.dom.replaceNode,
		Ready: b.dom.ready,
		setStyle: b.dom.setStyle,
		setStyles: b.dom.setStyles,
		getStyle: b.dom.getStyle,
		addClassName: b.dom.addClassName,
		hasClassName: b.dom.hasClassName,
		removeClassName: b.dom.removeClassName,
		builder: b.dom.builder,
		cascadeNode: b.dom.cascadeNode,
		selector: b.dom.selector,
		sizzle: b.dom.sizzle,
		addEvent: b.evt.addEvent,
		custEvent: b.evt.custEvent,
		removeEvent: b.evt.removeEvent,
		fireEvent: b.evt.fireEvent,
		fixEvent: b.evt.fixEvent,
		getEvent: b.evt.getEvent,
		stopEvent: b.evt.stopEvent,
		delegatedEvent: b.evt.delegatedEvent,
		preventDefault: b.evt.preventDefault,
		hotKey: b.evt.hotKey,
		memorize: b.func.memorize,
		bind: b.func.bind,
		getType: b.func.getType,
		methodBefore: b.func.methodBefore,
		timedChunk: b.func.timedChunk,
		funcEmpty: b.func.empty,
		ajax: b.io.ajax,
		jsonp: b.io.jsonp,
		ijax: b.io.ijax,
		scriptLoader: b.io.scriptLoader,
		require: b.io.require,
		jsonInclude: b.json.include,
		jsonCompare: b.json.compare,
		jsonClone: b.json.clone,
		jsonToQuery: b.json.jsonToQuery,
		queryToJson: b.json.queryToJson,
		jsonToStr: b.json.jsonToStr,
		strToJson: b.json.strToJson,
		objIsEmpty: b.obj.isEmpty,
		beget: b.obj.beget,
		cascade: b.obj.cascade,
		objSup: b.obj.sup,
		parseParam: b.obj.parseParam,
		bLength: b.str.bLength,
		dbcToSbc: b.str.dbcToSbc,
		leftB: b.str.leftB,
		trim: b.str.trim,
		encodeHTML: b.str.encodeHTML,
		decodeHTML: b.str.decodeHTML,
		parseURL: b.str.parseURL,
		parseHTML: b.str.parseHTML,
		queryString: b.str.queryString,
		htmlToJson: b.util.htmlToJson,
		cookie: b.util.cookie,
		drag: b.util.drag,
		timer: b.util.timer,
		jobsM: b.util.jobsM,
		listener: b.util.listener,
		winSize: b.util.winSize,
		pageSize: b.util.pageSize,
		templet: b.util.templet,
		queue: b.util.queue,
		stack: b.util.stack,
		swf: b.util.swf,
		URL: b.util.URL,
		scrollPos: b.util.scrollPos,
		scrollTo: b.util.scrollTo,
		getUniqueKey: b.util.getUniqueKey,
		storage: b.util.storage,
		pageletM: b.util.pageletM
	};
	for (var a in c) {
		STK.regShort(a, c[a])
	}
})();
;!function($) {

    if (!$.os.tablet && !$.os.phone && !$.os.ipod) {
        return;
    }

    if (window.devicePixelRatio && devicePixelRatio > 1) {
        var testEle = document.createElement('div');
        testEle.style.border = '.5px solid transparent';
        document.body.appendChild(testEle);
        if (testEle.offsetHeight == 1) {
            document.querySelector('html').classList.add('hairline');
        }
        document.body.removeChild(testEle);
    }
}(Zepto);

;!function($) {
    $('body').on('touchend', '[action-type=inputClick]', function(e){
        e.preventDefault();
        var input = $(this).find('input')[0];
       
        input.checked = !input.checked;
        $(input).trigger('change');
    })
}(Zepto);

// Copyright (c) 2005  Tom Wu
// All Rights Reserved.
// See "LICENSE" for details.

// Basic JavaScript BN library - subset useful for RSA encryption.

// Bits per digit
var dbits;

// JavaScript engine analysis
var canary = 0xdeadbeefcafe;
var j_lm = ((canary&0xffffff)==0xefcafe);

// (public) Constructor
function BigInteger(a,b,c) {
  if(a != null)
    if("number" == typeof a) this.fromNumber(a,b,c);
    else if(b == null && "string" != typeof a) this.fromString(a,256);
    else this.fromString(a,b);
}

// return new, unset BigInteger
function nbi() { return new BigInteger(null); }

// am: Compute w_j += (x*this_i), propagate carries,
// c is initial carry, returns final carry.
// c < 3*dvalue, x < 2*dvalue, this_i < dvalue
// We need to select the fastest one that works in this environment.

// am1: use a single mult and divide to get the high bits,
// max digit bits should be 26 because
// max internal value = 2*dvalue^2-2*dvalue (< 2^53)
function am1(i,x,w,j,c,n) {
  while(--n >= 0) {
    var v = x*this[i++]+w[j]+c;
    c = Math.floor(v/0x4000000);
    w[j++] = v&0x3ffffff;
  }
  return c;
}
// am2 avoids a big mult-and-extract completely.
// Max digit bits should be <= 30 because we do bitwise ops
// on values up to 2*hdvalue^2-hdvalue-1 (< 2^31)
function am2(i,x,w,j,c,n) {
  var xl = x&0x7fff, xh = x>>15;
  while(--n >= 0) {
    var l = this[i]&0x7fff;
    var h = this[i++]>>15;
    var m = xh*l+h*xl;
    l = xl*l+((m&0x7fff)<<15)+w[j]+(c&0x3fffffff);
    c = (l>>>30)+(m>>>15)+xh*h+(c>>>30);
    w[j++] = l&0x3fffffff;
  }
  return c;
}
// Alternately, set max digit bits to 28 since some
// browsers slow down when dealing with 32-bit numbers.
function am3(i,x,w,j,c,n) {
  var xl = x&0x3fff, xh = x>>14;
  while(--n >= 0) {
    var l = this[i]&0x3fff;
    var h = this[i++]>>14;
    var m = xh*l+h*xl;
    l = xl*l+((m&0x3fff)<<14)+w[j]+c;
    c = (l>>28)+(m>>14)+xh*h;
    w[j++] = l&0xfffffff;
  }
  return c;
}
if(j_lm && (navigator.appName == "Microsoft Internet Explorer")) {
  BigInteger.prototype.am = am2;
  dbits = 30;
}
else if(j_lm && (navigator.appName != "Netscape")) {
  BigInteger.prototype.am = am1;
  dbits = 26;
}
else { // Mozilla/Netscape seems to prefer am3
  BigInteger.prototype.am = am3;
  dbits = 28;
}

BigInteger.prototype.DB = dbits;
BigInteger.prototype.DM = ((1<<dbits)-1);
BigInteger.prototype.DV = (1<<dbits);

var BI_FP = 52;
BigInteger.prototype.FV = Math.pow(2,BI_FP);
BigInteger.prototype.F1 = BI_FP-dbits;
BigInteger.prototype.F2 = 2*dbits-BI_FP;

// Digit conversions
var BI_RM = "0123456789abcdefghijklmnopqrstuvwxyz";
var BI_RC = new Array();
var rr,vv;
rr = "0".charCodeAt(0);
for(vv = 0; vv <= 9; ++vv) BI_RC[rr++] = vv;
rr = "a".charCodeAt(0);
for(vv = 10; vv < 36; ++vv) BI_RC[rr++] = vv;
rr = "A".charCodeAt(0);
for(vv = 10; vv < 36; ++vv) BI_RC[rr++] = vv;

function int2char(n) { return BI_RM.charAt(n); }
function intAt(s,i) {
  var c = BI_RC[s.charCodeAt(i)];
  return (c==null)?-1:c;
}

// (protected) copy this to r
function bnpCopyTo(r) {
  for(var i = this.t-1; i >= 0; --i) r[i] = this[i];
  r.t = this.t;
  r.s = this.s;
}

// (protected) set from integer value x, -DV <= x < DV
function bnpFromInt(x) {
  this.t = 1;
  this.s = (x<0)?-1:0;
  if(x > 0) this[0] = x;
  else if(x < -1) this[0] = x+this.DV;
  else this.t = 0;
}

// return bigint initialized to value
function nbv(i) { var r = nbi(); r.fromInt(i); return r; }

// (protected) set from string and radix
function bnpFromString(s,b) {
  var k;
  if(b == 16) k = 4;
  else if(b == 8) k = 3;
  else if(b == 256) k = 8; // byte array
  else if(b == 2) k = 1;
  else if(b == 32) k = 5;
  else if(b == 4) k = 2;
  else { this.fromRadix(s,b); return; }
  this.t = 0;
  this.s = 0;
  var i = s.length, mi = false, sh = 0;
  while(--i >= 0) {
    var x = (k==8)?s[i]&0xff:intAt(s,i);
    if(x < 0) {
      if(s.charAt(i) == "-") mi = true;
      continue;
    }
    mi = false;
    if(sh == 0)
      this[this.t++] = x;
    else if(sh+k > this.DB) {
      this[this.t-1] |= (x&((1<<(this.DB-sh))-1))<<sh;
      this[this.t++] = (x>>(this.DB-sh));
    }
    else
      this[this.t-1] |= x<<sh;
    sh += k;
    if(sh >= this.DB) sh -= this.DB;
  }
  if(k == 8 && (s[0]&0x80) != 0) {
    this.s = -1;
    if(sh > 0) this[this.t-1] |= ((1<<(this.DB-sh))-1)<<sh;
  }
  this.clamp();
  if(mi) BigInteger.ZERO.subTo(this,this);
}

// (protected) clamp off excess high words
function bnpClamp() {
  var c = this.s&this.DM;
  while(this.t > 0 && this[this.t-1] == c) --this.t;
}

// (public) return string representation in given radix
function bnToString(b) {
  if(this.s < 0) return "-"+this.negate().toString(b);
  var k;
  if(b == 16) k = 4;
  else if(b == 8) k = 3;
  else if(b == 2) k = 1;
  else if(b == 32) k = 5;
  else if(b == 4) k = 2;
  else return this.toRadix(b);
  var km = (1<<k)-1, d, m = false, r = "", i = this.t;
  var p = this.DB-(i*this.DB)%k;
  if(i-- > 0) {
    if(p < this.DB && (d = this[i]>>p) > 0) { m = true; r = int2char(d); }
    while(i >= 0) {
      if(p < k) {
        d = (this[i]&((1<<p)-1))<<(k-p);
        d |= this[--i]>>(p+=this.DB-k);
      }
      else {
        d = (this[i]>>(p-=k))&km;
        if(p <= 0) { p += this.DB; --i; }
      }
      if(d > 0) m = true;
      if(m) r += int2char(d);
    }
  }
  return m?r:"0";
}

// (public) -this
function bnNegate() { var r = nbi(); BigInteger.ZERO.subTo(this,r); return r; }

// (public) |this|
function bnAbs() { return (this.s<0)?this.negate():this; }

// (public) return + if this > a, - if this < a, 0 if equal
function bnCompareTo(a) {
  var r = this.s-a.s;
  if(r != 0) return r;
  var i = this.t;
  r = i-a.t;
  if(r != 0) return (this.s<0)?-r:r;
  while(--i >= 0) if((r=this[i]-a[i]) != 0) return r;
  return 0;
}

// returns bit length of the integer x
function nbits(x) {
  var r = 1, t;
  if((t=x>>>16) != 0) { x = t; r += 16; }
  if((t=x>>8) != 0) { x = t; r += 8; }
  if((t=x>>4) != 0) { x = t; r += 4; }
  if((t=x>>2) != 0) { x = t; r += 2; }
  if((t=x>>1) != 0) { x = t; r += 1; }
  return r;
}

// (public) return the number of bits in "this"
function bnBitLength() {
  if(this.t <= 0) return 0;
  return this.DB*(this.t-1)+nbits(this[this.t-1]^(this.s&this.DM));
}

// (protected) r = this << n*DB
function bnpDLShiftTo(n,r) {
  var i;
  for(i = this.t-1; i >= 0; --i) r[i+n] = this[i];
  for(i = n-1; i >= 0; --i) r[i] = 0;
  r.t = this.t+n;
  r.s = this.s;
}

// (protected) r = this >> n*DB
function bnpDRShiftTo(n,r) {
  for(var i = n; i < this.t; ++i) r[i-n] = this[i];
  r.t = Math.max(this.t-n,0);
  r.s = this.s;
}

// (protected) r = this << n
function bnpLShiftTo(n,r) {
  var bs = n%this.DB;
  var cbs = this.DB-bs;
  var bm = (1<<cbs)-1;
  var ds = Math.floor(n/this.DB), c = (this.s<<bs)&this.DM, i;
  for(i = this.t-1; i >= 0; --i) {
    r[i+ds+1] = (this[i]>>cbs)|c;
    c = (this[i]&bm)<<bs;
  }
  for(i = ds-1; i >= 0; --i) r[i] = 0;
  r[ds] = c;
  r.t = this.t+ds+1;
  r.s = this.s;
  r.clamp();
}

// (protected) r = this >> n
function bnpRShiftTo(n,r) {
  r.s = this.s;
  var ds = Math.floor(n/this.DB);
  if(ds >= this.t) { r.t = 0; return; }
  var bs = n%this.DB;
  var cbs = this.DB-bs;
  var bm = (1<<bs)-1;
  r[0] = this[ds]>>bs;
  for(var i = ds+1; i < this.t; ++i) {
    r[i-ds-1] |= (this[i]&bm)<<cbs;
    r[i-ds] = this[i]>>bs;
  }
  if(bs > 0) r[this.t-ds-1] |= (this.s&bm)<<cbs;
  r.t = this.t-ds;
  r.clamp();
}

// (protected) r = this - a
function bnpSubTo(a,r) {
  var i = 0, c = 0, m = Math.min(a.t,this.t);
  while(i < m) {
    c += this[i]-a[i];
    r[i++] = c&this.DM;
    c >>= this.DB;
  }
  if(a.t < this.t) {
    c -= a.s;
    while(i < this.t) {
      c += this[i];
      r[i++] = c&this.DM;
      c >>= this.DB;
    }
    c += this.s;
  }
  else {
    c += this.s;
    while(i < a.t) {
      c -= a[i];
      r[i++] = c&this.DM;
      c >>= this.DB;
    }
    c -= a.s;
  }
  r.s = (c<0)?-1:0;
  if(c < -1) r[i++] = this.DV+c;
  else if(c > 0) r[i++] = c;
  r.t = i;
  r.clamp();
}

// (protected) r = this * a, r != this,a (HAC 14.12)
// "this" should be the larger one if appropriate.
function bnpMultiplyTo(a,r) {
  var x = this.abs(), y = a.abs();
  var i = x.t;
  r.t = i+y.t;
  while(--i >= 0) r[i] = 0;
  for(i = 0; i < y.t; ++i) r[i+x.t] = x.am(0,y[i],r,i,0,x.t);
  r.s = 0;
  r.clamp();
  if(this.s != a.s) BigInteger.ZERO.subTo(r,r);
}

// (protected) r = this^2, r != this (HAC 14.16)
function bnpSquareTo(r) {
  var x = this.abs();
  var i = r.t = 2*x.t;
  while(--i >= 0) r[i] = 0;
  for(i = 0; i < x.t-1; ++i) {
    var c = x.am(i,x[i],r,2*i,0,1);
    if((r[i+x.t]+=x.am(i+1,2*x[i],r,2*i+1,c,x.t-i-1)) >= x.DV) {
      r[i+x.t] -= x.DV;
      r[i+x.t+1] = 1;
    }
  }
  if(r.t > 0) r[r.t-1] += x.am(i,x[i],r,2*i,0,1);
  r.s = 0;
  r.clamp();
}

// (protected) divide this by m, quotient and remainder to q, r (HAC 14.20)
// r != q, this != m.  q or r may be null.
function bnpDivRemTo(m,q,r) {
  var pm = m.abs();
  if(pm.t <= 0) return;
  var pt = this.abs();
  if(pt.t < pm.t) {
    if(q != null) q.fromInt(0);
    if(r != null) this.copyTo(r);
    return;
  }
  if(r == null) r = nbi();
  var y = nbi(), ts = this.s, ms = m.s;
  var nsh = this.DB-nbits(pm[pm.t-1]);	// normalize modulus
  if(nsh > 0) { pm.lShiftTo(nsh,y); pt.lShiftTo(nsh,r); }
  else { pm.copyTo(y); pt.copyTo(r); }
  var ys = y.t;
  var y0 = y[ys-1];
  if(y0 == 0) return;
  var yt = y0*(1<<this.F1)+((ys>1)?y[ys-2]>>this.F2:0);
  var d1 = this.FV/yt, d2 = (1<<this.F1)/yt, e = 1<<this.F2;
  var i = r.t, j = i-ys, t = (q==null)?nbi():q;
  y.dlShiftTo(j,t);
  if(r.compareTo(t) >= 0) {
    r[r.t++] = 1;
    r.subTo(t,r);
  }
  BigInteger.ONE.dlShiftTo(ys,t);
  t.subTo(y,y);	// "negative" y so we can replace sub with am later
  while(y.t < ys) y[y.t++] = 0;
  while(--j >= 0) {
    // Estimate quotient digit
    var qd = (r[--i]==y0)?this.DM:Math.floor(r[i]*d1+(r[i-1]+e)*d2);
    if((r[i]+=y.am(0,qd,r,j,0,ys)) < qd) {	// Try it out
      y.dlShiftTo(j,t);
      r.subTo(t,r);
      while(r[i] < --qd) r.subTo(t,r);
    }
  }
  if(q != null) {
    r.drShiftTo(ys,q);
    if(ts != ms) BigInteger.ZERO.subTo(q,q);
  }
  r.t = ys;
  r.clamp();
  if(nsh > 0) r.rShiftTo(nsh,r);	// Denormalize remainder
  if(ts < 0) BigInteger.ZERO.subTo(r,r);
}

// (public) this mod a
function bnMod(a) {
  var r = nbi();
  this.abs().divRemTo(a,null,r);
  if(this.s < 0 && r.compareTo(BigInteger.ZERO) > 0) a.subTo(r,r);
  return r;
}

// Modular reduction using "classic" algorithm
function Classic(m) { this.m = m; }
function cConvert(x) {
  if(x.s < 0 || x.compareTo(this.m) >= 0) return x.mod(this.m);
  else return x;
}
function cRevert(x) { return x; }
function cReduce(x) { x.divRemTo(this.m,null,x); }
function cMulTo(x,y,r) { x.multiplyTo(y,r); this.reduce(r); }
function cSqrTo(x,r) { x.squareTo(r); this.reduce(r); }

Classic.prototype.convert = cConvert;
Classic.prototype.revert = cRevert;
Classic.prototype.reduce = cReduce;
Classic.prototype.mulTo = cMulTo;
Classic.prototype.sqrTo = cSqrTo;

// (protected) return "-1/this % 2^DB"; useful for Mont. reduction
// justification:
//         xy == 1 (mod m)
//         xy =  1+km
//   xy(2-xy) = (1+km)(1-km)
// x[y(2-xy)] = 1-k^2m^2
// x[y(2-xy)] == 1 (mod m^2)
// if y is 1/x mod m, then y(2-xy) is 1/x mod m^2
// should reduce x and y(2-xy) by m^2 at each step to keep size bounded.
// JS multiply "overflows" differently from C/C++, so care is needed here.
function bnpInvDigit() {
  if(this.t < 1) return 0;
  var x = this[0];
  if((x&1) == 0) return 0;
  var y = x&3;		// y == 1/x mod 2^2
  y = (y*(2-(x&0xf)*y))&0xf;	// y == 1/x mod 2^4
  y = (y*(2-(x&0xff)*y))&0xff;	// y == 1/x mod 2^8
  y = (y*(2-(((x&0xffff)*y)&0xffff)))&0xffff;	// y == 1/x mod 2^16
  // last step - calculate inverse mod DV directly;
  // assumes 16 < DB <= 32 and assumes ability to handle 48-bit ints
  y = (y*(2-x*y%this.DV))%this.DV;		// y == 1/x mod 2^dbits
  // we really want the negative inverse, and -DV < y < DV
  return (y>0)?this.DV-y:-y;
}

// Montgomery reduction
function Montgomery(m) {
  this.m = m;
  this.mp = m.invDigit();
  this.mpl = this.mp&0x7fff;
  this.mph = this.mp>>15;
  this.um = (1<<(m.DB-15))-1;
  this.mt2 = 2*m.t;
}

// xR mod m
function montConvert(x) {
  var r = nbi();
  x.abs().dlShiftTo(this.m.t,r);
  r.divRemTo(this.m,null,r);
  if(x.s < 0 && r.compareTo(BigInteger.ZERO) > 0) this.m.subTo(r,r);
  return r;
}

// x/R mod m
function montRevert(x) {
  var r = nbi();
  x.copyTo(r);
  this.reduce(r);
  return r;
}

// x = x/R mod m (HAC 14.32)
function montReduce(x) {
  while(x.t <= this.mt2)	// pad x so am has enough room later
    x[x.t++] = 0;
  for(var i = 0; i < this.m.t; ++i) {
    // faster way of calculating u0 = x[i]*mp mod DV
    var j = x[i]&0x7fff;
    var u0 = (j*this.mpl+(((j*this.mph+(x[i]>>15)*this.mpl)&this.um)<<15))&x.DM;
    // use am to combine the multiply-shift-add into one call
    j = i+this.m.t;
    x[j] += this.m.am(0,u0,x,i,0,this.m.t);
    // propagate carry
    while(x[j] >= x.DV) { x[j] -= x.DV; x[++j]++; }
  }
  x.clamp();
  x.drShiftTo(this.m.t,x);
  if(x.compareTo(this.m) >= 0) x.subTo(this.m,x);
}

// r = "x^2/R mod m"; x != r
function montSqrTo(x,r) { x.squareTo(r); this.reduce(r); }

// r = "xy/R mod m"; x,y != r
function montMulTo(x,y,r) { x.multiplyTo(y,r); this.reduce(r); }

Montgomery.prototype.convert = montConvert;
Montgomery.prototype.revert = montRevert;
Montgomery.prototype.reduce = montReduce;
Montgomery.prototype.mulTo = montMulTo;
Montgomery.prototype.sqrTo = montSqrTo;

// (protected) true iff this is even
function bnpIsEven() { return ((this.t>0)?(this[0]&1):this.s) == 0; }

// (protected) this^e, e < 2^32, doing sqr and mul with "r" (HAC 14.79)
function bnpExp(e,z) {
  if(e > 0xffffffff || e < 1) return BigInteger.ONE;
  var r = nbi(), r2 = nbi(), g = z.convert(this), i = nbits(e)-1;
  g.copyTo(r);
  while(--i >= 0) {
    z.sqrTo(r,r2);
    if((e&(1<<i)) > 0) z.mulTo(r2,g,r);
    else { var t = r; r = r2; r2 = t; }
  }
  return z.revert(r);
}

// (public) this^e % m, 0 <= e < 2^32
function bnModPowInt(e,m) {
  var z;
  if(e < 256 || m.isEven()) z = new Classic(m); else z = new Montgomery(m);
  return this.exp(e,z);
}

// protected
BigInteger.prototype.copyTo = bnpCopyTo;
BigInteger.prototype.fromInt = bnpFromInt;
BigInteger.prototype.fromString = bnpFromString;
BigInteger.prototype.clamp = bnpClamp;
BigInteger.prototype.dlShiftTo = bnpDLShiftTo;
BigInteger.prototype.drShiftTo = bnpDRShiftTo;
BigInteger.prototype.lShiftTo = bnpLShiftTo;
BigInteger.prototype.rShiftTo = bnpRShiftTo;
BigInteger.prototype.subTo = bnpSubTo;
BigInteger.prototype.multiplyTo = bnpMultiplyTo;
BigInteger.prototype.squareTo = bnpSquareTo;
BigInteger.prototype.divRemTo = bnpDivRemTo;
BigInteger.prototype.invDigit = bnpInvDigit;
BigInteger.prototype.isEven = bnpIsEven;
BigInteger.prototype.exp = bnpExp;

// public
BigInteger.prototype.toString = bnToString;
BigInteger.prototype.negate = bnNegate;
BigInteger.prototype.abs = bnAbs;
BigInteger.prototype.compareTo = bnCompareTo;
BigInteger.prototype.bitLength = bnBitLength;
BigInteger.prototype.mod = bnMod;
BigInteger.prototype.modPowInt = bnModPowInt;

// "constants"
BigInteger.ZERO = nbv(0);
BigInteger.ONE = nbv(1);

;!function($) {
    $.ispc = !$.os.tablet && !$.os.phone && !$.os.ipod;
    if ($.os.tablet || $.os.phone || $.os.ipod) {
        return;
    }
    var old_on = $.fn.on;
    $.fn.on = function(type) {
        if (type === 'tap') {
            arguments[0] = 'click';
        }
        if (type === 'touchend') {
            arguments[0] = 'mouseup';
        }
        return old_on.apply(this, arguments)
    }
    var old_tap = $.fn.tap;
    $.fn.tap = function() {
        return $.fn.click.apply(this, arguments);
    };
    var old_touchend = $.fn.touchend;
    $.fn.touchend = function() {
        return $.fn.mouseup.apply(this, arguments);
    };
}(Zepto);

// prng4.js - uses Arcfour as a PRNG

function Arcfour() {
  this.i = 0;
  this.j = 0;
  this.S = new Array();
}

// Initialize arcfour context from key, an array of ints, each from [0..255]
function ARC4init(key) {
  var i, j, t;
  for(i = 0; i < 256; ++i)
    this.S[i] = i;
  j = 0;
  for(i = 0; i < 256; ++i) {
    j = (j + this.S[i] + key[i % key.length]) & 255;
    t = this.S[i];
    this.S[i] = this.S[j];
    this.S[j] = t;
  }
  this.i = 0;
  this.j = 0;
}

function ARC4next() {
  var t;
  this.i = (this.i + 1) & 255;
  this.j = (this.j + this.S[this.i]) & 255;
  t = this.S[this.i];
  this.S[this.i] = this.S[this.j];
  this.S[this.j] = t;
  return this.S[(t + this.S[this.i]) & 255];
}

Arcfour.prototype.init = ARC4init;
Arcfour.prototype.next = ARC4next;

// Plug in your RNG constructor here
function prng_newstate() {
  return new Arcfour();
}

// Pool size must be a multiple of 4 and greater than 32.
// An array of bytes the size of the pool will be passed to init()
var rng_psize = 256;

// Random number generator - requires a PRNG backend, e.g. prng4.js

// For best results, put code like
// <body onClick='rng_seed_time();' onKeyPress='rng_seed_time();'>
// in your main HTML document.

var rng_state;
var rng_pool;
var rng_pptr;

// Mix in a 32-bit integer into the pool
function rng_seed_int(x) {
  rng_pool[rng_pptr++] ^= x & 255;
  rng_pool[rng_pptr++] ^= (x >> 8) & 255;
  rng_pool[rng_pptr++] ^= (x >> 16) & 255;
  rng_pool[rng_pptr++] ^= (x >> 24) & 255;
  if(rng_pptr >= rng_psize) rng_pptr -= rng_psize;
}

// Mix in the current time (w/milliseconds) into the pool
function rng_seed_time() {
  rng_seed_int(new Date().getTime());
}

// Initialize the pool with junk if needed.
if(rng_pool == null) {
  rng_pool = new Array();
  rng_pptr = 0;
  var t;
  if(typeof window !== "undefined" && window.crypto) {
    if (window.crypto.getRandomValues) {
      // Use webcrypto if available
      var ua = new Uint8Array(32);
      window.crypto.getRandomValues(ua);
      for(t = 0; t < 32; ++t)
        rng_pool[rng_pptr++] = ua[t];
    }
    if(navigator.appName == "Netscape" && navigator.appVersion < "5") {
      // Extract entropy (256 bits) from NS4 RNG if available
      var z = window.crypto.random(32);
      for(t = 0; t < z.length; ++t)
        rng_pool[rng_pptr++] = z.charCodeAt(t) & 255;
    }
  }
  while(rng_pptr < rng_psize) {  // extract some randomness from Math.random()
    t = Math.floor(65536 * Math.random());
    rng_pool[rng_pptr++] = t >>> 8;
    rng_pool[rng_pptr++] = t & 255;
  }
  rng_pptr = 0;
  rng_seed_time();
  //rng_seed_int(window.screenX);
  //rng_seed_int(window.screenY);
}

function rng_get_byte() {
  if(rng_state == null) {
    rng_seed_time();
    rng_state = prng_newstate();
    rng_state.init(rng_pool);
    for(rng_pptr = 0; rng_pptr < rng_pool.length; ++rng_pptr)
      rng_pool[rng_pptr] = 0;
    rng_pptr = 0;
    //rng_pool = null;
  }
  // TODO: allow reseeding after first request
  return rng_state.next();
}

function rng_get_bytes(ba) {
  var i;
  for(i = 0; i < ba.length; ++i) ba[i] = rng_get_byte();
}

function SecureRandom() {}

SecureRandom.prototype.nextBytes = rng_get_bytes;

// Depends on jsbn.js and rng.js

// Version 1.1: support utf-8 encoding in pkcs1pad2

// convert a (hex) string to a bignum object
function parseBigInt(str,r) {
  return new BigInteger(str,r);
}

function linebrk(s,n) {
  var ret = "";
  var i = 0;
  while(i + n < s.length) {
    ret += s.substring(i,i+n) + "\n";
    i += n;
  }
  return ret + s.substring(i,s.length);
}

function byte2Hex(b) {
  if(b < 0x10)
    return "0" + b.toString(16);
  else
    return b.toString(16);
}

// PKCS#1 (type 2, random) pad input string s to n bytes, and return a bigint
function pkcs1pad2(s,n) {
  if(n < s.length + 11) { // TODO: fix for utf-8
    throw new Error("Message too long for RSA");
  }
  var ba = new Array();
  var i = s.length - 1;
  while(i >= 0 && n > 0) {
    var c = s.charCodeAt(i--);
    if(c < 128) { // encode using utf-8
      ba[--n] = c;
    }
    else if((c > 127) && (c < 2048)) {
      ba[--n] = (c & 63) | 128;
      ba[--n] = (c >> 6) | 192;
    }
    else {
      ba[--n] = (c & 63) | 128;
      ba[--n] = ((c >> 6) & 63) | 128;
      ba[--n] = (c >> 12) | 224;
    }
  }
  ba[--n] = 0;
  var rng = new SecureRandom();
  var x = new Array();
  while(n > 2) { // random non-zero pad
    x[0] = 0;
    while(x[0] == 0) rng.nextBytes(x);
    ba[--n] = x[0];
  }
  ba[--n] = 2;
  ba[--n] = 0;
  return new BigInteger(ba);
}

// "empty" RSA key constructor
function RSAKey() {
  this.n = null;
  this.e = 0;
  this.d = null;
  this.p = null;
  this.q = null;
  this.dmp1 = null;
  this.dmq1 = null;
  this.coeff = null;
}

// Set the public key fields N and e from hex strings
function RSASetPublic(N,E) {
  if(N != null && E != null && N.length > 0 && E.length > 0) {
    this.n = parseBigInt(N,16);
    this.e = parseInt(E,16);
  }
  else
    throw new Error("Invalid RSA public key");
}

// Perform raw public operation on "x": return x^e (mod n)
function RSADoPublic(x) {
  return x.modPowInt(this.e, this.n);
}

// Return the PKCS#1 RSA encryption of "text" as an even-length hex string
function RSAEncrypt(text) {
  var m = pkcs1pad2(text,(this.n.bitLength()+7)>>3);
  if(m == null) return null;
  var c = this.doPublic(m);
  if(c == null) return null;
  var h = c.toString(16);
  if((h.length & 1) == 0) return h; else return "0" + h;
}

// Return the PKCS#1 RSA encryption of "text" as a Base64-encoded string
//function RSAEncryptB64(text) {
//  var h = this.encrypt(text);
//  if(h) return hex2b64(h); else return null;
//}

// protected
RSAKey.prototype.doPublic = RSADoPublic;

// public
RSAKey.prototype.setPublic = RSASetPublic;
RSAKey.prototype.encrypt = RSAEncrypt;
//RSAKey.prototype.encrypt_b64 = RSAEncryptB64;

;/**
 * pre monitor and logging module, add this in header to catch
 * global error in window
 * @author MrGalaxyn
 */
(function(global) {
    var start = new Date().getTime();
    var bee = {
        logs: [],
        timings: [],
        errors: [],
        pageLoaded: -1,
        domLoaded: -1,
        // store the log, we'll deal with it later
        log: function(msg, lv) {
            bee.logs.push({a:arguments, t: now() - start});
        },
        timing: function(name, time, real){
            bee.timings.push({n: name, t: (time || now()), r: real});
        },
        info: function(msg) { bee.log(msg, 'i'); },
        warn: function(msg) { bee.log(msg, 'w'); },
        error: function(msg) { bee.log(msg, 'e'); }
    };
    var addListener = function(el, type, fn) {
        if (el.addEventListener) {
            el.addEventListener(type, fn, false);
        } else if (el.attachEvent) {
            el.attachEvent("on" + type, fn);
        }
    };
    var now = (function() {
        try {
            if("performance" in window && window.performance && window.performance.now) {
                return function() {
                    return Math.round(window.performance.now() + window.performance.timing.navigationStart);
                };
            }
        }
        catch(ignore) { }
        return Date.now || function() { return new Date().getTime(); };
    }());
    var pageReady = function() { bee.pageLoaded = now(); };
    var domLoaded = function() { bee.domLoaded = now(); };

    // we store domcontentLoaded and load event trigger time
    addListener(window, "DOMContentLoaded", domLoaded);
    if(window.onpagehide || window.onpagehide === null) {
        addListener(window, "pageshow", pageReady);
    } else {
       addListener(window, "load", pageReady);
    }
    // store the error, we'll deal with it later
    window.onerror = function(message, file, line, column) {
        bee.errors.push({
            a: [{
                message: message,
                fileName: file,
                lineNumber: line || 0,
                columnNumber: column || 0
            }, "win.err"],
            t: now() - start
        });
        return false;
    };
    
    bee.start = start;
    global.bee = bee;
    bee.end = now();
    
})(window);
