function init_module_editor() {
	jQuery("#it-dialog-iframe", top.document).load(
		function(e) {
			show_hide_image_upload();
			jQuery("#show-image-upload-row").click(show_image_upload);
			
			jQuery('select').each( bim_show_hide_custom_options );
			jQuery('select').change( bim_show_hide_custom_options );
		}
	);
}

var show_image_upload = function() {
	jQuery("#image-already-uploaded-message").hide();
	jQuery("#image-upload-row").show();
	
	it_dialog_update_size();
}

function show_hide_image_upload() {
	if(jQuery("input[name='attachment']").val() == "") {
		jQuery("#image-upload-row").show();
	}
	else {
		 jQuery("#image-already-uploaded-message").show();
	}
	
	it_dialog_update_size();
}

var bim_show_hide_custom_options = function() {
	name = jQuery(this).attr('name') + '-options';
	options_container = jQuery('#' + name);
	
	if ( 0 == options_container.length )
		return;
	
	options_container = options_container[0];
	
	if ( 'custom' == jQuery(this).val() )
		options_container.show();
	else
		options_container.hide();
	
	it_dialog_update_size();
}
