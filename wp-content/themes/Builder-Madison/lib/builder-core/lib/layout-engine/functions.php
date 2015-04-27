<?php

/*
Written by Chris Jean for iThemes.com
Version 1.7.0

Version History
	1.3.0 - 2012-10-09 - Chris Jean
		Added the builder_get_pixel_widths and builder_get_percent_widths functions.
	1.4.0 - 2012-10-12 - Chris Jean
		Improved the builder_get_percent_widths function.
	1.5.0 - 2013-07-01 - Chris Jean
		Updated the storage of the default Layouts and Views to use a three format system: JSON, serialize+base64, and serialize. This is to avoid issues with servers that have problems properly reading the data.
	1.6.0 - 2013-08-09 - Chris Jean
		Added builder_import_theme_default_layouts_and_views().
		Use value from builder_import_theme_default_layouts_and_views() when setting up default Layouts if the function returns a non-empty result.
	1.7.0 - 2013-08-15 - Chris Jean
		Updated the theme importer to properly use the built-in importer system.
*/


function builder_get_default_layouts( $layouts ) {
//	builder_store_default_layouts( $layouts );
	
	
	if ( ! empty( $layouts ) && is_array( $layouts ) && isset( $layouts['default'] ) )
		return $layouts;
	
	
	set_transient( 'builder_fresh_install', true, 300 );
	
	
	$theme_defaults = builder_import_theme_default_layouts_and_views();
	
	return $theme_defaults;
}

function builder_import_theme_default_layouts_and_views( $method = 'add', $layouts_method = 'add', $views_method = 'skip' ) {
	if ( ! in_array( $method, array( 'add', 'replace' ) ) )
		$method = 'add';
	
	
	$theme_defaults_file = locate_template( 'defaults/layouts-and-views.zip' );
	
	if ( ! file_exists( $theme_defaults_file ) ) {
		$layouts = builder_get_builder_core_default_layouts();
		
		$storage = new ITStorage( 'layout_settings' );
		$storage->save( $layouts );
		
		return $layouts;
	}
	
	
	require_once( dirname( dirname( __FILE__ ) ) . '/import-export/class.builder-import-export.php' );
	
	$import = new BuilderImportExport( $theme_defaults_file );
	$data_sources = $import->get_data_sources();
	
	if ( ! isset( $data_sources['layouts-views'] ) ) {
		$import->cleanup();
		return false;
	}
	
	
	$settings = array(
		'data_sources' => array(
			'layouts-views' => array(
				'method'         => $method,
				'layouts_method' => $layouts_method,
				'views_method'   => $views_method,
			),
		),
	);
	
	$db_data = array(
		'layouts-views' => array(),
	);
	
	$results = $import->run_import( $settings, false, true, $db_data );
	
	$import->cleanup();
	
	if ( ! isset( $results['layouts-views'] ) || empty( $results['layouts-views'] ) )
		return false;
	
	return $results['layouts-views'];
}

function builder_get_builder_core_default_layouts() {
	$layouts = array();
	
	$default_data_path = dirname( __FILE__ ) . '/default-data';
	
	if ( is_callable( 'json_decode' ) )
		$defaults = json_decode( file_get_contents( "$default_data_path/default-layouts.json" ), true );
	else if ( is_callable( 'base64_decode' ) )
		$defaults = @unserialize( base64_decode( file_get_contents( "$default_data_path/default-layouts.base64" ) ) );
	else
		$defaults = unserialize( file_get_contents( "$default_data_path/default-layouts.txt" ) );
	
	
	include_once( dirname( __FILE__ ) . '/upgrade-storage.php' );
	$data = apply_filters( 'it_storage_upgrade_layout_settings', array( 'data' => $defaults ) );
	$defaults = $data['data'];
	
	require_once( dirname( __FILE__ ) . '/layout-settings-guid-randomizer.php' );
	$defaults = BuilderLayoutSettingsGUIDRandomizer::randomize_guids( $defaults );
	
	
	return ITUtility::merge_defaults( $layouts, $defaults );
}

function builder_store_default_layouts( $layouts ) {
	$default_data_path = dirname( __FILE__ ) . '/default-data';
	
	file_put_contents( "$default_data_path/default-layouts.txt", serialize( $layouts ) );
	file_put_contents( "$default_data_path/default-layouts.base64", base64_encode( serialize( $layouts ) ) );
	file_put_contents( "$default_data_path/default-layouts.json", json_encode( $layouts ) );
}

function builder_get_pixel_widths( $widths, $full_pixel_width ) {
	$total_width = array_sum( $widths );
	
	if ( $total_width >= 110 ) {
		$widths[count( $widths ) - 1] += $full_pixel_width - $total_width;
		
		return $widths;
	}
	
	
	$pixel_widths = array();
	$remaining_width = $full_pixel_width;
	
	foreach ( $widths as $index => $width ) {
		if ( ( $index + 1 ) == count( $widths ) )
			$width = $remaining_width;
		else
			$width = ceil( ( $width / 100 ) * $full_pixel_width );
		
		$pixel_widths[] = $width;
		$remaining_width -= $width;
	}
	
	return $pixel_widths;
}

function builder_get_percent_widths( $widths ) {
	$total_width = array_sum( $widths );
	
	if ( $total_width < 110 ) {
		$widths[count( $widths ) - 1] += 100 - $total_width;
		
		return $widths;
	}
	
	
	$percent_widths = array();
	$remaining_width = 100;
	
	$count = 1;
	
	foreach ( $widths as $index => $width ) {
		if ( $count == count( $widths ) )
			$width = $remaining_width;
		else
			$width = intval( $width / $total_width * 100000 ) / 1000;
		
		$percent_widths[$index] = $width;
		$remaining_width -= $width;
		
		$count++;
	}
	
	return $percent_widths;
}
