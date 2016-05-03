var Header = require('../../common/header');
var Search = require('../../common/search');
var Datalist = require('../../common/datalist');

module.exports = React.createClass({
    displayName: 'BookList',
    render: function(){
        return(
            <div>
                <Header />
                <Search />
                <Datalist url="booklist"/>
            </div>
        );
    }
});
