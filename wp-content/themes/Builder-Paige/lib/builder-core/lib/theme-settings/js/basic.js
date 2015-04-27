function builder_basic_tab_show_hide_options( id ) {
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
		
		jQuery('.' + name + '-options > div').hide();
		jQuery('.' + name + '-' + val + '-option').show();
	}
}


jQuery(document).ready(
	function() {
		jQuery(".show-hide-toggle").each(
			function() {
				builder_basic_tab_show_hide_options( jQuery(this).attr( 'id' ) );
			}
		);
		
		jQuery(".show-hide-toggle").change(
			function() {
				builder_basic_tab_show_hide_options( jQuery(this).attr( 'id' ) );
			}
		);
		
		
		if ( ( null != jQuery(".existing-custom-favicon-details") ) && ( null != jQuery(".existing-custom-favicon-details").html() ) && ( jQuery(".existing-custom-favicon-details").html().length > 0 ) ) {
			jQuery(".upload-favicon-info").hide();
			
			jQuery(".upload-new-custom-favicon").click(
				function() {
					jQuery(this).parent().hide();
					jQuery(".upload-favicon-info").show();
					
					return false;
				}
			);
		}
	}
);
