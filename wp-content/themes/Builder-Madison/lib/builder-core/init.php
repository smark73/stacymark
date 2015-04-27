<?php


$GLOBALS['it_builder_core_version'] = '5.0.26';


// Required file to initialize needed variables and load needed code.
require_once( dirname( __FILE__ ) . '/lib/main/init.php' );

// Load admin icon fonts
if ( is_admin() && version_compare( $GLOBALS['wp_version'], '3.7.10', '>=' ) ) {
	require_once( dirname( __FILE__ ) . '/lib/icon-fonts/load.php' );
}

// Load translations if they have not already been loaded by the theme's functions.php or functions-child.php.
if ( ! isset( $GLOBALS['l10n']['it-l10n-Builder-Madison'] ) )
	load_theme_textdomain( 'it-l10n-Builder-Madison', get_stylesheet_directory() . '/lang' );


// Builder features can be modified in a child theme's functions.php by using
// remove_theme_support and the builder_customize_theme_features action.
// The following code snippets are examples of how this can be used.

/*

// Disable Billboard and the Feedburner Widget
function child_theme_remove_theme_features() {
	remove_theme_support( 'builder-billboard' );
	remove_theme_support( 'builder-feedburner-widget' );
}
add_action( 'builder_customize_theme_features', 'child_theme_remove_theme_features' );


// Remove the My Theme menu for everyone but the user with a login of 'admin'
function child_theme_restrict_my_theme_menu() {
	$user = wp_get_current_user();
	
	if ( 'admin' !== $user->user_login )
		remove_theme_support( 'builder-my-theme-menu' );
}
add_action( 'builder_customize_theme_features', 'child_theme_restrict_my_theme_menu' );


// Remove the My Theme menu for everyone but super admins (multisite capability)
function child_theme_restrict_my_theme_menu() {
	if ( ! is_super_admin() )
		remove_theme_support( 'builder-my-theme-menu' );
}
add_action( 'builder_customize_theme_features', 'child_theme_restrict_my_theme_menu' );

*/


// Add support for different theme features
if ( ! function_exists( 'builder_add_theme_features' ) ) {
	function builder_add_theme_features() {
		// Add 2.9+ thumbnail support
		add_theme_support( 'post-thumbnails' );
		
		// Add 3.0+ menu support
		add_theme_support( 'menus' );
		
		// Add links to common RSS feeds
		add_theme_support( 'automatic-feed-links' );
		
		// Add the My Theme menu. If this is removed, the My Theme menu won't show for anyone.
		add_theme_support( 'builder-my-theme-menu' );
		
		// Allow Builder to use file caching to speed up site rendering
		add_theme_support( 'builder-file-cache' );
		
		// Allow Builder to load the the built-in default layouts found in default-layouts.php
		// This should not be disabled unless the child theme specifically provides it's own layouts
		add_theme_support( 'builder-default-layouts' );
		
		// Enable support for Builder Extensions
		add_theme_support( 'builder-extensions' );
		
		// Enable the import/export feature
		add_theme_support( 'builder-import-export' );
		
		// Load the Billboard feature found in the DisplayBuddy > Billboard menu
		add_theme_support( 'builder-billboard' );
		
		// Load Feedburner Widget code
		add_theme_support( 'builder-feedburner-widget' );
		
		// Load Duplicate Sidebar widget code
		add_theme_support( 'builder-widget-duplicate-sidebar' );
		
		// Load Widget Content code
		add_theme_support( 'builder-widget-widget-content' );
		
		// Add support for Builder to supply enhancements for specific plugins
		add_theme_support( 'builder-plugin-features' );
		
		// Add support for Widget Styles
		add_theme_support( 'builder-widget-styles' );
		
		// Add support for html5
		add_theme_support( 'html5' );
		
		// Add WordPress Admin Bar modifications for Builder
		add_theme_support( 'builder-admin-bar' );
		
		// Enable the header flush feature to improve site performance
		add_theme_support( 'builder-header-flush' );
		
		// Enable generation of the full page title tag
		add_theme_support( 'builder-title-tag' );
		
		// Enable custom gallery shortcode handler
		add_theme_support( 'builder-gallery-shortcode' );
		
		
		do_action( 'builder_customize_theme_features' );
	}
}
builder_add_theme_features();


// Load the custom-functions.php file if it exists.
if ( file_exists( get_template_directory() . '/custom-functions.php' ) )
	require_once( get_template_directory() . '/custom-functions.php' );


// Load the licensing and updater system.
if ( ! empty( $GLOBALS['builder_theme_package_name'] ) ) {
	function builder_updater_register( $updater ) {
		$updater->register( $GLOBALS['builder_theme_package_name'], get_template_directory() . '/style.css' );
	}
	
	add_action( 'ithemes_updater_register', 'builder_updater_register' );
	
	require( dirname( __FILE__ ) . '/lib/updater/load.php' );
}
