<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-11-23 - Chris Jean
		Release-ready
*/


if ( ! class_exists( 'BuilderWidgetContentSettings' ) ) {
	class BuilderWidgetContentSettings {
		function BuilderWidgetContentSettings() {
			add_action( 'builder_theme_features_loaded' , array( $this, 'init' ) );
			
			add_filter( 'builder_filter_theme_settings_defaults', array( $this, 'filter_default_theme_settings' ) );
		}
		
		function init() {
			builder_add_settings_editor_box( __( 'Widget Content', 'it-l10n-Builder-Cohen' ), array( $this, 'render_settings' ), array( 'priority' => 'low' ) );
		}
		
		function filter_default_theme_settings( $defaults ) {
			$new_defaults = array(
				'widget_content_the_content_filter' => 'no',
				'widget_content_edit_link'          => 'yes',
			);
			
			$defaults = ITUtility::merge_defaults( $defaults, $new_defaults );
			
			return $defaults;
		}
		
		function render_settings( $form ) {
			
?>
	<p><?php printf( __( 'By using the same editor offered for posts and pages, <a href="%s">Widget Content</a> allows for easy creation and management of widget content that has images, links, lists, and other complex elements.', 'it-l10n-Builder-Cohen' ), admin_url( 'edit.php?post_type=widget_content' ) ); ?></p>
	<p><?php _e( 'The following options can help make this feature work better on your site.', 'it-l10n-Builder-Cohen' ); ?></p>
	<hr />
	
	<p><?php printf( __( 'Most displayed post and page content is filtered by the <a href="%s"><code>the_content</code></a> filter in order for the content to display properly. Unfortunately, the Widget Content feature cannot use this filter to properly format the content by default. This is due to how many plugins, such as ones that add sharing links, will use this filter to add content modifications. Since these additions are typically undesireable for Widget Content output, the individual steps to prepare the content for display are applied manually.', 'it-l10n-Builder-Cohen' ), 'http://codex.wordpress.org/Plugin_API/Filter_Reference/the_content' ); ?></p>
	<p><?php _e( 'The following options allow you to decide whether or not the <code>the_content</code> filter or the manual formatting process should be used for Widget Content entries.', 'it-l10n-Builder-Cohen' ); ?></p>
	<ul class="no-bullets">
		<li><label for="widget_content_the_content_filter-no"><?php $form->add_radio( 'widget_content_the_content_filter', array( 'value' => 'no' ) ); ?> <?php _e( 'Use Builder\'s manual formatting to format Widget Content entries. (default)', 'it-l10n-Builder-Cohen' ); ?></label></li>
		<li><label for="widget_content_the_content_filter-yes"><?php $form->add_radio( 'widget_content_the_content_filter', array( 'value' => 'yes' ) ); ?> <?php _e( 'Use the <code>the_content</code> filter to format Widget Content entries.', 'it-l10n-Builder-Cohen' ); ?></label></li>
	</ul>
	<br />
	
	<p><?php _e( 'An "Edit this entry" link can be shown at the bottom of Widget Content entries to make it easier to quickly edit entries. This link will only be visible to logged in users that have rights to edit the content. Use the following option to enable or disable this feature.', 'it-l10n-Builder-Cohen' ); ?></p>
	<ul class="no-bullets">
		<li><label for="widget_content_edit_link-yes"><?php $form->add_radio( 'widget_content_edit_link', array( 'value' => 'yes' ) ); ?> <?php _e( 'Enable Widget Content edit entry link. (default)', 'it-l10n-Builder-Cohen' ); ?></label></li>
		<li><label for="widget_content_edit_link-no"><?php $form->add_radio( 'widget_content_edit_link', array( 'value' => 'no' ) ); ?> <?php _e( 'Disable Widget Content edit entry link.', 'it-l10n-Builder-Cohen' ); ?></label></li>
	</ul>
<?php
			
		}
	}
	
	new BuilderWidgetContentSettings();
}
