<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2010-12-15 - Chris Jean
		Release ready
*/


if ( ! class_exists( 'ITThemeSettingsStorageUpgrade' ) ) {
	class ITThemeSettingsStorageUpgrade {
		var $_var = 'builder-theme-settings';
		
		
		function ITThemeSettingsStorageUpgrade() {
			add_filter( "it_storage_upgrade_{$this->_var}", array( $this, 'do_upgrade' ) );
		}
		
		function do_upgrade( $upgrade_data ) {
			$data = $upgrade_data['data'];
			$current_version = $upgrade_data['current_version'];
			
			if ( version_compare( $data['storage_version'], '1.0', '<' ) )
				$data = $this->_upgrade_to_1_0( $data, $current_version );
			
			$upgrade_data['data'] = $data;
			
			return $upgrade_data;
		}
		
		function _upgrade_to_1_0( $data, $current_version ) {
			$old_settings = get_option( 'builder-options' );
			
			if ( is_array( $old_settings ) ) {
				if ( isset( $old_settings['include_pages'] ) && is_array( $old_settings['include_pages'] ) ) {
					if ( ! isset( $data['include_pages'] ) || ! is_array( $data['include_pages'] ) )
						$data['include_pages'] = array();
					$data['include_pages'] = array_unique( array_merge( $data['include_pages'], $old_settings['include_pages'] ) );
				}
				if ( isset( $old_settings['include_cats'] ) && is_array( $old_settings['include_cats'] ) ) {
					if ( ! isset( $data['include_cats'] ) || ! is_array( $data['include_cats'] ) )
						$data['include_cats'] = array();
					$data['include_cats'] = array_unique( array_merge( $data['include_cats'], $old_settings['include_cats'] ) );
				}
				
				if ( ! empty( $old_settings['identify_widget_areas'] ) && ( 'no' === $old_settings['identify_widget_areas'] ) )
					$data['identify_widget_areas_method'] = 'never';
				
				if ( ! empty( $old_settings['tracking'] ) ) {
					if ( ! empty( $old_settings['tracking_pos'] ) && ( 'header' === $old_settings['tracking_pos'] ) )
						$data['javascript_code_header'] = $old_settings['tracking'];
					else
						$data['javascript_code_footer'] = $old_settings['tracking'];
				}
				
				if ( ! empty( $old_settings['tag_as_keyword'] ) && ( 'no' === $old_settings['tag_as_keyword'] ) )
					$data['tag_as_keyword'] = 'no';
				
				if ( ! empty( $old_settings['cat_index'] ) && ( 'yes' === $old_settings['cat_index'] ) )
					$data['cat_index'] = 'yes';
			}
			
			@delete_option( 'builder-options' );
			
			$data['storage_version'] = '1.0';
			
			return $data;
		}
	}
	
	new ITThemeSettingsStorageUpgrade();
}
