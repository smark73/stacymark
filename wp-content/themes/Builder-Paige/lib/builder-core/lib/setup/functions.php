<?php

/*
Basic functions used during theme setup.
Written by Chris Jean for iThemes.com
Version 1.2.0

Version History
	1.0.0 - 2013-08-07 - Chris Jean
		Initial version
	1.0.1 - 2013-08-12 - Chris Jean
		Changed " - Child" default child theme name suffix to " - Custom".
	1.0.2 - 2013-08-13 - Chris Jean
		Added copying of the functions-child.php file to functions.php on child theme generation.
	1.0.3 - 2013-08-15 - Chris Jean
		Added copying of the images/ directory to the child theme.
	1.1.0 - 2013-09-04 - Chris Jean
		Improved naming of child theme directory.
		Added array of files and directories to copy over from parent theme to child.
		Added builder-filter-files-to-copy-to-child filter to control what files and directories are copied from parent theme to child.
	1.2.0 - 2013-09-17 - Chris Jean
		Added "Text Domain" and "Domain Path" copying to the style.css creation part of builder_create_child_theme().
		Added the lang/ directory to be copied when creating the child theme in builder_create_child_theme().
*/


function builder_create_child_theme( $args = array() ) {
	$default_args = array(
		'child_directory'    => false,
		'overwrite_existing' => false,
		'name'               => false,
		'source_directory'   => get_template_directory(),
		'source_type'        => 'parent',
		'parent_directory'   => get_template_directory(),
	);
	
	extract( ITUtility::merge_defaults( $args, $default_args ) );
	
	
	if ( empty( $name ) )
		$name = sprintf( __( '%s - Custom', 'it-l10n-Builder-Paige' ), $themes[basename( $parent_directory )]->get( 'Name' ) );
	
	$name = preg_replace( '/^\s+/', '', $name );
	$name = preg_replace( '/\s+$/', '', $name );
	
	$themes = wp_get_themes();
	$names = array();
	
	foreach ( $themes as $theme )
		$names[] = $theme->get( 'Name' );
	
	
	if ( in_array( $name, $names ) ) {
		$count = 2;
		
		while( in_array( "$name $count", $names ) )
			$count ++;
		
		$name = "$name $count";
	}
	
	
	if ( empty( $child_directory ) ) {
		$child_directory = preg_replace( '/[^a-z0-9]+/i', '-', ucfirst( $name ) );
		
		if ( ! preg_match( '/^builder/i', $child_directory ) )
			$child_directory = "Builder-$child_directory";
		
		$child_directory = dirname( $parent_directory ) . "/$child_directory";
	}
	
	if ( is_dir( $child_directory ) && ! $overwrite_existing ) {
		$count = 2;
		
		while ( is_dir( "$child_directory-$count" ) )
			$count++;
		
		$child_directory .= "-$count";
	}
	
	
	it_classes_load( 'it-file-utility.php' );
	
	
	if ( 'copy' == $source_type ) {
		ITFileUtility::copy( $source_directory, $child_directory, array( 'folder_mode' => 0775, 'file_mode' => 0664 ) );
		
		$stylesheet = file_get_contents( "$child_directory/style.css" );
		$stylesheet = preg_replace( '/(Theme Name:\s*).*/i', "$1$name", $stylesheet );
		
		file_put_contents( "$child_directory/style.css", $stylesheet );
	}
	else {
		ITFileUtility::mkdir( $child_directory, array( 'create_index' => false ) );
		
		
		$files = array(
			'style.css'            => '',
			'style-mobile.css'     => '',
			'style-tablet.css'     => '',
			'style-responsive.css' => '',
			'rtl.css'              => '',
			'functions-child.php'  => 'functions.php',
			'screenshot.png'       => '',
			'screenshot.jpg'       => '',
			'screenshot.gif'       => '',
			'images'               => '',
			'lang'                 => '',
			'plugin-features'      => '',
		);
		
		$files = apply_filters( 'builder-filter-files-to-copy-to-child', $files );
		
		
		$stylesheet_data_headers = array(
			'name'        => 'Theme Name',
			'theme_uri'   => 'Theme URI',
			'description' => 'Description',
			'author'      => 'Author',
			'author_uri'  => 'Author URI',
			'version'     => 'Version',
			'license'     => 'License',
			'license_uri' => 'License URI',
			'tags'        => 'Tags',
			'text_domain' => 'Text Domain',
			'domain_path' => 'Domain Path',
			'template'    => 'Template',
		);
		
		$stylesheet_data = get_file_data( "$parent_directory/style.css", $stylesheet_data_headers );
		
		$stylesheet_data['description'] = sprintf( __( 'This is a generated child theme for the %1$s theme. You should activate and modify this theme instead of %1$s. Doing so allows you to modify this child theme while allowing automatic upgrades for %1$s.', 'it-l10n-Builder-Paige' ), $stylesheet_data['name'] );
		$stylesheet_data['name'] = $name;
		$stylesheet_data['template'] = basename( $parent_directory );
		
		
		$child_header = "/*\n";
		
		foreach ( $stylesheet_data_headers as $index => $header )
			$child_header .= "$header: {$stylesheet_data[$index]}\n";
		
		$child_header .= '*/';
		
		
		if ( isset( $files['style.css'] ) ) {
			$stylesheet = file_get_contents( "$parent_directory/style.css" );
			$stylesheet = preg_replace( '|/\*.*Theme Name:.*?\*/|si', $child_header, $stylesheet );
		}
		else {
			$stylesheet = "$child_header\n\n\n@import url('../" . basename( $parent_directory ) . "/style.css');";
		}
		
		file_put_contents( "$child_directory/style.css", $stylesheet );
		
		
		foreach ( $files as $source => $destination ) {
			if ( 'style.css' == $source )
				continue;
			if ( ! file_exists( "$parent_directory/$source" ) )
				continue;
			
			
			if ( empty( $destination ) )
				$destination = $source;
			
			ITFileUtility::copy( "$parent_directory/$source", "$child_directory/$destination", array( 'folder_mode' => 0775, 'file_mode' => 0664 ) );
		}
	}
	
	
	add_option( 'builder_manually_switched_theme', true );
	
	switch_theme( basename( $child_directory ) );
}

function builder_get_child_themes( $template = false ) {
	if ( ! $template )
		$template = get_template();
	
	$themes = wp_get_themes();
	$child_themes = array();
	
	foreach ( $themes as $theme ) {
		if ( $theme->get_template() != $template )
			continue;
		if ( $theme->get_template() == $theme->get_stylesheet() )
			continue;
		
		$child_themes[] = $theme;
	}
	
	return $child_themes;
}
