
module.exports = function(control) {
    var queryJSON = STK.queryToJson(location.search && location.search.substr(1));
    queryJSON.page = queryJSON.page || 1;
    queryJSON.size = 10;
    var pathname = window.location.pathname.split('/');

    var  href = pathname[pathname.length - 1];
    if(href == 'bookList'){
        control.set('data', '/aj/booklist/booklist?' + STK.jsonToQuery(queryJSON));
    }
    else {
        control.set('data', '/aj/booklist/historybooklist?' + STK.jsonToQuery(queryJSON));
    }
};