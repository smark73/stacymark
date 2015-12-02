//Menu scripts

jQuery(document).ready(function($){

	//Paintings menu
	// START 
    var $ptgsMenuBox = jQuery(document).find('.ptgs-menu-box');

    $ptgsMenuBox.mouseenter(function(){
        jQuery(this).stop().animate({top: -50}, 100);
    });

    $ptgsMenuBox.mouseleave(function(){
        jQuery(this).stop().animate({top: -230}, 100);
    });
    // END


    // Contact menu
	// START toggle ptg-contact in navbar
	//store our targets in vars
	// var $ptgContactToggle = jQuery(document).find('.ptg-contact-toggle');
	// var $ptgContact = jQuery(document).find('.ptg-contact');
	// var $ptgContactForm = jQuery(document).find('.ptg-contact-form');

	//init search-form styles and classes
	// $ptgContactForm.addClass('hidden');
	// $ptgContactForm.css({opacity:0});


	//toggle function
	//$ptgContactToggle.click(function(){
	    //ptg-contact-nav is hidden until first click (otherwise shows on slow page loads)
	    //$ptgContact.removeClass('hidden');
	    //
	    //$ptgContact.toggleClass('web-contact-hide web-contact-show');

	//     if(($ptgContactForm).hasClass('hidden')){
	//         var ptgContactWait;
	//         clearTimeout(ptgContactWait);
	//         ptgContactWait = setTimeout(function(){$ptgContactForm.toggleClass('hidden visible').animate({opacity:1});} , 100);
	//     } else {
	//         $ptgContactForm.animate({opacity:0}).toggleClass('hidden visible');
	//     }
	// });
	// END

});