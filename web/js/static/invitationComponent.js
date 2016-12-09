import "../../less/invitation.less";
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
		});
	}
	touchSublimed(e){
		let username = $.trim($(this.refs.name).val());
		let phone = $.trim($(this.refs.phone).val());
		let people = $.trim($(this.refs.people).val());

		if ( !$.regTest('chinese',username) ) {
			alert("请正确填写您的姓名");
			$(this.refs.name).val("");
			return false;
		}

		if ( !$.regTest('phone',phone) ) {
			alert("手机格式不正确");
			$(this.refs.phone).val("");
			return false;
		}

		e.target.value = "正在提交";
		this._sendYourMsg({
			userName: username,
			phone: phone,
			people: people || 1
		},()=>{
			$(this.refs.name).val("");
			$(this.refs.phone).val("");
			$(this.refs.people).val("");
			$(this.refs.sublime).val("提交");
		});
	}
	_sendYourMsg(option,callback){

		$.GetAjax('/index.php?C=Message&M=setUserJoin', option, 'post', true, (data, t_state) => {
			if (t_state && data.code == 0) {
				alert('提交成功');
				callback();
			} else {
				alert('提交失败');
			}

		});
	}
	render() {
		return (
			<div className="swiper-box swiper-container">
			    <div className="swiper-wrapper">
			        <div className="swiper-slide">
			        	<section className="invitation-box-1">
							<div className="invitation-bg1"></div>
							<div className="invitation-bg2"><img src="../web/img/invitation/22.png"/></div>
							<div className="invitation-bg3"><img src="../web/img/invitation/33.png"/></div>
							<div className="invitation-bg4"><img src="../web/img/invitation/44.png"/></div>
							<div className="invitation-bg5"><img src="../web/img/invitation/55.png"/></div>
							<div className="invitation-bg6"><img src="../web/img/invitation/66.png"/></div>
							<div className="invitation-bg7"><img src="../web/img/invitation/77.png"/></div>
							<div className="invitation-bg8"><img src="../web/img/invitation/88.png"/></div>
							<div className="invitation-font">邀请函</div>
						</section>
			        </div>
			        <div className="swiper-slide">
			        	<section className="invitation-box-2">
			        		<div className="content-box">
			        			<img src="../web/img/svg/svgBg.svg" className="svg-bg"/>
			        			<img src="../web/img/svg/svgFlower.svg" className="svg-flower"/>
								<p className="font-p1">-诚挚邀请-</p>
								<p className="font-p2">新郎<span>周鑫建</span>与新娘<span>高霞</span>郑重邀请您参加我们的婚礼，望您百忙之中抽出空闲前来参加，我们真心期待您分享我们的甜蜜</p>
								<p className="font-p3">
									<span>—周鑫建</span>
									<img src="../web/img/svg/svgTaoxin.svg" className="svg-love"/>
									<span>高霞—</span>
								</p>
								<p className="font-p4">新婚典礼</p>
								<p className="font-p5">时间：2016.12.23 ~ 2016.12.24</p>
								<p className="font-p6">地点：眉山市东坡区思蒙镇家中</p>
			        		</div>
						</section>
			        </div>
			        <div className="swiper-slide">
			        	<div className="invitation-box-3">
			        		<div className="content-box">
			        			<img src="../web/img/svg/svgBg.svg" className="svg-bg"/>
			        			<p className="font-p1">-欢迎参加-</p>
								<div className="input-box">
									<input type="text" placeholder="姓名" ref="name"/>
									<input type="text" placeholder="电话号码" ref="phone"/>
									<input type="text" placeholder="几人参加(不填即默认1)" ref="people" />
									<input type="button" value="提交" onTouchEnd={this.touchSublimed.bind(this)} ref="sublime"/>
									<div className="phone-box">
										<div className="boyphone">
											<a href="tel:15892727529">
        										<img src="../web/img/b_phone.png" />
        										<span>打电话给新郎</span>
        									</a>
										</div>
										<div className="girlphone">
											<a href="tel:18783336914">
        										<img src="../web/img/g_phone.png" />
        										<span>打电话给新娘</span>
        									</a>
										</div>
									</div>
								</div>
			        		</div>
			        	</div>
			        </div>
			    </div>

			    {/*箭头 start*/}
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
				{/*箭头 end*/}
			</div>
		)
	}
}

module.exports = ContainerInvitation