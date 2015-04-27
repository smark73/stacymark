<?php

/*
Set up responsive features.
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2012-10-09 - Chris Jean
		Initial version
*/


require( dirname( __FILE__ ) . '/functions.php' );

add_action( 'builder_add_scripts', 'builder_add_fitvids_scripts' );
add_action( 'builder_add_meta_data', 'builder_add_responsive_viewport_meta' );
add_action( 'builder_add_stylesheets', 'builder_add_responsive_stylesheets', 0, 101 );
