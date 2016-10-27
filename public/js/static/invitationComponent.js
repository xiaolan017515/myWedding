import "../../less/invitation.less";
import "../plus/swiper-3.4.0.jquery.min.js";
import "../../css/swiper.min.css";
import React from 'react';

// 电子请柬页面
class ContainerInvitation extends React.Component {
	constructor(props) {
		super(props);
	}
	componentDidMount() {
		var mySwiper = new Swiper('.swiper-container', {
			direction: 'vertical',
			effect: 'cube',
			cube: {
				slideShadows: true,
				shadow: false
			}
		})
	}
	render() {
		return (
			<div className="swiper-box swiper-container">
			    <div className="swiper-wrapper">
			        {/*<div className="swiper-slide">
			        	<section className="invitation-box-1">
							<div className="invitation-bg1"></div>
							<div className="invitation-bg2"><img src="img/invitation/22.png"/></div>
							<div className="invitation-bg3"><img src="img/invitation/33.png"/></div>
							<div className="invitation-bg4"><img src="img/invitation/44.png"/></div>
							<div className="invitation-bg5"><img src="img/invitation/55.png"/></div>
							<div className="invitation-bg6"><img src="img/invitation/66.png"/></div>
							<div className="invitation-bg7"><img src="img/invitation/77.png"/></div>
							<div className="invitation-bg8"><img src="img/invitation/88.png"/></div>
							<div className="invitation-font">邀请函</div>
						</section>
			        </div>*/}
			        {/*<div className="swiper-slide">
			        	<section className="invitation-box-2">
			        		<div className="content-box">
			        			<img src="img/svg/svgBg.svg" className="svg-bg"/>
			        			<img src="img/svg/svgFlower.svg" className="svg-flower"/>
			        			<img src="img/svg/svgFlower.svg" className="svg-flower"/>
								<p className="font-p1">-诚挚邀请-</p>
								<p className="font-p2">新郎<span>周鑫建</span>与新娘<span>高霞</span>郑重邀请您参加我们的婚礼，望您百忙之中抽出空闲前来参加，我们真心期待您分享我们的甜蜜</p>
								<p className="font-p3">
									<span>—周鑫建</span>
									<img src="img/svg/svgTaoxin.svg" className="svg-love"/>
									<span>高霞—</span>
								</p>
								<p className="font-p4">新婚典礼</p>
								<p className="font-p5">时间：2016.12.25 ~ 2016.12.26</p>
								<p className="font-p6">地点：眉山市东坡区思蒙镇</p>
			        		</div>
						</section>
			        </div>*/}
			        <div className="swiper-slide">
			        	<div className="invitation-box-3">
			        		
			        	</div>
			        </div>
			    </div>
			    <section className="u-arrow-bottom">
				    <div className="pre-wrap">
				        <div className="pre-box1">
				            <div className="pre1"></div>
				        </div>
				        <div className="pre-box2">
				            <div className="pre2"></div>
				        </div>
				    </div>
				</section>
			</div>
		)
	}
} {
	/*<section className="invitation-box">
		<div className="invitation-bg1"></div>
		<div className="invitation-bg2"><img src="img/invitation/22.png"/></div>
		<div className="invitation-bg3"><img src="img/invitation/33.png"/></div>
		<div className="invitation-bg4"><img src="img/invitation/44.png"/></div>
		<div className="invitation-bg5"><img src="img/invitation/55.png"/></div>
		<div className="invitation-bg6"><img src="img/invitation/66.png"/></div>
		<div className="invitation-bg7"><img src="img/invitation/77.png"/></div>
		<div className="invitation-bg8"><img src="img/invitation/88.png"/></div>
		<div className="invitation-font">邀请函</div>
	</section>*/
}

module.exports = ContainerInvitation