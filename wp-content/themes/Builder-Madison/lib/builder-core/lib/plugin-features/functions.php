<?php

/*
API for Plugin Features
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-12-05 - Chris Jean
		Rewritten from simple functions
*/


if ( ! class_exists( 'BuilderPluginFeatures' ) ) {
	class BuilderPluginFeatures {
		var $_script_queue = array();
		var $_style_queue  = array();
		
		function BuilderPluginFeatures( $plugin_features ) {
			$plugin_features = apply_filters( 'builder_filter_plugin_features', $plugin_features );
			
			$enabled_plugin_features = $this->_get_enabled_plugin_features( $plugin_features );
			
			if ( empty( $enabled_plugin_features ) )
				return;
			
			foreach ( $enabled_plugin_features as $plugin_feature ) {
				if ( isset( $plugin_features[$plugin_feature] ) && $plugin_features[$plugin_feature] )
					$this->_setup_plugin_feature( $plugin_feature );
			}
		}
		
		function _get_enabled_plugin_features( $plugin_features ) {
			$enabled_plugin_features = apply_filters( 'builder_filter_enabled_plugin_features', array_keys( $plugin_features ) );
			
			if ( ! is_array( $enabled_plugin_features ) )
				return array();
			
			return $enabled_plugin_features;
		}
		
		function _setup_plugin_feature( $plugin_feature ) {
			$dir = "plugin-features/$plugin_feature";
			
			$template = locate_template( "$dir/init.php", true );
			
			if ( empty( $template ) && file_exists( builder_main_get_builder_core_path() . "/$dir/init.php" ) )
				require_once( builder_main_get_builder_core_path() . "/$dir/init.php" );
			
			
			if ( ! is_admin() ) {
				it_classes_load( 'it-file-utility.php' );
				
				$file = locate_template( "$dir/style.css" );
				if ( ! empty( $file ) ) {
					$url = ITFileUtility::get_url_from_file( $file );
					$this->_style_queue[] = array( $plugin_feature, $url );
				}
				
				$file = locate_template( "$dir/script.js" );
				if ( ! empty( $file ) ) {
					$url = ITFileUtility::get_url_from_file( $file );
					$this->_script_queue[] = array( $plugin_feature, $url );
				}
			}
			
			if ( ! empty( $this->_style_queue ) || ! empty( $this->_script_queue ) )
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_files' ) );
		}
		
		function enqueue_files() {
			foreach ( $this->_style_queue as $style )
				wp_enqueue_style( "builder-plugin-feature-{$style[0]}-style", $style[1] );
			
			foreach ( $this->_script_queue as $script )
				wp_enqueue_script( "builder-plugin-feature-{$script[0]}-script", $script[1] );
		}
	}
}
