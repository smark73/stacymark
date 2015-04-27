jQuery(function(){
	jQuery('#slides').slides({
		play: 5000,
		pause: 2500,
		hoverPause: true,
		animationStart: function(current){
			jQuery('.caption').animate({
				bottom:-35
			},100);
		},
		animationComplete: function(current){
			jQuery('.caption').animate({
				bottom:0
			},200);
		},
		slidesLoaded: function() {
			jQuery('.caption').animate({
				bottom:0
			},200);
		}
	});
});
