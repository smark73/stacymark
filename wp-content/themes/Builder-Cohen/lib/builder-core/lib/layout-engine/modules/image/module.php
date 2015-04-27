<?php

/*
Written by Chris Jean for iThemes.com
Version 2.4.1

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleImage' ) ) {
	class LayoutModuleImage extends LayoutModule {
		var $_name = '';
		var $_var = 'image';
		var $_description = '';
		var $_editor_width = '600';
		
		
		function LayoutModuleImage() {
			$this->_name = _x( 'Image', 'module', 'it-l10n-Builder-Cohen' );
			$this->_description = __( 'This module adds an image to the layout. This can be used to add static headers or just something to spice up your layout.', 'it-l10n-Builder-Cohen' );
			
			$this->LayoutModule();
		}
		
		function export( $data ) {
			if ( ! empty( $data['attachment'] ) && is_numeric( $data['attachment'] ) )
				do_action( 'builder_import_export_add_attachment', $data['attachment'] );
			
			return $data;
		}
		
		function import( $data, $attachments, $post_data ) {
			if ( ! empty( $data['attachment'] ) && is_numeric( $data['attachment'] ) ) {
				if ( isset( $attachments[$data['attachment']]['id'] ) )
					$data['attachment'] = $attachments[$data['attachment']]['id'];
				else
					$data['attachment'] = '';
			}
			
			return $data;
		}
		
		function _get_defaults( $defaults ) {
			$new_defaults = array(
				'image'       => '',
				'attachment'  => '',
				'height_type' => 'auto',
				'height'      => '150',
				'image_alt'   => '',
				'url'         => '',
				'new_window'  => '',
				'sidebar'     => 'none',
			);
			
			return ITUtility::merge_defaults( $new_defaults, $defaults );
		}
		
		function _validate( $result ) {
			$result['data']['notused'] = 'image';
			
			it_classes_load( 'it-file-utility.php' );
			
			if ( ITFileUtility::file_uploaded( 'image' ) ) {
				$file = ITFileUtility::upload_file( 'image' );
				
				if ( is_wp_error( $file ) )
					$result['errors'][] = sprintf( __( "Unable to save uploaded image. Ensure that the web server has permissions to write to the uploads folder.\n\nMessage: %s", 'it-l10n-Builder-Cohen' ), $file->get_error_message() );
				else {
					$result['data']['attachment'] = $file['id'];
					$_POST['attachment'] = $file['id'];
				}
			}
			else if ( empty( $_POST['attachment'] ) )
				$result['errors'][] = __( 'You must upload an image.', 'it-l10n-Builder-Cohen' );
			
			
			if ( empty( $_POST['height'] ) || preg_match( '/[^0-9]/', $_POST['height'] ) )
				$result['errors'][] = __( 'You must enter an integer value for the Height.', 'it-l10n-Builder-Cohen' );
			else if ( $_POST['height'] < 10 )
				$result['errors'][] = __( 'The minimum Height is 10 pixels. Please increase the Height.', 'it-l10n-Builder-Cohen' );
			
			
			return $result;
		}
		
		function _before_table_edit( $form, $results = true ) {
			
?>
	<div id="image-already-uploaded-message" style="margin-bottom:10px;display:none;">
		<?php _e( 'An image has already been uploaded.', 'it-l10n-Builder-Cohen' ); ?><br />
		<?php _e( '<a href="#" id="show-image-upload-row">Click here</a> to upload a new image.', 'it-l10n-Builder-Cohen' ); ?>
	</div>
<?php
			
		}
		
		function _start_table_edit( $form, $results = true ) {
			$height_types = array(
				'auto'   => __( 'Automatic, use a height that preserves the image\'s aspect ratio', 'it-l10n-Builder-Cohen' ),
				'custom' => __( 'Manual, select a height manually', 'it-l10n-Builder-Cohen' ),
			);
			
			if ( ! isset( $form->_options['height_type'] ) )
				$form->set_option( 'height_type', '' );
			
?>
	<tr id="image-upload-row" style="display:none;">
		<td><?php _e( 'Upload Image', 'it-l10n-Builder-Cohen' ); ?></td>
		<td>
			<?php $form->add_file_upload( 'image' ); ?>
		</td>
	</tr>
	<tr><td><label for="height_type"><?php _e( 'Height', 'it-l10n-Builder-Cohen' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'height_type', $height_types ); ?>
			<?php ITUtility::add_tooltip( __( 'If Manual is selected, the image will first be resized to fit the height and then cropped to fit the width.', 'it-l10n-Builder-Cohen' ), '', 'left' ); ?>
			
			<div id="height_type-options">
				<p><?php _e( 'Manual height', 'it-l10n-Builder-Cohen' ); ?> <?php $form->add_text_box( 'height', array( 'style' => 'width:50px;' ) ); ?> px</p>
			</div>
		</td>
	</tr>
	<tr><td valign="top"><label for="image_alt"><?php _e( 'Image Alt Attribute', 'it-l10n-Builder-Cohen' ); ?></label></td>
		<td>
			<?php $form->add_text_box( 'image_alt' ); ?>
			<?php ITUtility::add_tooltip( __( 'This sets the <code>alt</code> attribute of the <code>img</code> tag. This attribute is typically used to describe the image in case it cannot be viewed. If this is left blank, the Module\'s Name will be used instead.', 'it-l10n-Builder-Cohen' ) ); ?>
		</td>
	</tr>
	<tr><td valign="top"><label for="url"><?php _e( 'Link URL', 'it-l10n-Builder-Cohen' ); ?></label></td>
		<td>
			<?php $form->add_text_box( 'url' ); ?>
			<?php ITUtility::add_tooltip( __( 'Make sure that you use complete URLs including the <code>http://</code> portion. If you leave this fied empty, no link URL will be used.', 'it-l10n-Builder-Cohen' ) ); ?>
		</td>
	</tr>
	<tr><td valign="top"><label for="new_window"><?php _e( 'Link Opens New Window', 'it-l10n-Builder-Cohen' ); ?></label></td>
		<td>
			<?php $form->add_check_box( 'new_window' ); ?>
		</td>
	</tr>
<?php
			
		}
		
		function _after_table_edit( $form, $results = true ) {
			
?>
	<?php $form->add_hidden( 'attachment' ); ?>
	<?php $form->add_hidden( 'notused' ); ?>
	
	<script type="text/javascript">
		init_module_editor();
	</script>
<?php
			
		}
		
		function _render( $fields ) {
			$data = $fields['data'];
			
			if ( ! empty( $data['attachment'] ) ) {
				if ( ! wp_attachment_is_image( $data['attachment'] ) )
					return;
				
				$post = get_post( $data['attachment'] );
				
				$file = get_attached_file( $data['attachment'] );
			}
			else if ( ! empty( $data['manual_file'] ) ) {
				$file = builder_main_get_builder_core_path() . '/lib/layout-engine/default-images/' . $data['manual_file'];
				
				if ( ! file_exists( $file ) )
					return;
			}
			
			$image_width = $fields['widths']['element_width'];
			
			
			it_classes_load( 'it-file-utility.php' );
			
			if ( 'custom' == $data['height_type'] )
				$resized_image = ITFileUtility::resize_image( $file, $image_width, $data['height'], true );
			else
				$resized_image = ITFileUtility::resize_image( $file, $image_width );
			
			if ( ! is_array( $resized_image ) && is_wp_error( $resized_image ) )
				echo "<!-- Resize Error: " . $resized_image->get_error_message() . " -->";
			else
				$image_url = $resized_image['url'];
			
			
			if ( ! empty( $data['url'] ) ) {
				$attributes['href'] = $data['url'];
				
				if ( ! empty( $data['new_window'] ) )
					$attributes['target'] = '_blank';
				
				ITUtility::print_open_tag( 'a', $attributes );
			}
			
			
			if ( ! empty( $data['image_alt'] ) )
				$alt = $data['image_alt'];
			
			if ( empty( $alt ) )
				$alt = get_post_meta( $data['attachment'], '_wp_attachment_image_alt', true );
			
			if ( empty( $alt ) )
				$alt = $data['name'];
			
			$attributes = array(
				'src' => $image_url,
				'alt' => $alt,
			);
			
			$new_attributes = apply_filters( 'builder_image_module_filter_img_attributes', $attributes, $fields );
			
			if ( empty( $new_attributes ) )
				$new_attributes = $attributes;
			else if ( empty( $new_attributes['src'] ) )
				$new_attributes['src'] = $attributes['src'];
			
			$attributes = $new_attributes;
			
			ITUtility::print_self_closing_tag( 'img', $attributes );
			
			
			if ( ! empty( $data['url'] ) )
				echo "</a>";
		}
	}
	
	new LayoutModuleImage();
}
