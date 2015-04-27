jQuery(document).ready( function($) {
	postboxes.add_postbox_toggles('builder_seo', {} );
} );


var builder_seo_options_init = new Object();

jQuery(document).ready(
	function() {
		jQuery(".hndle").removeClass("hndle").addClass("hndl");
		
		
		builder_seo_show_hide_elements("#show_editor_information", ".information");
		builder_seo_show_hide_elements("#show_editor_advanced", ".advanced");
		
		builder_seo_show_hide_toggle("#title_type", "titles-options-group", "-titles-options-group");
		builder_seo_show_hide_toggle("#simple_title_separator", "title-separator-group", "-title-separator");
		
		builder_seo_main_settings_toggle("description");
		builder_seo_main_settings_toggle("title");
		builder_seo_main_settings_toggle("robots");
		builder_seo_main_settings_toggle("indexing", "custom");
		builder_seo_main_settings_toggle("keywords");
		
		jQuery(".show-hide-options").each(
			function() {
				builder_seo_show_hide_options(jQuery(this).attr("name"));
			}
		);
		
		jQuery(".show-hide-options").change(
			function() {
				builder_seo_show_hide_options(jQuery(this).attr("name"));
			}
		);
		
		
		jQuery(".title-views-options .show-sub-views").each(
			function() {
				builder_seo_show_hide_title_sub_views(jQuery(this).attr("name"));
			}
		);
		
		jQuery(".title-views-options .show-sub-views").change(
			function() {
				builder_seo_show_hide_title_sub_views(jQuery(this).attr("name"));
			}
		);
		
		
		jQuery("#indexing-options .show-sub-views").each(
			function() {
				builder_seo_show_hide_robots_sub_views(jQuery(this).attr("name"));
			}
		);
		
		jQuery("#indexing-options .show-sub-views").change(
			function() {
				builder_seo_show_hide_robots_sub_views(jQuery(this).attr("name"));
			}
		);
	}
);

function builder_seo_show_hide_elements(input, selector) {
	if(builder_seo_options_init[input] != true) {
		jQuery(input).change(
			function() {
				builder_seo_show_hide_elements(input, selector);
			}
		);
		
		builder_seo_options_init[input] = true;
	}
	
	var checked = jQuery(input + ":checked").length;
	
	if(checked > 0)
		jQuery(selector).show();
	else
		jQuery(selector).hide();
}

function builder_seo_show_hide_toggle(input, toggle_class, postfix) {
	if(builder_seo_options_init[input] != true) {
		jQuery(input).change(
			function() {
				builder_seo_show_hide_toggle(input, toggle_class, postfix);
			}
		);
		
		builder_seo_options_init[input] = true;
	}
	
	var value = jQuery(input).val();
	
	jQuery("." + toggle_class).hide();
	
	if(/^[a-zA-Z0-9\-_]+$/.test(value))
		jQuery("#" + value + postfix).show();
}

function builder_seo_main_settings_toggle(type, match) {
	match = (typeof(match) !== 'undefined') ? match : false;
	
	if(builder_seo_options_init[type] != true) {
		jQuery("#" + type + "_setting").change(
			function() {
				builder_seo_main_settings_toggle(type, match);
			}
		);
		
		builder_seo_options_init[type] = true;
	}
	
	var value = jQuery("#" + type + "_setting").val();
	
	if(((match === false) && (value != "")) || (value === match))
		jQuery("#" + type + "-options").show();
	else
		jQuery("#" + type + "-options").hide();
}

function builder_seo_show_hide_options(name) {
	var matches = name.match(/^enable_(.+)/);
	var selector = "#" + matches[1] + '-options';
	
	var checked = jQuery("input[name='" + name + "']:checked").length;
	
	if(checked > 0)
		jQuery(selector).show();
	else
		jQuery(selector).hide();
}

function builder_seo_show_hide_robots_sub_views(name) {
	var matches = name.match(/^customize_robots_sub_views_(.+)/);
	var selector = "#robots-overrides-" + matches[1];
	
	var checked = jQuery("input[name='" + name + "']:checked").length;
	
	if(checked > 0)
		jQuery(selector).show();
	else
		jQuery(selector).hide();
}

function builder_seo_show_hide_title_sub_views(name) {
	var matches = name.match(/^customize_title_sub_views_(.+)/);
	var selector = "#title-overrides-" + matches[1];
	
	var checked = jQuery("input[name='" + name + "']:checked").length;
	
	if(checked > 0)
		jQuery(selector).show();
	else
		jQuery(selector).hide();
}
