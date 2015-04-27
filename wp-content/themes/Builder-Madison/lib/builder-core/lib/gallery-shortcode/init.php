<?php

/*
Code responsible for loading the gallery shortcode customizations
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-02-14 - Chris Jean
		Initial version
*/


require( dirname( __FILE__ ) . '/functions.php' );

add_filter( 'post_gallery', 'builder_custom_post_gallery', 10, 2 );
