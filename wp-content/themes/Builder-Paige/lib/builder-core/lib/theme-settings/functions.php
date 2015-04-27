<?php

/*
Basic functions used by Builder's theme settings
Written by Chris Jean for iThemes.com
Version 1.2.1

Version History
	1.1.0 - 2012-10-17 - Chris Jean
		Added support for checking arguments in the builder_theme_supports function.
	1.1.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.2.0 - 2013-08-12 - Chris Jean
		Moved builder_get_theme_setting() to lib/data/functions.php.
		Moved builder_get_theme_option() to lib/data/functions.php.
		Moved builder_theme_supports() to lib/data/functions.php.
		Moved builder_load_theme_settings() to lib/data/functions.php.
		Moved builder_add_theme_feature_option() to lib/data/functions.php.
		Moved builder_remove_theme_feature_option() to lib/data/functions.php.
	1.2.1 - 2013-10-11 - Chris Jean
		Changed the default ID for builder_get_closed_comments_message() from "comments" to "respond".
*/


function builder_render_javascript_header_cache() {
	do_action( 'it_cache_render_builder-core', 'javascript' );
}

function builder_render_javascript_footer_cache() {
	do_action( 'it_cache_render_builder-core', 'javascript-footer' );
}

function builder_render_css_cache() {
	do_action( 'it_cache_render_builder-core', 'css' );
}

function builder_render_header_tracking_code() {
	$code = trim( builder_get_theme_setting( 'javascript_code_header' ) );
	
	if ( ! empty( $code ) )
		echo "\n$code\n";
}

function builder_render_footer_tracking_code() {
	$code = trim( builder_get_theme_setting( 'javascript_code_footer' ) );
	
	if ( ! empty( $code ) )
		echo "\n$code\n";
}

function builder_add_settings_tab( $name, $var, $class, $file = null ) {
	global $builder_settings_tabs;
	
	if ( ! is_array( $builder_settings_tabs ) )
		$builder_settings_tabs = array();
	
	$builder_settings_tabs[$var] = compact( 'name', 'class', 'file' );
}

function builder_remove_settings_tab( $var ) {
	global $builder_settings_tabs;
	
	if ( ! is_array( $builder_settings_tabs ) )
		return;
	if ( isset( $builder_settings_tabs[$var] ) )
		unset( $builder_settings_tabs[$var] );
}

function builder_add_settings_editor_box( $title, $callback, $args = array() ) {
	global $builder_settings_boxes;
	
	$default_args = array(
		'title'    => $title,
		'callback' => $callback,
		'var'      => preg_replace( '|[^a-zA-Z0-9_\-]+|', '_', $title ),
		'tab'      => 'basic',
		'priority' => ( isset( $args['_builtin'] ) && ( true === $args['_builtin'] ) ) ? 'default' : 'high',
		'_builtin' => false,
	);
	
	$args = array_merge( $default_args, $args );
	
	
	if ( ! isset( $builder_settings_boxes ) )
		$builder_settings_boxes = array();
	
	$tab = $args['tab'];
	$var = $args['var'];
	
	unset( $args['tab'] );
	unset( $args['var'] );
	
	if ( isset( $builder_settings_boxes[$var] ) ) {
		$count = 1;
		
		while( isset( $builder_settings_boxes["$var-$count"] ) )
			$count++;
		
		$var = "$var-$count";
	}
	
	$builder_settings_boxes[$tab][$var] = $args;
}

function builder_get_settings_editor_boxes( $tab ) {
	global $builder_settings_boxes;
	
	if ( isset( $builder_settings_boxes[$tab] ) && is_array( $builder_settings_boxes[$tab] ) )
		return $builder_settings_boxes[$tab];
	
	return array();
}

function _builder_analytics_add_option( $type, $args, $services ) {
	global $builder_analytics_options;
	
	if ( ( 'all' === $services ) || empty( $services ) )
		$services = array( 'google_analytics', 'woopra' );
	
	if ( ! is_array( $builder_analytics_options ) )
		$builder_analytics_options = array();
	
	foreach ( (array) $services as $service )
		$builder_analytics_options[$type][$service][] = $args;
}

function builder_analytics_add_setting( $description, $name, $type, $default = '', $services = 'all' ) {
	_builder_analytics_add_option( 'setting', compact( 'description', 'name', 'type', 'default' ), $services );
}

function builder_analytics_add_action_tracker( $description, $name, $default = true, $services = 'all' ) {
	_builder_analytics_add_option( 'action_tracker', compact( 'description', 'name', 'function', 'default' ), $services );
}

function builder_analytics_add_event_tracker( $description, $name, $function, $default = true, $services = 'all' ) {
	_builder_analytics_add_option( 'event_tracker', compact( 'description', 'name', 'function', 'default' ), $services );
}

function builder_analytics_add_data_tracker( $description, $name, $function, $default = true, $services = 'all' ) {
	_builder_analytics_add_option( 'data_tracker', compact( 'description', 'name', 'function', 'default' ), $services );
}

function builder_identify_widget_area( $sidebar_has_widgets ) {
	global $wp_theme_options;
	
	
	if ( 'never' === $wp_theme_options['identify_widget_areas_method'] ) {
		return false;
	}
	else if ( 'always' !== $wp_theme_options['identify_widget_areas_method'] ) {
		if ( $sidebar_has_widgets )
			return false;
	}
	
	if ( 'user' === $wp_theme_options['identify_widget_areas'] ) {
		if ( ! is_user_logged_in() )
			return false;
	}
	else if ( 'all' !== $wp_theme_options['identify_widget_areas'] ) {
		if ( ! builder_user_can_modify_widgets() )
			return false;
	}
	
	return true;
}

function builder_user_can_modify_widgets() {
	global $wp_version;
	
	if ( version_compare( $wp_version, '2.9.7', '>' ) )
		return current_user_can( 'edit_theme_options' );
	return current_user_can( 'switch_themes' );
}

function builder_show_comments() {
	$post_type = get_post_type();
	
	if ( empty( $post_type ) || ( function_exists( 'post_type_supports' ) && ! post_type_supports( $post_type, 'comments' ) ) || ! builder_get_theme_setting( "enable_comments_$post_type" ) )
		return false;
	
	return true;
}

function builder_get_closed_comments_message( $default_message, $before = '<div id="respond"><p class="nocomments comments-closed">', $after = '</p></div>' ) {
	$setting = builder_get_theme_setting( 'comments_disabled_message' );
	
	$message = '';
	
	if ( 'standard' === $setting )
		$message = $default_message;
	else if ( 'custom' === $setting )
		$message = builder_get_theme_setting( 'comments_disabled_message_custom_message' );
	
	$message = apply_filters( 'builder_filter_closed_comments_message', $message );
	
	if ( empty( $message ) )
		return '';
	
	return $before . $message . $after;
}
