var $ = STK;
require("common/trans/operate");
require('kit/dom/parseDOM');
var onSearch = require('common/channel/onSearch'); //search事件
var onBorrow = require('common/channel/onBorrow'); //借书事件

module.exports=React.createClass({
    getInitialState: function(){
        return {
            loading:false,
            err:false,
            searching:false,
            search: {
                page: 1,
                size: 10,
                key: ''
            },
            bookList:[]
        }
    },
    componentWillMount:function(){
        // this.setState({bookList: this.props.data.bookList});
    },
    componentWillReceiveProps:function(nextProps){
        // this.setState({bookList: nextProps.data.bookList});
    },
    componentDidMount: function(){
        this.scrollDataList();
        onSearch.register('search', this.bindListenerFuns.searchClick.bind(this));
        onSearch.register('cancel', this.bindListenerFuns.cancelSearch.bind(this));
    },
    bindListenerFuns: { //广播事件回调函数
        searchClick: function(searchText) { //点击搜索
            this.setState({searching:true,error:false});
            var data = this.state.search;
            data.key = searchText;
            // alert("search"+data.key);
            this.ajSearch('search',data);
        },
        cancelSearch: function() { //取消搜索
            this.setState({searching:true,error:false});
            var data = this.state.search;
            data.key = '';
            this.ajSearch('search',data);
        }
    },
    FUNS: { //非事件回调函数
        /*applyBookSuccess: function() { //借书成功
            $.custEvent.remove(_this.objs.objConfirm);
            _this.objs.objConfirm.destroy();
            var arg = {
                title: '借阅成功!',
                description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png">按钮查看',
                button1: '取消',
                button2: '立即查看'
            };
            _this.objs.objConfirm = confirmUI(arg);
            $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.look);
        },
        backBookSuccess: function() { //还书成功
            var arg = {
                title: '审核中!',
                description: '详情请点击 左上角<img src="http://js.catfood.wap.grid.sina.com.cn/img/lettlebook.png"/>按钮查看',
                button1: '取消',
                button2: '立即查看',
            };
            _this.objs.objConfirm = confirmUI(arg);
            $.custEvent.add(_this.objs.objConfirm, 'button2Click', _this.bindCustEvtFuns.look);
        },*/

    },
    scrollDataList: function() { //滚动到最下触发方法
        // this.setState({loading:true,error:false});
        if (this.props.url == 'booklist') {
            this.ajSearch('scroll_booklist');

        } else {
            this.ajSearch('scroll_mybooklist');
        }
    },
    ajSearch: function(api,data) {
        var _this = this;
        if(data){
            this.setState({search:data});
        }
        $.common.trans.operate.getTrans(api, {
            'onSuccess': function(data) {
                _this.setState({
                    bookList: data.data.bookList,
                    searching: false,
                    loading: false,
                    error: false
                });
            },
            'onError': function(){
                _this.setState({
                    searching: false,
                    loading: false,
                    error: true
                });
            }
        }).request(data);
    },
    build:function(bookList){
        function buildType(book){
            switch(book.book_category){
                case '1':
                    return "编程实践";
                    break;
                case '2':
                    return "架构与设计";
                break;
                case '3':
                    return "思想与领导力";
                    break;
                case '4':
                    return "方法学";
                    break;
            }
        }
        function buildOpt(book){
            switch(book.book_status){
                case '0':
                    return (
                        <a className="bookOperatorBorrow bookOperator"
                            href="javascript:void(0);" action-type="operatorClick"
                            action-data="book_id={book.book_id}&book_status={book.book_status}&detail_call_number={book.detail_call_number}" > 借 </a>
                    );
                    break;
                case '1':
                    return (<div className="bookOperatorTry bookOperator">审</div>);
                    break;
                case '3':
                    return (<div className="bookOperatorLack bookOperator">缺</div>);
                    break;
                case '2':
                    return (
                        <a className="bookOperatorReturn bookOperator"
                            href="javascript:void(0);"
                            action-type="operatorClick"
                            action-data="book_id={book.book_id}&book_status={book.book_status}&detail_call_number={book.detail_call_number}"> 还 </a>
                    );
                    break;
            }
        }
        var result = [];
        for(var i = 0,len = bookList.length;i < len; i++){
            result.push(<li className="bookMsgLi">
                <div className="bookData">
                    <div className="bookImg">
                        <img src={bookList[i].book_img} />
                    </div>
                    <div className="bookMsg">
                        <h3 title={bookList[i].book_name}> {bookList[i].book_name} </h3>
                        <p>{bookList[i].book_press}</p>
                        <p> {bookList[i].book_author}</p>
                        <br />
                        <p className="bookType">
                            {buildType(bookList[i])}
                        </p>
                    </div>
                    {buildOpt(bookList[i])}
                </div>
            </li>);
        }
        return result;
    },
    buildeFooter:function(){
        if(this.state.loading){
            return (
                <footer node-type="footer">
                    <span>努力加载中...</span>
                    <img src="http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif"/>
                </footer>
            );
        }
        else if(this.state.error){
            return (
                <footer node-type="footer" onClick={this.scrollDataList} >
                    <a href="javascript:void(0);">
                        <span>出错了，请重试</span>
                        <img  src="http://js.catfood.wap.grid.sina.com.cn/img/refresh.png"/>
                    </a>
                </footer>
            );
        }
        else if(this.state.bookList.length==0){
            return (
                <footer node-type="footer">
                    <span>暂无更多了，欢迎贡献</span>
                </footer>
            );
        }
        else{
            return (
                <footer node-type="footer">
                    <a href="http://catfood.wap.grid.sina.com.cn/catfood/bookList"><span>暂无更多了，去借书</span><img className="toBorrow" src="http://js.catfood.wap.grid.sina.com.cn/img/angle.png"/></a>
                </footer>
            );
        }
    },

    render: function(){
        return (
            <div>
                <ul className="dataListContent" node-type="dataListContent">
                    <div style={{textAlign: 'center',marginTop:'7em',height:'40em', display: this.state.searching?'':'none' }}>
                        <span style={{display:'inline-block',fontSize:'1.5em'}}>努力搜索中...</span>
                        <img src="http://js.catfood.wap.grid.sina.com.cn/img/bloading.gif" style={{display:'inline-block',width:'1.5em'}}/>
                    </div>
                    {this.build(this.state.bookList)}
                </ul>
                {this.buildeFooter()}
            </div>
        );
    }
});

