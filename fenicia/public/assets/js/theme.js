// JavaScript Document

//Banner Carousel
var bs = $('#banner-carousel');
bs.owlCarousel({
	autoplay:true,
	//autoplayTimeout:1000,
	//autoplaySpeed:700,
    loop:true,
    nav:true,
	dots:true,
	//animateOut: 'fadeOut',
    items: 1,
	navText: [ '<i class="fa fa-chevron--left"></i>', '<i class="fa fa-chevron--right"></i>' ],	
});
