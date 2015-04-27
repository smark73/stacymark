<?php

/*
Plugin compatibility code for Builder and WordPress SEO
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-02-15 - Chris Jean
		Initial version
*/


// Disable Builder title completion.
remove_filter( 'wp_title', 'builder_filter_wp_title', 20 );
