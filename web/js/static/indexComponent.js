import React from 'react';
import {
	Link
} from "react-router";

class IndexPicComponent extends React.Component {
	constructor(props) {
		super(props);
	}
	componentDidMount() {

	}
	start() {
		var mySwiper = new Swiper('.swiper-container', {
			effect: 'fade',
			loop: true,
			autoplay: 2500,
			observer: true,
			observeParents: true,
			autoplayDisableOnInteraction: false, // 自动轮播时，用户也可以操作,true不可操作
			onlyExternal: true //禁止用户操作
		});
		mySwiper.startAutoplay();
	}
	componentWillReceiveProps() {
		this.start();
	}
	render() {
		return (
			<div className="index-background">
				{/*<div className="wedding"></div>*/}
				<div className="swiper-container">
				    <div className="swiper-wrapper">
				    	{(()=>{
				    		let HTMLDOM = [];
				    		for(let i=1;i<=10;i++){
				    			HTMLDOM.push(<div className="swiper-slide" style={{background: 'url(../web/img/pic/pic'+i+'.jpg) no-repeat center center',backgroundSize: 'cover'}}></div>)
				    		}
				    		return HTMLDOM;
				    	})()}
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
		let config = [{
			url: '/invitation',
			name: '电子请柬',
			ico: '&#xe6f4;'
		}, {
			url: '/picture',
			name: '甜蜜合影',
			ico: '&#xe614;'
		}, {
			url: '/address',
			name: '婚宴地址',
			ico: '&#xe6a3;'
		}, {
			url: '/liuyan',
			name: '留言签到',
			ico: '&#xe8d9;'
		}]
		return (
			<nav className="index-nav">
				<ul>
					{
						config.map((data,k)=>{
							return  (<li>
										<Link to={data.url}>
											<i className={"icon iconfont i"+(k+1)} dangerouslySetInnerHTML={{__html: data.ico}}/>
					                    	<div className="name">{data.name}</div>
										</Link>
					                </li>)
						})
					}
				</ul>
			</nav>
		)
	}
}

module.exports = {
	IndexPicComponent: IndexPicComponent,
	IndexListComponent: IndexListComponent
}