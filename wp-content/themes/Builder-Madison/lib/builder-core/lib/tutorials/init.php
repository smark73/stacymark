<?php

/*
Code responsible for loading required parts of the theme
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-08-15 - Chris Jean
		Release ready
*/


if ( is_admin() )
	require_once( dirname( __FILE__ ) . '/tutorials.php' );
