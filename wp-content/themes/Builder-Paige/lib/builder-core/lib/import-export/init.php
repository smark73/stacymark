<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	0.0.1 - 2010-12-20 - Chris Jean
		Initial test version
	1.0.0 - 2011-09-07 - Chris Jean
		Broke out the admin-only functions to admin-init.php
*/


builder_set_data_version( 'builder-exports', '1.0' );


if ( is_admin() )
	require_once( dirname( __FILE__ ) . '/admin-init.php' );
