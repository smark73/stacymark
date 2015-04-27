<?php

/*
Basic functions used by various parts of Builder
Written by Chris Jean for iThemes.com
Version 1.12.1

Version History
	1.9.0 - 2012-09-24 - Chris Jean
		Removed title functions as they now reside in lib/title.
		Add builder_add_theme_stylesheet, builder_add_reset_stylesheet, and builder_add_structure_stylesheet functions.
		Updated builder_add_stylesheets to simply call the builder_add_stylesheets action.
	1.10.0 - 2013-02-14 - Chris Jean
		Removed the builder_custom_post_gallery function as it is now in lib/gallery-shortcode/functions.php.
		Updated builder_cached_function_value to use the ITUtility::get_cached_value function.
	1.10.1 - 2013-04-29 - Chris Jean
		Updated links to point to new lib/main location.
	1.11.0 - 2013-08-09 - Chris Jean
		Added builder_register_activation_hook().
		Added builder_handle_theme_activation().
	1.12.0 - 2013-08-12 - Chris Jean
		Moved builder_set_data_version() to lib/data/functions.php.
		Moved builder_get_data_version() to lib/data/functions.php.
	1.12.1 - 2013-10-21 - Chris Jean
		Updated builder_handle_theme_activation() to change its second argument to be optional as the theme activation hook can sometimes be called with just one argument.
*/


function builder_set_minimum_memory_limit( $new_memory_limit ) {
	$memory_limit = @ini_get( 'memory_limit' );
	
	if ( $memory_limit > -1 ) {
		$unit = strtolower( substr( $memory_limit, -1 ) );
		
		$new_unit = strtolower( substr( $new_memory_limit, -1 ) );
		
		if ( 'm' == $unit )
			$memory_limit *= 1048576;
		else if ( 'g' == $unit )
			$memory_limit *= 1073741824;
		else if ( 'k' == $unit )
			$memory_limit *= 1024;
		
		if ( 'm' == $new_unit )
			$new_memory_limit *= 1048576;
		else if ( 'g' == $new_unit )
			$new_memory_limit *= 1073741824;
		else if ( 'k' == $new_unit )
			$new_memory_limit *= 1024;
		
		if ( (int) $memory_limit < (int) $new_memory_limit )
			@ini_set( 'memory_limit', $new_memory_limit );
	}
}

function it_set_theme_menu_var( $menu_var ) {
	return $GLOBALS['theme_menu_var'];
}

function it_set_theme_index( $theme_index ) {
	return $GLOBALS['theme_index'];
}

function filter_it_tutorials_top_menu_icon( $icon ) {
	it_classes_load( 'it-file-utility.php' );
	
	return ITFileUtility::get_url_from_file( dirname( __FILE__ ) . '/images/builder-icon-16-inactive.png' );
}

function builder_add_global_admin_styles() {
	it_classes_load( 'it-file-utility.php' );
	
	wp_enqueue_style( 'builder-global-admin-style', ITFileUtility::get_url_from_file( dirname( __FILE__ ) . '/css/admin-global.css' ) );
}

function builder_set_start_here_url( $url ) {
	global $builder_start_here_url;
	
	if ( ! empty( $builder_start_here_url ) )
		$builder_start_here_url = esc_url( $builder_start_here_url );
	
	if ( empty( $builder_start_here_url ) )
		return $url;
	
	return $builder_start_here_url;
}

function builder_add_doctype() {
	if ( current_theme_supports( 'html5' ) )
		$doctype = '<!DOCTYPE html>';
	else
		$doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
	
	$doctype = apply_filters( 'builder_filter_doctype', $doctype );
	
	echo "$doctype\n";
}

function builder_add_html_tag() {
	ob_start();
	language_attributes();
	$language_attributes = ob_get_contents();
	ob_end_clean();
	
	if ( current_theme_supports( 'html5' ) )
		$html_tag = "<html %s $language_attributes>";
	else
		$html_tag = "<html %s $language_attributes xmlns=\"http://www.w3.org/1999/xhtml\">";
	
	$html_tag = apply_filters( 'builder_filter_html_tag', $html_tag );
	$html_tag .= "\n";
	
?>
<!--[if IE 6]>
	<?php printf( $html_tag, 'id="ie6"' ); ?>
<![endif]-->
<!--[if IE 7]>
	<?php printf( $html_tag, 'id="ie7"' ); ?>
<![endif]-->
<!--[if IE 8]>
	<?php printf( $html_tag, 'id="ie8"' ); ?>
<![endif]-->
<!--[if IE 9]>
	<?php printf( $html_tag, 'id="ie9"' ); ?>
<![endif]-->
<!--[if (gt IE 9) | (!IE)  ]><!-->
	<?php printf( $html_tag, '' ); ?>
<!--<![endif]-->
<?php
	
}

function builder_add_charset() {
	if ( current_theme_supports( 'html5' ) )
		$charset = '<meta charset="' . get_bloginfo( 'charset' ) . '" />';
	else
		$charset = '<meta http-equiv="Content-Type" content="' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) . '" />';
	
	$charset = apply_filters( 'builder_filter_charset', $charset );
	
	echo "$charset\n";
}

function builder_add_meta_data() {
	echo '<link rel="profile" href="http://gmpg.org/xfn/11" />' . "\n";
	echo '<link rel="pingback" href="' . get_bloginfo( 'pingback_url' ) . '" />' . "\n";
	
	do_action( 'builder_add_meta_data' );
}

function builder_add_stylesheets() {
	do_action( 'builder_add_stylesheets' );
}

function builder_add_theme_stylesheet() {
	if ( ! builder_disable_theme_stylesheets() )
		echo '<link rel="stylesheet" href="' . get_stylesheet_uri() . '" type="text/css" media="screen" />' . "\n";
}

function builder_add_reset_stylesheet() {
	echo '<link rel="stylesheet" href="' . builder_main_get_builder_core_url() . '/css/reset.css" type="text/css" media="screen" />' . "\n";
}

function builder_add_structure_stylesheet() {
	echo '<link rel="stylesheet" href="' . builder_main_get_builder_core_url() . '/css/structure.css?ver=2" type="text/css" media="screen" />' . "\n";
}

if ( ! function_exists( 'builder_add_scripts' ) ) {
	function builder_add_scripts() {
		// Add comment reply JavaScript if page is singular
		if ( is_singular() )
			wp_enqueue_script( 'comment-reply' );
		
?>
<!--[if lt IE 7]>
	<script src="<?php echo builder_main_get_builder_core_url(); ?>/js/dropdown.js" type="text/javascript"></script>
<![endif]-->
<!--[if lt IE 9]>
	<script src="<?php echo builder_main_get_builder_core_url(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
		
		do_action( 'builder_add_scripts' );
	}
}
else {
	require_once( dirname( dirname( __FILE__ ) ) . '/special-utilities/fix-builder_add_scripts-in-child-theme.php' );
}

function builder_add_favicon() {
	$favicon_url = apply_filters( 'builder_filter_favicon_url', '' );
	$favicon = '';
	
	if ( ! empty( $favicon_url ) ) {
		$favicon_url = ITUtility::fix_url( $favicon_url );
		$favicon = "<link rel=\"shortcut icon\" href=\"$favicon_url\" />";
	}
	
	apply_filters( 'builder_filter_favicon', $favicon );
	
	if ( ! empty( $favicon ) )
		echo "$favicon\n";
}

function builder_filter_favicon_url( $url ) {
	$favicon_option = builder_get_theme_setting( 'favicon_option' );
	
	if ( 'off' == $favicon_option )
		return $url;
	else if ( 'preset' == $favicon_option ) {
		$preset = builder_get_theme_setting( 'favicon_preset' );
		
		if ( ! empty( $preset ) )
			return builder_main_get_builder_core_url() . "/favicons/$preset.ico";
	}
	else if ( 'custom' == $favicon_option ) {
		$favicon = builder_get_theme_setting( 'favicon' );
		
		if ( is_array( $favicon ) && ! empty( $favicon['url'] ) )
			return $favicon['url'];
	}
	
	
	if ( file_exists( get_stylesheet_directory() . '/images/favicon.ico' ) )
		return get_stylesheet_directory_uri() . '/images/favicon.ico';
	else if ( file_exists( get_template_directory() . '/images/favicon.ico' ) )
		return get_template_directory_uri() . '/images/favicon.ico';
	else
		return builder_main_get_builder_core_url() . '/favicons/default.ico';
	
	return $url;
}

function builder_disable_theme_stylesheets() {
	return apply_filters( 'builder_filter_disable_theme_stylesheets', false );
}

function builder_enqueue_tooltip_script() {
	wp_enqueue_script( 'pluginbuddy-tooltip-js', ITFileUtility::url_from_file( dirname( __FILE__ ) . '/js/jquery.tooltip.js' ) );
}

function builder_filter_admin_body_classes( $classes = '' ) {
	global $wp_version;
	
	if ( version_compare( $wp_version, '3.2.0', '<' ) )
		$classes .= ' it-pre-wp-3-2';
	
	return $classes;
}

function builder_parent_is_active() {
	if ( builder_template_directory() == builder_stylesheet_directory() )
		return true;
	return false;
}

function builder_module_filter_css_prefix( $prefix ) {
	return 'builder-module';
}


// Get current taxonomy term title
function builder_get_tax_term_title() {
	if ( is_tax() ) {
		global $wp_query;
		
		$term = $wp_query->get_queried_object();
		return $term->name;
	}
	
	return '';
}


// Get current post's author link
function builder_get_author_link() {
	global $post;
	
	if ( isset( $post ) )
		return '<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" title="' . esc_attr( get_the_author() ) . '">' . get_the_author() . '</a>';
	return '';
}


// Load the not_found.php file
function builder_template_show_not_found() {
	locate_template( array( 'not_found.php' ), true );
}


// Customize image shortcode output
// Built from version 2.9.2
function builder_custom_caption_shortcode( $output, $attr, $content ) {
	$defaults = array(
		'id'      => '',
		'align'   => 'alignnone',
		'width'   => '',
		'caption' => '',
	);
	extract( shortcode_atts( $defaults, $attr ) );
	
	if ( 1 > (int) $width || empty( $caption ) )
		return $content;
	
	if ( ! empty( $id ) )
		$id = 'id="' . esc_attr( $id ) . '"';
	
	$align = esc_attr( $align );
	
	return "<div $id class='wp-caption $align' style='width:{$width}px;'>" . do_shortcode( $content ) . "<p class='wp-caption-text'>$caption</p></div>";
}

// Do smart comments_popup_link replacement
function builder_comments_popup_link( $before, $after, $format, $zero = '(0)', $one = '(1)', $multi = '(%)' ) {
	if ( ! builder_show_comments() || ( ! comments_open() && ! pings_open() ) || post_password_required() )
		return;
	
	ob_start();
	comments_popup_link( $zero, $one, $multi );
	$comments = ob_get_contents();
	ob_end_clean();
	
	echo $before;
	printf( $format, $comments );
	echo $after;
}

// Pass a module of * to register the style for all modules
function builder_register_module_style( $modules, $name, $selector ) {
	global $builder_module_styles;
	
	if ( ! is_array( $modules ) )
		$modules = array( $modules );
	if ( ! is_array( $builder_module_styles ) )
		$builder_module_styles = array();
	
	foreach ( (array) $modules as $module )
		$builder_module_styles[$module][$selector] = $name;
}

function builder_get_module_styles( $module = '' ) {
	global $builder_module_styles;
	
	if ( ! is_array( $builder_module_styles ) ) {
		$builder_module_styles = array();
		return array();
	}
	
	$styles = array();
	
	if ( is_array( $builder_module_styles['*'] ) )
		$styles = array_merge( $styles, $builder_module_styles['*'] );
	if ( ! empty( $module ) && is_array( $builder_module_styles[$module] ) )
		$styles = array_merge( $styles, $builder_module_styles[$module] );
	
	asort( $styles );
	
	return $styles;
}

function builder_get_custom_post_types() {
	global $builder_custom_post_types;
	
	if ( isset( $builder_custom_post_types ) )
		return $builder_custom_post_types;
	
	
	global $wp_version;
	
	$builder_custom_post_types = array();
	
	if ( version_compare( $wp_version, '2.9.7', '>' ) ) {
		$custom_post_type_objects = get_post_types( array( '_builtin' => false ), 'objects' );
		
		foreach ( (array) $custom_post_type_objects as $post_type => $settings )
			$builder_custom_post_types[$post_type] = $settings->labels->name;
	}
	else if ( version_compare( $wp_version, '2.8.7', '>' ) ) {
		$custom_post_type_objects = get_post_types();
		$core_post_types = array( 'post', 'page', 'revision', 'attachment' );
		
		foreach ( (array) $custom_post_type_objects as $post_type => $settings ) {
			if ( ! in_array( $post_type, $core_post_types ) )
				$builder_custom_post_types[$post_type] = $post_type;
		}
	}
	
	return $builder_custom_post_types;
}

function builder_get_custom_taxonomies() {
	global $builder_custom_taxonomies;
	
	if ( isset( $builder_custom_taxonomies ) )
		return $builder_custom_taxonomies;
	
	
	global $wp_version;
	
	$builder_custom_taxonomies = array();
	
	if ( version_compare( $wp_version, '2.9.7', '>' ) ) {
		$custom_taxonomy_objects = get_taxonomies( array( 'show_ui' => true, '_builtin' => false ), 'objects' );
		
		foreach ( (array) $custom_taxonomy_objects as $taxonomy => $settings )
			$builder_custom_taxonomies[$taxonomy] = $settings->labels->name;
	}
	
	return $builder_custom_taxonomies;
}

function builder_cached_function_value( $function ) {
	return ITUtility::get_cached_value( $function );
}

function builder_template_directory() {
	return builder_cached_function_value( 'get_template_directory' );
}

function builder_stylesheet_directory() {
	return builder_cached_function_value( 'get_stylesheet_directory' );
}

function builder_return_true() {
	return true;
}

function builder_main_get_builder_core_path() {
	if ( isset( $GLOBALS['builder_main_builder_core_path'] ) )
		return $GLOBALS['builder_main_builder_core_path'];
	
	$GLOBALS['builder_main_builder_core_path'] = dirname( dirname( dirname( __FILE__ ) ) );
	
	return $GLOBALS['builder_main_builder_core_path'];
}

function builder_main_get_builder_core_url() {
	if ( isset( $GLOBALS['builder_main_builder_core_url'] ) )
		return $GLOBALS['builder_main_builder_core_url'];
	
	$GLOBALS['builder_main_builder_core_url'] = ITUtility::get_url_from_file( builder_main_get_builder_core_path() );
	
	return $GLOBALS['builder_main_builder_core_url'];
}

function builder_register_activation_hook( $callback, $priority = 10 ) {
	add_action( 'after_switch_theme', $callback, $priority, 2 );
}

function builder_handle_theme_activation( $old_theme_name, $old_theme = false ) {
	if ( false != get_option( 'builder_manually_switched_theme' ) ) {
		delete_option( 'builder_manually_switched_theme' );
		return;
	}
	
	
	if ( false !== $old_theme )
		$GLOBALS['builder_old_theme'] = $old_theme;
	
	
//	$switch_to_setup_screen = false;
	
	$default_layouts = builder_get_theme_setting( 'activation_default_layouts' );
	$child_theme_setup = builder_get_theme_setting( 'activation_child_theme_setup' );
	
	if ( get_template_directory() != get_stylesheet_directory() )
		$child_theme_setup = 'ignore';
	
	
//	if ( ( 'ignore' != $default_layouts ) && ( 'ignore' != $child_theme_setup ) )
//		$switch_to_setup_screen = true;
	
	
//	if ( 'use' == $default_layouts )
//		add_filter( 'it_storage_filter_load_layout_settings', 'builder_import_theme_default_layouts_and_views', 20 );
	
	
	if ( 'create' == $child_theme_setup ) {
		require_once( builder_main_get_builder_core_path() . '/lib/setup/init.php' );
		
		add_action( 'wp_loaded', 'builder_create_child_theme' );
	}
	
	
//	if ( $switch_to_setup_screen ) {
	if ( current_theme_supports( 'builder-my-theme-menu' ) ) {
		add_action( 'admin_head', 'builder_show_setup_tab_notice' );
		
		wp_redirect( admin_url( 'admin.php?page=theme-settings&editor_tab=setup&theme_activation=1' ) );
	}
//	}
}

function builder_show_setup_tab_notice() {
	ITUtility::show_status_message( sprintf( __( 'Go to Builder\'s <a href="%s">Setup page</a> to finish setting up the Builder theme.', 'it-l10n-Builder-Madison' ), admin_url( 'admin.php?page=theme-settings&editor_tab=setup&theme_activation=1' ) ) );
}

function builder_set_data_version( $name, $version ) { 
	global $builder_data_versions;
	
	if ( ! isset( $builder_data_versions ) ) 
		$builder_data_versions = array();
	
	$builder_data_versions[$name] = $version;
}

function builder_get_data_version( $name ) { 
	global $builder_data_versions;
	
	return ( isset( $builder_data_versions[$name] ) ) ? $builder_data_versions[$name] : false;
}
