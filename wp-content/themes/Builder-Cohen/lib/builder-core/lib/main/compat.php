<?php

/*
Compatibility functions to ensure proper functionality with older versions of WordPress
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2010-01-18
		Initial release version
	1.0.1 - 2010-12-01
		Moved to lib/main
		Added theme supports compatibility functions
*/


if ( ! function_exists( 'get_file_data' ) ) {
	// Taken from version 2.9.1 of WP
	function get_file_data( $file, $default_headers, $context = '' ) {
		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );
		
		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );
		
		// PHP will close file handle, but we are good citizens.
		fclose( $fp );
		
		if( $context != '' ) {
			$extra_headers = apply_filters( "extra_$context".'_headers', array() );
			
			$extra_headers = array_flip( $extra_headers );
			foreach( $extra_headers as $key=>$value ) {
				$extra_headers[$key] = $key;
			}
			$all_headers = array_merge($extra_headers, $default_headers);
		} else {
			$all_headers = $default_headers;
		}
		
		foreach ( $all_headers as $field => $regex ) {
			preg_match( '/' . preg_quote( $regex, '/' ) . ':(.*)$/mi', $file_data, ${$field});
			if ( !empty( ${$field} ) )
				${$field} = _cleanup_header_comment( ${$field}[1] );
			else
				${$field} = '';
		}
		
		$file_data = compact( array_keys( $all_headers ) );
		
		return $file_data;
	}
}


// Taken from WordPress 3.1-beta1-16642
if ( ! function_exists( 'add_theme_support' ) ) {
	function add_theme_support( $feature ) {
		global $_wp_theme_features;
		
		if ( func_num_args() == 1 )
			$_wp_theme_features[$feature] = true;
		else
			$_wp_theme_features[$feature] = array_slice( func_get_args(), 1 ); 
	}
}

if ( ! function_exists( 'get_theme_support' ) ) {
	function get_theme_support( $feature ) {
		global $_wp_theme_features;
		if ( !isset( $_wp_theme_features[$feature] ) )
			return false;
		else
			return $_wp_theme_features[$feature];
	}
}

if ( ! function_exists( 'remove_theme_support' ) ) {
	function remove_theme_support( $feature ) {
		// Blacklist: for internal registrations not used directly by themes.
		if ( in_array( $feature, array( 'custom-background', 'custom-header', 'editor-style', 'widgets', 'menus' ) ) )
			return false;
		
		global $_wp_theme_features;
		
		if ( ! isset( $_wp_theme_features[$feature] ) )
			return false;
		unset( $_wp_theme_features[$feature] );
		return true;
	}
}

if ( ! function_exists( 'current_theme_supports' ) ) {
	function current_theme_supports( $feature ) {
		global $_wp_theme_features;
		
		if ( !isset( $_wp_theme_features[$feature] ) )
			return false;
		
		// If no args passed then no extra checks need be performed
		if ( func_num_args() <= 1 )
			return true;
		
		$args = array_slice( func_get_args(), 1 );
		
		// @todo Allow pluggable arg checking
		switch ( $feature ) {
			case 'post-thumbnails':
				// post-thumbnails can be registered for only certain content/post types by passing
				// an array of types to add_theme_support().  If no array was passed, then
				// any type is accepted
				if ( true === $_wp_theme_features[$feature] )  // Registered for all types
					return true;
				$content_type = $args[0];
				if ( in_array($content_type, $_wp_theme_features[$feature][0]) )
					return true;
				else
					return false;
				break;
		}
		
		return true;
	}
}

if ( ! function_exists( 'require_if_theme_supports' ) ) {
	function require_if_theme_supports( $feature, $include) {
		if ( current_theme_supports( $feature ) )
			require ( $include );
	}
}
