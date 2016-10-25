
import React from 'react';
import {Link} from "react-router";

class IndexPicComponent extends React.Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<div className="index-background">
				<div className="wedding"></div>
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
	                    <Link to="/dress">
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
	IndexPicComponent:IndexPicComponent,
	IndexListComponent:IndexListComponent
}