<?php

/*
Title generating functions
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2012-08-03 - Chris Jean
		Created from code taken from lib/main/functions.php
	1.0.1 - 2012-12-18 - Chris Jean
		Replaced is_home with builer_is_home to fix title bug when static blog page is used.
*/


// Temporary Builder title function until Builder SEO can be finished
function builder_add_title() {
	$seperator = apply_filters( 'builder_filter_title_seperator', '::' );
	
	$direction = ( is_rtl() ) ? 'left' : 'right';
	$direction = apply_filters( 'builder_filter_title_direction', $direction );
	
	$title = trim( wp_title( $seperator, false, $direction ) );
	
	$title = apply_filters( 'builder_filter_title', $title );
	
	echo "<title>$title</title>\n";
}

function builder_filter_wp_title( $title, $seperator, $direction ) {
	global $paged, $page;
	
	if ( is_feed() )
		return $title;
	
	
	$seperator = ' ' . trim( $seperator ) . ' ';
	
	if ( builder_is_home() && apply_filters( 'builder_filter_flip_title_direction_on_home', true ) )
		$direction = ( 'right' == $direction ) ? 'left' : 'right';
	
	if ( ( $paged >= 2 ) || ( $page >= 2 ) ) {
		$page_description = sprintf( __( 'Page %s', 'it-l10n-Builder-Cohen' ), max( $paged, $page ) );
		
		if ( 'right' == $direction )
			$title .= $page_description . $seperator;
		else if ( is_rtl() )
			$title = $seperator . $page_description . $title;
		else
			$title .= $seperator . $page_description;
	}
	
	if ( 'right' == $direction )
		$title .= get_bloginfo( 'name' );
	else
		$title = get_bloginfo( 'name' ) . $title;
	
	
	return $title;
}
