import "../../less/invitation.less";
import "../plus/swiper-3.4.0.jquery.min.js";
import "../../css/swiper.min.css";
import React from 'react';

// 电子请柬页面
class ContainerInvitation extends React.Component {
	constructor(props) {
		super(props);
	}
	componentDidMount(){
		var mySwiper = new Swiper ('.swiper-container', {
			direction: 'vertical',
			effect : 'cube',
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
			        <div className="swiper-slide">
			        	<section className="invitation-box">
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
			        </div>
			        <div className="swiper-slide">Slide 2</div>
			        <div className="swiper-slide">Slide 3</div>
			    </div>
			</div>
		)
	}
}
{/*<section className="invitation-box">
	<div className="invitation-bg1"></div>
	<div className="invitation-bg2"><img src="img/invitation/22.png"/></div>
	<div className="invitation-bg3"><img src="img/invitation/33.png"/></div>
	<div className="invitation-bg4"><img src="img/invitation/44.png"/></div>
	<div className="invitation-bg5"><img src="img/invitation/55.png"/></div>
	<div className="invitation-bg6"><img src="img/invitation/66.png"/></div>
	<div className="invitation-bg7"><img src="img/invitation/77.png"/></div>
	<div className="invitation-bg8"><img src="img/invitation/88.png"/></div>
	<div className="invitation-font">邀请函</div>
</section>*/}

module.exports = ContainerInvitation