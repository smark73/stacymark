<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.2

Version History
	1.0.0 - 2013-08-08 - Chris Jean
		Initial version.
	1.0.1 - 2013-08-12 - Chris Jean
		Changed " - Child" default child theme name suffix to " - Custom".
	1.1.0 - 2013-08-15 - Chris Jean
		Restructured entire page.
	1.1.1 - 2013-10-21 - Chris Jean
		Added details to the Layouts and Views import page to indicate that using the "Use the Layouts and Views..." option replace the current Layouts and Views.
		Added a confirmation dialog that appears if a user tries to replace their Layouts and Views in order to confirm that they wish to do so.
		Fixed broken logic that determined when to run the Layout and Views replacement code.
	1.1.2 - 2013-12-02 - Chris Jean
		Updated calls to screen_icon() to ITUtility::screen_icon().
*/


class ITThemeSettingsTabSetup extends ITThemeSettingsTab {
	var $_var = 'theme-settings-setup';
	
	
	function screen_settings( $settings, $screen ) {
		return $settings;
//		return 'This is the setup tab\'s settings.';
	}
	
	function _init() {
		require_once( builder_main_get_builder_core_path() . '/lib/setup/init.php' );
		
		if ( ! empty( $_POST ) )
			$this->_handle_post_data();
	}
	
	function add_admin_scripts() {
		$var = "{$this->_var}-setup-tab-script";
		
		$translations = array(
			'confirm_dialog_text' => __( 'Using the "Use the Layouts and Views included with the theme" option will cause all of your current Layout and Views to be removed and replaced with the ones from the theme. Please confirm that you wish to do this.', 'it-l10n-Builder-Paige' ),
		);
		
		wp_enqueue_script( $var, "{$this->_parent->_plugin_url}/js/setup.js", array( 'jquery' ) );
		wp_localize_script( $var, 'builder_setup_tab', $translations );
	}
	
	function contextual_help( $text, $screen ) {
		return $text;
		
		
		ob_start();
		
?>
<p><?php _e( 'This Settings page helps you control global settings for your Builder theme. To make working with the options easier, groups of options are divided into tabs.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php _e( 'Each tab is divided into sections that can be collapsed by clicking on the title bar of the section. You can also rearrange the sections by dragging them by the title bar. When you collapse and rearrange items, their arrangement will be remembered.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php _e( 'The Setup tab includes a mix of many different kinds of options that can help you quickly configure your Builder theme. Options include configuring setup menus, changing how Builder identifies widget areas, modifying comment functionality on your site, and enabling/disabling different Builder features. Some child themes add custom options in their own section to this tab. Each section describes its options in further details.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php _e( 'The Import/Export tab allows you to export and import Builder settings, including Layouts and Views. Please visit that tab for more details.', 'it-l10n-Builder-Paige' ); ?></p>
<?php
		
		$text = ob_get_contents();
		ob_end_clean();
		
		return $text;
	}
	
	function _editor() {
		$this->_index();
	}
	
	function _handle_post_data() {
		if ( ! empty( $_POST['theme_layouts'] ) ) {
			$this->_import_theme_layouts();
			
			wp_redirect( 'admin.php?page=ithemes-builder-theme' );
		}
		
		if ( ! empty( $_POST['create_child_theme'] ) )
			$this->_create_child_theme();
		
		
		if ( 'finish' == $_POST['step'] )
			wp_redirect( 'admin.php?page=ithemes-builder-theme' );
	}
	
	function _create_child_theme() {
		$args = array();
		
		
		if ( ! empty( $_POST['child_theme_option'] ) )
			$action = $_POST['child_theme_option'];
		else
			$action = 'create';
		
		
		if ( 'create' == $action ) {
			if ( ! empty( $_POST['child_theme_name'] ) )
				$args['name'] = $_POST['child_theme_name'];
			
			if ( ! empty( $_POST['child_theme_type'] ) && in_array( $_POST['child_theme_type'], array( 'copy', 'parent' ) ) ) {
				if ( 'copy' == $_POST['child_theme_type'] )
					$args['source_directory'] = get_stylesheet_directory();
				
				$args['source_type'] = $_POST['child_theme_type'];
			}
			
			builder_create_child_theme( $args );
		}
		else {
			add_option( 'builder_manually_switched_theme', true );
			
			switch_theme( $_POST['child_theme'] );
		}
	}
	
	function _import_theme_layouts() {
/*		if ( ! empty( $_POST['layouts_import_method'] ) && in_array( $_POST['layouts_import_method'], array( 'add', 'replace' ) ) )
			$method = $_POST['layouts_import_method'];
		else
			$method = 'add';
		
		builder_import_theme_default_layouts_and_views( $method, $method, 'replace' );*/
		
		
		if ( empty( $_POST['theme_layouts'] ) || ( 'use' !== $_POST['theme_layouts'] ) )
			return;
		
		builder_import_theme_default_layouts_and_views( 'replace', 'replace', 'replace' );
	}
	
	function _index() {
		if ( ! empty( $_REQUEST['step'] ) ) {
			if ( 'child_theme' == $_REQUEST['step'] )
				$this->_show_child_theme_options();
			else if ( 'layouts_and_views' == $_REQUEST['step'] )
				$this->_show_layouts_and_views_options();
		}
		else {
			$this->_show_child_theme_options();
		}
	}
	
	function _show_child_theme_options() {
		$options = array(
			'child_theme_type'   => 'parent',
			'child_theme_option' => 'activate',
			'theme_activation'   => ( ! empty( $_REQUEST['theme_activation'] ) ) ? 1 : '',
			'fresh_install'      => ( ! empty( $_REQUEST['fresh_install'] ) ) ? 1 : '',
			'step'               => 'layouts_and_views',
		);
		
		if ( get_transient( 'builder_fresh_install' ) ) {
			$options['step'] = 'finish';
			delete_transient( 'builder_fresh_install' );
		}
		
		
		$themes = wp_get_themes();
		
		$names = array();
		
		foreach ( $themes as $theme )
			$names[] = $theme->get( 'Name' );
		
		$options['child_theme_name'] = sprintf( __( '%s - Custom', 'it-l10n-Builder-Paige' ), $themes[basename( get_template_directory() )]->get( 'Name' ) );
		
		if ( in_array( $options['child_theme_name'], $names ) ) {
			$count = 2;
			
			while( in_array( "{$options['child_theme_name']} $count", $names ) )
				$count ++;
			
			$options['child_theme_name'] = "{$options['child_theme_name']} $count";
		}
		
		
		$logo_url = ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/builder-logo.png' );
		
		
		$child = wp_get_theme( get_stylesheet() );
		
		if ( get_template() == get_stylesheet() ) {
			$child_theme_objects = builder_get_child_themes();
			$child_themes = array();
			
			foreach ( $child_theme_objects as $theme )
				$child_themes[$theme->get_stylesheet()] = $theme->get( 'Name' );
			
			
			if ( empty( $child_themes ) )
				$type = 'create_child';
			else
				$type = 'select_or_create_child';
			
			$parent = $child;
		}
		else {
			$type = 'child_info';
			
			$parent = wp_get_theme( get_template() );
		}
		
		
		$form = new ITForm( $options );
		$this->_form =& $form;
		
		
?>
	<div class="wrap">
		<?php $form->start_form(); ?>
			<?php if ( empty( $_REQUEST['theme_activation'] ) ) : ?>
				<?php ITUtility::screen_icon(); ?>
				
				<?php $this->_print_editor_tabs(); ?>
			<?php endif; ?>
			
			<div class="it-brochure-box">
				<img class="it-logo" src="<?php echo $logo_url; ?>" alt="Builder Logo" />
				
				<h1>Builder Setup</h1>
				
				<?php if ( 'child_info' == $type ) : ?>
					<div class="it-notice"><?php printf( __( 'You are currently running a custom Builder child theme called <strong>%1$s</strong> created for the <strong>%2$s</strong> theme. All customizations should be made to this custom theme.', 'it-l10n-Builder-Paige' ), $child->get( 'Name' ), $parent->get( 'Name' ) ); ?></div>
					<br />
					
					<div class="it-input-set">
						<?php if ( 'finish' == $options['step'] ) : ?>
							<?php $form->add_submit( 'ignore', array( 'value' => __( 'Continue', 'it-l10n-Builder-Paige' ) ) ); ?>
						<?php else : ?>
							<?php $form->add_submit( 'ignore', array( 'value' => __( 'Continue to Layouts and Views Setup', 'it-l10n-Builder-Paige' ) ) ); ?>
						<?php endif; ?>
					</div>
				<?php elseif ( 'create_child' == $type ) : ?>
					<p><?php printf( __( 'To create a <a href="%1$s">child theme</a> that you can customize, choose a name below and click the <em>Create My Child Theme</em> button.', 'it-l10n-Builder-Paige' ), 'http://codex.wordpress.org/Child_Themes' ); ?></p>
					
					<div class="it-input-set">
						<label for="child_theme_name"><?php _e( 'Your child theme name', 'it-l10n-Builder-Paige' ); ?></label>
						<?php ITUtility::add_tooltip( __( 'Use a descriptive name that will help you identify your theme\'s name.', 'it-l10n-Builder-Paige' ) ); ?>
						<br />
						
						<?php $form->add_text_box( 'child_theme_name', array( 'class' => 'regular-text' ) ); ?>
					</div>
					
					<div class="it-input-set">
						<?php $form->add_submit( 'create_child_theme', array( 'value' => __( 'Create My Child Theme', 'it-l10n-Builder-Paige' ) ) ); ?>
						
						<?php if ( empty( $GLOBALS['builder_fresh_install'] ) ) : ?>
							<p><a class="it-skip" href="<?php echo admin_url( 'admin.php?page=theme-settings&editor_tab=setup&step=layouts_and_views' ); ?>"><?php _e( 'Skip to next step', 'it-l10n-Builder-Paige' ); ?></a></p>
						<?php else: ?>
							<p><a class="it-skip" href="<?php echo admin_url( 'admin.php?page=ithemes-builder-theme' ); ?>"><?php _e( 'Skip', 'it-l10n-Builder-Paige' ); ?></a></p>
						<?php endif; ?>
					</div>
				<?php else : ?>
					<p><?php printf( _n( 'Your site has a <a href="%1$s">child theme</a> for the %2$s theme available. As it is recommended to always run a child theme rather than the parent theme, please either activate the existing child theme or create a new one using the options below.', 'Your site has <a href="%1$s">child themes</a> for the %2$s theme available. As it is recommended to always run a child theme rather than the parent theme, please either activate an existing child theme or create a new one using the options below.', count( $child_themes ), 'it-l10n-Builder-Paige' ), 'http://codex.wordpress.org/Child_Themes', $parent->get( 'Name' ) ); ?></p>
					
					<div class="it-shrink-wrap-box">
						<label><?php printf( __( '%1$s Activate an existing child theme', 'it-l10n-Builder-Paige' ), $form->add_radio( 'child_theme_option', array( 'value' => 'activate', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<br />
						
						<label><?php printf( __( '%1$s Create a new child theme', 'it-l10n-Builder-Paige' ), $form->add_radio( 'child_theme_option', array( 'value' => 'create', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<br />
						<br />
						
						<div class="it-options-child_theme_option it-options-child_theme_option-activate">
							<p><label for="child_theme"><?php _e( 'Select the theme to activate:', 'it-l10n-Builder-Paige' ); ?></label></p>
							<?php $form->add_drop_down( 'child_theme', $child_themes ); ?>
							<br />
							<br />
							<br />
							<?php $form->add_submit( 'create_child_theme', array( 'value' => __( 'Activate My Child Theme', 'it-l10n-Builder-Paige' ) ) ); ?>
						</div>
						
						<div class="it-options-child_theme_option it-options-child_theme_option-create">
							<p>
								<label for="child_theme_name"><?php _e( 'Your child theme name', 'it-l10n-Builder-Paige' ); ?></label>
								<?php ITUtility::add_tooltip( __( 'Use a descriptive name that will help you identify your theme.', 'it-l10n-Builder-Paige' ) ); ?>
							</p>
							
							<?php $form->add_text_box( 'child_theme_name', array( 'class' => 'regular-text' ) ); ?>
							<br />
							<br />
							<br />
							
							<?php $form->add_submit( 'create_child_theme', array( 'value' => __( 'Create My Child Theme', 'it-l10n-Builder-Paige' ) ) ); ?>
						</div>
						
						<?php if ( empty( $GLOBALS['builder_fresh_install'] ) ) : ?>
							<p><a class="it-skip" href="<?php echo admin_url( 'admin.php?page=theme-settings&editor_tab=setup&step=layouts_and_views' ); ?>"><?php _e( 'Skip to next step', 'it-l10n-Builder-Paige' ); ?></a></p>
						<?php else: ?>
							<p><a class="it-skip" href="<?php echo admin_url( 'admin.php?page=ithemes-builder-theme' ); ?>"><?php _e( 'Skip', 'it-l10n-Builder-Paige' ); ?></a></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
			
			
			<?php $form->add_hidden( 'theme_activation' ); ?>
			<?php $form->add_hidden( 'fresh_install' ); ?>
			<?php $form->add_hidden( 'step' ); ?>
			<?php $form->add_hidden_no_save( 'editor_tab', $this->_parent->_active_tab ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
		
	}
	
	function _show_layouts_and_views_options() {
		$options = array(
			'child_theme_type' => 'parent',
			'theme_layouts'    => 'ignore',
			'theme_activation' => ( ! empty( $_REQUEST['theme_activation'] ) ) ? 1 : '',
			'fresh_install'    => ( ! empty( $_REQUEST['fresh_install'] ) ) ? 1 : '',
			'step'             => 'finish',
		);
		
		
		$logo_url = ITUtility::get_url_from_file( dirname( __FILE__ ) . '/images/builder-logo.png' );
		
		
		$old_theme = ( isset( $GLOBALS['builder_old_theme'] ) ) ? $GLOBALS['builder_old_theme'] : false;
		$current_theme = wp_get_theme();
		
		if ( $old_theme && ( $old_theme->get_template() != $current_theme->get_template() ) )
			$template_switch = true;
		else
			$template_switch = false;
		
		
		$form = new ITForm( $options );
		$this->_form =& $form;
		
		
?>
	<div class="wrap">
		<?php $form->start_form( array( 'id' => 'it-builder-setup' ) ); ?>
			<?php if ( empty( $_REQUEST['theme_activation'] ) ) : ?>
				<?php ITUtility::screen_icon(); ?>
				
				<?php $this->_print_editor_tabs(); ?>
			<?php endif; ?>
			
			<div class="it-brochure-box">
				<img class="it-logo" src="<?php echo $logo_url; ?>" alt="Builder Logo" />
				
				<h1>Builder Setup</h1>
				
				
				<?php if ( $template_switch ) : ?>
					<p><?php _e( 'You are switching from a different Builder theme. Would you like to continue to use your current Layouts and Views? Or do you want to use the Layouts and Views included with this new theme?', 'it-l10n-Builder-Paige' ); ?></p>
					
					<div class="it-shrink-wrap-box">
						<label><?php printf( __( '%1$s Keep the site\'s current Layouts and Views', 'it-l10n-Builder-Paige' ), $form->add_radio( 'theme_layouts', array( 'value' => 'ignore', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<br />
						<label><?php printf( __( '%1$s Use the Layouts and Views included with the new theme', 'it-l10n-Builder-Paige' ), $form->add_radio( 'theme_layouts', array( 'value' => 'use', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<div class="it-options-theme_layouts it-options-theme_layouts-use">
							<p class="description"><?php _e( 'Important: This option will replace your current Layouts and Views with the Layouts and Views provided by the theme.', 'it-l10n-Builder-Paige' ); ?></p>
						</div>
						
	<!--				<div class="it-indent-box it-options-theme_layouts it-options-theme_layouts-use">
							<label><?php printf( __( '%1$s Keep current Layouts', 'it-l10n-Builder-Paige' ), $form->add_radio( 'layouts_import_method', 'add' ) ); ?></label>
							<br />
							<label><?php printf( __( '%1$s Remove current Layouts', 'it-l10n-Builder-Paige' ), $form->add_radio( 'layouts_import_method', 'replace' ) ); ?></label>
						</div>-->
					</div>
				<?php else : ?>
					<p><?php _e( 'Your theme provides a set of default Layouts and Views. You can update your site to match these provided Layouts and Views.', 'it-l10n-Builder-Paige' ); ?></p>
					
					<div class="it-shrink-wrap-box">
						<label><?php printf( __( '%1$s Keep the site\'s current Layouts and Views', 'it-l10n-Builder-Paige' ), $form->add_radio( 'theme_layouts', array( 'value' => 'ignore', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<br />
						<label><?php printf( __( '%1$s Use the Layouts and Views included with the theme', 'it-l10n-Builder-Paige' ), $form->add_radio( 'theme_layouts', array( 'value' => 'use', 'class' => 'show-hide-toggle' ) ) ); ?></label>
						<div class="it-options-theme_layouts it-options-theme_layouts-use">
							<p class="description"><?php _e( 'Important: This option will replace your current Layouts and Views with the Layouts and Views provided by the theme.', 'it-l10n-Builder-Paige' ); ?></p>
						</div>
						
	<!--				<div class="it-indent-box it-options-theme_layouts it-options-theme_layouts-use">
							<label><?php printf( __( '%1$s Keep current Layouts', 'it-l10n-Builder-Paige' ), $form->add_radio( 'layouts_import_method', 'add' ) ); ?></label>
							<br />
							<label><?php printf( __( '%1$s Remove current Layouts', 'it-l10n-Builder-Paige' ), $form->add_radio( 'layouts_import_method', 'replace' ) ); ?></label>
						</div>-->
					</div>
				<?php endif; ?>
				
				<div class="it-input-set">
					<div class="it-options-theme_layouts it-options-theme_layouts-ignore">
						<?php $form->add_submit( 'update_layouts_and_views', array( 'value' => 'Keep current Layouts and Views' ) ); ?>
					</div>
					
					<div class="it-options-theme_layouts it-options-theme_layouts-use">
						<?php $form->add_submit( 'update_layouts_and_views', array( 'value' => 'Update Layouts and Views' ) ); ?>
					</div>
					
					
					<p><a class="it-skip" href="<?php echo admin_url( 'admin.php?page=ithemes-builder-theme' ); ?>"><?php _e( 'Skip' ); ?></a></p>
				</div>
			</div>
			
			
			<?php $form->add_hidden( 'theme_activation' ); ?>
			<?php $form->add_hidden( 'fresh_install' ); ?>
			<?php $form->add_hidden( 'step' ); ?>
			<?php $form->add_hidden_no_save( 'editor_tab', $this->_parent->_active_tab ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
		
	}
}
