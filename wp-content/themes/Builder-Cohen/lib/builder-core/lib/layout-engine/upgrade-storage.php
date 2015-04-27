<?php

/*
Written by Chris Jean for iThemes.com
Version 2.5.1

Version History
	1.0.0 - 2009-12-07
		Release ready
	1.0.1 - 2009-12-17
		Removed uniqid warning caused by PHP 4
	2.0.0 - 2010-01-07
		Added upgrade path for 1.2
	2.1.0 - 2010-01-15
		Added upgrade path for 1.3
	2.1.1 - 2010-12-14
		Added checks to prevent warnings
	2.2.0 - 2011-06-30 - Chris Jean
		Added upgrade path for 1.4
	2.3.0 - 2011-07-01 - Chris Jean
		Added upgrade path for 1.5
	2.4.0 - 2011-07-05 - Chris Jean
		Added upgrade path for 1.6
		Removed unneeded $current_version variable
	2.5.1 - 2013-10-21 - Chris Jean
		Added check to ensure that the storage_version variable is set in order to avoid warnings.
*/

if ( ! class_exists( 'BuilderLayoutStorageUpgrade' ) ) {
	class BuilderLayoutStorageUpgrade {
		var $_var = 'layout_settings';
		
		
		function BuilderLayoutStorageUpgrade() {
			add_filter( "it_storage_upgrade_{$this->_var}", array( $this, 'do_upgrade' ) );
		}
		
		function do_upgrade( $upgrade_data ) {
			$data = $upgrade_data['data'];
			
			if ( ! isset( $data['storage_version'] ) )
				$data['storage_version'] = '';
			
			$data = $this->_upgrade_to_1_1( $data );
			$data = $this->_upgrade_to_1_2( $data );
			$data = $this->_upgrade_to_1_3( $data );
			$data = $this->_upgrade_to_1_4( $data );
			$data = $this->_upgrade_to_1_5( $data );
			$data = $this->_upgrade_to_1_6( $data );
			
			$upgrade_data['data'] = $data;
			
			return $upgrade_data;
		}
		
		function _upgrade_to_1_1( $data ) {
			if ( ! empty( $data['layouts'] ) ) {
				foreach ( (array) $data['layouts'] as $id => $layout ) {
					foreach ( (array) $layout['modules'] as $module_id => $module )
						if ( empty( $module['guid'] ) )
							$data['layouts'][$id]['modules'][$module_id]['guid'] = uniqid( '' );
				}
				
				foreach ( (array) $data['layouts'] as $id => $layout ) {
					if ( empty( $layout['guid'] ) ) {
						global $wpdb;
						
						
						$new_id = uniqid( '' );
						
						$layout['guid'] = $new_id;
						
						unset( $data['layouts'][$id] );
						$data['layouts'][$new_id] = $layout;
						
						if ( $id === $data['default'] )
							$data['default'] = $new_id;
						
						foreach ( (array) $data['views'] as $view_id => $layout_id ) {
							if ( $id === $layout_id )
								$data['views'][$view_id] = $new_id;
						}
						
						
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value='$new_id' WHERE meta_key='_custom_layout' AND meta_value=%s", $id ) );
					}
				}
			}
			
			if ( version_compare( $data['storage_version'], '1.1', '<' ) )
				$data['storage_version'] = '1.1';
			
			return $data;
		}
		
		function _upgrade_to_1_2( $data ) {
			if ( ! empty( $data['layouts'] ) ) {
				foreach ( (array) $data['layouts'] as $id => $layout ) {
					if ( isset( $layout['layout_style'] ) ) {
						$data['layouts'][$id]['extension'] = $layout['layout_style'];
						unset( $data['layouts'][$id]['layout_style'] );
					}
				}
			}
			
			if ( version_compare( $data['storage_version'], '1.2', '<' ) )
				$data['storage_version'] = '1.2';
			
			return $data;
		}
		
		function _upgrade_to_1_3( $data ) {
			$replace_views = array(
				'is_home||is_front_page'	=> 'builder_is_home',
				'is_singular'				=> 'builder_is_singular',
				'is_page'					=> 'builder_is_page',
			);
			
			if ( ! empty( $data['views'] ) ) {
				foreach ( (array) $data['views'] as $view => $layout ) {
					if ( isset( $replace_views[$view] ) ) {
						unset( $data['views'][$view] );
						$data['views'][$replace_views[$view]] = $layout;
					}
				}
			}
			
			if ( version_compare( $data['storage_version'], '1.3', '<' ) )
				$data['storage_version'] = '1.3';
			
			return $data;
		}
		
		function _upgrade_to_1_4( $data ) {
			foreach ( (array) $data['views'] as $id => $layout ) {
				if ( is_string( $layout ) )
					$data['views'][$id] = array( 'layout' => $layout );
				else if ( ! is_array( $layout ) )
					unset( $data['views'][$id] );
			}
			
			if ( version_compare( $data['storage_version'], '1.4', '<' ) )
				$data['storage_version'] = '1.4';
			
			return $data;
		}
		
		function _upgrade_to_1_5( $data ) {
			foreach ( (array) $data['layouts'] as $guid => $layout ) {
				if ( ( (string) intval( $layout['width'] ) != (string) $layout['width'] ) && ! empty( $layout['custom_width'] ) ) {
					$layout['width'] = $layout['custom_width'];
				}
				else if ( (string) intval( $layout['width'] ) != (string) $layout['width'] ) {
					$layout['width'] = '960';
				}
				
				unset( $layout['custom_width'] );
				
				$data['layouts'][$guid] = $layout;
			}
			
			if ( version_compare( $data['storage_version'], '1.5', '<' ) )
				$data['storage_version'] = '1.5';
			
			return $data;
		}
		
		function _upgrade_to_1_6( $data ) {
			foreach ( (array) $data['layouts'] as $guid => $layout ) {
				foreach ( (array) $layout['modules'] as $index => $module ) {
					if ( 'image' != $module['module'] )
						continue;
					
					if ( ! isset( $module['data']['height_type'] ) )
						$data['layouts'][$guid]['modules'][$index]['data']['height_type'] = 'custom';
				}
			}
			
			if ( version_compare( $data['storage_version'], '1.6', '<' ) )
				$data['storage_version'] = '1.6';
			
			return $data;
		}
	}
	
	new BuilderLayoutStorageUpgrade();
}
