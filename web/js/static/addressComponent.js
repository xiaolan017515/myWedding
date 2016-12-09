import "../../less/address.less";
import React from 'react';

function loadJS(src, callback) {
	var script = document.createElement('script');
	var head = document.getElementsByTagName('head')[0];
	script.src = src;
	script.async = 'async';
	head.appendChild(script);
	if (typeof callback === 'function') {
		script.onload = script.onreadystatechange = function() {
			if (!script.readyState || /loaded|complete/.test(script.readyState)) {
				callback();
			}
		}
	}
}
// 相册浏览页面
class ContainerAddress extends React.Component {
	constructor(props) {
		super(props);
	}
	mapToCenter() { //定位地图到界面中心位置
		let ZOOM = 18;
		this.map = new BMap.Map("myMap");
		this.pointAttr = [103.749461, 29.932389];
		this.myPoint = [];
		this.point = new BMap.Point(this.pointAttr[0], this.pointAttr[1]);
		this.map.centerAndZoom(this.point, ZOOM);

		// 添加带有定位的导航控件
		var navigationControl = new BMap.NavigationControl({
			// 靠左上角位置
			anchor: BMAP_ANCHOR_TOP_LEFT,
			// LARGE类型
			type: BMAP_NAVIGATION_CONTROL_LARGE,
			// 启用显示定位
			enableGeolocation: true
		});
		this.map.addControl(navigationControl);

		// 添加定位控件
		var geolocationControl = new BMap.GeolocationControl();
		geolocationControl.addEventListener("locationSuccess", function(e) {
			this.map.removeControl(this.flyControl);
			let latCurrent = e.point.lat;
			let lngCurrent = e.point.lng;
			this.myPoint = [latCurrent, lngCurrent];
			this.toMyweddingControl();
		}.bind(this));
		geolocationControl.addEventListener("locationError", function(e) {
			// 定位失败事件
			alert(e.message);
		});
		this.map.addControl(geolocationControl);
		this.flytoweddingControl();
	}

	toMyweddingControl() { // 自定义的控件，返回我的婚礼地点
		let ZOOM = 18;

		function ZoomControl() { // 定义一个控件类,即function
			// 默认停靠位置和偏移量
			this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
			this.defaultOffset = new BMap.Size(10, 10);
		}

		// 通过JavaScript的prototype属性继承于BMap.Control
		ZoomControl.prototype = new BMap.Control();

		// 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
		// 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
		ZoomControl.prototype.initialize = (map) => {
			// 创建一个DOM元素
			var div = document.createElement("div");
			// 添加文字说明
			div.appendChild(document.createTextNode("婚宴地址"));
			// 设置样式
			div.style.width = '80px';
			div.style.height = "30px";
			div.style.lineHeight = "30px";
			div.style.textAlign = "center";
			div.style.color = "#666";
			div.style.cursor = "pointer";
			div.style.background = "rgba(255,255,255,.8)";
			div.style.webkitBoxShadow = "1px 1px 2px rgba(0,0,0,.4)";
			div.style.borderRadius = "3px";


			div.onclick = (e) => { // 绑定事件,点击一次放大两级
				this.map.centerAndZoom(this.point, ZOOM);
				this.map.removeControl(this.myZoomCtrl);
				this.flytoweddingControl();
			}

			this.map.getContainer().appendChild(div); // 添加DOM元素到地图中
			// 将DOM元素返回
			return div;
		}
		this.myZoomCtrl = new ZoomControl(); // 创建自定义控件
		this.map.addControl(this.myZoomCtrl); // 添加到地图当中
	}

	flytoweddingControl() { // 自定义的控件，返回我的婚礼地点
		let ZOOM = 18;

		function Control() { // 定义一个控件类,即function
			// 默认停靠位置和偏移量
			this.defaultAnchor = BMAP_ANCHOR_TOP_RIGHT;
			this.defaultOffset = new BMap.Size(10, 10);
		}

		// 通过JavaScript的prototype属性继承于BMap.Control
		Control.prototype = new BMap.Control();

		// 自定义控件必须实现自己的initialize方法,并且将控件的DOM元素返回
		// 在本方法中创建个div元素作为控件的容器,并将其添加到地图容器中
		Control.prototype.initialize = (map) => {
			// 创建一个DOM元素
			var div = document.createElement("div");
			// 添加文字说明
			div.appendChild(document.createTextNode("开始导航"));
			// 设置样式
			div.style.width = '80px';
			div.style.height = "30px";
			div.style.lineHeight = "30px";
			div.style.textAlign = "center";
			div.style.color = "#666";
			div.style.cursor = "pointer";
			div.style.background = "rgba(255,255,255,.8)";
			div.style.webkitBoxShadow = "1px 1px 2px rgba(0,0,0,.4)";
			div.style.borderRadius = "3px";


			div.onclick = (e) => { // 绑定事件,点击一次放大两级
				this.mapGeolocation();
			}

			this.map.getContainer().appendChild(div); // 添加DOM元素到地图中
			// 将DOM元素返回
			return div;
		}
		this.flyControl = new Control(); // 创建自定义控件
		this.map.addControl(this.flyControl); // 添加到地图当中
	}
	mapGeolocation() {
		let that = this;
		let geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r) {
			if (this.getStatus() == BMAP_STATUS_SUCCESS) {
				let mk = new BMap.Marker(r.point);
				// that.map.addOverlay(mk);
				// that.map.panTo(r.point); //地图中心点移到当前位置
				let latCurrent = r.point.lat;
				let lngCurrent = r.point.lng;
				that.mapLocationToHref(latCurrent, lngCurrent);
			} else {
				alert('出错了' + this.getStatus());
			}
		}, {
			enableHighAccuracy: true
		})
	}
	mapMarkPoint() { // 地图标注
		this.marker = new BMap.Marker(this.point); // 创建标注
		this.map.addOverlay(this.marker); // 将标注添加到地图中
		this.marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
	}
	mapLocationToHref(lat, lng) {
		let pointer = this.pointAttr;
		let region = "眉山";
		let url = "http://api.map.baidu.com/direction?origin=" + lat + "," + lng + "&destination=" + pointer[1] + "," + pointer[0] + "&mode=driving&region=" + region + "&output=html"
		location.href = url;
	}
	start() {
		this.mapToCenter();
		this.mapMarkPoint();
		// this.mapGeolocation();
	}
	componentDidMount() {
		this.start();
	}
	render() {
		return (
			<section className="address-box">
				<div id="myMap"></div>
			</section>
		)
	}
}

module.exports = ContainerAddress