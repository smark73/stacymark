<?php

/*
Basic functions used by the responsive feature of Builder.
Written by Chris Jean for iThemes.com
Version 1.4.0

Version History
	1.0.0 - 2012-10-09 - Chris Jean
		Initial version
	1.1.0 - 2012-10-12 - Chris Jean
		Commented out responsive.css output in order to focus on generated stylesheets.
	1.2.0 - 2012-10-18 - Chris Jean
		Added builder_add_responsive_stylesheets function.
	1.3.0 - 2013-09-04 - Chris Jean
		Added fallback where responsive stylesheets will be searched for in the child first and then fallback to the parent if they are missing.
	1.4.0 - 2013-11-05 - Chris Jean
		Added the builder_get_responsive_stylesheet_files filter.
*/


function builder_add_fitvids_scripts() {
	$base_url = ITUtility::get_url_from_file( dirname( __FILE__ ) );
	
	wp_register_script( 'fitvids', "$base_url/js/jquery.fitvids-max-width-modification.js", array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'builder-init-fitvids', "$base_url/js/init-fitvids.js", array( 'fitvids' ), '1.0', true );
}

function builder_add_responsive_viewport_meta() {
	echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">\n";
}

function builder_add_responsive_stylesheets() {
	$template_dir = get_template_directory();
	$template_url = get_template_directory_uri();
	$stylesheet_dir = get_stylesheet_directory();
	$stylesheet_url = get_stylesheet_directory_uri();
	
	$files = array(
		'style-responsive.css' => 'tablet-width',
		'style-tablet.css'     => array(
			'mobile-width',
			'tablet-width',
		),
		'style-mobile.css'     => 'mobile-width',
	);
	
	$files = apply_filters( 'builder_get_responsive_stylesheet_files', $files );
	
	$stylesheets = array();
	$use_template = false;
	
	if ( ( $template_dir != $stylesheet_dir ) && ! defined( 'BUILDER_DISABLE_PARENT_RESPONSIVE_STYLESHEETS' ) )
		$use_template = true;
	
	foreach ( array_keys( $files ) as $file ) {
		if ( file_exists( "$stylesheet_dir/$file" ) )
			$stylesheets[$file] = "$stylesheet_url/$file";
		else if ( $use_template && file_exists( "$template_dir/$file" ) )
			$stylesheets[$file] = "$template_url/$file";
	}
	
	
	if ( empty( $stylesheets ) )
		return;
	
	$size_widths = array(
		'tablet-width' => builder_theme_supports( 'builder-responsive', 'tablet-width' ),
		'mobile-width' => builder_theme_supports( 'builder-responsive', 'mobile-width' ),
		'layout-width' => apply_filters( 'builder_get_layout_width', '' ),
	);
	
	foreach ( $stylesheets as $file => $stylesheet ) {
		$widths = $files[$file];
		
		if ( is_array( $widths ) ) {
			$min_width = $widths[0];
			$max_width = $widths[1];
		}
		else {
			$min_width = '';
			$max_width = $widths;
		}
		
		if ( ! empty( $min_width ) && isset( $size_widths[$min_width] ) )
			$min_width = $size_widths[$min_width];
		if ( ! empty( $min_width ) && isset( $size_widths[$min_width] ) )
			$min_width = $size_widths[$min_width];
		
		if ( is_numeric( $min_width ) )
			$min_width .= 'px';
		
		if ( ! empty( $size_widths[$max_width] ) )
			$max_width = $size_widths[$max_width];
		if ( ! empty( $size_widths[$max_width] ) )
			$max_width = $size_widths[$max_width];
		
		if ( is_numeric( $max_width ) )
			$max_width .= 'px';
		
		
		if ( empty( $min_width ) )
			echo "<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" media=\"only screen and (max-width: $max_width)\" />\n";
		else
			echo "<link rel=\"stylesheet\" href=\"$stylesheet\" type=\"text/css\" media=\"only screen and (min-width: $min_width) and (max-width: $max_width)\" />\n";
	}
}
