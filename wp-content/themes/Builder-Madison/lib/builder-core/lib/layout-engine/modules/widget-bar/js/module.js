var widget_bar_dynamically_updated = false;

var update_widget_bar_percent_widths_var = function() {
	var win = window.dialogArguments || opener || parent || top;
	
	var id = jQuery("input[name='id']").val();
	
	var input_val = jQuery("#percent-width-container-" + jQuery("select[name='type']").val()).html();
	jQuery("#widget_percents_row select").replaceWith(input_val);
	jQuery("#widget_percents_row select").attr("name", "widget_percents");
	
	jQuery("select[name='widget_percents']").change(update_widget_percents);
	update_widget_percents();
	
	if(!widget_bar_dynamically_updated) {
		jQuery("select[name='widget_percents']").val(win.jQuery("input[name='module-" + id + "-widget_percents']").val());
		widget_bar_dynamically_updated = true;
	}
	else {
		jQuery("input[name='custom_widget_percents']").val(jQuery("#percent-width-" + jQuery("select[name='type']").val() + " option:first").val());
	}
}

function init_widget_bar_editor() {
	jQuery("#it-dialog-iframe", top.document).load(
		function(e) {
			if('undefined' !== typeof(jQuery)) {
				jQuery("select[name='type']").change(update_widget_bar_percent_widths_var);
				
				if(jQuery("input[name='had_error']").val() != 1) {
					update_widget_bar_percent_widths_var();
				}
				else {
					widget_bar_dynamically_updated = true;
				}
				
				jQuery("select[name='widget_percents']").change(update_widget_percents);
				update_widget_percents();
			}
		}
	);
}

var update_widget_percents = function() {
	if(jQuery("select[name='widget_percents']").val() == "custom") {
		jQuery("#widget-percents-custom").show();
	}
	else {
		jQuery("#widget-percents-custom").hide();
	}
	
	it_dialog_update_size();
}
