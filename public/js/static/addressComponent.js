import "../../less/address.less";
import React from 'react';

// 相册浏览页面
class ContainerAddress extends React.Component {
	constructor(props) {
		super(props);
	}
	// 定位地图到界面中心位置
	mapToCenter(){
		let ZOOM = 18;
		this.map = new BMap.Map("myMap");
		this.pointAttr = [103.749461,29.932389];
		this.point = new BMap.Point(this.pointAttr[0],this.pointAttr[1]);
		this.map.centerAndZoom(this.point, ZOOM);
	}
	// 地图标注
	mapMarkPoint(){
		this.marker = new BMap.Marker(this.point); // 创建标注
		this.map.addOverlay(this.marker); // 将标注添加到地图中
		this.marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画
	}
	// 定位当前位置坐标
	mapGeolocation(){
		let that = this;
		let geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r) {
			console.log(this.getStatus());
			if (this.getStatus() == BMAP_STATUS_SUCCESS) {
				let mk = new BMap.Marker(r.point);
				that.map.addOverlay(mk);
				that.map.panTo(r.point); //地图中心点移到当前位置
				let latCurrent = r.point.lat;
				let lngCurrent = r.point.lng;
				console.log('我的位置：' + latCurrent + ',' + lngCurrent);

			} else {
				alert('failed' + this.getStatus());
			}
		}, {
			enableHighAccuracy: true
		})
	}
	mapLocationToHref(lat,lng){
		let pointer = this.pointAttr;
		let region = "眉山";
		let url = "http://api.map.baidu.com/direction?origin=" + lat + "," + lng + "&destination=" + pointer[1] + "," + pointer[0] + "&mode=driving&region="+region+"&output=html"
		location.href = url;
	}
	componentDidMount() {
		this.mapToCenter();
		this.mapMarkPoint();
		// this.mapGeolocation();
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