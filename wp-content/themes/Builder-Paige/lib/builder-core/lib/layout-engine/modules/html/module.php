<?php

/*
Written by Chris Jean for iThemes.com
Version 2.3.0

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleHTML' ) ) {
	class LayoutModuleHTML extends LayoutModule {
		var $_name = '';
		var $_var = 'html';
		var $_description = '';
		var $_editor_width = 700;
		var $_can_remove_wrappers = true;
		
		
		function LayoutModuleHTML() {
			$this->_name = _x( 'HTML', 'module', 'it-l10n-Builder-Paige' );
			$this->_description = __( 'This module gives you a place to add freeform HTML to the layout. It also supports the use of shortcodes and execution of PHP code.', 'it-l10n-Builder-Paige' );
			
			$this->LayoutModule();
		}
		
		function _get_defaults( $defaults ) {
			$new_defaults = array(
				'html'           => '',
				'sidebar'        => 'none',
				'sidebar_widths' => '',
				'enable_eval'    => 'no',
			);
			
			return ITUtility::merge_defaults( $new_defaults, $defaults );
		}
		
		function _before_table_edit( $form, $results = true ) {
			
?>
	<p><?php _e( 'This module isn\'t limited to just static HTML content. You can also use shortcodes and PHP code to add generated content. PHP support must first be activated with the Enable PHP option below.', 'it-l10n-Builder-Paige' ); ?></p>
	<p><?php _e( 'Using shortcodes is preferred over PHP code as allowing PHP code has security risks.', 'it-l10n-Builder-Paige' ); ?></p>
	
	<p>
		<label for="html"><?php _ex( 'HTML', 'module', 'it-l10n-Builder-Paige' ); ?></label>
		<br />
		<?php $form->add_text_area( 'html', array( 'style' => 'width:100%; max-width:100%; min-height:100px;', 'rows' => '10' ) ); ?>
	</p>
	<br />
<?php
			
		}
		
		function _start_table_edit( $form, $results = true ) {
			
?>
<?php
			
		}
		
		function _end_table_edit( $form, $results = true ) {
			
?>
	<tr><td><label for="enable_eval"><?php _e( 'Enable PHP', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'enable_eval', array( 'no' => __( 'No', 'it-l10n-Builder-Paige' ), 'yes' => __( 'Yes', 'it-l10n-Builder-Paige' ) ) ); ?> <?php ITUtility::add_tooltip( __( 'Enabling this option allows for processing of PHP code inside the module. <strong>Always review the PHP code if taken from a third party as it could pose a serious security threat.</strong>', 'it-l10n-Builder-Paige' ) ); ?>
		</td>
	</tr>
<?php
			
		}
		
		function _render( $fields ) {
			$data = $fields['data'];
			
			$html = do_shortcode( $data['html'] );
			
			if ( isset( $data['enable_eval'] ) && ( 'yes' === $data['enable_eval'] ) ) {
				if ( ! defined( 'BUILDER_DISABLE_HTML_MODULE_PHP' ) || ! constant( 'BUILDER_DISABLE_HTML_MODULE_PHP' ) )
					eval( "?>$html" );
				else
					echo "<p><strong>PHP execution for the HTML module has been disabled for security reasons.</strong></p>\n" . htmlentities( $html );
			}
			else
				echo $html;
		}
	}
	
	new LayoutModuleHTML();
}
