import "../../less/liuyanban.less";
import "common";
import React from 'react';
import ReactDOM from 'react-dom';

// 相册浏览页面

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
					<img src="img/ico/aaaaa.jpg"/>
				</div>
				<div className="right">
					<b>
						<span className="name">{this.listData.userName}</span>
						<span className="time">
							<i></i>{this.listData.date}
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
	}
	componentDidMount(){
		this.startRain();
		this.getDatas();
	}
	updateDidMount(data=[]) {
		if (data[0]) {
			this.listDom = data.map((item, num) => {
				return <ContainerLiuyanList item={item} num={num} key={num}/>
			});
		};
		this.setState({
			status: true
		});
	}
	getDatas(){

		let config = {
			C:'Message',
			M:'getMessage'
		};

		$.GetAjax('/index.php', config, 'GET', true, (data,t_state) => {
			if (t_state) {
				this.updateDidMount(data.info);
			}else{
				setTimeout(() => {
					this._getDatas();
					console.log('主人，刚才服务器出了一下小差');
				}, 2000);
			}
			
		});
	}
	handleClick(){
		let input = ReactDOM.findDOMNode(this.refs.contentInput);
		let inputValue = input.value;
		if (!inputValue) { alert('请填写数据'); return };
		let config = {
			content:inputValue
		};

		$.GetAjax('/index.php?C=Message&M=setMessage', config, 'post', true, (data,t_state) => {
			if (t_state && data.code == 0) {
				this.listDom = void 0;
				this.getDatas();

			}else if(!t_state){
				setTimeout(() => {
					this._getDatas();
					console.log('主人，刚才服务器出了一下小差');
				}, 2000);
			}else{
				alert('数据为null');
			}
			
		});
	}
	startRain(){
		const image = document.getElementById('background');
		const div = document.getElementById('canvas');
        image.onload = function() {
            var engine = new RainyDay({
                image: this,
                parentElement:div,
                gravityAngle: Math.PI / 9 //斜着滑
            });
            // engine.trail = engine.TRAIL_SMUDGE; // 开启滑动插窗效果
            engine.rain([[1, 0, 20],[3, 3, 1]],100);
        };
	}
	render() {
		return (
			<section className="liuyan-box">
				<div className="canvas-box" id="canvas">
					<img src="img/rain.jpg" id="background"/>
				</div>
				<div className="content-box">
					<header><img src="img/cccx.png"/></header>
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