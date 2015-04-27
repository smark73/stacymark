<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.1

Version History
	1.0.0 - 2010-10-05 - Chris Jean
		Release-ready
	1.1.0 - 2013-07-19 - Chris Jean
		Added revision support.
	1.1.1 - 2013-08-05 - Chris Jean
		Fixed settings to set "public" to false, "show_ui" to true, and remove the other public-related settings.
*/


if ( ! class_exists( 'ITPostTypeWidgetContent' ) ) {
	it_classes_load( 'it-post-type.php' );
	
	class ITPostTypeWidgetContent extends ITPostType {
		var $_file = __FILE__;
		
		var $_var = 'widget_content';
		var $_name = 'Widget Contents';
		var $_name_plural = 'Widget Content';
		
		var $_settings = array(
			'rewrite'             => array(
				'slug' => 'post-type-widget-content',
			),
			'supports'            => array( 'title', 'editor', 'revisions' ),
			'public'              => false,
			'show_ui'             => true,
		);
		
		function ITPostTypeWidgetContent() {
			ITPostTypeWidgetContent::ITPostType();
			
			add_filter( 'builder_layout_filter_non_layout_post_types', array( $this, 'filter_non_layout_post_types' ) );
		}
		
		function filter_non_layout_post_types( $post_types ) {
			if ( ! in_array( $this->_var, $post_types ) )
				$post_types[] = $this->_var;
			
			return $post_types;
		}
	}
	
	new ITPostTypeWidgetContent();
}
