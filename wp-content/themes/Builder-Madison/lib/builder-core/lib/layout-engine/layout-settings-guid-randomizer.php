<?php

/*
When new Layout Settings are loaded, the GUIDs for Layouts and Modules should be randomized. This class' randomize_guids function does just this.

Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2011-07-01 - Chris Jean
		Release ready
	1.0.1 - 2013-06-27 - Chris Jean
		Changed randomize_guids to a "public static" function.
*/

if ( ! class_exists( 'BuilderLayoutSettingsGUIDRandomizer' ) ) {
	class BuilderLayoutSettingsGUIDRandomizer {
		public static function randomize_guids( $data ) {
			$guids = array();
			
			foreach ( (array) $data['layouts'] as $guid => $layout ) {
				foreach ( (array) $layout['modules'] as $index => $module )
					$layout['modules'][$index]['guid'] = uniqid( '' );
				
				
				$new_guid = uniqid( '' );
				
				$layout['guid'] = $new_guid;
				
				unset( $data['layouts'][$guid] );
				$data['layouts'][$new_guid] = $layout;
				
				$guids[$guid] = $new_guid;
			}
			
			$data['default'] = $guids[$data['default']];
			
			foreach ( (array) $data['views'] as $function => $view_data )
				$data['views'][$function]['layout'] = $guids[$view_data['layout']];
			
			return $data;
		}
	}
}
