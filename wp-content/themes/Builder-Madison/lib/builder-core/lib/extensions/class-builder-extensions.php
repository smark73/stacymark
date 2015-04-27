<?php

/*
Written by Chris Jean for iThemes.com
Version 2.3.3

Version History
	2.2.0 - 2013-05-21 - Chris Jean
		Added details about the active Extension to the Builder rendering details comment block.
		Removed assign by reference.
	2.3.0 - 2013-08-13 - Chris Jean
		Added builder-core/extensions to the directories searched for Extensions.
	2.3.1 - 2013-09-04 - Chris Jean
		Changed ordering of extensions directories to use (in order) child theme, parent theme, builder-core.
	2.3.2 - 2013-11-25 - Chris Jean
		Fixed Extensions applied by generic Views (such as an all categories View) overriding the Extension applied by specific Views (such as a specific category View).
	2.3.3 - 2013-12-18 - Chris Jean
		Added Extension directory to render comments output.
*/


if ( ! class_exists( 'BuilderExtensions' ) ) {
	class BuilderExtensions {
		var $_active_extension = null;
		
		var $_extension_directory = 'extensions';
		var $_extensions = array();
		
		function BuilderExtensions() {
			add_action( 'builder_layout_engine_identified_layout', array( $this, 'enable_extension' ), 10, 4 );
			
			add_filter( 'builder_filter_saved_layout_data', array( $this, 'filter_layout_data' ) );
			add_filter( 'builder_filter_disable_theme_stylesheets', array( $this, 'filter_disable_theme_style' ) );
			add_filter( 'builder_get_extensions', array( $this, 'get_extensions' ) );
			add_filter( 'builder_get_extensions_with_names', array( $this, 'get_extensions_with_names' ) );
			add_filter( 'builder_get_extensions_data', array( $this, 'get_extensions_data' ) );
			add_filter( 'builder_get_extension_data', array( $this, 'get_extension_data' ), 10, 2 );
			
			if ( is_admin() )
				$this->_run_all_functions_files();
		}
		
		function filter_layout_data( $layout ) {
			if ( ! isset( $_POST['extension'] ) )
				return;
			
			$layout['extension'] = $_POST['extension'];
			
			if ( ! empty( $layout['extension'] ) ) {
				$extension_data = apply_filters( 'builder_get_extension_data', array(), $layout['extension'] );
				
				$layout['extension_disables_theme_style'] = ( $extension_data['disable_theme_style'] ) ? true : false;
			}
			
			return $layout;
		}
		
		function _run_all_functions_files() {
			$extensions = apply_filters( 'builder_get_extensions', array(), true );
			
			foreach ( (array) $extensions as $extension ) {
				if ( file_exists( "{$extension}/functions.php" ) )
					include_once( "{$extension}/functions.php" );
			}
		}
		
		function _get_layout() {
			if ( isset( $this->_layout ) )
				return $this->_layout;
			
			$this->_layout = apply_filters( 'builder_get_current_layout', array() );
			
			return $this->_layout;
		}
		
		function filter_disable_theme_style( $disable ) {
			$layout = $this->_get_layout();
			
			if ( ! empty( $layout['extension'] ) && isset( $layout['disable_style'] ) && ( 'yes' === $layout['disable_style'] ) )
				return true;
			return $disable;
		}
		
		function add_stylesheet() {
			if ( empty( $this->_active_extension_directory ) )
				return;
			
			it_classes_load( 'it-file-utility.php' );
			
			$url = ITFileUtility::get_url_from_file( "{$this->_active_extension_directory}/style.css" );
			
			echo "<link rel='stylesheet' href='$url' type='text/css' media='screen' />\n";
		}
		
		function enable_extension( $layout_id = null, $data = array(), $layout_views = array(), $layout_functions = array() ) {
			if ( ! is_null( $this->_active_extension ) || ! is_array( $data ) || ! is_array( $layout_views ) || ! is_array( $layout_functions ) ) {
				return;
			}
			
			
			$extension_source = '';
			$disable_style = false;
			
			foreach ( $layout_functions as $function ) {
				if ( empty( $data['views'][$function] ) || empty( $data['views'][$function]['extension'] ) ) {
					continue;
				}
				
				if ( '//DISABLE_EXTENSION//' == $data['views'][$function]['extension'] ) {
					$this->_active_extension = '';
					return;
				}
				
				$extension = $data['views'][$function]['extension'];
				$disable_style = ( isset( $data['views'][$function]['extension_data']['disable_theme_style'] ) ) ?  $data['views'][$function]['extension_data']['disable_theme_style'] : false;
				
				$extension_source = 'View';
			}
			
			if ( empty( $extension ) ) {
				if ( empty( $data['layouts'] ) || empty( $data['layouts'][$layout_id] ) || empty( $data['layouts'][$layout_id]['extension'] ) )
					return;
				
				$extension = $data['layouts'][$layout_id]['extension'];
				$disable_style = $data['layouts'][$layout_id]['extension_disables_theme_style'];
				
				$extension_source = 'Layout';
			}
			
			if ( empty( $extension ) )
				return;
			
			
			if ( false !== strpos( $extension, '%WP_CONTENT_DIR%' ) )
				$extension = basename( $extension );
			
			$directory = $this->_get_active_extension_directory( $extension );
			
			
			$this->_active_extension = $extension;
			$this->_active_extension_directory = $directory;
			$this->_extension_source = $extension_source;
			
			
			add_action( 'wp_enqueue_scripts', array( $this, 'add_stylesheet' ) );
			
			if ( file_exists( "$directory/functions.php" ) )
				include_once( "$directory/functions.php" );
			
			if ( $disable_style )
				add_filter( 'builder_filter_disable_theme_stylesheets', array( $this, 'return_true' ) );
			
			
			add_action( 'builder_print_render_comments', array( $this, 'add_print_render_comments' ) );
		}
		
		function add_print_render_comments() {
			echo "\n";
			echo "\tExtension:           {$this->_active_extension}\n";
			echo "\tExtension Directory: " . preg_replace( '/^' . preg_quote( ABSPATH, '/' ) . '/', '', $this->_active_extension_directory ) . "\n";
			echo "\tExtension Source:    {$this->_extension_source}\n";
		}
		
		function return_true( $disable ) {
			return true;
		}
		
		function _get_extension_directories() {
			if ( isset( $this->_directories ) )
				return $this->_directories;
			
			$this->_directories = array(
				get_stylesheet_directory() . '/' . $this->_extension_directory,
				get_template_directory() . '/' . $this->_extension_directory,
				builder_main_get_builder_core_path() . '/extensions',
			);
			$this->_directories = array_unique( $this->_directories );
			
			$this->_directories = apply_filters( 'builder_get_extension_directories', $this->_directories );
			
			foreach ( (array) $this->_directories as $index => $directory ) {
				if ( ! is_dir( $directory ) )
					unset( $this->_directories[$index] );
			}
			
			return $this->_directories;
		}
		
		function get_extensions( $filter_extensions = array() ) {
			$directories = $this->_get_extension_directories();
			
			$files = array();
			
			foreach ( (array) $directories as $directory ) {
				$files = array_merge( (array) $files, (array) glob( "$directory/*/style.css" ) );
				$files = array_merge( (array) $files, (array) glob( "$directory/*/functions.php" ) );
			}
			
			
			$extensions = array();
			
			foreach ( (array) $files as $index => $file )
				$extensions[basename( dirname( $file ) )] = dirname( $file );
			
			return array_merge( $filter_extensions, $extensions);
		}
		
		function get_extensions_with_names( $extensions = array() ) {
			$raw_extensions = apply_filters( 'builder_get_extensions', array() );
			
			foreach ( $raw_extensions as $extension => $path ) {
				$data = apply_filters( 'builder_get_extension_data', array(), $path );
				
				if ( is_array( $data ) && ! empty( $data['name'] ) )
					$extensions[$extension] = $data['name'];
			}
			
			ksort( $extensions );
			
			return $extensions;
		}
		
		function get_extensions_data( $extensions = array() ) {
			$raw_extensions = apply_filters( 'builder_get_extensions', array() );
			
			foreach ( $raw_extensions as $extension => $path ) {
				$data = apply_filters( 'builder_get_extension_data', array(), $path );
				
				if ( is_array( $data ) )
					$extensions[$extension] = $data;
			}
			
			$extensions = ITUtility::sort_array( $extensions, 'name' );
			
			return $extensions;
		}
		
		function get_extension_data( $data = array(), $extension_path ) {
			if ( basename( $extension_path ) == $extension_path )
				$extension_path = $this->_get_active_extension_directory( $extension_path );
			else
				$extension_path = realpath( $extension_path );
			
			$default_data = array(
				'name'                => ucwords( preg_replace( '/[\-_]+/', ' ', basename( $extension_path ) ) ),
				'description'         => '',
				'disable_theme_style' => false,
				'functions'           => null,
				'style'               => null,
			);
			
			$file_keys = array();
			
			foreach ( array_keys( $default_data ) as $key ) {
				$header = ucwords( str_replace( '_', ' ', $key ) );
				$file_keys[$key] = $header;
			}
			
			$data = array();
			
			if ( file_exists( "$extension_path/style.css" ) ) {
				$data = get_file_data( "$extension_path/style.css", $file_keys, 'extension' );
				
				foreach ( $data as $key => $val ) {
					if ( empty( $val ) )
						unset( $data[$key] );
				}
				
				$data['style'] = "$extension_path/style.css";
			}
			
			if ( file_exists( "$extension_path/functions.php" ) )
				$data['functions'] = "$extension_path/functions.php";
			
			$data = array_merge( $default_data, $data );
			
			if ( ! $data['disable_theme_style'] || ( 'no' == strtolower( $data['disable_theme_style'] ) ) )
				$data['disable_theme_style'] = false;
			else
				$data['disable_theme_style'] = true;
			
			return $data;
		}
		
		function _get_active_extension_directory( $extension ) {
			$directories = $this->_get_extension_directories();
			
			foreach ( $directories as $directory ) {
				if ( is_dir( "$directory/$extension" ) )
					return "$directory/$extension";
			}
		}
	}
	
	new BuilderExtensions();
}
