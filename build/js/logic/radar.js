steel.d("logic/radar",["kit/dom/parseDOM"],function(a,b,c){a("kit/dom/parseDOM");var d=STK,e=Math.PI/180;c.exports=function(a){function b(a){a.strokeStyle="#00ff00",a.beginPath(),a.arc(.5*k,.5*k,.4*k,0,2*Math.PI,!0),a.font="16px 微软雅黑",a.fillStyle="#fff",a.fillText("编程实践",.05*k,.08*k),a.fillText("架构与设计",.75*k,.08*k),a.fillText("方法学",.05*k,.95*k),a.fillText("思想与领导力",.7*k,.95*k),a.closePath(),a.stroke(),a.beginPath(),a.arc(.5*k,.5*k,.3*k,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.5*k,.5*k,.2*k,0,2*Math.PI,!0),a.closePath(),a.stroke()}function c(a){a.beginPath(),a.arc(.2*k,.3*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.25*k,.35*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.75*k,.35*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.7*k,.25*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.3*k,.6*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.45*k,.6*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.7*k,.7*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke(),a.beginPath(),a.arc(.6*k,.65*k,3,0,2*Math.PI,!0),a.closePath(),a.stroke()}function f(a){a.beginPath(),a.moveTo(.5*k,.03*k),a.lineTo(.5*k,.97*k),a.closePath(),a.stroke(),a.beginPath(),a.moveTo(.03*k,.5*k),a.lineTo(.96*k,.5*k),a.closePath(),a.stroke()}function g(a,b){a.fillStyle="rgba(75,225,24,0.3)",a.beginPath(),a.arc(k/2,k/2,.4*k,e*b,e*(b+90),!1),a.lineTo(k/2,k/2),a.fill()}function h(a,b){var c=0;l=setInterval(function(){a.clearRect(0,0,b.width,b.height),a.translate(0,0),c+=3,i(a),g(a,c)},80)}function i(a){b(a),c(a),f(a)}var j={},k=window.innerWidth,l={},m={DOM:{},objs:{},DOM_eventFun:{myCanvas:function(a){var b=a.clientX,c=d.position(m.DOM.myCanvas).t,e=a.clientY-c,f="http://catfood.wap.grid.sina.com.cn/catfood/bookList?book_type=";if(.5*k>b&&e>.02*k&&.5*k>e)steel.setState(f+"1");else if(b>.5*k&&e>.02*k&&.5*k>e)steel.setState(f+"2");else if(.5*k>b&&e>.5*k&&.98*k>e)steel.setState(f+"4");else{if(!(b>.5*k&&e>.5*k&&.98*k>e))return;steel.setState(f+"3")}}},bindCustEvtFuns:{},bindListenerFuns:{}},n=function(){if(null==a||null!=a&&!d.isNode(a))throw"[]:argsCheck()-The param node is not a DOM node."},o=function(){m.DOM=d.kit.dom.parseDOM(d.builder(a).list)},p=function(){var a=m.DOM.myCanvas,b=a.getContext("2d"),c=window.devicePixelRatio||1,d=b.webkitBackingStorePixelRatio||b.mozBackingStorePixelRatio||b.msBackingStorePixelRatio||b.oBackingStorePixelRatio||b.backingStorePixelRatio||1,e=c/d;if(c!==d){var f=k,g=k;a.width=f*e,a.height=g*e,a.style.width=f+"px",a.style.height=g+"px",b.scale(e,e)}else a.width=k,a.height=k;i(b),h(b,a)},q=function(){},r=function(){d.addEvent(m.DOM.myCanvas,"click",m.DOM_eventFun.myCanvas)},s=function(){},t=function(){},u=function(){m&&(d.removeEvent(m.DOM.myCanvas,"click",m.DOM_eventFun.myCanvas),d.removeEvent(m.DOM.borrow,"click",m.DOM_eventFun.borrow),clearInterval(l),d.foreach(m.objs,function(a){a&&a.destroy&&a.destroy()}),m=null)},v=function(){n(),o(),p(),q(),r(),s(),t()};return v(),j.destroy=u,j}}),steel.d("kit/dom/parseDOM",[],function(a,b,c){STK.register("kit.dom.parseDOM",function(a){return function(a){for(var b in a)a[b]&&1==a[b].length&&(a[b]=a[b][0]);return a}})});