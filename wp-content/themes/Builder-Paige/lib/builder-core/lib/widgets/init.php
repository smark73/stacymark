<?php

/*
Widget style functions and Builder-provided widget initialization code
Written by Chris Jean for iThemes.com
Version 1.1.0

Version History
	1.0.0 - 2010-10-05 - Chris Jean
		Release-ready
	1.0.1 - 2010-12-15 - Chris Jean
		Added builder_register_widget_style and builder_get_widget_styles
	1.1.0 - 2011-10-06 - Chris Jean
		Improved efficiency
*/


if ( ! function_exists( 'builder_register_widget_style' ) ) {
	function builder_register_widget_style( $name, $selector ) {
		global $builder_widget_styles;
		
		if ( ! is_array( $builder_widget_styles ) )
			$builder_widget_styles = array();
		
		$builder_widget_styles[$selector] = $name;
	}
}

if ( ! function_exists( 'builder_get_widget_styles' ) ) {
	function builder_get_widget_styles() {
		global $builder_widget_styles;
		
		if ( ! is_array( $builder_widget_styles ) )
			$builder_widget_styles = array();
		
		asort( $builder_widget_styles );
		
		return $builder_widget_styles;
	}
}

//if ( builder_theme_supports( 'builder-widget-styles' ) )
//	ITUtility::require_file_once( 'lib/widgets/widget-styler.php' );


require_once( dirname( __FILE__) . '/duplicate-sidebar/init.php' );
require_once( dirname( __FILE__) . '/widget-content/init.php' );
