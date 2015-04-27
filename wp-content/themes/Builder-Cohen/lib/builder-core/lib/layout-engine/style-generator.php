<?php

/*
Written by Chris Jean for iThemes.com
Version 1.7.0

Version History
	1.5.0 - 2012-12-07 - Chris Jean
		Added support for builder-disable-stylesheet-cache theme support.
		Added another error handler for when cached file generation fails.
	1.6.0 - 2012-12-14 - Chris Jean
		Improved generation of the stylesheet version value to make it more clear on what is supported.
	1.6.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.7.0 - 2013-10-24 - Chris Jean
		Updated generator_version to 7 to account for styling fix in layout-engine.php version 5.7.2.
*/


class IT_Builder_Layout_Style_Generator {
	var $path = 'it-file-cache/builder-layouts';
	var $generator_version = 7;
	
	function IT_Builder_Layout_Style_Generator() {
		add_action( 'builder_add_stylesheets', array( $this, 'add_stylesheet' ), 0, 101 );
		add_action( 'wp_head', array( $this, 'add_stylesheet' ), 0, -100 );
		
		add_action( 'builder_layout_engine_identified_layout', array( $this, 'layout_identified' ), 0, 2 );
		add_action( 'builder_generate_layout_stylesheet', array( $this, 'generate_stylesheet' ) );
		
		if ( ! empty( $_REQUEST['builder_debug'] ) )
			add_action( 'builder_print_render_comments', array( $this, 'add_print_render_comments' ) );
	}
	
	function layout_identified( $layout_id, $layout_settings ) {
		$this->layout_id = $layout_id;
		$this->layout_settings =& $layout_settings;
		$this->layout =& $layout_settings['layouts'][$layout_id];
	}
	
	function update_layout_storage() {
		if ( empty( $this->layout_settings ) )
			return;
		
		$storage = new ITStorage( 'layout_settings' );
		
		$storage->save( $this->layout_settings );
	}
	
	function add_print_render_comments() {
		echo "\n";
		echo "\tStylesheet Type:    {$this->stylesheet_type}\n";
		echo "\tStylesheet Version: {$this->stylesheet_version}\n";
	}
	
	function generate_stylesheet( $layout_id, $layout_settings ) {
		it_classes_load( 'it-file-utility.php' );
		require_once( dirname( __FILE__ ) . '/layout-engine.php' );
		
		$this->stylesheet = apply_filters( 'builder_get_layout_style_rules', '', $layout_id, $layout_settings );
		
		$file = "{$this->path}/$layout_id.css";
		$file = ITFileUtility::get_writable_file( $file );
		
		if ( is_wp_error( $file ) || ( false === file_put_contents( $file, $this->stylesheet ) ) )
			return;
		
		$this->layout['stylesheet_version'] = $this->get_current_stylesheet_version();
		$this->layout['stylesheet_file'] = $file;
		$this->layout_settings['layouts'][$this->layout_id] = $this->layout;
		
		$this->update_layout_storage();
	}
	
	function add_stylesheet() {
		if ( empty( $this->layout ) )
			return;
		
		
		remove_action( 'wp_head', array( $this, 'add_stylesheet' ), 0, -100 );
		
		
		$current_stylesheet_version = $this->get_current_stylesheet_version();
		$this->stylesheet_version = $current_stylesheet_version;
		
		if ( ! isset( $this->layout['stylesheet_version'] ) )
			$this->layout['stylesheet_version'] = '';
		
		
		if ( builder_theme_supports( 'builder-disable-stylesheet-cache' ) ) {
			$this->stylesheet_type = 'disabled-cache';
			$this->layout['stylesheet_file'] = '';
		}
		else {
			if ( (  $current_stylesheet_version != (string) $this->layout['stylesheet_version'] ) || ! empty( $_GET['builder_force_regenerate_stylesheet'] ) ) {
				$this->stylesheet_type = 'regenerated';
				$this->generate_stylesheet( $this->layout_id, $this->layout_settings );
			}
			else {
				$this->stylesheet_type = 'cached';
			}
			
			if ( ! empty( $this->layout['stylesheet_file'] ) ) {
				if ( is_string( $this->layout['stylesheet_file'] ) && file_exists( $this->layout['stylesheet_file'] ) ) {
					$url = ITUtility::get_url_from_file( $this->layout['stylesheet_file'] );
				}
				else if ( is_wp_error( $this->layout['stylesheet_file'] ) ) {
					echo "\n<!--\n\t" . __( 'Builder Error: Unable to find a writable location to store the generated stylesheet. Check directory permissions in the wp-content/uploads directory.', 'it-l10n-Builder-Cohen' ) . "\n";
					echo "\t" . sprintf( __( 'Error Message (%s): %s', 'it-l10n-Builder-Cohen' ), $this->layout['stylesheet_file']->get_error_code(), $this->layout['stylesheet_file']->get_error_message() ) . "\n";
					echo "-->\n";
				}
			}
			
			if ( ! empty( $url ) ) {
				$version = md5( $current_stylesheet_version );
				echo "<link rel=\"stylesheet\" href=\"$url?version=$version\" type=\"text/css\" media=\"screen\" />\n";
				
				return;
			}
		}
		
		
		if ( empty( $this->stylesheet ) )
			$this->generate_stylesheet( $this->layout_id, $this->layout_settings );
		
		if ( empty( $this->stylesheet_type ) )
			$this->stylesheet_type = 'fallback';
		
		echo "<style type=\"text/css\" media=\"screen\">\n{$this->stylesheet}</style>\n";
	}
	
	function get_current_stylesheet_version() {
		$version = $this->generator_version;
		
		if ( empty( $this->layout['version'] ) )
			$version .= ',1';
		else
			$version .= ',' . $this->layout['version'];
		
		$version .= ',' . get_stylesheet();
		
		
		if ( builder_theme_supports( 'builder-full-width-modules-legacy' ) && ! isset( $GLOBALS['builder_theme_supports_defaults']['builder-full-width-modules-legacy'] ) )
			$GLOBALS['builder_theme_supports_defaults']['builder-full-width-modules-legacy'] = false;
		
		foreach ( $GLOBALS['builder_theme_supports_defaults'] as $feature => $args ) {
			$support = builder_theme_supports( $feature );
			
			if ( ! $support )
				continue;
			
			
			$version .= ',{' . $feature . ':';
			
			$data = array();
			
			if ( ! empty( $args ) && $support ) {
				foreach ( $args as $arg => $value ) {
					$arg_support = builder_theme_supports( $feature, $arg );
					
					if ( $arg_support )
						$data[] = $arg . '=' . (string) $arg_support;
				}
			}
			else {
				$data[] = (string) $support;
			}
			
			$version .= implode( ',', $data );
			$version .= '}';
		}
		
		
		$modules = array();
		
		foreach ( $this->layout['modules'] as $module )
			$modules[] = $module['guid'];
		
		$version .= ',{modules:' . implode( ',', $modules ) . '}';
		
		
		$version = apply_filters( 'builder_get_layout_stylesheet_version', $version );
		
		return $version;
	}
}

new IT_Builder_Layout_Style_Generator();
