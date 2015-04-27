<?php

function builder_pods_integration_filter_page_templates( $templates ) {
	$layout_data = apply_filters( 'it_storage_load_layout_settings', array() );
	
	$unset_list = array(
		'Blog Template',
		'Search Template',
		'Sitemap',
	);
	
	foreach ( $unset_list as $name )
		unset( $templates[$name] );
	
	foreach ( $layout_data['layouts'] as $id => $layout )
		$templates[" Builder Layout: {$layout['description']}"] = "builder-layout::$id";
	
	return $templates;
}

function builder_pods_integration_replace_template_redirect() {
	global $pod_page_exists;
	
	if ( ! isset( $pod_page_exists ) && function_exists( 'pod_page_exists' ) )
		$pod_page_exists =& pod_page_exists();
	
	if ( ! is_array( $pod_page_exists ) || ! isset( $pod_page_exists['page_template'] ) )
		return;
	
	if ( preg_match( '/^builder-layout::/', $pod_page_exists['page_template'] ) )
		add_filter( 'builder_filter_current_layout', 'builder_pods_integration_filter_current_layout' );
	else if ( ! empty( $pod_page_exists['page_template'] ) )
		return;
	
	
	global $pods_init;
	
	remove_action( 'template_redirect', array( &$pods_init, 'template_redirect' ) );
	add_action( 'template_redirect', 'builder_pods_integration_template_redirect' );
}

function builder_pods_integration_template_redirect() {
	if ( '' == locate_template( array( 'pods.php' ), true ) )
		require_once( dirname( __FILE__ ) . '/default-pods-template.php' );
	
	exit;
}

function builder_pods_integration_filter_current_layout( $layout_id ) {
	global $pod_page_exists;
	
	if ( ! preg_match( '/^builder-layout::([a-zA-Z0-9]+)/', $pod_page_exists['page_template'], $matches ) )
		return $layout_id;
	
	return $matches[1];
}
