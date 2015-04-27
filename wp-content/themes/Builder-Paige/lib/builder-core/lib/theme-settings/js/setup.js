function builder_setup_tab_show_hide_options( id ) {
	var type = jQuery('#' + id).attr( 'type' );
	
	if ( 'checkbox' == type ) {
		var checked = jQuery('input[id="' + id + '"]:checked').length;
		var selector = '.' + id + '-option';
		
		if ( checked > 0 )
			jQuery(selector).show();
		else
			jQuery(selector).hide();
	}
	else if ( 'radio' == type ) {
		var name = jQuery('#' + id).attr( 'name' );
		var val = jQuery('input:radio[name=' + name + ']:checked').val();
		
		jQuery('.it-options-' + name).hide();
		jQuery('.it-options-' + name + '-' + val).show();
	}
}


jQuery(document).ready(
	function() {
		jQuery(".show-hide-toggle").each(
			function() {
				builder_setup_tab_show_hide_options( jQuery(this).attr( 'id' ) );
			}
		);
		
		jQuery(".show-hide-toggle").change(
			function() {
				builder_setup_tab_show_hide_options( jQuery(this).attr( 'id' ) );
			}
		);
		
		
		jQuery('#it-builder-setup').submit(
			function( event ) {
				if ( 'use' != jQuery('input[name=theme_layouts]:checked').val() )
					return true;
				
				var response = confirm( builder_setup_tab.confirm_dialog_text );
				return response;
			}
		);
	}
);
