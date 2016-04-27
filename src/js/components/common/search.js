var onSearch = require('common/channel/onSearch');
var $ = STK;

module.exports = React.createClass({
    componentDidMount:function(){
    },
    DOM_eventFun: { //DOM事件行为容器
        searchInputClick: function() {
            $.setStyle(this.refs.searchInput, 'display', 'none');
            $.setStyle(this.refs.searchTextReal, 'display', 'block');
            this.refs.searchInputReal.focus();
        },
        searchInputChange: function() {
            var searchText = this.refs.searchInputReal.value;
            if (searchText) {
                this.refs.searchButton.innerHTML = '搜索';
            } else {
                this.refs.searchButton.innerHTML = '取消';
            }

        },
        searchButtonClick: function() {
            var searchText = this.refs.searchInputReal.value;
            if (this.refs.searchButton.innerHTML === '搜索') {
                onSearch.fire('search', [searchText]);
                this.refs.searchButton.innerHTML = '取消';
            } else {
                $.setStyle(this.refs.searchTextReal, 'display', 'none');
                $.setStyle(this.refs.searchInput, 'display', 'block');
                this.refs.searchInputReal.value = '';

                onSearch.fire('cancel', []);
            }

        }
    },
    render: function(){
        var searchTextInit = "查找我想借的书";
        return (
            <div className="search">
                <div className="searchInput" ref="searchInput" 
                    onClick={this.DOM_eventFun.searchInputClick.bind(this)} >
                    <img className="searchInputImg"  src="http://js.catfood.wap.grid.sina.com.cn/img/search.png" />
                    <p className="placeholder">{searchTextInit}</p>
                </div>
                <div className="searchTextReal" ref="searchTextReal" style={{display:"none"}} >
                    <img className="searchTextRealImg" src="http://js.catfood.wap.grid.sina.com.cn/img/search.png" />
                    <div className="searchInputBorder">
                        <input className="searchInputReal" type="text" ref="searchInputReal" maxlength="30" 
                            onChange={this.DOM_eventFun.searchInputChange.bind(this)} />
                    </div>
                    <a className="searchButton" href="javascript:void(0);" ref="searchButton" 
                        onClick={this.DOM_eventFun.searchButtonClick.bind(this)}> 取消 </a>
                </div>
            </div>

        );
    }
});
        
