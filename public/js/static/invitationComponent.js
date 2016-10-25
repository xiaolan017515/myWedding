
import "../../less/invitation.less";
import React from 'react';

// 电子请柬页面
class ContainerInvitation extends React.Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<section className="invitation-box">
				<div className="invitation-large-bg"></div>
				<div className="invitation-hudie-bg"></div>
			</section>
		)
	}
}

module.exports = ContainerInvitation