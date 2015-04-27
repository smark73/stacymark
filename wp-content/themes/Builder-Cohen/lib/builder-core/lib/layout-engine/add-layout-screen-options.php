<?php

/*
Add a Layout column to post and page listings
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2011-04-07
		Initial version
	1.0.1 - 2011-11-10 - Chris Jean
		Added &nbsp; output when the layout is empty. This prevents rendering issues for
			the table cell when the cell is empty.
*/


if ( ! class_exists( 'BuilderAddLayoutScreenOptions' ) ) {
	class BuilderAddLayoutScreenOptions {
		var $_layout_data = array();
		
		function BuilderAddLayoutScreenOptions() {
			add_action( 'manage_posts_custom_column', array( $this, 'display_column' ), 0, 2 );
			add_action( 'manage_pages_custom_column', array( $this, 'display_column' ), 0, 2 );
			
			add_filter( 'manage_posts_columns', array( $this, 'add_column' ), 0, 2 );
			add_filter( 'manage_pages_columns', array( $this, 'add_column' ), 0 );
		}
		
		function add_column( $columns, $post_type = 'page' ) {
			$columns['builder_layout'] = __( 'Layout', 'it-l10n-Builder-Cohen' );
			
			return $columns;
		}
		
		function display_column( $column_name, $post_id ) {
			if ( 'builder_layout' !== $column_name )
				return;
			
			$layout = get_post_meta( $post_id, '_custom_layout', true );
			
			if ( empty( $layout ) ) {
				echo "&nbsp;";
				return;
			}
			
			if ( empty( $this->_layout_data ) )
				$this->_layout_data = apply_filters( 'it_storage_load_layout_settings', array() );
			
			if ( isset( $this->_layout_data['layouts'][$layout]['description'] ) && is_string( $this->_layout_data['layouts'][$layout]['description'] ) )
				echo $this->_layout_data['layouts'][$layout]['description'];
		}
	}
	
	new BuilderAddLayoutScreenOptions();
}
