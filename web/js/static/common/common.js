/*动态加载*/
$.extend({
    getloadingimg: (callback, id) => {
        $.loadingImg = [];
        var imgs = document.images;
        for (var i = 0; i < imgs.length; i++) {
            $.loadingImg.push(imgs[i].src);
        }

        var cssImages = $.getallBgimages();
        for (var j = 0; j < cssImages.length; j++) {
            $.loadingImg.push(cssImages[j]);
        }

        $.loadingstart(callback, id);
    },
    loadingstart: (callback, id) => {
        var images = $.loadingImg;
        var loader = new WxMoment.Loader();

        //声明资源文件列表
        var fileList = images;

        for (var i = 0; i < fileList.length; i++) {
            loader.addImage(fileList[i]);
        }

        //进度监听
        var functionList = void 0;
        loader.addProgressListener(e => {
            var percent = Math.round((e.completedCount / e.totalCount) * 100);
            //Loading 页面中百分比的显示
            id && $.animate_num(id, percent, "%", (num) => {
                functionList();
            });

        });

        //加载完成
        loader.addCompletionListener((data) => {
            functionList = function() {
                if (callback && typeof(callback) === 'function') {
                    callback();
                }
            }

        });

        //启动加载
        loader.start();
    },
    getallBgimages: () => {
        var url,
            B = [],
            A = document.getElementsByTagName('*');
        A = B.slice.call(A, 0, A.length);
        while (A.length) {
            url = $.deepCss(A.shift(), 'background-image');
            if (url) url = /url\(['"]?([^")]+)/.exec(url) || [];
            url = url[1];
            if (url && B.indexOf(url) == -1) B[B.length] = url;
        }
        return B;
    },
    deepCss: (who, css) => {
        if (!who || !who.style) return '';
        var sty = css.replace(/\-([a-z])/g, function(a, b) {
            return b.toUpperCase();
        });
        if (who.currentStyle) {
            return who.style[sty] || who.currentStyle[sty] || '';
        }
        var dv = document.defaultView || window;

        return who.style[sty] || dv.getComputedStyle(who, "").getPropertyValue(css) || '';

        Array.prototype.indexOf = Array.prototype.indexOf || function(what, index) {
            index = index || 0;
            var L = this.length;
            while (index < L) {
                if (this[index] === what) return index;
                ++index;
            }
            return -1;
        }
    },
    animate_num: (type, value, fu, func) => {
        var num = $(type);
        num.animate({
            count: value
        }, {
            duration: 300,
            step: function() {
                num.html(parseInt(this.count) + fu);
            },
            complete: function() {
                num.html(parseInt(value) + fu);
                if (parseInt(value) == 100) {
                    func(parseInt(value));
                }
            }
        });
    },
    splitString: (string, start, end) => {
        var result = string.substring(start, end);
        return result;
    },
    // 图片懒加载
    lazyloadImg: function(url, callback) {
        var img = new Image();
        img.src = url;
        img.onload = callback(img);
    },
    MathRand: function(sum = 5) {
        var Num = "";
        for (var i = 0; i < sum; i++) {
            Num += Math.floor(Math.random() * 10);
        }
        return Num;
    },
    formatMsgTime: function(timespan) {
        var dateTime = new Date(timespan),
            year = dateTime.getFullYear(),
            month = $.addZ(dateTime.getMonth() + 1),
            day = $.addZ(dateTime.getDate()),
            hour = $.addZ(dateTime.getHours()),
            minute = $.addZ(dateTime.getMinutes()),
            second = $.addZ(dateTime.getSeconds()),
            now = new Date(),
            now_new = Date.parse(now.toDateString()); //typescript转换写法

        var milliseconds = now - dateTime,
            timeSpanStr = void 0;

        if (milliseconds <= 1000 * 60 * 1) {
            timeSpanStr = '刚刚';
        } else if (1000 * 60 * 1 < milliseconds && milliseconds <= 1000 * 60 * 60) {
            timeSpanStr = Math.round((milliseconds / (1000 * 60))) + '分钟前';
        } else if (1000 * 60 * 60 * 1 < milliseconds && milliseconds <= 1000 * 60 * 60 * 24) {
            timeSpanStr = Math.round(milliseconds / (1000 * 60 * 60)) + '小时前';
        } else if (1000 * 60 * 60 * 24 < milliseconds && milliseconds <= 1000 * 60 * 60 * 24 * 15) {
            timeSpanStr = Math.round(milliseconds / (1000 * 60 * 60 * 24)) + '天前';
        } else if (milliseconds > 1000 * 60 * 60 * 24 * 15 && year == now.getFullYear()) {
            timeSpanStr = month + '-' + day + ' ' + hour + ':' + minute;
        } else {
            timeSpanStr = year + '-' + month + '-' + day + ' ' + hour + ':' + minute;
        }
        console.log(timeSpanStr);
        return timeSpanStr;
    },
    addZ: function(num) {
        var result = num < 10 ? "0" + num : num;
        return result;
    },
    regTest: function( type, str ) {
        var reg = null;
        if ( type == "phone" ) {
            reg = /^0*(13|14|15|17|18|19)\d{9}$/
        } else if ( type == "email" ) {
            reg = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/; 
        } else if ( type == "chinese" ) {
            reg = /^[\u4e00-\u9fa5]+$/
        } else if ( type == "null" ) {
            reg = /^\S+$/
        }
        return reg.test(str);
    } 
});