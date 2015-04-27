<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.3

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleFooter' ) ) {
	class LayoutModuleFooter extends LayoutModule {
		var $_name = '';
		var $_var = 'footer';
		var $_description = '';
		var $_max = 1;
		var $_editor_width = 450;
		
		
		function LayoutModuleFooter() {
			$this->_name = _x( 'Footer', 'module', 'it-l10n-Builder-Cohen' );
			$this->_description = __( 'This module adds a place for the footer to render. Most layouts will have this module at the bottom of the layout.', 'it-l10n-Builder-Cohen' );
			
			$this->LayoutModule();
		}
		
		function _get_defaults( $defaults ) {
			$defaults['sidebar'] = 'none';
			
			return $defaults;
		}
		
		function _render( $fields ) {
			get_footer();
			do_action( 'builder_layout_engine_render_footer' );
		}
	}
	
	new LayoutModuleFooter();
}


?>
