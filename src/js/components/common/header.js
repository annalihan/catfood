module.exports= React.createClass({
    render: function(){
        function build(){
            var isLogin = $CONFIG.username;
            if(isLogin){
                return(
                    <div className="headerTop" node-type="headerTop">
                        <a href="mybooklist">
                            <img src="http://js.catfood.wap.grid.sina.com.cn/img/mybook.png" node-type="mybook" className="mybook" />
                            <i className="circlePoint" node-type="circlePoint" style={{display:"none"}} ></i>
                        </a>
                        <img src="http://js.catfood.wap.grid.sina.com.cn/img/back.png" node-type="back" className="back" />
                        <a href="index">
                            <img src="http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif" className="book_angle" />
                        </a>
                        <div className="user" node-type="user">
                            <img src="http://js.catfood.wap.grid.sina.com.cn/img/log.png" node-type="user_image" />
                            <span node-type="name" className="name">{$CONFIG.username}</span>
                        </div>
                    </div>
                );
            }
            else{
                return(
                    <div className="headerTop" node-type="headerTop">
                        <a href="mybooklist" style={{display:"none"}}>
                            <img src="http://js.catfood.wap.grid.sina.com.cn/img/mybook.png" node-type="mybook" className="mybook" />
                        </a>
                        <a href="index">
                            <img src="http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif" className="book_angle" />
                        </a>
                    </div>
                );
            }
        }
        
        return build();
    }
})
