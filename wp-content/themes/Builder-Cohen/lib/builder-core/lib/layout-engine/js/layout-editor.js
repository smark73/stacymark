var builder_edit_layout_reminder = false;

var builder_do_edit_layout_reminder = function() {
	if(builder_edit_layout_reminder) {
		return;
	}
	
	window.onbeforeunload = function () { return 'You must click the "Save Layout" or "Save Layout and Continue Editing" buttons in order to save your changes.'; };
	
	builder_edit_layout_reminder = true;
}

var builder_clear_edit_layout_reminder = function() {
	window.onbeforeunload = null;
	
	builder_edit_layout_reminder = false;
}


function load_module_data(id) {
	var win = window.dialogArguments || opener || parent || top;
	
	if('%id' === String(id)) {
		id = win.jQuery("input[name='next-id']").val() - 1;
		
		jQuery("input[name='id']").val(id);
	}
	
	win.jQuery("input[name^='module-" + id + "-']").each(
		function(e) {
			var matches = jQuery(this).attr("name").match(/^module-\d+-(.+)/);
			var name = matches[1];
			var value = jQuery(this).val();
			var type = jQuery("[name='" + name + "']").attr("type");
			
			if('checkbox' === String(type)) {
				if('' !== String(value)) {
					jQuery("[name='" + name + "']").attr('checked', true);
				}
				else {
					jQuery("[name='" + name + "']").attr('checked', false);
				}
			}
			else {
				jQuery("[name='" + name + "']").val(value);
			}
		}
	);
	
	if('custom' === String(win.jQuery("select[name='width']").val())) {
		jQuery("input[name='layout_width']").val(win.jQuery("input[name='custom_width']").val());
	}
	else {
		jQuery("input[name='layout_width']").val(win.jQuery("select[name='width']").val());
	}
}

function save_module_data(id) {
	var win = window.dialogArguments || opener || parent || top;
	
	jQuery("input[name^='module-" + id + "-']").each(
		function(e) {
			var name = jQuery(this).attr("name");
			var value = jQuery(this).val();
			
			win.jQuery("input[name='" + name + "']").val(value);
		}
	);
	
	
	jQuery( function() {
		it_dialog_remove();
	} );
}

function add_module_screen_hide_maxed_modules() {
	var win = window.dialogArguments || opener || parent || top;
	
	jQuery( function() {
		var module_counts = win.count_layout_modules();
		
		win.jQuery("input[name^='module-max-']").each(
			function() {
				var matches = jQuery(this).attr("name").match(/^module-max-(.+)/);
				var module = matches[1];
				
				if(('undefined' !== typeof(module_counts[module])) && (0 !== parseInt(jQuery(this).attr("value"), 10)) && (parseInt(jQuery(this).attr("value") - module_counts[module], 10) <= 0)) {
					jQuery(".add-module-" + module).remove();
				}
			}
		);
		
		jQuery("input[name='module']:first").attr("checked", "1");
	} );
}

function count_layout_modules() {
	var modules = new Object();
	
	jQuery(".preview-container div[class^='module-']").each(
		function() {
			if(!jQuery(this).attr("id").match(/%id%/)) {
				var matches = jQuery(this).attr("class").match(/^module-(.+)/);
				var module = matches[1];
				
				if('undefined' === typeof(modules[module])) {
					modules[module] = 1;
				}
				else {
					modules[module]++;
				}
			}
		}
	);
	
	return modules;
}


function init_layout_listing() {
	jQuery(document).ready(
		function() {
			jQuery(".bulk-action-submit").click( builder_editor_confirm_bulk_action );
		}
	);
}

var builder_editor_confirm_bulk_action = function() {
	if ( 'submit_bulk_action_1' == jQuery(this).attr('name') )
		action = jQuery('#bulk_action_1').val();
	else if ( 'submit_bulk_action_2' == jQuery(this).attr('name') )
		action = jQuery('#bulk_action_2').val();
	else
		return true;
	
	if ( 'delete_layout' != action )
		return true;
	
	return confirm( 'Please confirm that you would like to delete the selected Layouts.' );
}


function init_layout_editor() {
	jQuery(document).ready(
		function() {
			jQuery("select[name='width']").change( update_layout_width );
			update_layout_width();
			update_module_links();
			setup_background_options();
			
			jQuery("select").change( builder_do_edit_layout_reminder );
			jQuery("input[type='text']").change( builder_do_edit_layout_reminder );
			jQuery("input[type='submit']").click( builder_clear_edit_layout_reminder );
			
			if ( 'undefined' != typeof( builder_extension_details ) ) {
				builder_show_hide_extension_details();
				jQuery("#extension").change( builder_show_hide_extension_details );
			}
		}
	);
}

var update_layout_width = function() {
	if('custom' === String(jQuery("select[name='width']").val())) {
		jQuery("#layout-width-custom").show();
	}
	else {
		jQuery("#layout-width-custom").hide();
	}
}

function remove_module(position) {
	jQuery(".module-position[value='" + position + "']").parents(".module-row").prev().remove();
	jQuery(".module-position[value='" + position + "']").parents(".module-row").remove();
	
	jQuery(".module-position").each(
		function(e) {
			if(parseInt(jQuery(this).val(), 10) > parseInt(position, 10)) {
				jQuery(this).val(jQuery(this).val() - 1);
			}
		}
	);
	
	jQuery("input[name='next-position']").val(jQuery("input[name='next-position']").val() - 1);
	
	var row_count = jQuery(".layout-modules > tbody > tr").not(".add-module-help").size();
	
	if(1 === parseInt(row_count, 10)) {
		jQuery(".add-module-help").show();
	}
	else {
		jQuery(".add-module-help").hide();
	}
}

function remove_new_module() {
	var id = jQuery("input[name='next-id']").val() - 1;
	var position = jQuery("input[name='position-" + id + "']").val();
	
	remove_module(position);
}

function add_module(module_id, preview_image) {
	var position = jQuery("input[name='current-position']").val();
	var id = jQuery("input[name='next-id']").val();
	var added = false;
	
	var content = jQuery("#module-editor-" + module_id + " tbody:first").html();
	content = content.replace(/%id%/g, id);
	content = content.replace(/%position%/g, position);
	
	jQuery(".module-position").each(
		function(e) {
			if(!jQuery(this).val().match(/%position%/)) {
				if(parseInt(jQuery(this).val(), 10) === parseInt(position, 10)) {
					var row = jQuery(this).parents(".module-row").prev();
					row.before(content);
					
					added = true;
				}
				
				if(parseInt(jQuery(this).val(), 10) >= parseInt(position, 10)) {
					jQuery(this).val(parseInt(jQuery(this).val(), 10) + 1);
				}
			}
		}
	);
	
	if(!added) {
		if('undefined' === typeof(jQuery(".layout-modules > tbody"))) {
			jQuery(".layout-modules").html(content);
		}
		else {
			jQuery(".layout-modules > tbody").append(content);
		}
	}
	
	update_preview_image( id, preview_image );
	update_module_name( id );
	update_module_links();
	
	
	jQuery("input[name='next-position']").val(parseInt(jQuery("input[name='next-position']").val(), 10) + 1);
	jQuery("input[name='next-id']").val(parseInt(jQuery("input[name='next-id']").val(), 10) + 1);
}

function update_module_links() {
	var selfLink = jQuery("input[name='self-link']").val();
	var baseLink = jQuery("#base_url").val();
	var layout = jQuery("input[name='layout']").val();
	
	jQuery(".module-links").each(
		function(e) {
			if(!(jQuery(this).parents(".module-row").find("input.module-position").attr("name").match(/%id%/))) {
				jQuery(this).html("");
				
				var module = jQuery(this).parents(".module-row").find("input.module-var").val();
				var moduleName = jQuery("input[name='module-name-" + module + "']").val();
				var moduleEditable = jQuery("input[name='module-editable-" + module + "']").val();
				
				var matches = jQuery(this).parents(".module-row").find("input.module-var").attr("name").match(/^module-(\d+)$/);
				var id = matches[1];
				
				jQuery(this).append('<div>');
				
				if("1" == moduleEditable) {
					jQuery(this).append('<a class="modify-module-link it-dialog" href="' + selfLink + '&modify_module_settings=' + module + '&id=' + id + '&render_clean=dialog&it-dialog-max-width=650&it-dialog-modal=true&it-dialog-prevent-shrink=true" title="Modify ' + moduleName + ' Settings">Modify Settings</a> | ');
				}
				
				jQuery(this).append('<a class="remove-module-link" href="#">Remove Module</a>');
				jQuery(this).append('</div>');
			}
		}
	);
	
	jQuery(".add-module-link-row").remove();
	
	var url = selfLink + "&add_module_screen=1&layout=" + layout + "&render_clean=dialog&it-dialog-max-width=650&it-dialog-modal=true&it-dialog-prevent-shrink=true";
	var addLink = '<tr class="add-module-link-row"><td style="text-align:center;height:20px;padding:0px;margin:0px;background:top center no-repeat url(\'' + baseLink + '/images/add-module.gif\');"><a class="add-module-link it-dialog" style="text-decoration:none;display:block;" href="' + url + '" title="Add a Module to the Layout">Add Module</a></td><td></td></tr>' + "\n";
	
	jQuery(".layout-modules > tbody > tr").not(".add-module-help").each(
		function(e) {
			jQuery(this).before(addLink);
		}
	);
	
	jQuery(".layout-modules").append(addLink);
	
	
	jQuery(".modify-module-link").click(builder_do_edit_layout_reminder);
	
	jQuery(".remove-module-link").click(
		function(e) {
			remove_module(jQuery(this).parents(".module-row").find("input.module-position").val());
			builder_do_edit_layout_reminder();
			
			return false;
		}
	);
	
	jQuery(".add-module-link").click(
		function(e) {
			var position = jQuery(this).parents(".add-module-link-row").prev().find("input.module-position").val();
			
			if('undefined' === typeof(position)) {
				position = 1;
			}
			else {
				position = parseInt(position, 10) + 1;
			}
			
			jQuery("input[name='current-position']").val(position);
			
			builder_do_edit_layout_reminder();
		}
	);
	
	var row_count = jQuery(".layout-modules > tbody > tr").not(".add-module-help").size();
	
	if(1 === parseInt(row_count, 10)) {
		jQuery(".add-module-help").show();
	}
	else {
		jQuery(".add-module-help").hide();
	}
}

function update_preview_image( id, image_url ) {
	jQuery("#module-" + id + "-preview").html('<img src="' + image_url + '" />');
}

function update_module_name( id ) {
	var name = jQuery('[name="module-' + id + '-name"]').val();
	var module = jQuery( '[name="module-' + id + '"]').val();
	var module_name = module.replace( '-', ' ').replace( /(^|\s)([a-z])/g , function(m, p1, p2){ return p1 + p2.toUpperCase(); } );
	
	if ( '' == name )
		name = module_name;
	
	jQuery('#module-' + id + ' .module-name').html( name );
}

function setup_background_options() {
	jQuery("#show_hide_customize_background").click(
		function(e) {
			jQuery("#customize_background_container").toggle();
			
			if('none' === String(jQuery("#customize_background_container").css("display"))) {
				jQuery("#show_hide_customize_background").html("Show Background Customization Options");
			}
			else {
				jQuery("#show_hide_customize_background").html("Hide Background Customization Options");
			}
		}
	);
}

function init_modify_view_screen() {
	jQuery(document).ready(
		function(e) {
			update_view_options();
			jQuery("select[name='view']").change(update_view_options);
			
			if ( 'undefined' != typeof( builder_extension_details ) ) {
				builder_show_hide_extension_details();
				jQuery("#extension").change( builder_show_hide_extension_details );
			}
		}
	);
}

function init_modify_views() {
	refresh_view_rows();
}

function refresh_view_rows() {
	if(jQuery("#views-table > tbody > tr").size() > 0) {
		jQuery("#views-container").show();
		jQuery("#no-views-container").hide();
	}
	else {
		jQuery("#no-views-container").show();
		jQuery("#views-container").hide();
	}
	
	jQuery("tr[id^='view-']:even").addClass("alternate");
	jQuery("tr[id^='view-']:odd").removeClass("alternate");
}

var update_archive_options = function() {
	var view = jQuery("select[name='view']").val();
	
	if('is_category' === String(view))
		jQuery("#category-options").show();
	else
		jQuery("#category-options").hide();
	
	if('is_tag' === String(view))
		jQuery("#tag-options").show();
	else
		jQuery("#tag-options").hide();
	
	if('is_author' === String(view))
		jQuery("#author-options").show();
	else
		jQuery("#author-options").hide();
}

var update_view_options = function() {
	var view = parse_view_id(jQuery("select[name='view']").val());
	var text = jQuery("#view-" + view).html();
	
	if ( null != text )
		text = '<p>' + text + '</p>';
	
	jQuery("#view-description").html( text );
	
	
	view = jQuery("select[name='view']").val();
	
	if('is_category' === String(view))
		jQuery("#category-options").show();
	else
		jQuery("#category-options").hide();
	
	if('is_tag' === String(view))
		jQuery("#tag-options").show();
	else
		jQuery("#tag-options").hide();
	
	if('is_author' === String(view))
		jQuery("#author-options").show();
	else
		jQuery("#author-options").hide();
	
	if('is_single' === String(view))
		jQuery("#post-category-options").show();
	else
		jQuery("#post-category-options").hide();
	
	
	it_dialog_update_size();
}

function add_view(data) {
	var added = false;
	
	var parsed_view_id = parse_view_id(data['view_id']);
	
	var content = jQuery("#new-view-container tbody:first").html();
	
	content = content.replace( /%parsed_view_id%/g, parsed_view_id );
	
	for ( field in data ) {
		regex = new RegExp( '%' + field + '%', 'g' );
		content = content.replace( regex, data[field] );
	}
	
	jQuery(".view-entry").each(
		function( e ) {
			if( ! added && ! jQuery(this).find( ".view-name" ).html().match( /%view_name%/ ) ) {
				if( String( jQuery(this).find( ".view-name" ).html() ) > String( data['view_name'] ) ) {
					jQuery(this).before( content );
					
					added = true;
				}
			}
		}
	);
	
	
	if( ! added ) {
		if( 'undefined' === typeof( jQuery( "#views-table > tbody" ) ) ) {
			jQuery("#views-table").html( content );
		}
		else {
			jQuery("#views-table > tbody").append( content );
		}
	}
	
	
	jQuery('[href$="&layout="]').add('[href$="&layout=//INHERIT//"]').each(
		function() {
			jQuery(this).replaceWith( jQuery(this).html() );
		}
	);
	
	
	var origColor = jQuery("#view-" + parsed_view_id).css( "background-color" );
	jQuery("#view-" + parsed_view_id).show();
	
	refresh_view_rows();
}

function remove_view(view) {
	jQuery("#view-" + parse_view_id( view )).remove();
	
	refresh_view_rows();
}

function parse_view_id(view) {
	view = view.replace( /\~/g, '-' );
	view = view.replace( /&/g, '-' );
	view = view.replace( /\|/g, '_' );
	return view;
}

var builder_show_hide_extension_details = function() {
	var extension = jQuery('#extension').val();
	var extension_details = jQuery('#extension-details');
	
	if ( '' == extension ) {
		extension_details.hide();
		extension_details.html();
	}
	else {
		extension_details.html( builder_extension_details[extension] );
		extension_details.show();
	}
	
	if ( 'undefined' != typeof it_dialog_update_size )
		it_dialog_update_size();
}
