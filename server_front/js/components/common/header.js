steel.d("components/common/header", [],function(require, exports, module) {
module.exports= React.createClass({displayName: "exports",
    render: function(){
        function build(){
            var isLogin = $CONFIG.username;
            if(isLogin){
                return(
                    React.createElement("div", {className: "headerTop", "node-type": "headerTop"}, 
                        React.createElement("a", {href: "mybooklist"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/mybook.png", "node-type": "mybook", className: "mybook"}), 
                            React.createElement("i", {className: "circlePoint", "node-type": "circlePoint", style: {display:"none"}})
                        ), 
                        React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/back.png", "node-type": "back", className: "back"}), 
                        React.createElement("a", {href: "index"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif", className: "book_angle"})
                        ), 
                        React.createElement("div", {className: "user", "node-type": "user"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/log.png", "node-type": "user_image"}), 
                            React.createElement("span", {"node-type": "name", className: "name"}, $CONFIG.username)
                        )
                    )
                );
            }
            else{
                return(
                    React.createElement("div", {className: "headerTop", "node-type": "headerTop"}, 
                        React.createElement("a", {href: "mybooklist", style: {display:"none"}}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/mybook.png", "node-type": "mybook", className: "mybook"})
                        ), 
                        React.createElement("a", {href: "index"}, 
                            React.createElement("img", {src: "http://js.catfood.wap.grid.sina.com.cn/img/book_angle.gif", className: "book_angle"})
                        )
                    )
                );
            }
        }
        
        return build();
    }
})

});