import "../../less/index.less";
import "jquery";
import "ajax-plus";
import React from 'react';
import ReactDOM from 'react-dom';
import { Router,Route,createHistory} from "react-router";

import {IndexPicComponent,IndexListComponent} from "./indexComponent.js";
import ContainerInvitation from "./invitationComponent.js";
import ContainerPicture from "./picturesComponent.js";
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
	</Router>,
	document.getElementById('main')
);