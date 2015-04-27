<?php

/*
Central data handler.
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-08-09 - Chris Jean
		Initial version.
*/


require( dirname( __FILE__ ) . '/functions.php' );
require( dirname( __FILE__ ) . '/defaults.php' );


builder_set_data_version( 'theme-settings', '1.0' );


function builder_theme_settings_upgrade() {
	require_once( dirname( __FILE__ ) . '/upgrade.php' );
}
add_action( 'it_storage_do_upgrade_builder-theme-settings', 'builder_theme_settings_upgrade' );



do_action( 'builder_theme_settings_pre_settings_load' );

builder_load_theme_settings( true );

do_action( 'builder_theme_settings_loaded' );
