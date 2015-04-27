<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	See history.txt
*/


require_once( dirname( __FILE__ ) . '/widget.php' );

if ( ! class_exists( 'LayoutModuleHeader' ) ) {
	class LayoutModuleHeader extends LayoutModule {
		var $_name = '';
		var $_var = 'header';
		var $_description = '';
		var $_editor_width = 450;
		
		
		function LayoutModuleHeader() {
			$this->_name = _x( 'Header', 'module', 'it-l10n-Builder-Paige' );
			$this->_description = __( 'This module offers an easy-to-configure header for your site.', 'it-l10n-Builder-Paige' );
			
			$this->LayoutModule();
		}
		
		function _get_defaults( $defaults ) {
			$new_defaults = array(
				'title_type'        => 'default',
				'title_custom'      => '',
				'tagline_type'      => 'default',
				'tagline_custom'    => '',
				'seo_type'          => 'default',
				'seo_title_home'    => 'h1',
				'seo_title_other'   => 'div',
				'seo_tagline_home'  => 'div',
				'seo_tagline_other' => 'div',
				'sidebar'           => 'none',
			);
			
			return ITUtility::merge_defaults( $new_defaults, $defaults );
		}
		
		function _before_table_edit( $form, $results = true ) {
			
?>
	<p><?php printf( __( 'By default, this module uses the Site Title and Tagline as set in your WordPress Dashboard\'s <a href="%s" target="_blank">General > Settings</a>. This module will also wrap the title and tagline in div tags with an exception for the title on a home page view. On the home page, the title will be wrapped in an <code>h1</code> tag.', 'it-l10n-Builder-Paige' ), admin_url( 'options-general.php' ) ); ?></p>
	<p><?php _e( 'These options can be controlled below.', 'it-l10n-Builder-Paige' ); ?></p>
<?php
			
		}
		
		function _start_table_edit( $form, $results = true ) {
			$title_types = array(
				'default'  => __( 'Show Site Title (default)', 'it-l10n-Builder-Paige' ),
				'custom'   => __( 'Use custom Site Title text', 'it-l10n-Builder-Paige' ),
				'disabled' => __( 'Do not show Site Title', 'it-l10n-Builder-Paige' ),
			);
			
			$tagline_types = array(
				'default'  => __( 'Show Tagline (default)', 'it-l10n-Builder-Paige' ),
				'custom'   => __( 'Use custom Tagline text', 'it-l10n-Builder-Paige' ),
				'disabled' => __( 'Do not show Tagline', 'it-l10n-Builder-Paige' ),
			);
			
			$seo_types = array(
				'default' => __( 'Recommended Settings (default)', 'it-l10n-Builder-Paige' ),
				'custom'  => __( 'Custom', 'it-l10n-Builder-Paige' ),
			);
			
			$tag_options = array(
				'div' => __( 'div (default)', 'it-l10n-Builder-Paige' ),
				'h1'  => 'h1',
				'h2'  => 'h2',
				'h3'  => 'h3',
				'h4'  => 'h4',
				'h5'  => 'h5',
				'h6'  => 'h6',
			);
			
			$title_home_tag_options = array(
				'div' => 'div',
				'h1'  => __( 'h1 (default)', 'it-l10n-Builder-Paige' ),
				'h2'  => 'h2',
				'h3'  => 'h3',
				'h4'  => 'h4',
				'h5'  => 'h5',
				'h6'  => 'h6',
			);
			
?>
	<tr><td><label for="title_type"><?php _e( 'Site Title', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'title_type', $title_types ); ?>
			<?php ITUtility::add_tooltip( __( 'By default, this is the Site Title as configured in WordPress\' General Settings.', 'it-l10n-Builder-Paige' ) ); ?>
			
			<div id="title_type_custom_options">
				<p><label for="title_custom"><?php _e( 'Custom Site Title', 'it-l10n-Builder-Paige' ); ?></label> <?php $form->add_text_box( 'title_custom' ); ?></p>
				<br />
			</div>
		</td>
	</tr>
	<tr><td><label for="tagline_type"><?php _e( 'Tagline', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'tagline_type', $tagline_types ); ?>
			<?php ITUtility::add_tooltip( __( 'By default, this is the Tagline as configured in WordPress\' General Settings.', 'it-l10n-Builder-Paige' ) ); ?>
			
			<div id="tagline_type_custom_options">
				<p><label for="tagline_custom"><?php _e( 'Custom Tagline', 'it-l10n-Builder-Paige' ); ?></label> <?php $form->add_text_box( 'tagline_custom' ); ?></p>
				<br />
			</div>
		</td>
	</tr>
	<tr><td><label for="seo_type"><?php _e( 'SEO', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'seo_type', $seo_types ); ?>
			<?php ITUtility::add_tooltip( __( 'By default, the Header Module uses the recommended settings of an <code>h1</code> tag on the Site Title only on the home page view and a <code>div</code> tag all other times.', 'it-l10n-Builder-Paige' ) ); ?>
			
			<table id="seo_type_custom_options">
				<tr><th style="text-align:left;" colspan="2"><p><?php _e( 'Home Page View', 'it-l10n-Builder-Paige' ); ?></p></th></tr>
				<tr><td><label for="seo_title_home"><?php _e( 'Tag for Site Title', 'it-l10n-Builder-Paige' ); ?></label></td>
					<td><?php $form->add_drop_down( 'seo_title_home', $title_home_tag_options ); ?></td>
				</tr>
				<tr><td><label for="seo_tagline_home"><?php _e( 'Tag for Tagline', 'it-l10n-Builder-Paige' ); ?></label></td>
					<td><?php $form->add_drop_down( 'seo_tagline_home', $tag_options ); ?></td>
				</tr>
				<tr><th style="text-align:left;" colspan="2"><p><?php _e( 'Other Views', 'it-l10n-Builder-Paige' ); ?></p></th></tr>
				<tr><td><label for="seo_title_other"><?php _e( 'Tag for Site Title', 'it-l10n-Builder-Paige' ); ?></label></td>
					<td><?php $form->add_drop_down( 'seo_title_other', $tag_options ); ?></td>
				</tr>
				<tr><td><label for="seo_tagline_other"><?php _e( 'Tag for Tagline', 'it-l10n-Builder-Paige' ); ?></label></td>
					<td><?php $form->add_drop_down( 'seo_tagline_other', $tag_options ); ?></td>
				</tr>
			</table>
			<br />
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
<?php
			
		}
		
		function _render( $fields ) {
			$data = $fields['data'];
			$data = array_merge( $this->_get_defaults( array() ), $data );
			
			if ( 'default' == $data['title_type'] )
				$title = get_bloginfo( 'title' );
			else if ( 'custom' == $data['title_type'] )
				$title = $data['title_custom'];
			
			if ( 'default' == $data['tagline_type'] )
				$tagline = get_bloginfo( 'description' );
			else if ( 'custom' == $data['tagline_type'] )
				$tagline = $data['tagline_custom'];
			
			if ( 'custom' == $data['seo_type'] ) {
				if ( builder_is_home() ) {
					$title_tag = $data['seo_title_home'];
					$tagline_tag = $data['seo_tagline_home'];
				}
				else {
					$title_tag = $data['seo_title_other'];
					$tagline_tag = $data['seo_tagline_other'];
				}
			}
			else {
				$title_tag = ( builder_is_home() ) ? 'h1' : 'div';
				$tagline_tag = 'div';
			}
			
			$link = get_bloginfo( 'url' );
			
			if ( 'disabled' != $data['title_type'] )
				echo "<$title_tag class='site-title'><a href='$link'>$title</a></$title_tag>\n";
			if ( 'disabled' != $data['tagline_type'] )
				echo "<$tagline_tag class='site-tagline'><a href='$link'>$tagline</a></$tagline_tag>\n";
		}
	}
	
	new LayoutModuleHeader();
}
