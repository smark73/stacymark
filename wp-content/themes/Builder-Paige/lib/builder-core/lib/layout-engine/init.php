<?php

/*
Written by Chris Jean for iThemes.com
Version 2.6.0

Version History
	2.4.0 - 2011-07-05 - Chris Jean
		Added filter to push to builder_get_default_layouts
		Updated layout-settings data version to 1.6
	2.4.1 - 2011-10-06 - Chris Jean
		Minor performance improvements
	2.5.0 - 2012-08-23 - Chris Jean
		Changed require_once to require.
		Changed require for modules.php to module-loader.php.
		Replaced frequent dirname requests with a variable.
	2.6.0 - 2012-10-12 - Chris Jean
		Added require for style-generator.php.
*/


builder_set_data_version( 'layout-settings', '1.6' );

$layout_engine_dir = dirname( __FILE__ );


require( $layout_engine_dir . '/functions.php' );
require( $layout_engine_dir . '/available-views.php' );
require( $layout_engine_dir . '/module-loader.php' );
require( $layout_engine_dir . '/sidebars.php' );
require( $layout_engine_dir . '/style-generator.php' );


if ( builder_theme_supports( 'builder-default-layouts' ) )
	add_filter( 'it_storage_filter_load_layout_settings', 'builder_get_default_layouts', 0 );

if ( is_admin() ) {
	if ( current_theme_supports( 'builder-my-theme-menu' ) )
		require( $layout_engine_dir . '/editor.php' );
	
	require( $layout_engine_dir . '/add-layout-screen-options.php' );
	
	builder_add_import_export_data_source( 'BuilderDataSourceLayoutsViews', $layout_engine_dir . '/data-source-layouts-views.php' );
}
else {
	require( $layout_engine_dir . '/layout-selector.php' );
	require( $layout_engine_dir . '/layout-engine.php' );
}
