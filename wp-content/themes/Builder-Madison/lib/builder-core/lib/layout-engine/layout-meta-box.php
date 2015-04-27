<?php

/*
Add a "Custom Layout" meta box to content types
Written by Chris Jean for iThemes.com
Version 2.1.2

Version History
	2.1.0 - 2011-10-06 - Chris Jean
		Changed class name to BuilderLayoutMetaBox
	2.1.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	2.1.2 - 2013-07-03 - Chris Jean
		Improved the description in the meta box.
*/


if ( ! class_exists( 'BuilderLayoutMetaBox' ) ) {
	class BuilderLayoutMetaBox {
		var $_var = 'layout-meta-box';
		
		
		function BuilderLayoutMetaBox() {
			global $wp_version;
			
			if ( version_compare( $wp_version, '2.9.7', '>' ) )
				add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			else
				add_action( 'admin_menu', array( $this, 'add_meta_box' ) );
			
			add_action( 'save_post', array( $this, 'save_data' ) );
		}
		
		function add_meta_box( $post_type = null ) {
			if ( ! empty( $post_type ) ) {
				$non_layout_post_types = array( 'link', 'comment', 'attachment', 'revision', 'nav_menu_item' );
				$non_layout_post_types = apply_filters( 'builder_layout_filter_non_layout_post_types', $non_layout_post_types );
				
				if ( ! in_array( $post_type, $non_layout_post_types ) )
					add_meta_box( 'layout_meta_box', __( 'Custom Builder Layout', 'it-l10n-Builder-Madison' ), array( $this, 'render_box' ), $post_type, 'side', 'low' );
			}
			else {
				add_meta_box( 'layout_meta_box', __( 'Custom Builder Layout', 'it-l10n-Builder-Madison' ), array( $this, 'render_box' ), 'post', 'side' );
				add_meta_box( 'layout_meta_box', __( 'Custom Builder Layout', 'it-l10n-Builder-Madison' ), array( $this, 'render_box' ), 'page', 'side' );
			}
		}
		
		function render_box( $post ) {
			$layout_data = apply_filters( 'it_storage_load_layout_settings', array() );
			
			$layouts = array( '' => '' );
			foreach ( (array) $layout_data['layouts'] as $layout => $layout_data )
				$layouts[$layout] = $layout_data['description'];
			
			if ( ! empty( $GLOBALS['post_type'] ) && is_callable( 'get_post_type_object' ) ) {
				$post_type = get_post_type_object( $GLOBALS['post_type'] );
				$type = $post_type->labels->singular_name;
			}
			else {
				$type = ( preg_match( '|page[^/]+$|', $_SERVER['REQUEST_URI'] ) ) ? 'page' : 'post';
			}
			
			$data = array();
			if ( isset( $post->ID ) )
				$data['custom_layout'] = get_post_meta( $post->ID, '_custom_layout', true );
			
			$form = new ITForm( $data, array( 'prefix' => $this->_var ) );
			
?>
	<p><strong><?php _e( 'Select a Layout:', 'it-l10n-Builder-Madison' ); ?></strong></p>
	<p><?php $form->add_drop_down( 'custom_layout', $layouts ); ?></p>
	<p><?php printf( __( 'Choosing a Custom Builder Layout causes the chosen Layout to be used when viewing this %1$s. It will override any custom Views you have configured in the <a href="%2$s">Views Editor</a>.', 'it-l10n-Builder-Madison' ), $type, admin_url( 'admin.php?page=layout-editor&editor_tab=views' ) ); ?></p>
	<?php $form->add_hidden( 'nonce', wp_create_nonce( $this->_var ) ); ?>
<?php
			
		}
		
		function save_data( $post_id ) {
			// Skip if the nonce check fails
			if ( ! isset( $_POST["{$this->_var}-nonce"] ) || ! wp_verify_nonce( $_POST["{$this->_var}-nonce"], $this->_var ) )
				return;
			
			// Don't save or update on autosave
			if ( defined( 'DOING_AUTOSAVE' ) && ( true === DOING_AUTOSAVE ) )
				return;
			
			// Only allow those with permissions to modify the type to save/update a layout
			if ( ! current_user_can( 'edit_post', $post_id ) )
				return;
			
			
			// Finally, time to do some real work
			if ( ! empty( $_POST["{$this->_var}-custom_layout"] ) )
				update_post_meta( $post_id, '_custom_layout', $_POST["{$this->_var}-custom_layout"] );
			else
				delete_post_meta( $post_id, '_custom_layout' );
		}
	}
	
	new BuilderLayoutMetaBox();
}
