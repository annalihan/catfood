/*
 * author xxx | xxx2@staff.sina.com.cn
 * 示例功能
 * 写清楚作者和功能
 */
require("kit/dom/parseDOM");
//---常量定义区----------------------------------
var $ = STK;
var DEG = (Math.PI) / 180;

module.exports = function (node) {
    //---变量定义区---------------------------------
    var that = {};
    var w = window.innerWidth;
    var timer = {};

    var _this = {
        DOM: {},//节点容器
        objs: {},//组件容器
        //直接与dom操作相关的方法都存放在DOM_eventFun
        DOM_eventFun: {
            myCanvas: function (e) {
                var x = e.clientX;
                var top = $.position(_this.DOM.myCanvas).t;
                var y = e.clientY - top;
                var url = '/catfood/bookList?book_type=';

                if (x < 0.5 * w && y > 0.02 * w && y < 0.5 * w) {
                    steel.router.push(url + '1');
                } else if (x > 0.5 * w && y > 0.02 * w && y < 0.5 * w) {
                    steel.router.push(url + '2');
                } else if (x < 0.5 * w && y > 0.5 * w && y < 0.98 * w) {
                    steel.router.push(url + '4');
                } else if (x > 0.5 * w && y > 0.5 * w && y < 0.98 * w) {
                    steel.router.push(url + '3');
                } else {
                    return;
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

    //---模块初始化数据------------------------
    var initData = function() {
        /*扫描雷达*/
        var radar = _this.DOM.myCanvas;
        var canvas_obj = radar.getContext('2d');
        var devicePixelRatio = window.devicePixelRatio || 1;
        var backingStorePixelRatio = canvas_obj.webkitBackingStorePixelRatio ||
            canvas_obj.mozBackingStorePixelRatio ||
            canvas_obj.msBackingStorePixelRatio ||
            canvas_obj.oBackingStorePixelRatio ||
            canvas_obj.backingStorePixelRatio || 1;
        var ratio = devicePixelRatio / backingStorePixelRatio;

        if (devicePixelRatio !== backingStorePixelRatio) {
            var oldWidth = w;
            var oldHeight = w;

            radar.width = oldWidth * ratio;
            radar.height = oldHeight * ratio;

            radar.style.width = oldWidth + 'px';
            radar.style.height = oldHeight + 'px';

            canvas_obj.scale(ratio, ratio);
        }
        else {
            radar.width = w;
            radar.height = w;
        }

        initRadder(canvas_obj);
        move(canvas_obj, radar);
    }
    //-------------------------------------------

    //---模块的初始化方法定义区-------------------------
    var initPlugins = function () {

    }
    //-------------------------------------------

    //---DOM事件绑定方法定义区-------------------------
    var bindDOM = function () {
        $.addEvent(_this.DOM.myCanvas, 'click', _this.DOM_eventFun.myCanvas);
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
            $.removeEvent(_this.DOM.myCanvas, 'click', _this.DOM_eventFun.myCanvas);
            $.removeEvent(_this.DOM.borrow, 'click', _this.DOM_eventFun.borrow);
            clearInterval(timer);
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
        initData();
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

    //绘圆
    function circle(canvas_obj) {
        canvas_obj.strokeStyle = '#00ff00';
        canvas_obj.beginPath();
        canvas_obj.arc(0.5 * w, 0.5 * w, 0.4 * w, 0, Math.PI * 2, true);
        canvas_obj.font = '16px 微软雅黑';
        canvas_obj.fillStyle = '#fff';
        canvas_obj.fillText('编程实践', 0.05 * w, 0.08 * w);
        canvas_obj.fillText('架构与设计', 0.75 * w, 0.08 * w);
        canvas_obj.fillText('方法学', 0.05 * w, 0.95 * w);
        canvas_obj.fillText('思想与领导力', 0.7 * w, 0.95 * w);
        canvas_obj.closePath();
        canvas_obj.stroke();

        canvas_obj.beginPath();
        canvas_obj.arc(0.5 * w, 0.5 * w, 0.3 * w, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();

        canvas_obj.beginPath();
        canvas_obj.arc(0.5 * w, 0.5 * w, 0.2 * w, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
    }


    /*点*/
    function spot(canvas_obj) {
        canvas_obj.beginPath();
        canvas_obj.arc(0.2 * w, 0.3 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.25 * w, 0.35 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.75 * w, 0.35 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.7 * w, 0.25 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.3 * w, 0.6 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.45 * w, 0.6 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.7 * w, 0.7 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
        canvas_obj.beginPath();
        canvas_obj.arc(0.6 * w, 0.65 * w, 3, 0, Math.PI * 2, true);
        canvas_obj.closePath();
        canvas_obj.stroke();
    }

    /*线条*/
    function line(canvas_obj) {
        canvas_obj.beginPath();
        canvas_obj.moveTo(0.5 * w, 0.03 * w);
        canvas_obj.lineTo(0.5 * w, 0.97 * w);
        canvas_obj.closePath();
        canvas_obj.stroke();

        canvas_obj.beginPath();
        canvas_obj.moveTo(0.03 * w, 0.5 * w);
        canvas_obj.lineTo(0.96 * w, 0.5 * w);
        canvas_obj.closePath();
        canvas_obj.stroke();
    }

    /*扇形*/
    function sector(canvas_obj, d) {
        canvas_obj.fillStyle = 'rgba(75,225,24,0.3)';
        canvas_obj.beginPath();
        canvas_obj.arc(w / 2, w / 2, w * 0.4, DEG * d, DEG * (d + 90), false);
        canvas_obj.lineTo(w / 2, w / 2);
        canvas_obj.fill();
    }

    /*旋转*/
    function move(canvas_obj, radar) {
        var d = 0;
        timer = setInterval(function () {
            canvas_obj.clearRect(0, 0, radar.width, radar.height);
            canvas_obj.translate(0, 0);
            d = d + 3;
            initRadder(canvas_obj);
            sector(canvas_obj, d);

        },80);
    }

    function initRadder(canvas_obj) {
        circle(canvas_obj);
        spot(canvas_obj);
        line(canvas_obj);
    }

    return that;
};