<?php

/*
Written by Chris Jean for iThemes.com
Version 1.3.0

Version History
	1.1.0 - 2011-10-06 - Chris Jean
		Added loader for editor-features.php
	1.1.1 - 2011-10-19 - Chris Jean
		Removed references to TEMPLATEPATH
	1.2.0 - 2011-12-20 - Chris Jean
		Added builder_theme_settings_pre_settings_load just above builder_load_theme_settings()
	1.2.1 - 2013-02-15 - Chris Jean
		Removed unused SEO code.
	1.3.0 - 2013-08-12 - Chris Jean
		Removed all data-handling features as they are now in lib/data/
*/


require_once( dirname( __FILE__ ) . '/functions.php' );

if ( is_admin() ) {
	if ( current_theme_supports( 'builder-my-theme-menu' ) ) {
		require_once( dirname( __FILE__ ) . '/editor-features.php' );
		require_once( dirname( __FILE__ ) . '/editor.php' );
	}
	
	builder_add_import_export_data_source( 'BuilderDataSourceThemeSettings', dirname( __FILE__ ) . '/class.builder-data-source-theme-settings.php' );
}

function builder_theme_settings_load_javascript_cache_generators() {
	require_once( dirname( __FILE__ ) . '/generators/analytics.php' );
}
add_action( 'it_file_cache_prefilter_builder-core_javascript', 'builder_theme_settings_load_javascript_cache_generators' );


add_action( 'wp_head', 'builder_render_javascript_header_cache' );
add_action( 'wp_head', 'builder_render_css_cache' );
add_action( 'builder_layout_engine_render_container', 'builder_render_javascript_footer_cache', 20 );

add_action( 'wp_head', 'builder_render_header_tracking_code' );
add_action( 'builder_layout_engine_render_container', 'builder_render_footer_tracking_code', 20 );
