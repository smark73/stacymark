<?php

/*
Written by Chris Jean for iThemes.com
Version 2.2.1

Version History
	2.0.0 - 2010-01-17
		Finally added a version history
		Changed builder_layout_engine_get_available_views to builder_get_available_views
	2.0.1 - 2010-04-26
		Internationalized strings
	2.0.2 - 2010-07-16
		Internationalized description strings
		Added custom post type views
	2.0.3 - 2011-10-06 - Chris Jean
		Minor code optimizations
	2.1.0 - 2011-10-24 - Chris Jean
		Added support for custom taxonomy views
	2.1.1 - 2012-08-06 - Chris Jean
		Improved search result names
	2.2.0 - 2013-06-24 - Chris Jean
		Added the Multisite Signup Page View.
	2.2.1 - 2013-06-28 - Chris Jean
		builder_is_signup_page() now recongizes alternate signup pages as supplied by the wp_signup_location filter.
		Added result caching to builder_is_signup_page().
		Updated builder_is_singular() to return false when builder_is_signup_page() is true.
		Updated builder_is_page() to return false when builder_is_signup_page() is true.
*/


add_filter( 'builder_get_available_views', 'builder_set_available_views' );
add_filter( 'builder_get_available_views', 'builder_add_blog_view' );
add_filter( 'builder_get_available_views', 'builder_add_custom_post_types' );
add_filter( 'builder_get_available_views', 'builder_add_custom_taxonomies' );

if ( is_multisite() )
	add_filter( 'builder_get_available_views', 'builder_add_multisite_views' );


function builder_set_available_views( $views ) {
	$new_views = array(
		'builder_is_home'     => array(
			'name'        => _x( 'Home', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '20',
			'description' => __( 'The front page or home of your site', 'it-l10n-Builder-Cohen' ),
		),
		'builder_is_singular' => array(
			'name'        => _x( 'Singular', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '5',
			'description' => __( 'Any post, page, or attachment', 'it-l10n-Builder-Cohen' ),
		),
		'is_single'           => array(
			'name'        => _x( 'Post', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any post', 'it-l10n-Builder-Cohen' ),
		),
		'builder_is_page'     => array(
			'name'        => _x( 'Page', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any page', 'it-l10n-Builder-Cohen' ),
		),
		'is_attachment'       => array(
			'name'        => _x( 'Attachment', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '15',
			'description' => __( 'Any attachment (image or other attached file)', 'it-l10n-Builder-Cohen' ),
		),
		'is_archive'          => array(
			'name'        => _x( 'Archives', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '5',
			'description' => __( 'Any archive view (category, tag, author, or date)', 'it-l10n-Builder-Cohen' ),
		),
		'is_category'         => array(
			'name'        => _x( 'Category', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any category archive view', 'it-l10n-Builder-Cohen' ),
		),
		'is_tag'              => array(
			'name'        => _x( 'Tag', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any tag archive view', 'it-l10n-Builder-Cohen' ),
		),
		'is_author'           => array(
			'name'        => _x( 'Author', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any author archive view', 'it-l10n-Builder-Cohen' ),
		),
		'is_date'             => array(
			'name'        => _x( 'Date Archive', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '10',
			'description' => __( 'Any date-specific view (such as year, month, or day)', 'it-l10n-Builder-Cohen' ),
		),
		'is_search'           => array(
			'name'        => _x( 'Search Results', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '5',
			'description' => __( 'A search results page', 'it-l10n-Builder-Cohen' ),
		),
		'is_404'              => array(
			'name'        => _x( '404', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'    => '5',
			'description' => __( 'The "Page Not Found" error page', 'it-l10n-Builder-Cohen' ),
		),
	);
	
	return array_merge( $views, $new_views );
}

function builder_add_blog_view( $views ) {
	if ( 'page' != get_option('show_on_front') )
		return $views;
	
	$new_views = array(
		'builder_is_blog' => array(
			'name'          => _x( 'Blog', 'view', 'it-l10n-Builder-Cohen' ),
			'priority'      => '20',
			'description'   => __( 'This view is for when a static page is used as the front (or home) page and you want to apply a specific layout to the selected posts page', 'it-l10n-Builder-Cohen' ),
		),
	);
	
	return array_merge( $views, $new_views );
}

function builder_add_custom_post_types( $views ) {
	$custom_post_types = builder_get_custom_post_types();
	$new_views = array();
	
	foreach ( (array) $custom_post_types as $post_type => $name ) {
		$new_views["builder_is_custom_post_type|$post_type"] = array(
			'name'        => sprintf( _x( 'Post Type - %s', 'view', 'it-l10n-Builder-Cohen' ), $name ),
			'priority'    => '15',
			'description' => sprintf( _x( 'Any %s entry', 'view description', 'it-l10n-Builder-Cohen' ), $name ),
		);
	}
	
	return array_merge( $views, $new_views );
}

function builder_add_custom_taxonomies( $views ) {
	$custom_taxonomies = builder_get_custom_taxonomies();
	$new_views = array();
	
	foreach ( (array) $custom_taxonomies as $taxonomy => $name ) {
		$new_views["builder_is_custom_taxonomy|$taxonomy"] = array(
			'name'        => sprintf( _x( 'Taxonomy - %s', 'view', 'it-l10n-Builder-Cohen' ), $name ),
			'priority'    => '15',
			'description' => sprintf( _x( 'Any %s taxonomy entry', 'view description', 'it-l10n-Builder-Cohen' ), $name ),
		);
	}
	
	return array_merge( $views, $new_views );
}

function builder_add_multisite_views( $views ) {
	$new_views = array(
		'builder_is_signup_page' => array(
			'name'        => __( 'Signup Page', 'it-l10n-Builder-Cohen' ),
			'priority'    => '20',
			'description' => __( 'The multisite user signup page (<code>wp-signup.php</code>)', 'it-l10n-Builder-Cohen' ),
		),
	);
	
	return array_merge( $views, $new_views );
}

function builder_is_signup_page() {
	if ( isset( $GLOBALS['it_builder_cache_is_signup_page'] ) )
		return $GLOBALS['it_builder_cache_is_signup_page'];
	
	$GLOBALS['it_builder_cache_is_signup_page'] = false;
	
	if ( ! is_multisite() )
		return false;
	
	
	$signup_url_location = apply_filters( 'wp_signup_location', network_site_url( 'wp-signup.php' ) );
	$current_url_location = $_SERVER['REQUEST_URI'];
	
	if ( false !== strpos( $signup_url_location, '?' ) )
		list( $signup_url_location, $signup_url_query ) = explode( '?', $signup_url_location );
	if ( false !== strpos( $current_url_location, '?' ) )
		list( $current_url_location, $current_url_query ) = explode( '?', $current_url_location );
	
	if ( empty( $signup_url_query ) )
		$signup_url_query = '';
	if ( empty( $current_url_query ) )
		$current_url_query = '';
	
	parse_str( $signup_url_query, $signup_url_query );
	parse_str( $current_url_query, $current_url_query );
	
	
	if ( ! preg_match( '/' . preg_quote( $current_url_location, '/' ) . '$/', $signup_url_location ) ) {
		return false;
	}
	else {
		foreach ( $signup_url_query as $var => $val ) {
			if ( ! isset( $current_url_query[$var] ) || ( $val != $current_url_query[$var] ) )
				return false;
		}
	}
	
	
	$GLOBALS['it_builder_cache_is_signup_page'] = true;
	
	return true;
}

function builder_is_home() {
	if ( builder_is_signup_page() )
		return false;
	
	if ( ( 'page' == get_option('show_on_front') ) ) {
		if ( is_front_page() )
			return true;
		else
			return false;
	}
	
	if ( is_home() )
		return true;
	
	return false;
}

function builder_is_blog() {
	if ( 'page' != get_option( 'show_on_front' ) )
		return false;
	
	if ( is_home() && ! is_singular() )
		return true;
	
	return false;
}

function builder_is_singular() {
	if ( builder_is_signup_page() )
		return false;
	
	if ( builder_is_blog() )
		return true;
	
	return is_singular();
}

function builder_is_page() {
	if ( builder_is_signup_page() )
		return false;
	
	if ( builder_is_blog() )
		return true;
	
	return is_page();
}

function builder_is_custom_post_type( $post_type = false ) {
	if ( ! is_single() )
		return false;
	
	
	global $wp_version;
	
	if ( version_compare( $wp_version, '2.9.7', '>' ) )
		$default_types = get_post_types( array( '_builtin' => true ) );
	else
		$default_types = array( 'post', 'page', 'attachment', 'revision' );
	
	$current_post_type = get_post_type();
	
	if ( in_array( $current_post_type, $default_types ) )
		return false;
	
	if ( false == $post_type )
		return true;
	
	if ( $current_post_type == $post_type )
		return true;
	
	return false;
}

function builder_is_custom_taxonomy( $taxonomy = false ) {
	if ( ! is_tax() || ! function_exists( 'get_taxonomies' ) )
		return false;
	
	
	global $wp_query;
	
	$default_types = get_taxonomies( array( '_builtin' => true ) );
	
	$current_taxonomy = $wp_query->tax_query->queries[0]['taxonomy'];
	
	if ( in_array( $current_taxonomy, $default_types ) )
		return false;
	
	if ( false == $taxonomy )
		return true;
	
	if ( $current_taxonomy == $taxonomy )
		return true;
	
	return false;
}
