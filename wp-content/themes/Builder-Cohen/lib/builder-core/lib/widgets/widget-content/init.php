<?php

/*
Written by Chris Jean for iThemes.com
Version 1.2.2

Version History
	1.0.0 - 2010-10-05 - Chris Jean
		Release-ready
	1.0.1 - 2010-12-15 - Chris Jean
		Post type initialization code no longer requires the action hook
	1.1.0 - 2011-10-06 - Chris Jean
		Improved efficiency
	1.2.0 - 2011-11-23 - Chris Jean
		Added loader for settings.php
		Moved loader for widget.php and post-types/content.php to the
			builder_theme_settings_loaded hook
		Added builder_add_theme_feature_option call
	1.2.1 - 2011-12-20 - Chris Jean
		Changed builder_theme_settings_loaded hook to builder_theme_settings_pre_settings_load
	1.2.2 - 2012-07-11 - Chris Jean
		Broke the builder_widget_content_init function into two functions, each
			on a separate hook to allow the Widget Content feature to be enabled
			and disabled via theme settings
*/


if ( ! function_exists( 'get_post_type_object' ) )
	return;


if ( is_admin() )
	require_once( dirname( __FILE__ ) . '/settings.php' );


function builder_widget_content_init() {
	builder_add_theme_feature_option( 'builder-widget-widget-content', __( 'Widget Content', 'it-l10n-Builder-Cohen' ), __( 'The Widget Content feature adds a new top-level menu called "Widget Content" that allows for easy creation of content that can then be added to a sidebar by using the Widget Content widget.', 'it-l10n-Builder-Cohen' ), 0 );
}
add_action( 'builder_theme_settings_pre_settings_load', 'builder_widget_content_init' );

function builder_widget_content_load() {
	if ( ! builder_theme_supports( 'builder-widget-widget-content' ) )
		return;
	
	require_once( dirname( __FILE__ ) . '/widget.php' );
	require_once( dirname( __FILE__ ) . '/post-types/content.php' );
}
add_action( 'builder_theme_settings_loaded', 'builder_widget_content_load' );
