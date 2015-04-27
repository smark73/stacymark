var module_dynamically_updated = false;

var update_module_sidebar_widths_var = function() {
	var win = window.dialogArguments || opener || parent || top;
	
	var id = jQuery("input[name='id']").val();
	
	var input_val = jQuery("#sidebar-widths-container-" + jQuery("select[name='sidebar']").val()).html();
	if(null !== input_val) {
		jQuery("#sidebar-widths-row select").replaceWith(input_val);
		jQuery("#sidebar-widths-row select").attr("name", "sidebar_widths");
	}
	
	jQuery("select[name='sidebar_widths']").change(update_sidebar_widths);
	update_sidebar_widths();
	
	if(!module_dynamically_updated) {
		jQuery("select[name='sidebar_widths']").val(win.jQuery("input[name='module-" + id + "-sidebar_widths']").val());
		module_dynamically_updated = true;
	}
	else {
		jQuery("input[name='custom_sidebar_widths']").val(jQuery("#sidebar-widths-" + jQuery("select[name='sidebar']").val() + " option:first").val());
	}
	
	if(jQuery("select[name='sidebar']").val() == "none") {
		jQuery("#sidebar-widths-row").hide();
	}
	else {
		jQuery("#sidebar-widths-row").show();
	}
	
	it_dialog_update_size();
};

function init_module_sidebar_editor() {
	jQuery("#it-dialog-iframe", top.document).load(
		function(e) {
			jQuery("select[name='sidebar']").change(update_module_sidebar_widths_var);
			
			if(jQuery("input[name='had_error']").val() != 1) {
				update_module_sidebar_widths_var();
			}
			else {
				module_dynamically_updated = true;
			}
			
			jQuery("select[name='sidebar_widths']").change(update_sidebar_widths);
			update_sidebar_widths();
		}
	);
}

var update_sidebar_widths = function() {
	if(jQuery("select[name='sidebar_widths']").val() == "custom") {
		jQuery("#sidebar-widths-custom").show();
	}
	else {
		jQuery("#sidebar-widths-custom").hide();
	}
	
	it_dialog_update_size();
};
