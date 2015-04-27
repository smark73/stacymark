<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.0

Version History
	1.0.0 - 2011-09-07 - Chris Jean
		Created from version 0.0.1 of init.php
	1.1.0 - 2013-08-13 - Chris Jean
		Changed builder_theme_settings_loaded action to builer_theme_features_loaded.
*/


function builder_add_import_export_settings_tab() {
	builder_add_settings_tab( __( 'Import / Export', 'it-l10n-Builder-Cohen' ), 'import-export', 'ITThemeSettingsTabImportExport', dirname( __FILE__ ) . '/settings-tab.php' );
}

if ( current_theme_supports( 'builder-import-export' ) )
	add_action( 'builder_theme_features_loaded', 'builder_add_import_export_settings_tab' );


function builder_add_import_export_data_source( $class, $file = null ) {
	global $builder_import_export_data_sources;
	
	if ( ! is_array( $builder_import_export_data_sources ) )
		$builder_import_export_data_sources = array();
	
	$builder_import_export_data_sources[] = compact( 'class', 'file' );
}

function builder_import_export_cleanup( $guid, $path ) {
	require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
	
	BuilderImportExport::cleanup( $guid, $path );
}
add_action( 'builder_import_export_cleanup', 'builder_import_export_cleanup', 10, 2 );
