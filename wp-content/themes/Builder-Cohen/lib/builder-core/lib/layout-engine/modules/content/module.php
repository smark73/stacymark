<?php

/*
Written by Chris Jean for iThemes.com
Version 3.0.0

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleContent' ) ) {
	class LayoutModuleContent extends LayoutModule {
		var $_name = '';
		var $_var = 'content';
		var $_description = '';
		var $_max = 1;
		var $_editor_width = 450;
		
		
		function LayoutModuleContent() {
			$this->_name = _x( 'Content', 'module', 'it-l10n-Builder-Cohen' );
			$this->_description = __( 'This module adds a place for the content to render. Most layouts will have this module.', 'it-l10n-Builder-Cohen' );
			
			$this->LayoutModule();
		}
		
		function _render( $fields ) {
			if ( ! function_exists( 'dynamic_loop' ) || ! dynamic_loop() )
				do_action( 'builder_layout_engine_render_content' );
		}
	}
	
	new LayoutModuleContent();
}
