(function($) {
	"use strict";
	var isOn = 0, sets, fx, toAnimate = "#effect", settings = {
		animation:8,
		animationType: "in",
		backwards: false,
		easing: "easeOutQuint",
		speed: 1000,
		sequenceDelay: 100,
		startDelay: 0,
		offsetX: 100,
		offsetY: 50,
		onComplete: fireThis,
		restoreHTML: true
	};

	$(document).ready(function() {
		fx = $("#effect");
		$.cjTextFx(settings);
		$.cjTextFx.animate(toAnimate);
	});
		
	function fireThis() {
		if(isOn === 12) return;
		isOn < 12 ? isOn++ : isOn = 0;
		switch(isOn) {

			case 1:
				sets = {animation: 8,animationType: "out", restoreHTML: false};
			break;
			
			case 2:
				fx.html("正在加载中");
				sets = {animation: 11};
			break;
			
			case 3:
				sets = {animation: 11, animationType: "out", restoreHTML: false};
			break;
			
			case 4:
				fx.html("请稍等。。。");
				sets = {animation: 1};
			break;
			
			case 5:
				sets = {animation: 1, animationType: "out", restoreHTML: false};
			break;

			case 6:
				fx.html("LOADING...");
				sets = {animation: 6, backwards: true};
			break;
			
			case 7:
				sets = {animation: 4, animationType: "out", backwards: true, restoreHTML: false};
			break;
			
			case 8:
				fx.html("欢迎您来参观");
				sets = {animation: 2, easing: "easeOutBounce"};
			break;
			
			case 9:
				sets = {animation: 2, animationType: "out", speed:500, easing: "easeInBack", restoreHTML: false};
			break;
			
			case 10:
				fx.html("^_^^_^^_^");
				sets = {animation: 14, easing: "easeInBack", restoreHTML: false};
			break;
			
			default:
				sets = {animation: 8,animationType: "out", restoreHTML: false};
				isOn = 1;//在这设定返回从头开始
			break;
		}
		$.cjTextFx.animate(toAnimate, sets);
	}
})(jQuery);