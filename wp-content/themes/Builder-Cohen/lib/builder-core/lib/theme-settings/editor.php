<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.2

Version History
	1.1.0 - 2011-12-09 - Chris Jean
		Added set_help_sidebar() function
	1.1.1 - 2012-08-22 - Chris Jean
		Removed call to tb_init as Thickbox is no longer used.
	1.1.2 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! class_exists( 'ITThemeSettings' ) ) {
	class ITThemeSettings extends ITCoreClass {
		var $_var = 'builder-theme-settings';
		var $_page_var = 'theme-settings';
		var $_menu_priority = '-5';
		var $_it_storage_version = '2';
		
		var $_closed_meta_boxes = array();
		
		var $_editor_tabs = array();
		var $_tab_order = array();
		
		
		function ITThemeSettings() {
			$this->_storage_version = builder_get_data_version( 'theme-settings' );
			
			$this->_page_title = _x( 'Theme Settings', 'page title', 'it-l10n-Builder-Cohen' );
			$this->_menu_title = _x( 'Settings', 'menu title', 'it-l10n-Builder-Cohen' );
			
			$this->ITCoreClass();
			
			$this->_file = __FILE__;
		}
		
		function active_init() {
			global $builder_settings_tabs;
			
			$tab_order = array_keys( $builder_settings_tabs );
			
			$this->_editor_tabs = apply_filters( 'builder_filter_theme_settings_tabs', $builder_settings_tabs );
			$this->_tab_order = apply_filters( 'builder_filter_theme_settings_tab_order', $tab_order );
			
			
			ksort( $this->_tab_order );
			$this->_active_tab = ( ! empty( $_REQUEST['editor_tab'] ) ) ? $_REQUEST['editor_tab'] : reset( $this->_tab_order );
			
			if ( ! isset( $this->_editor_tabs[$this->_active_tab] ) )
				ITError::fatal( 'The active tab for the Theme Settings page does not exist in the theme_settings_tabs array. This indicates that some custom code added a tab using the builder_filter_theme_settings_tab_order filter but did not update the registered tabs using the builder_filter_theme_settings_tabs filter.' );
			
			
			require_once( dirname( __FILE__ ) . '/class.settings-tab.php' );
			
			foreach ( (array) $this->_editor_tabs as $id => $tab ) {
				if ( is_null( $tab['class'] ) )
					continue;
				
				if ( ! empty( $tab['file'] ) && is_file( $tab['file'] ) )
					require_once( $tab['file'] );
				
				if ( ! class_exists( $tab['class'] ) )
					ITError::fatal( "The {$this->_editor_tabs[$this->_active_tab]['class']} class should have been registered. However, the class was not found." );
				
				$object = new $tab['class']( $this );
				
				if ( $id == $this->_active_tab )
					$this->_tab_object = $object;
			}
			
			$this->_tabless_self_link = $this->_self_link;
			$this->_self_link .= "&editor_tab={$this->_active_tab}";
			
			
			if ( method_exists( $this->_tab_object, 'init' ) )
				$this->_tab_object->init();
			if ( isset( $_POST['save'] ) && method_exists( $this->_tab_object, 'save_handler' ) )
				$this->_tab_object->save_handler();
			if ( isset( $_POST['reset'] ) && method_exists( $this->_tab_object, 'reset_handler' ) )
				$this->_tab_object->reset_handler();
		}
		
		function contextual_help( $text, $screen ) {
			if ( is_callable( array( $this->_tab_object, 'contextual_help' ) ) )
				return $this->_tab_object->contextual_help( $text, $screen );
			
			return $text;
		}
		
		function set_help_sidebar() {
			builder_set_help_sidebar();
		}
		
		function screen_settings( $settings, $screen ) {
			if ( is_callable( array( $this->_tab_object, 'screen_settings' ) ) )
				return $this->_tab_object->screen_settings( $settings, $screen );
			
			return $settings;
		}
		
		function add_admin_scripts() {
			ITCoreClass::add_admin_scripts();
			
			wp_enqueue_script( 'postbox' );
			
			if ( is_callable( array( $this->_tab_object, 'add_admin_scripts' ) ) )
				return $this->_tab_object->add_admin_scripts();
		}
		
		function add_admin_styles() {
			ITCoreClass::add_admin_styles();
			
			wp_enqueue_style( "{$this->_var}-theme-settings", "{$this->_plugin_url}/css/editor.css" );
			
			if ( is_callable( array( $this->_tab_object, 'add_admin_styles' ) ) )
				return $this->_tab_object->add_admin_styles();
		}
		
		function _reset_data() {
			$this->_storage->reset();
			$this->_options = $this->_storage->load();
			$GLOBALS['wp_theme_options'] = $this->_options;
			
			ITUtility::show_status_message( __( 'Data reset', 'it-l10n-Builder-Cohen' ) );
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			ITCoreClass::index();
			
			if ( ! empty( $_REQUEST['reset_data'] ) )
				$this->_reset_data();
			
			$this->_tab_object->index();
		}
	}
	
	new ITThemeSettings();
}
