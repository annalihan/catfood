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
