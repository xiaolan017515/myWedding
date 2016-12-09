import React from 'react';
import ReactDOM from 'react-dom';
import {
	Router,
	Route,
	hashHistory
} from "react-router";

import ContainerIndex from "./index.js";
import ContainerInvitation from "./invitationComponent.js";
import ContainerPicture from "./picturesComponent.js";
import ContainerAddress from "./addressComponent.js";
import ContainerLiuyan from "./liuyanComponent.js";


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