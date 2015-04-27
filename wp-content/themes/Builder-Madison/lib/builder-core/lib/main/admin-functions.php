<?php

/*
Basic admin functions used by various parts of Builder
Written by Chris Jean for iThemes.com
Version 1.0.3

Version History
	1.0.0 - 2011-10-12 - Chris Jean
		Initial release version
	1.0.1 - 2011-11-14 - Chris Jean
		Changed builder_add_help_sidebar to builder_set_help_sidebar to match WordPress API change
		Changed add_help_sidebar to set_help_sidebar to match WordPress API change
	1.0.2 - 2011-12-05 - Chris Jean
		Updated to reflect current WordPress 3.3 API changes
	1.0.3 - 2011-12-09 - Chris Jean
		Fixed problem with calling set_help_sidebar on invalid screen
*/


function builder_set_help_sidebar() {
	ob_start();
	
?>
<p><strong><?php _e( 'For more information:', 'it-l10n-Builder-Madison' ); ?></strong></p>
<p><?php _e( '<a href="http://ithemes.com/forum/" target="_blank">Support Forum</a>' ); ?></p>
<p><?php _e( '<a href="http://ithemes.com/codex/page/Builder" target="_blank">Codex</a>' ); ?></p>
<?php
	
	$help = ob_get_contents();
	ob_end_clean();
	
	$help = apply_filters( 'builder_filter_help_sidebar', $help );
	
	if ( ! empty( $help ) ) {
		$screen = get_current_screen();
		
		if ( is_callable( array( $screen, 'set_help_sidebar' ) ) )
			$screen->set_help_sidebar( $help );
	}
}
