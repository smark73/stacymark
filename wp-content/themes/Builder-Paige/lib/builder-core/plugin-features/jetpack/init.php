<?php

/*
Plugin compatibility code for Builder and Jetpack
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-02-15 - Chris Jean
		Initial version
*/


// Force Carousel to add its gallery modifications.
add_filter( 'jp_carousel_force_enable', 'builder_return_true' );
