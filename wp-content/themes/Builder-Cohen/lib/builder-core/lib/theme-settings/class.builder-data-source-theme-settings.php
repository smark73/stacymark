<?php

/*
Interface class for all import export data source classes

Written by Chris Jean for iThemes.com
Version 1.1.0

Version History
	1.0.0 - 2010-12-20 - Chris Jean
		Initial version
	1.0.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.1.0 - 2013-08-15 - Chris Jean
		Added $return_data argument and relevant code to run_import().
*/


if ( ! class_exists( 'BuilderDataSourceThemeSettings' ) ) {
	class BuilderDataSourceThemeSettings extends BuilderDataSource {
		function get_name() {
			return 'Theme Settings';
		}
		
		function get_var() {
			return 'theme-settings';
		}
		
		function get_version() {
			return builder_get_data_version( 'theme-settings' );
		}
		
		function get_export_data() {
			$storage = new ITStorage2( 'builder-theme-settings', $this->get_version() );
			$settings = $storage->load();
			
			return $settings;
		}
		
		function run_import( $info, $data, $post_data, $attachments, $return_data, $view_settings ) {
			$storage = new ITStorage2( 'builder-theme-settings', $this->get_version() );
			$settings = $storage->load();
			
			$storage->save( $data );
			
			if ( $return_data )
				return $data;
			
			return true;
		}
	}
}
