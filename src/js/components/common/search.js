var onSearch = require('common/channel/onSearch');
var $ = STK;

module.exports = React.createClass({
    getInitialState:function(){
        return {
            searching: false,
            key:''
        }
    },
    componentDidMount:function(){
    },
    DOM_eventFun: { //DOM事件行为容器
        searchInputClick: function() {
            this.setState({searching:true});
            // this.refs.searchInputReal.focus();
        },
        searchInputChange: function(e) {
            var searchText = e.target.value;
            this.setState({key:searchText});
        },
        searchButtonClick: function() {
            var searchText = this.state.key;
            if (searchText) {
                onSearch.fire('search', [searchText]);
                this.setState({key:''});
            } else {
                this.setState({
                    searching:false
                });
                onSearch.fire('cancel', []);
            }

        }
    },
    render: function(){
        var searchTextInit = "查找我想借的书";
        return (
            <div className="search">
                <div className="searchInput"
                    style={{display:(this.state.searching?"none":'block')}}
                    onClick={this.DOM_eventFun.searchInputClick.bind(this)} >
                    <img className="searchInputImg"  src="http://js.catfood.wap.grid.sina.com.cn/img/search.png" />
                    <p className="placeholder">{searchTextInit}</p>
                </div>
                <div className="searchTextReal"
                    style={{display:(this.state.searching?'block':'none')}} >
                    <img className="searchTextRealImg" src="http://js.catfood.wap.grid.sina.com.cn/img/search.png" />
                    <div className="searchInputBorder">
                        <input className="searchInputReal"
                            type="text" maxlength="30" ref="searchInputReal"
                            value={this.state.key}
                            onChange={this.DOM_eventFun.searchInputChange.bind(this)} />
                    </div>
                    <a className="searchButton" href="javascript:void(0);"
                        onClick={this.DOM_eventFun.searchButtonClick.bind(this)}>
                        {this.state.key?"确定":"取消"}
                    </a>
                </div>
            </div>

        );
    }
});

