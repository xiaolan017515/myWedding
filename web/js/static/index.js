import "../../less/index.less";
import "../../less/loading.less";
import "../../css/swiper.css";
import "../../css/iconfont.css";
import "common";
import React from 'react';
import ReactDOM from 'react-dom';
import {
	Router,
	Route,
	hashHistory
} from "react-router";

import {
	IndexPicComponent,
	IndexListComponent
} from "./indexComponent.js";
import ContainerInvitation from "./invitationComponent.js";
import ContainerPicture from "./picturesComponent.js";
import ContainerAddress from "./addressComponent.js";
import ContainerLiuyan from "./liuyanComponent.js";


new WxMoment.OrientationTip();
let firstIntMyHouse = localStorage.getItem('userName') || false;


$('#goback').off().on('touchend',function(){
    hashHistory.push('/');
});

class ContainerUsers extends React.Component {
	niming(e) {
		localStorage.setItem('userName', '匿名用户');
		this.setState({
			status: true
		})
	}
	sublime(e) {
		let value = $.trim(ReactDOM.findDOMNode(this.refs.yourName).value);
		if (!value) {
			alert('姓名不能为空');
			return false;
		}
		localStorage.setItem('userName', value);
		this.setState({
			status: true
		})
	}
	state = {
		status: false
	}
	render() {
		if (this.state.status) {
			return false;
		}
		if (firstIntMyHouse) {
			return false;
		} else {
			return (
				<div className="users">
					<div className="userInput">
						<p>输入姓名去留言</p>
						<input type="text" placeholder="your name..." ref="yourName"/>
						<button className="niming" onClick={ this.niming.bind(this) }>我要匿名</button>
						<button className="sublime" onClick={ this.sublime.bind(this) }>提交</button>
					</div>
					
				</div>
			)
		}

	}
}

class ContainerIndex extends React.Component {
	callback() {
		$.cjTextFx.remove('#effect');
		$("#loading").fadeOut(3000);
		$('.container').fadeIn(3000);
		$('#goback').show();
		this.setState({
			status: true
		});
		this.bindAudios();
	}
	bindAudios() {
		var audio = document.getElementById('media');
		audio.play();
		$('#audio_btn').addClass('rotate');
		if(audio!==null){             
            $('#audio_btn').off().on('touchend',function(){
				if ( $(this).hasClass('rotate') ) {
					$(this).removeClass('rotate');
					if(!audio.paused){
		                audio.pause();
		            }
				}else{
					$(this).addClass('rotate');
		            audio.play();
				}
			});
        }  
		
	}
	componentDidMount() {
		$.getloadingimg(this.callback.bind(this), '#loadShow');
	}
	render() {
		return (
			<section className="container-box">
				<IndexPicComponent/>
				<IndexListComponent/>
				<ContainerUsers/>
			</section>
		)
	}
}

hashHistory.push('/');

class ContainerLoading extends React.Component {
	componentDidMount() {
		require("../plus/jquery.easing.1.3.js");
		require("../plus/texteffect.js");
	}
	render() {
		return (
			<section className="loading-box">
                <p id="loadShow" class="loading">0%</p>
                <div id="effect">
                  <span>W</span>
                  <span>E</span>
                  <span>L</span>
                  <span>C</span>
                  <span>O</span>
                  <span>M</span>
                  <span>E</span>
                </div>
            </section>
		)
	}
}

ReactDOM.render(
	<ContainerLoading/>,
	document.getElementById('loading')
);

ReactDOM.render(
	<Router history={hashHistory}>
		<Route path="/" component={ContainerIndex}/>
		<Route path="/invitation" component={ContainerInvitation}/>
		<Route path="/picture" component={ContainerPicture}/>
		<Route path="/address" component={ContainerAddress}/>
		<Route path="/liuyan" component={ContainerLiuyan}/>
	</Router>,
	document.getElementById('main')
);