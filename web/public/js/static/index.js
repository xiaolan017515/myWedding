import "../../less/index.less";
import "ajax-plus";
import React from 'react';
import ReactDOM from 'react-dom';
import {
	Router,
	Route,
	createHistory
} from "react-router";

import {
	IndexPicComponent,
	IndexListComponent
} from "./indexComponent.js";
import ContainerInvitation from "./invitationComponent.js";
import ContainerPicture from "./picturesComponent.js";
import ContainerAddress from "./addressComponent.js";
import ContainerLiuyan from "./liuyanComponent.js";
// import PubSub from 'pubsub-js';

class ContainerIndex extends React.Component {
	render() {
		return (
			<section className="container-box">
				<IndexPicComponent/>
				<IndexListComponent/>
			</section>
		)
	}
}

ReactDOM.render(
	<Router history={createHistory}>
		<Route path="/" component={ContainerIndex}/>
		<Route path="/invitation" component={ContainerInvitation}/>
		<Route path="/picture" component={ContainerPicture}/>
		<Route path="/address" component={ContainerAddress}/>
		<Route path="/liuyan" component={ContainerLiuyan}/>
	</Router>,
	document.getElementById('main')
);