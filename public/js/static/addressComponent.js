import "../../less/address.less";
import React from 'react';

// 相册浏览页面
class ContainerAddress extends React.Component {
	componentDidMount() {
		// 百度地图API功能
		var map = new BMap.Map("myMap");
		var point = new BMap.Point(103.749461, 29.932389);
		map.centerAndZoom(point, 18);
		var marker = new BMap.Marker(point); // 创建标注
		map.addOverlay(marker); // 将标注添加到地图中
		marker.setAnimation(BMAP_ANIMATION_BOUNCE); //跳动的动画

		// var myIcon = new BMap.Icon("myicon.png", new BMap.Size(30, 30), {
		// 	anchor: new BMap.Size(10, 10)
		// });

		// var marker = new BMap.Marker(point, {
		// 	icon: myIcon
		// });
		// map.addOverlay(marker);

		var geolocation = new BMap.Geolocation();
		geolocation.getCurrentPosition(function(r) {
			if (this.getStatus() == BMAP_STATUS_SUCCESS) {
				var mk = new BMap.Marker(r.point);
				map.addOverlay(mk);
				// map.panTo(r.point); //地图中心点移到当前位置
				var latCurrent = r.point.lat;
				var lngCurrent = r.point.lng;
				console.log('我的位置：' + latCurrent + ',' + lngCurrent);

				// location.href = "http://api.map.baidu.com/direction?origin=" + latCurrent + "," + lngCurrent + "&destination=29.932389,103.749461&mode=driving&region=眉山&output=html";

			} else {
				alert('failed' + this.getStatus());
			}
		}, {
			enableHighAccuracy: true
		})
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