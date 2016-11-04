/*动态加载*/
import PubSub from 'pubsub-js';
$.extend({
	includePath: './js/static/',
	include: function(file) {
		var files = typeof file == "string" ? [file] : file;
		for (var i = 0; i < files.length; i++) {
			var name = files[i].replace(/^\s|\s$/g, "");
			var att = name.split('.');
			var ext = att[att.length - 1].toLowerCase();
			var isCSS = ext == "css";
			var tag = isCSS ? "link" : "script";
			var attr = isCSS ? " type='text/css' rel='stylesheet' " : " language='javascript' type='text/javascript' ";
			var link = (isCSS ? "href" : "src") + "='" + $.includePath + name + "'";
			if ($(tag + "[" + link + "]").length == 0) document.write("<" + tag + attr + link + "></" + tag + ">");
		}
	},
	onloadJavascript: function(url, async, cache, callback) {
		$.ajax({
			url: url,
			dataType: "script",
			header: {
				"sig": $.getUrlParam('sig')
			},
			async: async,
			cache: cache
		}).done(function(data) {
			if (callback && typeof(callback) === 'function') {
				callback(data);
			}
		});
	},
	getUrlParam: function(key) {
		var reg = new RegExp("(^|&)" + key + "=([^&]*)(&|$)");
		var result = window.location.search.substr(1).match(reg);
		return result ? decodeURIComponent(result[2]) : null;
	}
});

//提取字符串中介于两个指定下标之间的字符
export function splitString(string, start, end) {
	var result = string.substring(start, end);
	return result;
};

export function show_num(type, value, isShowEnd) {
	var num = $(type);
	num.animate({
		count: value
	}, {
		duration: 800,
		step: function() {
			num.html(formatPrice(String(parseInt(this.count)), isShowEnd));
		},
		complete: function() {
			num.html(formatPrice(String(parseInt(value)), isShowEnd));
		}
	});
};