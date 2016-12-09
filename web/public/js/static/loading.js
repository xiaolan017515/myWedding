import "../../less/loading.less";
import React from 'react';
import ReactDOM from 'react-dom';

class ContainerLoading extends React.Component {
	render() {
		return (
			<section className="loading-box">
				<div className="loading">
					<img src="img/bars.svg"/>
					<p id="loadShow">0%</p>
				</div>
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

class ContainerOrientation extends React.Component {
	componentDidMount(){
		this.hengshuping();
		window.addEventListener("onorientationchange" in window ? "orientationchange" : "resize", this.hengshuping, false);
	}
	hengshuping(){
		if(window.orientation==180||window.orientation==0){  
	        $('#orientation').css('display','none');
	   	}else if(window.orientation==90||window.orientation==-90){  
	        $('#orientation').css('display','block');
	    }  
	}
	render() {
		return (
			<section className="orientation-box">
		        <div className="Inner">
		            <div className="Mobile">
		                <img src="img/User/Mobile.png"/>
		            </div>
		            <div className="Phone">
		                <img src="img/User/Phone.png"/>
		            </div>
		            <div className="Floata1">
		                更好的用户体验<br/>请您竖屏浏览
		            </div>
		        </div>
			</section>
		)
	}
}

ReactDOM.render(
	<ContainerOrientation/>,
	document.getElementById('orientation')
);

ReactDOM.render(
	<ContainerLoading/>,
	document.getElementById('loading')
);