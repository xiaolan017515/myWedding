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
	},
	getAreaName: function(callback, async) {
		var value = void 0;
		$.ajax({
			url: $.getCtx() + '/rest/sys/getParentArea',
			data: {
				areaId: $.getUrlParam("areaId")
			},
			async: async || false,
			header: {
				"sig": $.getUrlParam('sig')
			},
			type: "GET",
			success: function(data) {
				if (data.code == 0) {
					value = data;
					typeof(callback) === 'function' ? callback(data): '';
				} else {
					$.noDataFunc();
				}

			}
		});
		return value;
	},
	checkAreaName: function(id, name, shortName) {
		let data = [id, name, shortName];
		PubSub.publish('checkMapSelect', data);
	},
	noDataFunc: function() {
			setTimeout(() => {
				$('.onLoading').find('img').attr('src', 'img/common/noData.png');
				$('.onLoading').find('.puffLoading').html('数据完善中...');
			}, 500);
		}
		//线上路径
		// getCtx: function() {
		// 	var webroot=document.location.href;
		// 	webroot=webroot.substring(webroot.indexOf('//')+2,webroot.length);
		// 	webroot=webroot.substring(webroot.indexOf('/')+1,webroot.length);
		// 	webroot=webroot.substring(0,webroot.indexOf('/'));
		// 	var rootpath="/"+webroot;
		// 	return rootpath;
		// }
});
//本地路径
$.onloadJavascript("./js/static/common/publicCommon.js", false, true);

//提取字符串中介于两个指定下标之间的字符
export function splitString(string, start, end) {
	var result = string.substring(start, end);
	return result;
};

export function getJson(areaid) {
	var value;
	var data = $.ajax({
		url: '../../mapJson/' + areaid + '.json',
		dataType: 'json',
		header: {
			"sig": $.getUrlParam('sig')
		},
		async: false,
		success: function(data) {
			value = data;
		}
	});
	return value;
};

export function getAreaCp(areaid) {
	var jsonObject = getJson(areaid);
	var mapAreaCpObject = '{';
	for (var i in jsonObject.features) {
		var feature = jsonObject.features[i];
		mapAreaCpObject += '"' + feature.properties.name + '"' + ':';
		mapAreaCpObject += JSON.stringify(feature.properties.cp);
		if (i != jsonObject.features.length - 1) {
			mapAreaCpObject += ',';
		}
	}
	mapAreaCpObject = mapAreaCpObject + '}';
	return JSON.parse(mapAreaCpObject);
};

export function pushScrollNum(value, tag) {
	var num = $(tag);
	num.animate({
		count: value
	}, {
		duration: 800,
		step: function() {
			num.html(formatPrice(String(parseInt(this.count))));
		},
		complete: function() {
			num.html(formatPrice(String(parseInt(value))));
		}
	});
};

//将数字转化成 亿万文字分开
export function formatPrice(count, isShowEnd) {
	if (!count) {
		return false;
	}
	count = parseInt(count);
	var result = "";
	if (count < 10000) {
		result = addZero(count);
	} else if (count > 10000 && count < 100000000) {
		var end = count % 10000;
		var wan = parseInt(count / 10000);
		result = "" + wan + "<span class='short-size-span'>万</span>" + addZero(end);
	} else if (count > 100000000) {
		var yi = parseInt(count / 100000000);
		var yiEnd = count % 100000000;
		var end = yiEnd % 10000;
		var wan = parseInt(yiEnd / 10000);
		result = "" + yi + "<span class='short-size-span'>亿</span>" + addZero(wan) + "<span class='short-size-span'>万</span>" + addZero(end);
	} else {
		result = count;
	}

	return isShowEnd ? result + "<span class='short-size-span'>元</span>" : result;
};
//位数不够补零
export function addPlaceHolder(number) {
	var len = ("" + number).length;
	if (len == 1) {
		number = "0" + number;
	} else {
		number = number;
	};
	return number;
};
//补零
export function addZero(number) {
	var len = ("" + number).length;
	if (len == 1) {
		number = "000" + number;
	} else if (len == 2) {
		number = "00" + number;
	} else if (len == 3) {
		number = "0" + number;
	}
	return number;
};

//将数字转化成千分符格式
export function toThousands(num) {
	var result = [],
		counter = 0;
	num = (num || 0).toString().split('');
	for (var i = num.length - 1; i >= 0; i--) {
		counter++;
		result.unshift(num[i]);
		if (!(counter % 3) && i != 0) {
			result.unshift(',');
		}
	}
	return result.join('');
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
