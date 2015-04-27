<?php

/*
Written by Chris Jean for iThemes.com
Version 2.1.0

Version History
	2.0.0 - 2010-01-07
		Changed filter builder_get_layout_modules to builder_get_modules
		Changed filter builder_register_layout_modules to builder_register_modules
		Changed filter builder_layout_modules_loaded to builder_modules_loaded
	2.0.1 - 2011-10-06 - Chris Jean
		Minor performance improvements
	2.1.0 - 2012-08-23 - Chris Jean
		Changed to class IT_Builder_Module_Loader.
		Replaced directory search with directly loading the needed modules.
*/


class IT_Builder_Module_Loader {
	var $_modules;
	
	
	function IT_Builder_Module_Loader() {
		add_filter( 'builder_get_modules', array( &$this, '_get_layout_modules' ), 0 );
		
		$this->_load_modules();
	}
	
	function register_module( &$module ) {
		$this->_modules[$module->_var] =& $module;
	}
	
	function _get_layout_modules( $modules ) {
		$modules = array_merge( $modules, $this->_modules );
		
		return $modules;
	}
	
	function _load_modules() {
		$dir = dirname( __FILE__ ) . '/modules';
		
		require( "{$dir}/class-layout-module.php" );
		
		require( "{$dir}/content/module.php" );
		require( "{$dir}/footer/module.php" );
		require( "{$dir}/header/module.php" );
		require( "{$dir}/html/module.php" );
		require( "{$dir}/image/module.php" );
		require( "{$dir}/navigation/module.php" );
		require( "{$dir}/widget-bar/module.php" );
		
		do_action( 'builder_modules_loaded' );
	}
}

$GLOBALS['builder_modules'] = new IT_Builder_Module_Loader();

do_action( 'builder_register_modules' );
