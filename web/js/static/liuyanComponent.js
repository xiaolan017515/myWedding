import "../../less/liuyanban.less";
import "ajax-plus";
import React from 'react';
import ReactDOM from 'react-dom';


class ContainerLiuyanList extends React.Component {
	constructor(props) {
		super(props);
		this.listData = this.props.item;
	}
	componentWillReceiveProps(nextProps) {
		this.listData = nextProps.item;
	}
	render() {
		return (
			<li>
				<div className="left">
					<img src="../web/img/xi.jpg"/>
				</div>
				<div className="right">
					<b>
						<span className="name">{this.listData.userName}</span>
						<span className="time">
							<i></i>{$.formatMsgTime(this.listData.date)}
						</span>
					</b>
					<p>{this.listData.content}</p>
				</div>
			</li>
		)
	}
}

class ContainerLiuyan extends React.Component {
	state = {
		status: false
	}
	constructor(props) {
		super(props);
		this.listDom = void 0;
		this.updateDidMount();
		this.engine = void 0;
	}
	callback() {
		$("#loading").fadeOut(3000);
		$('.container').fadeIn(3000);
	}
	componentDidMount() {
		this.getDatas();
		this.startRain();
		$.getloadingimg(this.callback, '#loadShow');
	}
	updateDidMount(data = []) {
		if (data[0]) {
			data = data.reverse();
			this.listDom = data.map((item, num) => {
				return <ContainerLiuyanList item={item} num={num} key={num}/>
			});
		};
		this.setState({
			status: true
		});
	}
	getDatas() {

		let config = {
			C: 'Message',
			M: 'getMessage'
		};

		$.GetAjax('/index.php', config, 'GET', true, (data, t_state) => {
			if (t_state) {
				this.updateDidMount(data.info);
			} else {
				alert('数据请求失败');
			}

		});
	}
	handleClick() {
		let input = ReactDOM.findDOMNode(this.refs.contentInput);
		let inputValue = $.trim(input.value);
		if (!inputValue) {
			alert('请填写数据');
			return
		};
		let config = {
			userName: localStorage.getItem('userName'),
			content: inputValue
		};

		$.GetAjax('/index.php?C=Message&M=setMessage', config, 'post', true, (data, t_state) => {
			if (t_state && data.code == 0) {
				this.listDom = void 0;
				this.getDatas();

			} else {
				alert('提交失败');
			}

		});
	}
	startRain() {
		let RainyDay = require("../plus/rainyday.js");
		let image = document.getElementById('background');
		let div = document.getElementById('canvas');
		var that = this;
		image.onload = function() {
			that.engine = new RainyDay({
				image: this,
				parentElement: div,
				gravityAngle: Math.PI / 9 //斜着滑
			});
			// engine.trail = engine.TRAIL_SMUDGE; // 开启滑动插窗效果
			that.engine.rain([
				[1, 0, 20],
				[3, 3, 1]
			], 100);
		};
	}
	componentWillUnmount() {
		this.engine.stopAnimFrame();
	}
	render() {
		return (
			<section className="liuyan-box">
				<div className="canvas-box" id="canvas">
					<img src="../web/img/pic/pic12.jpg" id="background"/>
				</div>
				<div className="content-box">
					<header><img src="../web/img/cccx.png"/></header>
					<ul className="content">
						{this.listDom}
					</ul>
				</div>
				<div className="text-box">
					<input type="text" placeholder="在这里留言" ref="contentInput"/>
					<button onClick={this.handleClick.bind(this)}>送祝福</button>
				</div>
			</section>
		)
	}
}

module.exports = ContainerLiuyan