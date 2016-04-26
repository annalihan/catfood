;!function($) {
    $('body').on('touchend', '[action-type=inputClick]', function(e){
        e.preventDefault();
        var input = $(this).find('input')[0];
       
        input.checked = !input.checked;
        $(input).trigger('change');
    })
}(Zepto);
