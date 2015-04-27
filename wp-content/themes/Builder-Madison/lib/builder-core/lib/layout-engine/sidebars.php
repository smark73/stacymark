<?php

/*
Written by Chris Jean for iThemes.com
Version 2.6.0

Version History
	2.5.0 - 2012-08-06 - Chris Jean
		Added Layout name output on the widgets.php page when the Layout's for a specific layout are being shown.
		Added check to ensure that a module's register_sidebars function is callable before calling it.
		Fixed localization domain problem. Changed it to LION.
	2.5.1 - 2012-08-08 - Chris Jean
		Added the clearfix class to widgets. This fixes issues with widgets that float their output.
	2.5.2 - 2012-12-03 - Chris Jean
		Added fix for WP 3.5 issue where Appearance > Widgets does not appear due to not having any registered sidebars.
	2.5.3 - 2012-12-03 - Chris Jean
		Fixed not hiding the Unused Sidebar.
	2.5.4 - 2012-12-06 - Chris Jean
		Minor fix to remove a notice.
	2.5.5 - 2013-01-16 - Chris Jean
		Added a fallback to ensure that the _layout_settings properly load. Without this, if the Layout Editor does not
			load, due to error or configuration, then no sidebars will be registered on the Widgets page.
		Removed the if_class_exists check.
	2.5.6 - 2013-02-06 - Chris Jean
		Cleaned up tabbing issues.
	2.5.7 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	2.6.0 - 2013-08-09 - Chris Jean
		Changed priority of the init action in order to allow after_switch_theme to fire first.
*/


class BuilderSidebars {
	var $_modules = array();
	var $_layout_settings = array();
	var $_registered_layouts = array();
	var $_sidebar_sanitized_names = array();
	var $_registered_sidebars = array();
	
	var $_filler_widget_id = 0;
	
	
	function BuilderSidebars() {
		// Only run when a form hasn't been submitted or if the form being submitted is part of a widget save process
		if ( empty( $_POST ) || isset( $_POST['savewidget'] ) || isset( $_POST['removewidget'] ) || isset( $_POST['widget_logic-options-submit'] ) )
			add_action( 'sidebar_admin_setup', array( $this, 'admin_register_all_sidebars' ) );
		
		// This is needed to prevent widgets from moving to the Inactive Sidebar areas on theme switch
		add_action( 'init', array( $this, 'queue_admin_register_all_sidebars' ), 1000 );
		
		// The priority of this hook must be high in order to allow after_switch_theme to fire first.
		add_action( 'init', array( $this, 'init' ), 1000 );
		
		if ( ! empty( $_REQUEST['builder_layout_id'] ) )
			add_action( 'sidebar_admin_setup', array( $this, 'init_modify_widgets_editor_title' ), 100 );
		
		add_action( 'builder_sidebar_register_layout_sidebars', array( $this, 'register_layout_sidebars' ) );
		add_action( 'builder_sidebar_register', array( $this, 'register_sidebar' ) );
		add_action( 'builder_sidebar_render', array( $this, 'render_sidebar' ) );
		
		add_filter( 'pre_update_option_sidebars_widgets', array( $this, 'filter_sidebars_widgets' ) );
		
		add_filter( 'dynamic_sidebar_params', array( $this, 'filter_widget_params' ) );
	}
	
	function init() {
		$this->_modules = apply_filters( 'builder_get_modules', array() );
		$this->_layout_settings = apply_filters( 'it_storage_load_layout_settings', array() );
		
		if ( empty( $this->_layout_settings ) )
			$this->_force_load_layout_settings();
		
		// This is needed to ensure that the Appearance > Widgets menu entry is added due to a change in WordPress 3.5.
		register_sidebar( array( 'id' => '__builder_temp_sidebar', 'name' => 'Unused Sidebar', 'description' => 'This sidebar should not appear on any site. It is only here to fix a problem with having the Widgets menu entry appear in WordPress 3.5. It is not used anywhere in Builder.' ) );
	}
	
	function queue_admin_register_all_sidebars() {
		add_action( 'sidebar_admin_setup', array( $this, 'admin_register_all_sidebars' ) );
	}
	
	function _force_load_layout_settings() {
		$storage_version = builder_get_data_version( 'layout-settings' );
		$storage = new ITStorage( 'layout_settings', $storage_version );
		
		$this->_layout_settings = apply_filters( 'it_storage_load_layout_settings', array() );
	}
	
	function init_modify_widgets_editor_title() {
		add_filter( 'gettext', array( $this, 'filter_widgets_editor_title' ), 100, 3 );
	}
	
	function filter_widgets_editor_title( $translated, $text, $domain ) {
		if ( ( 'Widgets' != $text ) || ( 'default' != $domain ) )
			return $translated;
		
		remove_filter( 'gettext', array( $this, 'filter_widgets_editor_title' ), 100 );
		
		
		$storage = new ITStorage( 'layout_settings' );
		$layout_settings = $storage->load();
		
		if ( ! isset( $layout_settings['layouts'][$_REQUEST['builder_layout_id']] ) )
			return $translated;
		
		return "$translated - {$layout_settings['layouts'][$_REQUEST['builder_layout_id']]['description']}";
	}
	
	function filter_sidebars_widgets( $data ) {
		if ( ! defined( 'DOING_AJAX' ) || ( true !== DOING_AJAX ) || ! isset( $_REQUEST['action'] ) || ( 'widgets-order' !== $_REQUEST['action'] ) )
			return $data;
		
		$widgets = get_option( 'sidebars_widgets' );
		
		foreach ( (array) $data as $id => $entry )
			$widgets[$id] = $entry;
		
		return $widgets;
	}
	
	function admin_register_all_sidebars() {
		global $wp_registered_sidebars;
		
		foreach ( (array) $this->_layout_settings['layouts'] as $layout => $layout_data ) {
			if ( ! empty( $_REQUEST['builder_layout_id'] ) ) {
				if ( $layout == $_REQUEST['builder_layout_id'] )
					$this->register_layout_sidebars( $layout );
			}
			else if ( ! isset( $layout_data['hide_widgets'] ) || ( 'yes' !== $layout_data['hide_widgets'] ) )
				$this->register_layout_sidebars( $layout );
		}
		
		$cached_sidebars = $wp_registered_sidebars;
		
		foreach ( (array) $this->_layout_settings['layouts'] as $layout => $layout_data ) {
			if ( ! empty( $_REQUEST['builder_layout_id'] ) ) {
				if ( $layout != $_REQUEST['builder_layout_id'] )
					$this->register_layout_sidebars( $layout );
			}
			else if ( isset( $layout_data['hide_widgets'] ) && ( 'yes' === $layout_data['hide_widgets'] ) )
				$this->register_layout_sidebars( $layout );
		}
		
		$this->_hidden_sidebars = array();
		
		foreach ( (array) $wp_registered_sidebars as $id => $sidebar ) {
			if ( ! isset( $cached_sidebars[$id] ) )
				$this->_hidden_sidebars[] = $id;
		}
		
		add_action( 'admin_head', array( $this, 'admin_unregister_hidden_sidebars' ) );
	}
	
	function admin_add_unregister_hidden_sidebars() {
		add_action( 'admin_head', array( $this, 'admin_unregister_hidden_sidebars' ) );
	}
	
	function admin_unregister_hidden_sidebars() {
		foreach ( (array) $this->_hidden_sidebars as $id )
			unregister_sidebar( $id );
		
		// Unregister temp sidebar for WP 3.5 fix.
		unregister_sidebar( '__builder_temp_sidebar' );
	}
	
	function register_layout_sidebars( $layout ) {
		if ( ! empty( $this->_registered_layouts[$layout] ) )
			return;
		
		if ( isset( $this->_layout_settings['layouts'][$layout] ) ) {
			foreach ( (array) $this->_layout_settings['layouts'][$layout]['modules'] as $id => $module ) {
				if ( isset( $this->_modules[$module['module']] ) && method_exists( $this->_modules[$module['module']], 'register_sidebars' ) )
					$this->_modules[$module['module']]->register_sidebars( $module, $id, $this->_layout_settings['layouts'][$layout]['description'], $layout );
			}
			
			$this->_registered_layouts[$layout] = true;
		}
	}
	
	function register_sidebar( $options ) {
		$default_options = array(
			'before_widget' => '<div class="widget-background-wrapper" id="%1$s-background-wrapper"><div class="widget %2$s" id="%1$s">',
			'after_widget'  => '</div></div>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>'
		);
		
		if ( ! is_array( $options ) )
			$options = array( 'name' => $options );
		
		$options = array_merge( $default_options, $options );
		
		if ( empty( $options['id'] ) )
			$options['id'] = str_replace( ' ', '', preg_replace( '/(\w)\s(\w)/', '$1_$2', strtolower( $options['name'] ) ) );
		
		$options = apply_filters( 'builder_filter_register_sidebar_options', $options );
		
		
		$options['short_name'] = $options['name'];
		
		if ( ! empty( $options['layout'] ) )
			$options['full_name'] = "{$options['layout']} - {$options['name']}";
		else
			$options['full_name'] = $options['name'];
		
		if ( ! is_admin() || empty( $_REQUEST['builder_layout_id'] ) || ( isset( $options['layout_id'] ) && ( $_REQUEST['builder_layout_id'] != $options['layout_id'] ) ) || ! preg_match( '/\/widgets\.php$/', $_SERVER['SCRIPT_NAME'] ) )
			$options['name'] = $options['full_name'];
		
		
		$id = register_sidebar( $options );
		
		$this->_sidebar_names[$options['full_name']] = $id;
		
		$this->_registered_sidebars[] = $options;
	}
	
	function render_sidebar( $options ) {
		global $wp_registered_sidebars;
		
		$this->_widget_count = 1;
		
		if ( ! isset( $this->_widgets ) )
			$this->_widgets = wp_get_sidebars_widgets();
		
		
		if ( ! empty( $options['sidebar_id'] ) ) {
			$this->_sidebar_index = $options['sidebar_id'];
		}
		else {
			$this->_sidebar_index = $options['sidebar_name'];
			
			if ( is_int( $this->_sidebar_index ) )
				$this->_sidebar_index = "sidebar-{$this->_sidebar_index}";
			else
				$this->_sidebar_index = $this->_get_registered_sidebar_index( $this->_sidebar_index );
		}
		
		if ( isset( $this->_widgets[$this->_sidebar_index] ) )
			$this->_num_widgets = count( $this->_widgets[$this->_sidebar_index] );
		else
			$this->_num_widgets = 0;
		
		
		if ( ! empty( $options['sidebar_id'] ) ) {
			if ( ! empty( $wp_registered_sidebars[$options['sidebar_id']] ) && ! empty( $wp_registered_sidebars[$options['sidebar_id']]['full_name'] ) )
				$options['sidebar_name'] = $wp_registered_sidebars[$options['sidebar_id']]['full_name'];
			
			$this->dynamic_sidebar( $options['sidebar_id'], $options['sidebar_name'] );
		}
		else
			$this->dynamic_sidebar( $options['sidebar_name'], $options['sidebar_name'] );
		
		
		unset( $this->_widget_count );
		unset( $this->_current_sidebar );
	}
	
	function filter_widget_params( $params ) {
		if ( isset( $this->_widget_count ) ) {
			$classes = array( "widget-$this->_widget_count" );
			
			if ( $this->_num_widgets <= 1 )
				$classes[] = 'widget-single';
			else if ( 1 === $this->_widget_count )
				$classes[] = 'widget-top';
			else if ( $this->_num_widgets === $this->_widget_count )
				$classes[] = 'widget-bottom';
			else
				$classes[] = 'widget-middle';
			
			$classes[] = 'clearfix';
			
			$class = implode( ' ', $classes );
			
			/*
				Should this really be hardcoded to the 0 index?
				wp-includes/widgets.php dynamic_sidebar function hardcodes to 0, but is that a mistake too?
			*/
			$params[0]['before_widget'] = preg_replace( '/(class="widget) /', "$1 $class ", $params[0]['before_widget'] );
			
			$this->_widget_count++;
		}
		
		return $params;
	}
	
	function dynamic_sidebar( $index, $name = null ) {
		global $builder_current_sidebar;
		
		
		if ( is_null( $name ) )
			$name = $index;
		
		
		if ( ! is_array( $builder_current_sidebar ) )
			$builder_current_sidebar = array();
		
		if ( isset( $this->_sidebar_names[$index] ) )
			$builder_current_sidebar[] = $this->_sidebar_names[$index];
		else
			$builder_current_sidebar[] = $index;
		
		$sidebar_index = $this->_get_registered_sidebar_index( $index );
		
		if ( empty( $sidebar_index ) ) {
			if ( current_user_can( 'switch_themes' ) )
				printf( __( 'A problem happened with the widget area registration. This sidebar has an index of <code>%s</code> but it was unable to be found in the registered sidebars.', 'it-l10n-Builder-Madison' ), $index );
		}
		else if ( builder_identify_widget_area( dynamic_sidebar( $sidebar_index ) ) ) {
			$instance = array(
				'title'  => __( 'This is a Widget Section', 'it-l10n-Builder-Madison' ),
				'text'   => sprintf( __( 'This section is widgetized. If you would like to add content to this section, you may do so by using the Widgets panel from within your WordPress Admin Dashboard. This Widget Section is called "<strong>%s</strong>"', 'it-l10n-Builder-Madison' ), $name ),
				'filter' => 'on',
			);
			
			
			$sidebar = $this->_get_registered_sidebar( $sidebar_index );
			
			$widget_id = 'builder_info_widget_' . $this->_filler_widget_id++;
			
			$params = array( array_merge( $sidebar, array( 'widget_id' => $widget_id, 'widget_name' => 'text' ) ) );
			
			// Substitute HTML id and class attributes into before_widget
			$params[0]['before_widget'] = sprintf( $params[0]['before_widget'], $widget_id, 'widget_text' );
			
			$params = apply_filters( 'dynamic_sidebar_params', $params );
			
			
			$args = apply_filters( 'builder_filter_sidebar_default_widget_args', array( 'WP_Widget_Text', $instance, $params[0] ) );
			call_user_func_array( 'the_widget', $args );
			
			if ( defined( 'BUILDER_DEBUG_ADD_EXTRA_PLACEHOLDER_WIDGETS' ) && BUILDER_DEBUG_ADD_EXTRA_PLACEHOLDER_WIDGETS ) {
				for ( $count = 0; $count < intval( BUILDER_DEBUG_ADD_EXTRA_PLACEHOLDER_WIDGETS ); $count++ )
					call_user_func_array( 'the_widget', $args );
			}
		}
		
		array_pop( $builder_current_sidebar );
	}
	
	function _get_registered_sidebar_index( $name ) {
		global $wp_registered_sidebars;
		
		if ( isset( $wp_registered_sidebars[$name] ) )
			return $name;
		if ( isset( $this->_sidebar_names[$name] ) && isset( $wp_registered_sidebars[$this->_sidebar_names[$name]] ) )
			return $this->_sidebar_names[$name];
		
		
		// Fallback for non-theme sidebars
		
		$name = sanitize_title( $name );
		
		if ( isset( $this->_sidebar_sanitized_names[$name] ) && isset( $wp_registered_sidebars[$this->_sidebar_sanitized_names[$name]] ) )
			return $wp_registered_sidebars[$this->_sidebar_sanitized_names[$name]];
		
		foreach ( (array) $wp_registered_sidebars as $key => $value )
			$this->_sidebar_sanitized_names[sanitize_title( $value['name'] )] = $key;
		
		if ( isset( $this->_sidebar_sanitized_names[$name] ) && isset( $wp_registered_sidebars[$this->_sidebar_sanitized_names[$name]] ) )
			return $this->_sidebar_sanitized_names[$name];
		
		return '';
	}
	
	function _get_registered_sidebar( $name ) {
		global $wp_registered_sidebars;
		
		$index = $this->_get_registered_sidebar_index( $name );
		
		if ( isset( $wp_registered_sidebars[$index] ) )
			return $wp_registered_sidebars[$index];
		
		return array();
	}
}

new BuilderSidebars();
