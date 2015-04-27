<?php

/*
Basic functions for loading and saving data.
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-08-09 - Chris Jean
		Initial version.
		Some code migrated from lib/theme-settings/functions.php.
*/


function builder_get_theme_setting( $name ) {
	global $wp_theme_options;
	
	if ( isset( $wp_theme_options[$name] ) )
		return $wp_theme_options[$name];
	else
		return null;
}

// Keeping legacy function
function builder_get_theme_option( $name ) {
	return builder_get_theme_setting( $name );
}

function builder_get_seo_setting( $name ) {
	return $GLOBALS['wp_theme_options']['seo'][$name];
}

function builder_theme_supports( $feature, $arg = '' ) {
	global $wp_theme_options;
	
	if ( isset( $wp_theme_options["theme_supports_$feature"] ) && empty( $wp_theme_options["theme_supports_$feature"] ) )
		return false;
	
	if ( ! current_theme_supports( $feature ) )
		return false;
	
	if ( empty( $arg ) )
		return true;
	
	$args = get_theme_support( $feature );
	
	if ( isset( $args[0] ) && isset( $args[0][$arg] ) )
		return $args[0][$arg];
	
	if ( isset( $GLOBALS['builder_theme_supports_defaults'][$feature][$arg] ) )
		return $GLOBALS['builder_theme_supports_defaults'][$feature][$arg];
	
	return false;
}

function builder_load_theme_settings( $set_global = false ) {
	$storage = new ITStorage2( 'builder-theme-settings', builder_get_data_version( 'theme-settings' ) );
	$settings = $storage->load();
	
	if ( true === $set_global )
		$GLOBALS['wp_theme_options'] = $settings;
	
	return $settings;
}

function builder_add_theme_feature_option( $feature, $name, $description, $priority = 10, $default_enabled = true ) {
	global $builder_theme_feature_options;
	
	$default_enabled = ( true === $default_enabled ) ? 'enable' : '';
	
	if ( ! is_array( $builder_theme_feature_options ) )
		$builder_theme_feature_options = array();
	if ( ! isset( $builder_theme_feature_options[$priority] ) )
		$builder_theme_feature_options[$priority] = array();
	
	$builder_theme_feature_options[$priority][$feature] = compact( 'name', 'description', 'default_enabled' );
}

function builder_remove_theme_feature_option( $feature ) {
	global $builder_theme_feature_options;
	
	if ( isset( $builder_theme_feature_options[$feature] ) )
		unset( $builder_theme_feature_options[$feature] );
}
