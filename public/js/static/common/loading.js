/*
 * loading.js - CSS loaded/ready state notification and loading show
 *
 * Author: zhouxinjian
 * Version: 0.1
 * Created: 20160902
 */
var onLoadObj = {
	init: function(){
		this.creatCss();
	},
	creatLoad:function(){
		var createNode = document.createElement("div"),
			createDivTop = document.createElement("div"),
			createDivBot = document.createElement("div"),
			createImg = document.createElement("img"),
			createP = document.createElement("p"),
	    	createTextNode = document.createTextNode("正在从云端获取数据");

	    document.body.appendChild(createNode);
	    createNode.className = "onLoading";
	    createDivTop.className = "top";
	    createDivBot.className = "bottom";
	    createImg.src = "img/svg/bar.svg";
	    createP.className = "puffLoading";
	    createP.appendChild(createTextNode);
	    createNode.appendChild(createDivTop);
	    createNode.appendChild(createDivBot);

	    createDivTop.appendChild(createImg);
	    createDivBot.appendChild(createP);
	},
	creatCss:function(){
		var csslink = document.createElement('link');
        csslink.setAttribute('rel', 'stylesheet');
        csslink.setAttribute('type', 'text/css');
        csslink.setAttribute('href', 'css/loading.min.css');
        document.head.appendChild(csslink);
        this.cssReady(this.creatLoad);
	},
	cssReady:function(fn, link){
		var d = document, 
		t = d.createStyleSheet, 
		r = t ? 'rules' : 'cssRules', 
		s = t ? 'styleSheet' : 'sheet', 
		l = d.getElementsByTagName('link'); 
		link || (link = l[l.length - 1]); 
		function check() { 
			try { 
				return link && link[s] && link[s][r] && link[s][r][0]; 
			} catch(e) { 
				return false; 
			} 
		} 
		(function poll() { 
			check() && setTimeout(fn, 0) || setTimeout(poll, 100);
		})();
	}
};
onLoadObj.init();