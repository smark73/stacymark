jQuery(document).ready(function() {

	jQuery(".builder-module-navigation .menu").addClass("it-mobile-menu-hidden");
	jQuery(".builder-module-navigation").addClass("mobile");
	
	jQuery(".it-mobile-menu-hidden").before('<div class="it-mobile-menu">&#8801; Menu</div>');
	
	jQuery(".it-mobile-menu").click(function(){
		jQuery(this).next().slideToggle();
		jQuery(".builder-module-navigation.mobile").toggleClass("borderless");
	});
	
	jQuery(window).resize(function(){
		if(window.innerWidth > 500) {
			jQuery(".menu").removeAttr("style");
			jQuery(".builder-module-navigation.mobile").removeAttr("style");
		}
	});

});
