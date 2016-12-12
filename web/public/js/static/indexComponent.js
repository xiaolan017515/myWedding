import "../plus/swiper-3.4.0.jquery.min.js";
import "../../css/swiper.min.css";
import React from 'react';
import {
	Link
} from "react-router";

class IndexPicComponent extends React.Component {
	constructor(props) {
		super(props);
	}
	componentDidMount() {
		var mySwiper = new Swiper('.swiper-container', {
			effect: 'fade',
			loop: true,
			autoplay: 5000,
			updateOnImagesReady: true,
			observer: true,
			observeParents: true,
			onlyExternal: true
		})
	}
	render() {
		return (
			<div className="index-background">
				<div className="wedding"></div>
				<div className="swiper-container">
				    <div className="swiper-wrapper">
				        <div className="swiper-slide background-slide-1"></div>
				        <div className="swiper-slide background-slide-2"></div>
				        <div className="swiper-slide background-slide-3"></div>
				        <div className="swiper-slide background-slide-4"></div>
				    </div>
				</div>
			</div>
		)
	}
}

class IndexListComponent extends React.Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<nav className="index-nav">
				<ul>
					<li>
						<Link to="/invitation">
							<i className="icon iconfont i1">&#xe6f4;</i>
	                    	<div className="name">电子请柬</div>
						</Link>
	                </li>
	                <li>
	                    <Link to="/picture">
							<i className="icon iconfont i2">&#xe614;</i>
	                    	<div className="name">甜蜜合影</div>
						</Link>
	                </li>
	                <li>
	                    <Link to="/address">
							<i className="icon iconfont i3">&#xe6a3;</i>
	                    	<div className="name">婚宴地址</div>
						</Link>
	                </li>
	                <li>
	                    <Link to="/liuyan">
							<i className="icon iconfont i4">&#xe8d9;</i>
	                    	<div className="name">留言签到</div>
						</Link>
	                </li>
				</ul>
			</nav>
		)
	}
}

module.exports = {
	IndexPicComponent: IndexPicComponent,
	IndexListComponent: IndexListComponent
}