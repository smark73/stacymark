jQuery(document).ready(
	function() {
		jQuery('select').each( bhm_show_hide_custom_options );
		jQuery('select').change( bhm_show_hide_custom_options );
	}
);


var bhm_show_hide_custom_options = function() {
	name = jQuery(this).attr('name') + '_custom_options';
	options_container = jQuery('#' + name);
	
	if ( 0 == options_container.length )
		return;
	
	options_container = options_container[0];
	
	if ( 'custom' == jQuery(this).val() )
		options_container.show();
	else
		options_container.hide();
	
	it_dialog_update_size();
};
