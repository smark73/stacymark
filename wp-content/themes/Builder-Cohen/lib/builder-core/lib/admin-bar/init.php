<?php

/*
Load the WordPress Admin Bar modifications specific to Builder
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-08-31 - Chris Jean
		Release ready
*/


if ( is_user_logged_in() )
	require_once( dirname( __FILE__ ) . '/class-builder-admin-bar.php' );
