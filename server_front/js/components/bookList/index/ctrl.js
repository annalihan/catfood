steel.d("components/bookList/index/ctrl", [],function(require, exports, module) {
/**
 * 图书列表
 */
module.exports = function(control) {
    control.set({
        data: null,
        tpl: 'tpl/bookList/bookList'
    });
};
});