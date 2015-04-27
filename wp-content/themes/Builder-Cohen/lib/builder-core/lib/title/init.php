<?php

/*
Code responsible for loading the title functionality
Written by Chris Jean for iThemes.com
Version 1.1.0

Version History
	1.0.0 - 2012-08-03 - Chris Jean
		Initial version
	1.1.0 - 2013-02-15 - Chris Jean
		Moved wp_title filter to be conditional on support for builder-title-tag.
*/


require_once( dirname( __FILE__ ) . '/functions.php' );


add_action( 'builder_add_title', 'builder_add_title' );

if ( builder_theme_supports( 'builder-title-tag' ) )
	add_filter( 'wp_title', 'builder_filter_wp_title', 20, 3 );
