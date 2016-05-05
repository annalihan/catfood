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


