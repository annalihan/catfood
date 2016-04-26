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