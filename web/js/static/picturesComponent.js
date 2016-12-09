import React from 'react';
import "../../less/picture.less";

// 相册浏览页面
class ContainerPicture extends React.Component {
	componentDidMount() {
		var galleryTop = new Swiper('.gallery-top', {
			observer: true,
			observeParents: true,
			nextButton: '.swiper-button-next',
			prevButton: '.swiper-button-prev',
			spaceBetween: 10,
			paginationClickable: true,
			preloadImages: false,
			lazyLoading: true
		});
		var galleryThumbs = new Swiper('.gallery-thumbs', {
			observer: true,
			observeParents: true,
			spaceBetween: 10,
			centeredSlides: true,
			slidesPerView: 'auto',
			touchRatio: 0.2,
			slideToClickedSlide: true,
		});
		galleryTop.params.control = galleryThumbs;
		galleryThumbs.params.control = galleryTop;
	}
	render() {
		return (
			<div className="picture-box">
				<div className="swiper-container gallery-top">
				    <div className="swiper-wrapper">
				    	{(()=>{
				    		let [HTMLDOM=[],Child] = [];
				    		for(let i=1;i<=50;i++){
				    			Child = [];
				    			Child.push(<img data-src={"../web/img/pic/pic"+i+".jpg"} className="swiper-lazy"/>)
				    			Child.push(<div className="swiper-lazy-preloader swiper-lazy-preloader-white"></div>)
				    			HTMLDOM.push(<div className="swiper-slide">{ Child }</div>)
				    		}
				    		return HTMLDOM;
				    	})()}
				    </div>
        			<div className="swiper-button-next swiper-button-white"></div>
        			<div className="swiper-button-prev swiper-button-white"></div>
				</div>
				<div className="swiper-container gallery-thumbs">
				    <div className="swiper-wrapper">
				    	{(()=>{
				    		let HTMLDOM = [];
				    		for(let i=1;i<=50;i++){
				    			HTMLDOM.push(<div className="swiper-slide" style={{background: 'url(../web/img/pic/thumb/pic'+i+'.gif) no-repeat center center',backgroundSize: 'cover'}}></div>)
				    		}
				    		return HTMLDOM;
				    	})()}
				    </div>
				</div>
			</div>
		)
	}
}

module.exports = ContainerPicture