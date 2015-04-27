<?php

/*
Written by Chris Jean for iThemes.com
Version 2.6.2

Version History
	2.4.0 - 2012-08-13 - Chris Jean
		Fixed a bug that breaks Views that are set to use the Active Layout when
			the Layouts listing is shown.
		Added support for the new module function get_preview_image.
	2.5.0 - 2012-10-17 - Chris Jean
		Added version tracking to layouts. The version is incremented each time a change is saved.
	2.5.1 - 2012-12-03 - Chris Jean
		Fixed a logical error where a Layout's Widgets link would not be shown if "accessibility mode" for widgets were
			enabled.
	2.5.2 - 2013-01-09 - Chris Jean
		Fixed issues with the ITDialog not closing properly.
	2.5.3 - 2013-06-24 - Chris Jean
		Removed assign by reference.
		Added class of title to the h3 titles.
	2.5.4 - 2013-07-16 - Chris Jean
		Updated layout-meta-box.php location to point to new lib/main location.
	2.6.0 - 2013-08-09 - Chris Jean
		Changed priority of init hook to allow the after_switch_theme hook to fire first.
	2.6.1 - 2013-11-25 - Chris Jean
		Fixed how changing a specific View (a specific category View) to a generic View (all categories View) would result in the original View remaining with the addition of the new View.
	2.6.2 - 2013-12-02 - Chris Jean
		Updated screen_icon() to ITUtility::screen_icon().
*/


if ( ! class_exists( 'BuilderLayoutEditor' ) ) {
	class BuilderLayoutEditor extends ITCoreClass {
		var $_var = 'layout_settings';
		var $_page_title = '';
		var $_page_var = 'layout-editor';
		var $_menu_title = '';
		
		var $_modules = array();
		
		
		function BuilderLayoutEditor() {
			$this->_storage_version = builder_get_data_version( 'layout-settings' );
			
			$this->_page_title = _x( 'Manage Layouts and Views', 'page title', 'it-l10n-Builder-Cohen' );
			$this->_menu_title = _x( 'Layouts & Views', 'menu title', 'it-l10n-Builder-Cohen' );
			
			
			$this->ITCoreClass();
			
			$this->_file = __FILE__;
			
			require_once( builder_main_get_builder_core_path() . '/lib/layout-engine/layout-meta-box.php' );
			
			
			// This action has to be pushed back in order to allow the after_switch_theme action to fire first.
			remove_action( 'init', array( &$this, 'init' ), 0 );
			add_action( 'init', array( &$this, 'init' ), 1000 );
		}
		
		function init() {
			ITCoreClass::init();
			
			$this->_modules = apply_filters( 'builder_get_modules', array() );
			
			
			$this->_tabs = array(
				'layouts' => __( 'Layouts', 'it-l10n-Builder-Cohen' ),
				'views'   => __( 'Views', 'it-l10n-Builder-Cohen' ),
			);
			
			$this->_active_tab = ( ! empty( $_REQUEST['editor_tab'] ) ) ? $_REQUEST['editor_tab'] : key( $this->_tabs );
			
			$this->_tabless_self_link = $this->_self_link;
			$this->_self_link .= '&editor_tab=' . urlencode( $this->_active_tab );
		}
		
		function contextual_help( $text, $screen ) {
			ob_start();
			
			if ( 'layouts' == $this->_active_tab ) {
				if ( ! empty( $_REQUEST['layout'] ) || ! empty( $_REQUEST['add_layout'] ) ) {
					
?>
	<p><?php _e( 'Layouts are at the heart of how Builder gives you more control over your site. The goal is to allow you to create a site structure that meet your needs and the needs of your site rather than forcing you to structure your site to fit the mold of a specific theme.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'This editor allows you to create and modify Layouts for your site. There are a number of options, but most users will only use the following three regularly: Name, Width, and the Design section.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The Name is just an easy way to keep track of a specific Layout and is also used as the base of the name for a Layout\'s Widget areas.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The Width sets how wide the layout is. Since this is a Layout option, you can have Layouts of various widths used for different parts of your site.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'Extensions can modify how a layout functions and what the layout looks like. They are like mini-themes in that they can have style.css and functions.php files.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php printf ( __( 'Using the Hide Widget Areas options allow for showing or hiding a Layout\'s widget areas from WordPress\' <a href="%s">Widgets editor</a>. Since a Builder site can have a very large number of widget areas, hiding select Layout\'s widget areas can make managing widgets much easier. This option can also be toggled from the main Layout listing by hovering over a Layout\'s row and selecting either "Show widget areas" or "Hide widget areas".', 'it-l10n-Builder-Cohen' ), admin_url( 'widgets.php' ) ); ?></p>
	<p><?php _e( 'The Design area of this editor is where you assemble your layout using Modules. Each Module represents a different part of a layout. The Navigation Module adds a navigation menu. The Content Module holds the main content of that view, such as a listing of posts for the home page or page content if you are viewing a specific page. Clicking the "Add Module" link in the Design area shows the available modules and describes what they add to your Layout. By assembling a mix of desired Modules, you can quickly build a Layout for your site.', 'it-l10n-Builder-Cohen' ); ?></p>
<?php
					
				}
				else {
					
?>
	<p><?php _e( 'Layouts are at the heart of how Builder gives you more control over your site. The goal is to allow you to create a site structure that meet your needs and the needs of your site rather than forcing you to structure your site to fit the mold of a specific theme.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'For details about what a Layout is and how it is put together, click the "Create Layout" button below to create a new Layout from scratch or click the "Edit" link for a specific Layout and view the "Help" area from within the Layout editor.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'To create a new Layout, click one the "Create Layout" buttons below. As you hover over a Layout\'s row, additional options become visible. These additional options allow you to Edit, Duplicate, or Delete a Layout. They also offer options to set a Layout as the Default Layout and to show or hide widget areas.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'When you duplicate a Layout, the new Layout will have all the same settings and Modules as the original Layout. This is a quick way of making Layouts that are a slight modification of an existing Layout. Simply duplicate the Layout, modify the new Layout, and make the needed modifications.' ); ?></p>
	<p><?php printf( __( 'Since Builder\'s Layouts can create a very large number of Widget areas very quickly, there is the ability to show or hide a Layout\'s Widget areas from the <a href="%s">Widgets editor</a>. A good workflow is to hide all Layouts\' Widget areas except the Layout you are currently configuring. This makes managing the Widget areas much easier. To show or hide a Layout\'s Widget areas, simply hover over the Layout\'s row and select the "Show Widget areas" or "Hide Widget areas" link.', 'it-l10n-Builder-Cohen' ), admin_url( 'widgets.php' ) ); ?></p>
	<p><?php _e( 'Views is another very important aspect to Builder. Make sure to click the "Views" tab below and learn about what Views can do for you and your site.', 'it-l10n-Builder-Cohen' ); ?></p>
<?php
					
				}
			}
			else if ( 'views' == $this->_active_tab ) {
				
?>
	<p><?php _e( 'Views allow you to harness the true power of Builder\'s Layouts. By using Views, you can have one Layout show on just your Home page and another Layout show on all pages. Simply click the "Add View" button below, select the desired View and Layout, and click the "Add" button to start using Views.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The default Layout is used on all Views that do not have a specific Layout selected. You can set the default Layout by going to the Layouts tab, hovering over the layout you wish to set as the default Layout, and clicking the "Set as default" link.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'You can also set a Layout to be used for specific posts and pages by selecting the desired Layout from the "Custom Layout" box found in the editor for posts and pages. The "Custom Layout" box is typically found on the right side of the editor underneath the "Featured Image" box.', 'it-l10n-Builder-Cohen' ); ?></p>
<?php
				
			}
			
			$new_text = ob_get_contents();
			ob_end_clean();
			
			if ( ! empty( $new_text ) )
				$text = $new_text;
			
			return $text;
		}
		
		function set_help_sidebar() {
			builder_set_help_sidebar();
		}
		
		function add_admin_scripts() {
			ITCoreClass::add_admin_scripts();
			
			wp_enqueue_script( "{$this->_var}-theme-options", "{$this->_plugin_url}/js/layout-editor.js" );
			wp_enqueue_script( 'scriptaculous-effects' );
			
			do_action( 'builder_module_enqueue_admin_scripts', "{$this->_plugin_url}/modules" );
		}
		
		function add_admin_styles() {
			ITCoreClass::add_admin_styles();
			
			wp_enqueue_style( "{$this->_var}-theme-options", "{$this->_plugin_url}/css/layout-editor.css" );
			
			do_action( 'builder_module_enqueue_admin_styles', "{$this->_plugin_url}/modules" );
		}
		
		
		// Pages //////////////////////////////////////
		
		function index() {
			ITCoreClass::index();
			
			if ( 'views' === $this->_active_tab ) {
				if ( ! empty( $_REQUEST['delete_view'] ) )
					$this->_delete_view();
				else if ( ! empty( $_REQUEST['delete_view_screen'] ) )
					$this->_delete_view_screen();
				else if ( ! empty( $_REQUEST['modify_view'] ) )
					$this->_modify_view();
				else if ( ! empty( $_REQUEST['modify_view_screen'] ) )
					$this->_modify_view_screen();
				else
					$this->_modify_views();
			}
			else {
				if ( ! empty( $_REQUEST['cancel'] ) )
					$this->_list_layouts();
				else if ( ! empty( $_REQUEST['reset_data'] ) )
					$this->_reset_data();
				else if ( ! empty( $_REQUEST['submit_bulk_action_1'] ) || ! empty( $_REQUEST['submit_bulk_action_2'] ) )
					$this->_handle_bulk_action();
				else if ( ! empty( $_REQUEST['hide_widget_areas'] ) )
					$this->_hide_widget_areas();
				else if ( ! empty( $_REQUEST['show_widget_areas'] ) )
					$this->_show_widget_areas();
				else if ( ! empty( $_REQUEST['modify_module_settings'] ) )
					$this->_modify_module_settings();
				else if ( ! empty( $_REQUEST['delete_layout'] ) )
					$this->_delete_layout();
				else if ( ! empty( $_REQUEST['delete_layout_screen'] ) )
					$this->_delete_layout_screen();
				else if ( ! empty( $_REQUEST['set_default_layout'] ) )
					$this->_setDefaultLayout();
				else if ( ! empty( $_REQUEST['set_default_layout_screen'] ) )
					$this->_setDefaultLayoutScreen();
				else if ( ! empty( $_REQUEST['duplicate_layout'] ) )
					$this->_duplicateLayout();
				else if ( ! empty( $_REQUEST['duplicate_layout_screen'] ) )
					$this->_duplicateLayoutScreen();
				else if ( ! empty( $_REQUEST['add_module'] ) )
					$this->_add_module();
				else if ( ! empty( $_REQUEST['add_module_screen'] ) )
					$this->_add_module_screen();
				else if ( ! empty( $_REQUEST['save'] ) || ! empty( $_REQUEST['save_and_continue'] ) )
					$this->_save_layout();
				else if ( ! empty( $_REQUEST['layout'] ) || ! empty( $_REQUEST['add_layout'] ) )
					$this->_modify_layout();
				else
					$this->_list_layouts();
			}
		}
		
		function _print_tabs() {
			echo "<h2 class='nav-tab-wrapper'>";
			
			foreach ( (array) $this->_tabs as $var => $name ) {
				$link = $this->_tabless_self_link . '&editor_tab=' . urlencode( $var );
				$class = 'nav-tab' . ( ( $var === $this->_active_tab ) ? ' nav-tab-active' : '' );
				
				echo "<a class='$class' href='$link'>$name</a>";
			}
			
			echo "</h2>\n";
		}
		
		function _handle_bulk_action() {
			if ( ! empty( $_REQUEST['submit_bulk_action_1'] ) )
				$action = $_REQUEST['bulk_action_1'];
			else if ( ! empty( $_REQUEST['submit_bulk_action_2'] ) )
				$action = $_REQUEST['bulk_action_2'];
			
			if ( 'show_widget_areas' == $action )
				$this->_show_widget_areas();
			else if ( 'hide_widget_areas' == $action )
				$this->_hide_widget_areas();
			else if ( 'delete_layout' == $action )
				$this->_delete_layout();
			else
				$this->_list_layouts();
		}
		
		function _hide_widget_areas() {
			if ( isset( $_REQUEST['hide_widget_areas'] ) ) {
				$layouts = $_REQUEST['hide_widget_areas'];
				$bulk = false;
			}
			else if ( isset( $_REQUEST['layouts'] ) ) {
				$layouts = $_REQUEST['layouts'];
				$bulk = true;
			}
			else {
				$layouts = array();
				$bulk = true;
			}
			
			$successful = array();
			
			foreach ( (array) $layouts as $layout ) {
				if ( isset( $this->_options['layouts'][$layout] ) ) {
					$this->_options['layouts'][$layout]['hide_widgets'] = 'yes';
					$successful[] = $this->_options['layouts'][$layout]['description'];
				}
			}
			
			if ( ! empty( $successful ) ) {
				if ( 1 == count( $successful ) )
					ITUtility::show_status_message( sprintf( __( 'Widget areas for %s are now hidden from the <a href="widgets.php">Widgets editor</a>.', 'it-l10n-Builder-Cohen' ), $successful[0] ) );
				else
					ITUtility::show_status_message( __( 'Widget areas for the requested Layouts are now hidden from the <a href="widgets.php">Widgets editor</a>.', 'it-l10n-Builder-Cohen' ) );
				
				$this->_save();
			}
			else if ( ! $bulk )
				ITUtility::show_error_message( __( 'Unable to find the requested layout.', 'it-l10n-Builder-Cohen' ) );
			
			$this->_list_layouts();
		}
		
		function _show_widget_areas() {
			if ( isset( $_REQUEST['show_widget_areas'] ) ) {
				$layouts = $_REQUEST['show_widget_areas'];
				$bulk = false;
			}
			else if ( isset( $_REQUEST['layouts'] ) ) {
				$layouts = $_REQUEST['layouts'];
				$bulk = true;
			}
			else {
				$layouts = array();
				$bulk = true;
			}
			
			$successful = array();
			
			foreach ( (array) $layouts as $layout ) {
				if ( isset( $this->_options['layouts'][$layout] ) ) {
					$this->_options['layouts'][$layout]['hide_widgets'] = 'no';
					$successful[] = $this->_options['layouts'][$layout]['description'];
				}
			}
			
			if ( ! empty( $successful ) ) {
				if ( 1 == count( $successful ) )
					ITUtility::show_status_message( sprintf( __( 'Widget areas for %s now appear in the <a href="widgets.php">Widgets editor</a>.', 'it-l10n-Builder-Cohen' ), $successful[0] ) );
				else
					ITUtility::show_status_message( __( 'Widget areas for the requested Layouts now appear in the <a href="widgets.php">Widgets editor</a>.', 'it-l10n-Builder-Cohen' ) );
				
				$this->_save();
			}
			else if ( ! $bulk )
				ITUtility::show_error_message( __( 'Unable to find the requested layout.', 'it-l10n-Builder-Cohen' ) );
			
			$this->_list_layouts();
		}
		
		function _reset_data() {
			global $wpdb;
			
			foreach ( (array) $this->_options['layouts'] as $id => $layout )
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='_custom_layout' AND meta_value=%s", $id ) );
			
			
			do_action( "it_storage_reset_{$this->_var}" );
			
			ITUtility::show_status_message( __( 'Data reset', 'it-l10n-Builder-Cohen' ) );
			
			$this->_list_layouts();
		}
		
		function _modify_module_settings( $data = false ) {
			if ( false === $data )
				$data = $_REQUEST;
			
			$module = $data['modify_module_settings'];
			$id = $data['id'];
			$result = array();
			
			if ( isset( $_POST['save'] ) ) {
				if ( method_exists( $this->_modules[$module], 'validate' ) )
					$result = $this->_modules[$module]->validate();
				
				$save_data = ( isset( $result['data'] ) && is_array( $result['data'] ) ) ? $result['data'] : $_POST;
				
				if ( ! isset( $result['errors'] ) ) {
					$this->_save_module_settings( $save_data );
					return;
				}
			}
			
			if ( method_exists( $this->_modules[$module], 'edit' ) ) {
				$form = new ITForm( $_POST );
				
				$form->start_form();
				
				echo '<h1>' . sprintf( __( 'Modify %s Module Settings', 'it-l10n-Builder-Cohen' ), $this->_modules[$module]->_name ) . "</h1>";
				
				$this->_modules[$module]->edit( $form, $result );
				
				echo '<p class="submit">';
				
				$form->add_submit( 'save', array( 'value' => 'Save', 'class' => 'button-primary save' ) );
				
				echo ' ';
				
				if ( ( '%id' === $data['id'] ) || ( ! empty( $_REQUEST['new_module'] ) ) ) {
					$form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary', 'onclick' => 'var win = window.dialogArguments || opener || parent || top; win.remove_new_module(); it_dialog_remove();' ) );
					$form->add_hidden( 'new_module', '1' );
				}
				else
					$form->add_submit( 'cancel', array( 'value' => 'Cancel', 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) );
				
				echo '</p>';
				
				$form->add_hidden( 'modify_module_settings', $module );
				$form->add_hidden( 'id', $id );
				$form->add_hidden( 'layout_width', '0' );
				
				if ( isset( $data['layout'] ) )
					$form->add_hidden( 'layout', $data['layout'] );
				
				$form->end_form();
				
?>
	<?php if ( empty( $result['errors'] ) ) : ?>
		<script type="text/javascript">
//			it_dialog_update_size();
			
			load_module_data("<?php echo $id; ?>");
		</script>
	<?php endif; ?>
<?php
				
				return;
			}
			
?>
	<script type="text/javascript">
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _save_module_settings( $data ) {
			$module = $_REQUEST['modify_module_settings'];
			$id = $_REQUEST['id'];
			$layout_option = $this->_modules[$module]->get_layout_option();
			
			$form = new ITForm();
			
			$form->start_form();
			foreach ( (array) $this->_modules[$module]->get_defaults() as $var => $val ) {
				if ( ! isset( $data[$var] ) )
					$data[$var] = '';
				$form->add_hidden( "module-$id-$var", $data[$var] );
			}
			$form->end_form();
			
			$preview_image = $this->_modules[$module]->get_preview_image( $data );
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		save_module_data( "<?php echo $id; ?>" );
		win.update_preview_image( "<?php echo $id; ?>", "<?php echo $preview_image; ?>" );
		win.update_module_name( "<?php echo $id; ?>" );
	</script>
<?php
			
		}
		
		function _delete_layout() {
			$this->_add_layout_views();
			
			if ( isset( $_REQUEST['delete_layout_screen'] ) ) {
				$layouts = $_REQUEST['delete_layout_screen'];
				$bulk = false;
			}
			else if ( isset( $_REQUEST['layouts'] ) ) {
				$layouts = $_REQUEST['layouts'];
				$bulk = true;
			}
			else {
				$layouts = array();
				$bulk = true;
			}
			
			$success = array();
			
			foreach ( (array) $layouts as $layout ) {
				if ( ! isset( $this->_options['layouts'][$layout] ) )
					continue;
				
				$description = $this->_options['layouts'][$layout]['description'];
				
				if ( ( $this->_options['layouts'][$layout]['total_num_views'] > 0 ) || ( $layout === $this->_options['default'] ) ) {
					if ( $bulk || empty( $_POST['replacement_layout'] ) ) {
						if ( $bulk ) {
							if ( ( $this->_options['layouts'][$layout]['total_num_views'] > 0 ) && ( $layout === $this->_options['default'] ) )
								ITUtility::show_error_message( sprintf( __( 'The %s Layout is both the Default Layout and has Views relying on it. It will need to be manually deleted.', 'it-l10n-Builder-Cohen' ), $description ) );
							else if ( $this->_options['layouts'][$layout]['total_num_views'] > 0 )
								ITUtility::show_error_message( sprintf( __( 'The %s Layout has Views relying on it. It will need to be manually deleted.', 'it-l10n-Builder-Cohen' ), $description ) );
							else if ( $layout === $this->_options['default'] )
								ITUtility::show_error_message( sprintf( __( 'The %s Layout is the Default Layout. It will need to be manually deleted.', 'it-l10n-Builder-Cohen' ), $description ) );
						}
						else {
							ITUtility::show_error_message( __( 'You must select a replacement layout.', 'it-l10n-Builder-Cohen' ) );
							$this->_delete_layout_screen();
							
							return;
						}
					}
					else if ( ! is_array( $this->_options['layouts'][$_POST['replacement_layout']] ) || ( $_POST['replacement_layout'] === $layout ) ) {
						ITUtility::show_error_message( __( 'The replacement layout chosen is unavailable. Please select a different replacement layout.', 'it-l10n-Builder-Cohen' ) );
						$this->_delete_layout_screen();
						
						return;
					}
					else {
						global $wpdb;
						
						$replacement = $_POST['replacement_layout'];
						
						$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value=%s WHERE meta_key='_custom_layout' AND meta_value=%s", $replacement, $layout ) );
						
						foreach ( (array) $this->_options['views'] as $id => $view ) {
							if ( $layout === $view['layout'] )
								$this->_options['views'][$id]['layout'] = $replacement;
						}
						
						if ( $this->_options['default'] === $layout )
							$this->_options['default'] = $replacement;
					}
				}
				else {
					$success[] = $description;
					unset( $this->_options['layouts'][$layout] );
				}
			}
			
			$this->_save();
			
			if ( $bulk ) {
				if ( count( $success ) > 1 )
					ITUtility::show_status_message( __( 'The requested Layouts have been deleted.', 'it-l10n-Builder-Cohen' ) );
				else if ( ! empty( $success ) )
					ITUtility::show_status_message( sprintf( __( 'The %s Layout has been deleted.', 'it-l10n-Builder-Cohen' ), $success[0] ) );
				
				$this->_list_layouts();
				
				return;
			}
			
			
			$this->_add_layout_views();
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		win.jQuery("#entry-<?php echo $layout; ?> .num_page_views").html("0");
		win.jQuery("#entry-<?php echo $layout; ?> .num_post_views").html("0");
		win.jQuery("#entry-<?php echo $layout; ?> .num_views").html("0");
		win.jQuery("#entry-<?php echo $layout; ?> .set_default").html("&nbsp;");
		
		win.jQuery("#entry-<?php echo $layout; ?>").css("background-color", "#FF3333").fadeOut("slow").attr("id", "___<?php echo $layout; ?>");
		
		<?php if ( ! empty( $replacement ) ) : ?>
			win.jQuery("#entry-<?php echo $replacement; ?> .num_page_views").html("<?php echo $this->_options['layouts'][$replacement]['num_page_views']; ?>");
			win.jQuery("#entry-<?php echo $replacement; ?> .num_post_views").html("<?php echo $this->_options['layouts'][$replacement]['num_post_views']; ?>");
			win.jQuery("#entry-<?php echo $replacement; ?> .num_views").html("<?php echo $this->_options['layouts'][$replacement]['num_views']; ?>");
			<?php if ( $this->_options['default'] === $replacement ) : ?>
				win.jQuery("#entry-<?php echo $replacement; ?> .set_default").html("<strong>Yes</strong>");
			<?php endif; ?>
		<?php endif; ?>
		
		win.jQuery("tr[id^='entry-']:even").addClass("alternate");
		win.jQuery("tr[id^='entry-']:odd").removeClass("alternate");
		
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _delete_layout_screen() {
			$form = new ITForm();
			
			if ( count( $this->_options['layouts'] ) < 2 ) {
				ITUtility::show_error_message( __( 'There must be at least one layout. This layout can only be removed after adding additional layouts.', 'it-l10n-Builder-Cohen' ) );
				
				echo '<p class="submit">';
				$form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) );
				echo '</p>';
				
				return;
			}
			
			
			$this->_add_layout_views();
			
			$layouts = array( '' => '' );
			uksort( $this->_options['layouts'], array( $this, '_orderedSort' ) );
			foreach ( (array) $this->_options['layouts'] as $id => $layout )
				if ( $id != $_REQUEST['delete_layout_screen'] )
					$layouts[$id] = $layout['description'];
			
			$layout = $_REQUEST['delete_layout_screen'];
			$data = $this->_options['layouts'][$layout];
			
			$form = new ITForm();
			
?>
	<?php $form->start_form(); ?>
		<?php if ( ( $data['total_num_views'] > 0 ) || ( $this->_options['default'] === $layout ) ) : ?>
			<?php if ( $data['total_num_views'] > 0 ) : ?>
				<div><?php _e( 'This layout is currently being used by:', 'it-l10n-Builder-Cohen' ); ?></div>
				<?php
					$views = array( 'num_page_views' => _x( 'Page', 'view', 'it-l10n-Builder-Cohen' ), 'num_post_views' => _x( 'Post', 'view', 'it-l10n-Builder-Cohen' ), 'num_views' => _x( 'View', 'view', 'it-l10n-Builder-Cohen' ) );
					
					foreach ( (array) $views as $var => $name )
						if ( $data[$var] > 0 )
							echo "<div style=\"margin-left:10px;\">{$data[$var]} $name" . ( ( $data[$var] > 1 ) ? 's' : '' ) . "</div>";
				?>
				<br />
			<?php endif; ?>
			
			<?php if ( $this->_options['default'] === $layout ) : ?>
				<div><?php _e( 'This layout is the default layout. The selected replacement will be set as the new default.', 'it-l10n-Builder-Cohen' ); ?></div>
				<br />
			<?php endif; ?>
			
			<div><?php _e( 'Please select a layout to use as a replacement:', 'it-l10n-Builder-Cohen' ); ?></div>
			<div><?php $form->add_drop_down( 'replacement_layout', array( 'value' => $layouts, 'name' => 'replacement_layout' ) ); ?></div>
		<?php else : ?>
			<div><?php printf( __( 'Please confirm that you would like to delete the <strong>%s</strong> layout.', 'it-l10n-Builder-Cohen' ), $this->_options['layouts'][$layout]['description'] ); ?></div>
		<?php endif; ?>
		
		<p class="submit">
			<?php $form->add_submit( 'delete_layout', array( 'value' => __( 'Delete', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
		
		<?php $form->add_hidden( 'delete_layout_screen', $layout ); ?>
		<?php $form->add_hidden( 'render_clean', 'dialog' ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		function _duplicateLayout() {
			$source = $_POST['duplicate_layout_screen'];
			$description = $_POST['duplicate_name'];
			$id = uniqid( '' );
			
			if ( empty( $description ) ) {
				ITUtility::show_error_message( __( 'You must supply a name for the duplicated layout.', 'it-l10n-Builder-Cohen' ) );
				$this->_duplicateLayoutScreen();
				return;
			}
			foreach ( (array) $this->_options['layouts'] as $layout ) {
				if ( $description === $layout['description'] ) {
					ITUtility::show_error_message( __( 'A layout with that name already exists. Please enter a different name.', 'it-l10n-Builder-Cohen' ) );
					$this->_duplicateLayoutScreen();
					return;
				}
			}
			
			$this->_options['layouts'][$id] = $this->_options['layouts'][$source];
			$this->_options['layouts'][$id]['description'] = $description;
			
			$this->_options['layouts'][$id]['guid'] = $id;
			
			foreach ( (array) $this->_options['layouts'][$id]['modules'] as $module_id => $module_data )
				$this->_options['layouts'][$id]['modules'][$module_id]['guid'] = uniqid( '' );
			
			
			$this->_save();
			
			
			$layout = $this->_options['layouts'][$id];
			
			
			if ( isset( $layout['hide_widgets'] ) && ( 'yes' === $layout['hide_widgets'] ) ) {
				$hide_widgets_text = '<strong>' . __( 'Yes', 'it-l10n-Builder-Cohen' ) . '</strong>';
				$hide_widgets_link_title = __( "Show this layout's widget areas in the Widgets editor", 'it-l10n-Builder-Cohen' );
				$hide_widgets_link_text = __( 'Show widget areas', 'it-l10n-Builder-Cohen' );
			}
			else {
				$hide_widgets_text = 'No';
				$hide_widgets_link_title = __( "Hide this layout's widget areas from the Widgets editor", 'it-l10n-Builder-Cohen' );
				$hide_widgets_link_text = __( 'Hide widget areas', 'it-l10n-Builder-Cohen' );
			}
			
			
			if ( builder_theme_supports( 'builder-extensions' ) ) {
				$extensions = apply_filters( 'builder_get_extensions_with_names', array() );
				
				$extension_title = __( 'This layout does not use an Extension', 'it-l10n-Builder-Cohen' );
				$extension_text = '&nbsp;';
				
				if ( ! empty( $layout['extension'] ) ) {
					$extension_text = $extensions[$layout['extension']];
					
					if ( ! empty( $layout['disable_style'] ) && ( 'yes' === $layout['disable_style'] ) ) {
						$extension_title = sprintf( __( 'Uses only the %s extension for styling', 'it-l10n-Builder-Cohen' ), $extension_text );
						$extension_text = '<strong>' . __( $extension_text, 'it-l10n-Builder-Cohen' ) . '</strong>';
					}
					else
						$extension_title = sprintf( __( 'Uses the %s extension to modify the default theme styling', 'it-l10n-Builder-Cohen' ), $extension_text );
				}
			}
			
			$default_title_description = __( 'The default layout is used for all views that don\'t have a specific view set. New layouts inherit the default layout\'s background options.', 'it-l10n-Builder-Cohen' );
			$layout_width_text = $layout['width'];
			$hide_widgets_description = __( 'Widget areas for layouts can be hidden from the Widgets editor to make management of widgets easier.', 'it-l10n-Builder-Cohen' );
			$modify_layout_settings_title = __( 'Modify Layout Settings', 'it-l10n-Builder-Cohen' );
			$edit_link_text = __( 'Edit', 'it-l10n-Builder-Cohen' );
			$manage_widgets_title = __( 'Manage Widgets for this Layout', 'it-l10n-Builder-Cohen' );
			$manage_widgets_link_text = __( 'Widgets', 'it-l10n-Builder-Cohen' );
			$duplicate_layout_title = __( 'Duplicate Layout', 'it-l10n-Builder-Cohen' );
			$duplicate_layout_text = __( 'Duplicate', 'it-l10n-Builder-Cohen' );
			$delete_layout_title = __( 'Delete Layout', 'it-l10n-Builder-Cohen' );
			$delete_layout_text = __( 'Delete', 'it-l10n-Builder-Cohen' );
			$page_views_title = sprintf( __( 'The number of pages using %s as the Custom Layout', 'it-l10n-Builder-Cohen' ), $description );
			$post_views_title = sprintf( __( 'The number of posts using %s as the Custom Layout', 'it-l10n-Builder-Cohen' ), $description );
			$views_title = sprintf( __( 'The number of views using %s', 'it-l10n-Builder-Cohen' ), $description );
			
			
			$description = str_replace( '\'', '\\\'', $description );
			$layout_width_text = str_replace( '\'', '\\\'', $layout_width_text );
			
			if ( builder_theme_supports( 'builder-extensions' ) )
				$extension_title = str_replace( "'", "\\'", $extension_title );
			
			$hide_widgets_text = str_replace( "'", "\\'", $hide_widgets_text );
			$hide_widgets_link_title = str_replace( "'", "\\'", $hide_widgets_link_title );
			$hide_widgets_link_text = str_replace( "'", "\\'", $hide_widgets_link_text );
			$default_title_description = str_replace( "'", "\\'", $default_title_description );
			$hide_widgets_description = str_replace( "'", "\\'", $hide_widgets_description );
			$modify_layout_settings_title = str_replace( "'", "\\'", $modify_layout_settings_title );
			$edit_link_text = str_replace( "'", "\\'", $edit_link_text );
			$manage_widgets_title = str_replace( "'", "\\'", $manage_widgets_title );
			$manage_widgets_link_text = str_replace( "'", "\\'", $manage_widgets_link_text );
			$duplicate_layout_title = str_replace( "'", "\\'", $duplicate_layout_title );
			$duplicate_layout_text = str_replace( "'", "\\'", $duplicate_layout_text );
			$delete_layout_title = str_replace( "'", "\\'", $delete_layout_title );
			$delete_layout_text = str_replace( "'", "\\'", $delete_layout_text );
			
			$page_views_title = str_replace( "'", "\\'", $page_views_title );
			$post_views_title = str_replace( "'", "\\'", $post_views_title );
			$views_title = str_replace( "'", "\\'", $views_title );
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		var newRow = '<tr style="display:none;" id="entry-<?php echo $id; ?>">';
		newRow += '<th scope="row" class="check-column"><input type="checkbox" name="layouts[]" class="administrator layouts" value="<?php echo $id; ?>" /></th>';
		newRow += '<td>';
		newRow += '<strong><a href="<?php echo $this->_self_link; ?>&layout=<?php echo $id; ?>" title="<?php echo $modify_layout_settings_title; ?>"><?php echo $description; ?></a></strong><br />';
		newRow += '<div class="row-actions">';
		newRow += '<span class="edit"><a href="<?php echo $this->_self_link; ?>&layout=<?php echo $id; ?>" title="<?php echo $modify_layout_settings_title; ?>"><?php echo $edit_link_text; ?></a> | </span>';
		
		<?php if ( 'on' != get_user_setting( 'widgets_access' ) ) : ?>
			newRow += '<span class="manage-widgets"><a href="<?php echo admin_url( 'widgets.php?builder_layout_id=' . $id ); ?>" title="<?php echo $manage_widgets_title; ?>"><?php echo $manage_widgets_link_text; ?></a> | </span>';
		<?php endif; ?>
		
		newRow += '<span class="duplicate"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&duplicate_layout_screen=$id", array( 'width' => '250' ) ); ?>" class="it-dialog" title="<?php echo $duplicate_layout_title; ?>"><?php echo $duplicate_layout_text; ?></a> | </span>';
		newRow += '<span class="delete"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&delete_layout_screen=$id" ); ?>" class="it-dialog" title="<?php echo $delete_layout_title; ?>"><?php echo $delete_layout_text; ?></a></span>';
		newRow += '</div>';
		newRow += '</td>';
		newRow += '<td class="set_default" title="<?php echo $default_title_description; ?>">';
		newRow += '<div class="row-actions">';
		newRow += '<a href="<?php echo ITDialog::get_link( "{$this->_self_link}&set_default_layout_screen=$id" ); ?>" class="it-dialog">Set as default</a>';
		newRow += '</div>';
		newRow += '</td>';
		newRow += '<td><?php echo $layout_width_text; ?></td>';
		
		<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
			newRow += '<td class="extension" title="<?php echo $extension_title; ?>"><?php echo $extension_text; ?></td>';
		<?php endif; ?>
		
		newRow += '<td class="widget_areas_hidden" title="<?php echo $hide_widgets_description; ?>">';
		newRow += '<?php echo $hide_widgets_text; ?>';
		newRow += '<div class="row-actions">';
		newRow += '<a href="/wp-admin/admin.php?page=layout-editor&hide_widget_areas=<?php echo $id; ?>" title="<?php echo $hide_widgets_link_title; ?>"><?php echo $hide_widgets_link_text; ?></a>';
		newRow += '</div>';
		newRow += '</td>';
		newRow += '<td class="num_page_views" title="<?php echo $page_views_title; ?>">';
		newRow += '0';
		newRow += '</td>';
		newRow += '<td class="num_post_views" title="<?php echo $post_views_title; ?>">';
		newRow += '0';
		newRow += '</td>';
		newRow += '<td class="num_views" title="<?php echo $views_title; ?>">';
		newRow += '0';
		newRow += '</td>';
		newRow += '</tr>';
		
		var rows = win.jQuery("tr[id^='entry-']");
		var i;
		for(i = 0; i < rows.get().length; i++) {
			if("<?php echo strtolower( $description ); ?>" < win.jQuery("tr[id^='entry-']:eq(" + i + ") a[title='<?php echo $modify_layout_settings_title; ?>']").html().toLowerCase()) {
				break;
			}
		}
		
		i--;
		
		if((rows.get().length > 0) && (i >= 0)) {
			win.jQuery("tr[id^='entry-']:eq(" + i + ")").after(newRow);
		}
		else {
			if(win.jQuery("table#layouts > tbody") == undefined) {
				win.jQuery("table#layouts").html(newRow);
			}
			else {
				win.jQuery("table#layouts > tbody").prepend(newRow);
			}
		}
		
		win.jQuery("tr[id^='entry-']:even").addClass("alternate");
		win.jQuery("tr[id^='entry-']:odd").removeClass("alternate");
		
		var origColor = win.jQuery("#entry-<?php echo $id; ?>").css("background-color");
		win.jQuery("#entry-<?php echo $id; ?>").css("background-color", "#33FF33").fadeIn("slow").animate({backgroundColor:origColor}, 300);
		
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _duplicateLayoutScreen() {
			$layout = $_REQUEST['duplicate_layout_screen'];
			
			$data = array();
			if ( isset( $_REQUEST['duplicate_name'] ) )
				$data['duplicate_name'] = $_REQUEST['duplicate_name'];
			
			$form = new ITForm( $data );
			
?>
	<?php $form->start_form(); ?>
		<div><?php printf( __( 'Duplicating <strong>%s</strong>.', 'it-l10n-Builder-Cohen' ), $this->_options['layouts'][$layout]['description'] ); ?></div>
		<br />
		
		<div>Please name the new layout:</div>
		<div><?php $form->add_text_box( 'duplicate_name' ); ?></div>
		
		<p class="submit">
			<?php $form->add_submit( 'duplicate_layout', array( 'value' => __( 'Create Duplicate', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
		
		<?php $form->add_hidden( 'duplicate_layout_screen', $layout ); ?>
		<?php $form->add_hidden( 'render_clean', 'dialog' ); ?>
	</form>
<?php
			
		}
		
		function _setDefaultLayout() {
			$layout = $_REQUEST['set_default_layout_screen'];
			$description = $this->_options['layouts'][$layout]['description'];
			
			$original_default = $this->_options['default'];
			
			$this->_options['default'] = $layout;
			$this->_save();
			
			$set_default_link = "<div class='row-actions'><a href='" . ITDialog::get_link( "{$this->_self_link}&set_default_layout_screen=$original_default" ) . "' class='it-dialog' title='" . __( 'Set this layout as the default', 'it-l10n-Builder-Cohen' ) . "'>";
			$set_default_link .= __( 'Set as default', 'it-l10n-Builder-Cohen' );
			$set_default_link .= "</a></div>";
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		win.jQuery("#entry-<?php echo $original_default; ?> .set_default").html("<?php echo $set_default_link; ?>");
		win.jQuery("#entry-<?php echo $layout; ?> .set_default").html("<strong><?php _e( 'Yes', 'it-l10n-Builder-Cohen' ); ?></strong>");
		
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _setDefaultLayoutScreen() {
			$layout = $_REQUEST['set_default_layout_screen'];
			
			$form = new ITForm();
			
?>
	<?php $form->start_form(); ?>
		<div><?php printf( __( 'Please confirm that you would like to set <strong>%s</strong> as the default layout.', 'it-l10n-Builder-Cohen' ), $this->_options['layouts'][$layout]['description'] ); ?></div>
		
		<p class="submit">
			<?php $form->add_submit( 'set_default_layout', array( 'value' => __( 'Set as Default', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
		
		<?php $form->add_hidden( 'set_default_layout_screen', $layout ); ?>
		<?php $form->add_hidden( 'render_clean', 'dialog' ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		function _add_module() {
			$preview_image = $this->_modules[$_POST['module']]->get_preview_image();
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		win.add_module("<?php echo $_POST['module']; ?>", "<?php echo $preview_image; ?>" );
	</script>
<?php
			
			$data = array ( 'modify_module_settings' => $_POST['module'], 'id' => '%id' );
			
			$this->_modify_module_settings( $data );
		}
		
		function _add_module_screen() {
			ksort( $this->_modules );
			
			$form = new ITForm();
			
?>
	<?php $form->start_form(); ?>
		<table style="border-spacing:0px 10px;">
			<h1><?php _e( 'Select a module to add to the layout', 'it-l10n-Builder-Cohen' ); ?></h1>
			
			<?php $selected = " checked"; ?>
			<?php foreach ( (array) $this->_modules as $module_id => $module ) : ?>
				<tr style="vertical-align:top;" class="add-module-<?php echo $module_id; ?>">
					<td style="padding-right:5px;"><input type="radio" name="module" value="<?php echo $module_id; ?>" id="module-<?php echo $module_id; ?>"<?php echo $selected; ?> /></td>
					<td>
						<label for="module-<?php echo $module_id; ?>" style="display:block;">
							<strong><?php echo $module->_name; ?></strong><br />
							<?php echo $module->_description; ?>
						</label>
					</td>
				</tr>
				<?php $selected = ''; ?>
			<?php endforeach; ?>
		</table>
		
		<p class="submit">
			<?php $form->add_submit( 'add_module', array( 'value' => __( 'Add Module', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
	<?php $form->end_form(); ?>
	
	<script type="text/javascript">
		add_module_screen_hide_maxed_modules();
		
		jQuery( function() {
			it_dialog_add_form_submission_message( "<?php echo esc_js( __( 'Creating module.', 'it-l10n-Builder-Cohen' ) ); ?>" );
		} );
	</script>
<?php
			
		}
		
		function _list_layouts() {
			uksort( $this->_options['layouts'], array( $this, '_orderedSort' ) );
			
			$this->_add_layout_views();
			
			$bulk_actions = array(
				''                  => 'Bulk Actions',
				'delete_layout'     => 'Delete',
				'show_widget_areas' => 'Show Widget Areas',
				'hide_widget_areas' => 'Hide Widget Areas',
			);
			
			$extensions = apply_filters( 'builder_get_extensions_with_names', array() );
			
			
			$form = new ITForm();
			
?>
	<div class="wrap">
		<?php $form->start_form(); ?>
			<?php ITUtility::screen_icon(); ?>
			<?php $this->_print_tabs(); ?>
			
			<p><?php _e( 'Builder\'s Layouts allow you to change the structure of your site. For details, click the "Help" button at the top-right.', 'it-l10n-Builder-Cohen' ); ?></p>
			
			<div class="tablenav">
				<div class="alignleft actions">
					<?php $form->add_drop_down( 'bulk_action_1', array( 'value' => $bulk_actions ) ); ?>
					<?php $form->add_submit( 'submit_bulk_action_1', array( 'value' => 'Apply', 'class' => 'button-secondary bulk-action-submit' ) ); ?>
					<?php $form->add_submit( 'add_layout', array( 'value' => __( 'Create Layout', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary add' ) ); ?>
				</div>
				
				<br class="clear" />
			</div>
			
			<br class="clear" />
			
			<table id="layouts" class="widefat fixed" cellspacing="0">
				<thead>
					<?php ob_start(); ?>
					<tr class="thead">
						<th class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
						<th><?php _e( 'Layout Description', 'it-l10n-Builder-Cohen' ); ?></th>
						<th><?php _e( 'Default Layout', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'The default layout is used for all views that don\'t have a specific view set.', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<th><?php _e( 'Width', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'The width of the Layout in pixels.', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
							<th><?php _e( 'Extension', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'Listings in bold override the default style.css.', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<?php endif; ?>
						<th><?php _e( 'Hide Widget Areas', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'Widget areas for layouts can be hidden from the Widgets editor to make management of widgets easier.', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<th><?php _e( 'Pages', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'The number of pages using the layout as the Custom Layout', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<th><?php _e( 'Posts', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'The number of posts using the layout as the Custom Layout', 'it-l10n-Builder-Cohen' ) ); ?></th>
						<th><?php _e( 'Views', 'it-l10n-Builder-Cohen' ); ?> <?php ITUtility::add_tooltip( __( 'The number of views using the layout', 'it-l10n-Builder-Cohen' ) ); ?></th>
					</tr>
					<?php $headings = ob_get_contents(); ?>
					<?php ob_end_flush(); ?>
				</thead>
				<tfoot>
					<?php echo $headings; ?>
				</tfoot>
				
				<tbody>
					<?php $class = ' class="alternate"'; ?>
					<?php foreach ( (array) $this->_options['layouts'] as $id => $layout ) : ?>
						<tr id="entry-<?php echo $id; ?>"<?php echo $class; ?>>
							<th scope="row" class="check-column"><input type="checkbox" name="layouts[]" class="administrator layouts" value="<?php echo $id; ?>" /></th>
							<td>
								<strong><a href="<?php echo $this->_self_link; ?>&layout=<?php echo $id; ?>" title="<?php _e( 'Modify Layout Settings', 'it-l10n-Builder-Cohen' ); ?>"><?php echo $layout['description']; ?></a></strong><br />
								<div class="row-actions">
									<span class="edit"><a href="<?php echo $this->_self_link; ?>&layout=<?php echo $id; ?>" title="<?php _e( 'Modify Layout Settings', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Edit', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
									
									<?php if ( current_user_can( 'edit_theme_options' ) ) : ?>
										<span class="manage-widgets"><a href="<?php echo admin_url( 'widgets.php?builder_layout_id=' . $id ); ?>" title="<?php _e( 'Manage Widgets for this Layout', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Widgets', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
									<?php endif; ?>
									
									<span class="duplicate"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&duplicate_layout_screen=$id", array( 'width' => '250' ) ); ?>" class="it-dialog" title="<?php _e( 'Duplicate Layout', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Duplicate', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
									<span class="delete"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&delete_layout_screen=$id" ); ?>" class="it-dialog" title="<?php _e( 'Delete Layout', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Delete', 'it-l10n-Builder-Cohen' ); ?></a></span>
								</div>
							</td>
							<td class="set_default" title="<?php _e( 'The default layout is used for all views that don\'t have a specific view set.', 'it-l10n-Builder-Cohen' ); ?>">
								<?php if ( $this->_options['default'] == $id ) : ?>
									<strong><?php _e( 'Yes', 'it-l10n-Builder-Cohen' ); ?></strong>
								<?php else : ?>
									<div class="row-actions">
										<a href="<?php echo ITDialog::get_link( "{$this->_self_link}&set_default_layout_screen=$id" ); ?>" class="it-dialog" title="<?php _e( 'Set this layout as the default', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Set as default', 'it-l10n-Builder-Cohen' ); ?></a>
									</div>
								<?php endif; ?>
							</td>
							<td><?php echo $layout['width']; ?></td>
							<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
								<?php
									$extension_title = __( 'This layout does not use a Extension', 'it-l10n-Builder-Cohen' );
									$extension_text = '&nbsp;';
									
									if ( ! empty( $layout['extension'] ) && ! empty( $extensions[$layout['extension']] ) ) {
										$extension_text = $extensions[$layout['extension']];
										
										if ( ! empty( $layout['disable_style'] ) && ( 'yes' === $layout['disable_style'] ) ) {
											$extension_title = sprintf( __( 'Uses only the %s extension for styling', 'it-l10n-Builder-Cohen' ), $extension_text );
											$extension_text = '<strong>' . __( $extension_text, 'it-l10n-Builder-Cohen' ) . '</strong>';
										}
										else
											$extension_title = sprintf( __( 'Uses the %s extension to modify the default theme styling', 'it-l10n-Builder-Cohen' ), $extension_text );
									}
								?>
								<td class="extension" title="<?php echo $extension_title; ?>">
									<?php echo $extension_text; ?>
								</td>
							<?php endif; ?>
							<td class="widget_areas_hidden" title="<?php _e( 'Widget areas for layouts can be hidden from the Widgets editor to make management of widgets easier.', 'it-l10n-Builder-Cohen' ); ?>">
								<?php if ( isset( $layout['hide_widgets'] ) && ( 'yes' === $layout['hide_widgets'] ) ) : ?>
									<strong><?php _e( 'Yes', 'it-l10n-Builder-Cohen' ); ?></strong>
									<div class="row-actions">
										<a href="<?php echo $this->_self_link; ?>&show_widget_areas=<?php echo $id; ?>" title="<?php _e( 'Show this layout\'s widget areas in the Widgets editor', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Show widget areas', 'it-l10n-Builder-Cohen' ); ?></a>
									</div>
								<?php else : ?>
									<?php _e( 'No', 'it-l10n-Builder-Cohen' ); ?>
									<div class="row-actions">
										<a href="<?php echo $this->_self_link; ?>&hide_widget_areas=<?php echo $id; ?>" title="<?php _e( 'Hide this layout\'s widget areas from the Widgets editor', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Hide widget areas', 'it-l10n-Builder-Cohen' ); ?></a>
									</div>
								<?php endif; ?>
							</td>
							<td class="num_page_views" title="<?php printf( __( 'The number of pages using %s as the Custom Layout', 'it-l10n-Builder-Cohen' ), $layout['description'] ); ?>">
								<?php echo $layout['num_page_views']; ?>
							</td>
							<td class="num_post_views" title="<?php printf( __( 'The number of posts using %s as the Custom Layout', 'it-l10n-Builder-Cohen' ), $layout['description'] ); ?>">
								<?php echo $layout['num_post_views']; ?>
							</td>
							<td class="num_views" title="<?php printf( __( 'The number of views using %s', 'it-l10n-Builder-Cohen' ), $layout['description'] ); ?>">
								<?php echo $layout['num_views']; ?>
							</td>
						</tr>
						<?php $class = ( $class == '' ) ? ' class="alternate"' : ''; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<br class="clear" />
			
			<div class="tablenav">
				<div class="alignleft actions">
					<?php $form->add_drop_down( 'bulk_action_2', array( 'value' => $bulk_actions ) ); ?>
					<?php $form->add_submit( 'submit_bulk_action_2', array( 'value' => 'Apply', 'class' => 'button-secondary bulk-action-submit' ) ); ?>
					<?php $form->add_submit( 'add_layout', array( 'value' => __( 'Create Layout', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary add' ) ); ?>
				</div>
				
				<br class="clear" />
			</div>
		<?php $form->end_form(); ?>
	</div>
	
	<script type="text/javascript">
//		jQuery( function() {
//			init_layout_listing();
//		} );
	</script>
<?php
			
		}
		
		function _delete_view() {
			unset( $this->_options['views'][$_POST['view']] );
			$this->_save();
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		win.remove_view("<?php echo $_POST['view']; ?>");
		
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _delete_view_screen() {
			$available_views = apply_filters( 'builder_get_available_views', array() );
			
			$form = new ITForm();
			
			$view = $this->_get_view_data( $_REQUEST['delete_view_screen'] );
			
?>
	<?php $form->start_form(); ?>
		<div><?php printf( __( 'Please confirm that you would like to remove the view customization for <strong>%s</strong>.', 'it-l10n-Builder-Cohen' ), $view['name'] ); ?></div>
		
		<p class="submit">
			<?php $form->add_submit( 'delete_view', array( 'value' => __( 'Remove', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
		<?php $form->add_hidden( 'editor_tab', $_REQUEST['editor_tab'] ); ?>
		<?php $form->add_hidden( 'view', $_REQUEST['delete_view_screen'] ); ?>
		<?php $form->add_hidden( 'render_clean', 'dialog' ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		function _modify_view() {
			$errors = array();
			
			if ( empty( $_POST['view'] ) ) {
				$errors[] = "You must select a View";
			}
			
			if ( ! empty( $errors ) ) {
				$this->_modify_view_screen( $errors );
				return;
			}
			
			
			$available_views = apply_filters( 'builder_get_available_views', array() );
			
			if ( ! is_array( $available_views[$_POST['view']] ) ) {
				ITUtility::show_error_message( __( 'Received bad data. Either the requested Layout or View is missing.', 'it-l10n-Builder-Cohen' ) );
				
				return;
			}
			
			
			$view = $_POST['view'];
			
			if ( ( 'is_category' === $view ) && ! empty( $_POST['category_id'] ) ) {
				$view = "{$view}__{$_POST['category_id']}";
			} else if ( ( 'is_tag' === $view ) && ! empty( $_POST['tag_id'] ) ) {
				$view = "{$view}__{$_POST['tag_id']}";
			} else if ( ( 'is_author' === $view ) && ! empty( $_POST['author_id'] ) ) {
				$view = "{$view}__{$_POST['author_id']}";
			}
			
			
			if ( ! empty( $_POST['original_view'] ) && ( $_POST['original_view'] !== $view ) ) {
				unset( $this->_options['views'][$_POST['original_view']] );
			}
			
			
			$this->_options['views'][$view] = array( 'layout' => $_POST['layout'] );
			
			
			if ( isset( $_POST['extension'] ) ) {
				$extension_rules = array(
					''                      => __( 'Use the Active Extension', 'it-l10n-Builder-Cohen' ),
					'//DISABLE_EXTENSION//' => __( 'Disable the Active Extension', 'it-l10n-Builder-Cohen' ),
				);
				
				$extension = $_POST['extension'];
				$extension_data = array();
				
				if ( isset( $extension_rules[$extension] ) ) {
					$extension_data = array();
					$extension_name = $extension_rules[$extension];
				} else if ( ! empty( $extension ) ) {
					$extension_data = apply_filters( 'builder_get_extension_data', array(), $extension );
					$extension_name = $extension_data['name'];
				}
				
				$this->_options['views'][$view]['extension'] = $extension;
				$this->_options['views'][$view]['extension_data'] = $extension_data;
			}
			
			$this->_save();
			
			
			$view = $this->_get_view_data( $view );
			
			if ( isset( $extension_name ) ) {
				$view['extension'] = $extension_name;
			}
			
			foreach ( (array) $view as $var => $val ) {
				$view[$var] = str_replace( '\\', '\\\\', $view[$var] );
				$view[$var] = str_replace( '\'', '\\\'', $view[$var] );
			}
			
			
			$data_fields = array(
				'view_id'            => 'view_id',
				'view_name'          => 'name',
				'view_description'   => 'description',
				'layout'             => 'layout',
				'layout_description' => 'layout_description',
			);
			
			if ( builder_theme_supports( 'builder-extensions' ) ) {
				$data_fields['extension'] = 'extension';
			}
			
			$data = '';
			
			foreach ( $data_fields as $js_field => $data_field ) {
				if ( ! empty( $data ) ) {
					$data .= ', ';
				}
				
				if ( ! isset( $view[$data_field] ) ) {
					$view[$data_field] = '';
				}
				
				$data .= "'$js_field': '{$view[$data_field]}'";
			}
			
?>
	<script type="text/javascript">
		var win = window.dialogArguments || opener || parent || top;
		
		<?php if ( ! empty( $_POST['original_view'] ) ) : ?>
			win.remove_view("<?php echo $_POST['original_view']; ?>");
		<?php endif; ?>
		win.add_view({<?php echo $data; ?>});
		
		jQuery( function() {
			it_dialog_remove();
		} );
	</script>
<?php
			
		}
		
		function _modify_view_screen( $errors = array() ) {
			foreach ( (array) $errors as $error )
				ITUtility::show_error_message( $error );
			
			$view = array();
			if ( isset( $_POST['layout'] ) ) {
				$view = array(
					'layout'      => $_POST['layout'],
					'view'        => $_POST['view'],
					'category_id' => $_POST['category_id'],
					'tag_id'      => $_POST['tag_id'],
					'author_id'   => $_POST['author_id'],
					'extension'   => ( isset( $_POST['extension'] ) ) ? $_POST['extension'] : '',
				);
			}
			else if ( isset( $_REQUEST['original_view'] ) && isset( $this->_options['views'][$_REQUEST['original_view']] ) ) {
				$view = $this->_get_view_data( $_REQUEST['original_view'] );
			}
			
			
			$layouts = array();
			
			$layouts[__( 'Rules', 'it-l10n-Builder-Cohen' )] = array(
				'//INHERIT//' => __( 'Use this View\'s Active Layout', 'it-l10n-Builder-Cohen' ),
				''            => __( 'Use the site\'s Default Layout', 'it-l10n-Builder-Cohen' ),
			);
			
			$layout_index_name = __( 'Layouts', 'it-l10n-Builder-Cohen' );
			
			uksort( $this->_options['layouts'], array( $this, '_orderedSort' ) );
			foreach ( (array) $this->_options['layouts'] as $id => $layout )
				$layouts[$layout_index_name][$id] = $layout['description'];
			
			
			$available_views = apply_filters( 'builder_get_available_views', array() );
			
			if ( empty( $_REQUEST['original_view'] ) && empty( $_REQUEST['view'] ) )
				$views = array( '' => '' );
			foreach ( (array) $available_views as $id => $view_data )
				if ( empty( $this->_options['views'][$id] ) || ( isset( $_REQUEST['original_view'] ) && ( $_REQUEST['original_view'] === $id ) ) || in_array( $id, array( 'is_category', 'is_tag', 'is_author' ) ) )
					$views[$id] = $view_data['name'];
			asort( $views );
			
			
			$exclude_categories = '';
			$exclude_tags = '';
			$exclude_authors = array();
			
			foreach ( (array) $this->_options['views'] as $view_id => $view_data ) {
				if ( preg_match( '/is_category__(\d+)/', $view_id, $matches ) && ( empty( $view['view_id'] ) || ( $view_id !== $view['view_id'] ) ) ) {
					if ( ! empty( $exclude_categories ) )
						$exclude_categories .= ',';
					$exclude_categories .= $matches[1];
				}
				else if ( preg_match( '/is_tag__(\d+)/', $view_id, $matches ) && ( empty( $view['view_id'] ) || ( $view_id !== $view['view_id'] ) ) ) {
					if ( ! empty( $exclude_tags ) )
						$exclude_tags .= ',';
					$exclude_tags .= $matches[1];
				}
				else if ( preg_match( '/is_author__(\d+)/', $view_id, $matches ) && ( empty( $view['view_id'] ) || ( $view_id !== $view['view_id'] ) ) ) {
					$exclude_authors[] = $matches[1];
				}
			}
			
			
			$category_arguments = array( 'hide_empty' => 0, 'orderby' => 'name', 'hierarchical' => true, 'exclude' => $exclude_categories );
			if ( isset( $view['category_id'] ) && ! is_null( $view['category_id'] ) ) {
				$category_arguments['selected'] = $view['category_id'];
			}
			
			$category_options = array();
			if ( ! isset( $this->_options['views']['is_category'] ) || ( isset( $view['view_id'] ) && ( 'is_category' === $view['view_id'] ) ) ) {
				$category_options['__optgroup_1'][] = __( 'All Categories', 'it-l10n-Builder-Cohen' );
			}
			
			$categories = get_categories( $category_arguments );
			
			if ( ! empty( $categories ) ) {
				foreach ( (array) $categories as $category ) {
					$category_options['__optgroup_2'][$category->term_id] = $this->_get_full_term_name( $category->term_id, $category->taxonomy );
				}
				
				natcasesort( $category_options['__optgroup_2'] );
			}
			
			if ( empty( $category_options ) ) {
				unset( $views['is_category'] );
			}
			
			
			$tag_arguments = array( 'hide_empty' => 0, 'orderby' => 'name', 'exclude' => $exclude_tags );
			
			$tag_options = array();
			if ( ! isset( $this->_options['views']['is_tag'] ) || ( isset( $view['view_id'] ) && ( 'is_tag' === $view['view_id'] ) ) ) {
				$tag_options['__optgroup_1'][] = __( 'All Tags', 'it-l10n-Builder-Cohen' );
			}
			
			$tags = get_tags( $tag_arguments );
			
			if ( ! empty( $tags ) ) {
				foreach ( (array) $tags as $tag ) {
					$tag_options['__optgroup_2'][$tag->term_id] = $tag->name;
				}
				
				natcasesort( $tag_options['__optgroup_2'] );
			}
			
			if ( empty( $tag_options ) ) {
				unset( $views['is_tag'] );
			}
			
			
			$author_options = array();
			if ( ! isset( $this->_options['views']['is_author'] ) || ( isset( $view['view_id'] ) && ( 'is_author' === $view['view_id'] ) ) ) {
				$author_options['__optgroup_1'][] = __( 'All Authors', 'it-l10n-Builder-Cohen' );
			}
			
			$authors = ( function_exists( 'get_users' ) ) ? get_users( array( 'who' => 'authors' ) ) : get_author_user_ids();
			
			if ( ! empty( $authors ) ) {
				foreach ( (array) $authors as $author ) {
					if ( is_object( $author ) && isset( $author->ID ) ) {
						$author_id = $author->ID;
					} else {
						$author_id = $author;
					}
					
					if ( ! in_array( $author_id, $exclude_authors ) ) {
						$author = get_userdata( $author_id );
						$author_options['__optgroup_2'][$author_id] = "$author->display_name ($author->user_login)";
					}
				}
				
				natcasesort( $author_options['__optgroup_2'] );
			}
			
			if ( empty( $author_options ) ) {
				unset( $views['is_author'] );
			}
			
			
			if ( builder_theme_supports( 'builder-extensions' ) ) {
				$extension_options = array();
				
				$extension_options[__( 'Rules', 'it-l10n-Builder-Cohen' )] = array(
					''                      => __( 'Use the Active Extension', 'it-l10n-Builder-Cohen' ),
					'//DISABLE_EXTENSION//' => __( 'Disable the Active Extension', 'it-l10n-Builder-Cohen' ),
				);
				
				$extension_options[__( 'Extensions', 'it-l10n-Builder-Cohen' )] = apply_filters( 'builder_get_extensions_with_names', array() );
				
				
				$extensions_data = apply_filters( 'builder_get_extensions_data', array() );
				
				foreach ( $extensions_data as $extension => $extension_data ) {
					$description = $extension_data['description'];
					
					if ( ! empty( $description ) )
						$description = "<p>$description</p>";
					
					if ( $extension_data['disable_theme_style'] )
						$description .= __( '<p><strong>Notice:</strong> This Extension replaces theme styling with its own.</p>', 'it-l10n-Builder-Cohen' );
					
					$extension_descriptions[] = '"' . str_replace( '"', '\\"', $extension ) . '": "' . str_replace( '"', '\\"', $description ) . '"';
				}
			}
			
			
			$form = new ITForm( $view );
			
?>
	<?php $form->start_form(); ?>
		<h1><?php echo ( empty( $_REQUEST['original_view'] ) ) ? __( 'Add View', 'it-l10n-Builder-Cohen' ) : __( 'Modify View', 'it-l10n-Builder-Cohen' ); ?></h1>
		
		<table class="valign-top">
			<tr><td><?php _e( 'View', 'it-l10n-Builder-Cohen' ); ?></td>
				<td>
					<?php $form->add_drop_down( 'view', $views ); ?>
					<div id="view-description"></div>
				</td>
			</tr>
			<tr id="tag-options" style="display:none;"><td><?php _e( 'Tag', 'it-l10n-Builder-Cohen' ); ?></td>
				<td>
					<?php $form->add_drop_down( 'tag_id', $tag_options ); ?>
				</td>
			</tr>
			<tr id="category-options" style="display:none;"><td><?php _e( 'Category', 'it-l10n-Builder-Cohen' ); ?></td>
				<td>
					<?php $form->add_drop_down( 'category_id', $category_options ); ?>
				</td>
			</tr>
			<tr id="author-options" style="display:none;"><td><?php _e( 'Author', 'it-l10n-Builder-Cohen' ); ?></td>
				<td>
					<?php $form->add_drop_down( 'author_id', $author_options ); ?>
				</td>
			</tr>
			<tr><td><?php _e( 'Layout', 'it-l10n-Builder-Cohen' ); ?></td>
				<td>
					<?php $form->add_drop_down( 'layout', $layouts ); ?>
					<?php ITUtility::add_tooltip( __( 'The Active Layout is the Layout that would be used if this View did not exist.', 'it-l10n-Builder-Cohen' ) ); ?>
				</td>
			</tr>
			<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
				<tr><td><?php _e( 'Extension', 'it-l10n-Builder-Cohen' ); ?></td>
					<td>
						<?php $form->add_drop_down( 'extension', $extension_options ); ?>
						<?php ITUtility::add_tooltip( __( 'The Active Extension is the Extension that would be used if this View did not exist.', 'it-l10n-Builder-Cohen' ) ); ?>
						
						<div id="extension-details"></div>
					</td>
				</tr>
			<?php endif; ?>
		</table>
		<?php $form->add_hidden( 'render_clean', 'dialog' ); ?>
		<?php if ( isset( $_REQUEST['original_view'] ) ) $form->add_hidden( 'original_view', $_REQUEST['original_view'] ); ?>
		
		<p class="submit">
			<?php $save_desc = ( empty( $_REQUEST['original_view'] ) ) ? __( 'Add', 'it-l10n-Builder-Cohen' ) : __( 'Update', 'it-l10n-Builder-Cohen' ); ?>
			<?php $form->add_submit( 'modify_view', array( 'value' => $save_desc, 'class' => 'button-primary save' ) ); ?>
			<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary', 'onclick' => 'it_dialog_remove();' ) ); ?>
		</p>
		
		<div id="view-descriptions" style="display:none;">
			<?php foreach ( (array) $available_views as $id => $view ) : ?>
				<div id="view-<?php echo $this->_parse_view_id( $id ); ?>"><?php echo $view['description']; ?></div>
			<?php endforeach; ?>
		</div>
		
		<?php $form->add_hidden( 'editor_tab', $_REQUEST['editor_tab'] ); ?>
	<?php $form->end_form(); ?>
	<script type="text/javascript">
		<?php if ( isset( $extension_descriptions ) ) : ?>
			var builder_extension_details = {<?php echo implode( ",\n", $extension_descriptions ); ?>};
		<?php endif; ?>
		
//		it_dialog_update_size();
		
		
		init_modify_view_screen();
	</script>
<?php
			
		}
		
		function _modify_views() {
			$available_views = apply_filters( 'builder_get_available_views', array() );
			
			$views = array();
			foreach ( array_keys( (array) $this->_options['views'] ) as $view_id ) {
				$view = $this->_get_view_data( $view_id );
				
				if ( empty( $view ) )
					continue;
				
				$views[$view_id] = $view;
			}
			
			$views = ITUtility::sort_array( $views, 'name' );
			
			
			if ( builder_theme_supports( 'builder-extensions' ) )
				$extensions = apply_filters( 'builder_get_extensions_with_names', array() );
			
			$layout_rules = array(
				'//INHERIT//' => __( 'Use this View\'s Active Layout', 'it-l10n-Builder-Cohen' ),
				''            => __( 'Use the site\'s Default Layout', 'it-l10n-Builder-Cohen' ),
			);
			
			$extension_rules = array(
				''                      => __( 'Use the Active Extension', 'it-l10n-Builder-Cohen' ),
				'//DISABLE_EXTENSION//' => __( 'Disable the Active Extension', 'it-l10n-Builder-Cohen' ),
			);
			
			
			$form = new ITForm();
			
			$add_link = ITDialog::get_link( "{$this->_self_link}&modify_view_screen=1", "modal=true" );
			
?>
	<div class="wrap">
		<?php $form->start_form(); ?>
			<?php ITUtility::screen_icon(); ?>
			<?php $this->_print_tabs(); ?>
			
			<p><?php _e( 'Views allow you to apply specific Layouts to specific parts of your site. For details, click the "Help" button at the top-right.', 'it-l10n-Builder-Cohen' ); ?></p>
			
			<div id="no-views-container">
				<br />
				<p><?php _e( 'Your site currently does not have any Views configured. Please click the "Add View" button below to apply a specific Layout to a site View.', 'it-l10n-Builder-Cohen' ); ?></p>
				<a href="<?php echo $add_link; ?>" class="it-dialog button-secondary link-secondary"><?php _e( 'Add View', 'it-l10n-Builder-Cohen' ); ?></a>
			</div>
			
			<div id="views-container">
				<div class="tablenav">
					<div class="alignleft actions">
						<a href="<?php echo $add_link; ?>" class="it-dialog button-secondary link-secondary"><?php _e( 'Add View', 'it-l10n-Builder-Cohen' ); ?></a>
					</div>
					
					<br class="clear" />
				</div>
				
				<br class="clear" />
				
				<table id="views-table" class="widefat fixed">
					<thead>
						<tr class="thead">
							<th><?php _e( 'Site View', 'it-l10n-Builder-Cohen' ); ?></th>
							<th><?php _e( 'Site View Description', 'it-l10n-Builder-Cohen' ); ?></th>
							<th title=""><?php _e( 'Layout', 'it-l10n-Builder-Cohen' ); ?></th>
							<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
								<th><?php _e( 'Extension', 'it-l10n-Builder-Cohen' ); ?></th>
							<?php endif; ?>
						</tr>
					</thead>
					<tfoot>
						<tr class="thead">
							<th><?php _e( 'Site View', 'it-l10n-Builder-Cohen' ); ?></th>
							<th><?php _e( 'Site View Description', 'it-l10n-Builder-Cohen' ); ?></th>
							<th title=""><?php _e( 'Layout', 'it-l10n-Builder-Cohen' ); ?></th>
							<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
								<th><?php _e( 'Extension', 'it-l10n-Builder-Cohen' ); ?></th>
							<?php endif; ?>
						</tr>
					</tfoot>
					<tbody>
						<?php $class = ' alternate'; ?>
						<?php foreach ( (array) $views as $view_id => $view ) : ?>
							<tr class="view-entry" id="view-<?php echo $this->_parse_view_id( $view_id ); ?>"<?php echo $class; ?>>
								<td>
									<strong><a class="view-name it-dialog" href="<?php echo ITDialog::get_link( "{$this->_self_link}&modify_view_screen=1&original_view=$view_id", "modal=true&max-width=400" ); ?>" title="<?php _e( 'Modify View', 'it-l10n-Builder-Cohen' ); ?>"><?php echo $view['name']; ?></a></strong><br />
									<div class="row-actions">
										<span class="edit"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&modify_view_screen=1&original_view=$view_id", "modal=true&max-width=400" ); ?>" class="it-dialog" title="Modify View"><?php _e( 'Edit', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
										<span class="delete"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&delete_view_screen=$view_id" ); ?>" class="it-dialog" title="Remove View Customization"><?php _e( 'Remove', 'it-l10n-Builder-Cohen' ); ?></a></span>
									</div>
								</td>
								<td><?php echo $view['description']; ?></td>
								<td class="view-layout">
									<?php if ( isset( $this->_options['layouts'][$view['layout']] ) ) : ?>
										<a href="<?php echo $this->_tabless_self_link; ?>&editor_tab=layouts&layout=<?php echo $view['layout']; ?>" title="<?php _e( 'Modify Layout', 'it-l10n-Builder-Cohen' ); ?>">
											<?php echo $this->_options['layouts'][$view['layout']]['description']; ?>
										</a>
									<?php elseif ( isset( $layout_rules[$view['layout']] ) ) : ?>
										<?php echo $layout_rules[$view['layout']]; ?>
									<?php endif; ?>
								</td>
								<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
									<td>
										<?php if ( isset( $view['extension'] ) && isset( $extensions[$view['extension']] ) ) : ?>
											<?php echo $extensions[$view['extension']]; ?>
										<?php elseif ( isset( $extension_rules[$view['extension']] ) ) : ?>
											<?php echo $extension_rules[$view['extension']]; ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>
							</tr>
							<?php $class = ( $class == '' ) ? ' alternate' : ''; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
				
				<br class="clear" />
				
				<div class="tablenav">
					<div class="alignleft actions">
						<a href="<?php echo $add_link; ?>" class="button-secondary link-secondary it-dialog"><?php _e( 'Add View', 'it-l10n-Builder-Cohen' ); ?></a>
					</div>
					
					<br class="clear" />
				</div>
			</div>
			
			<div id="new-view-container" style="display:none;">
				<table>
					<tr class="view-entry" id="view-%parsed_view_id%" style="display:none;">
						<td>
							<strong><a class="view-name it-dialog" href="<?php echo ITDialog::get_link( "{$this->_self_link}&modify_view_screen=1&original_view=%view_id%", "modal=true" ); ?>" title="<?php _e( 'Modify View', 'it-l10n-Builder-Cohen' ); ?>">%view_name%</a></strong><br />
							<div class="row-actions">
								<span class="edit"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&modify_view_screen=1&original_view=%view_id%", "modal=true" ); ?>" class="it-dialog" title="Modify View"><?php _e( 'Edit', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
								<span class="delete"><a href="<?php echo ITDialog::get_link( "{$this->_self_link}&delete_view_screen=%view_id%" ); ?>" class="it-dialog" title="Remove View Customization"><?php _e( 'Remove', 'it-l10n-Builder-Cohen' ); ?></a></span>
							</div>
						</td>
						<td>%view_description%</td>
						<td class="" title="">
							<a href="<?php echo $this->_self_link; ?>&layout=%layout%" title="<?php _e( 'Modify Layout', 'it-l10n-Builder-Cohen' ); ?>">
								%layout_description%
							</a>
						</td>
						<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
							<td>%extension%</td>
						<?php endif; ?>
					</tr>
				</table>
			</div>
			
			<div id="js-vars" style="display:none;">
				<?php foreach ( (array) $available_views as $view => $data ) : ?>
					<div id="view-name-<?php echo $this->_parse_view_id( $view ); ?>"><?php echo $data['name']; ?></div>
					<div id="view-description-<?php echo $this->_parse_view_id( $view ); ?>"><?php echo $data['description']; ?></div>
				<?php endforeach; ?>
				
				<?php foreach ( (array) $this->_options['layouts'] as $layout => $data ) : ?>
					<div id="layout-description-<?php echo $layout; ?>"><?php echo $data['description']; ?></div>
					<div id="layout-link-href-<?php echo $layout; ?>"><?php echo $this->_self_link; ?>&layout=<?php echo $layout; ?></div>
				<?php endforeach; ?>
			</div>
		<?php $form->end_form(); ?>
	</div>
	
	<script type="text/javascript">
		init_modify_views();
	</script>
<?php
		}
		
		function _save_layout() {
			$layout = array();
			
			if ( isset( $_POST['layout-guid'] ) ) {
				if ( ! empty( $this->_options['layouts'][$_POST['layout-guid']] ) )
					$layout = $this->_options['layouts'][$_POST['layout-guid']];
				else
					$layout['guid'] = $_POST['layout-guid'];
			}
			
			$layout['description'] = $_POST['description'];
			$layout['hide_widgets'] = $_POST['hide_widgets'];
			$layout['modules'] = array();
			
			if ( 'custom' == $_POST['width'] )
				$layout['width'] = $_POST['custom_width'];
			else
				$layout['width'] = $_POST['width'];
			
			
			ksort( $_POST );
			foreach ( (array) $_POST as $var => $val ) {
				if ( preg_match( '/^module-(\d+)-(.+)$/', $var, $matches ) )
					$layout['modules'][$_POST["position-{$matches[1]}"]]['data'][$matches[2]] = $val;
				else if ( preg_match( '/^module-(\d+)$/', $var, $matches ) ) {
					$layout['modules'][$_POST["position-{$matches[1]}"]]['module'] = $val;
					
					if ( ! isset( $layout['modules'][$_POST["position-{$matches[1]}"]]['data'] ) )
						$layout['modules'][$_POST["position-{$matches[1]}"]]['data'] = array();
				}
				else if ( preg_match( '/^module-guid-(\d+)$/', $var, $matches ) )
					$layout['modules'][$_POST["position-{$matches[1]}"]]['guid'] = $val;
			}
			
			foreach ( (array) $layout['modules'] as $id => $module ) {
				if ( ! isset( $module['guid'] ) || empty( $module['guid'] ) )
					$layout['modules'][$id]['guid'] = uniqid( '' );
			}
			
			
			ksort( $layout['modules'] );
			
			
			$layout = apply_filters( 'builder_filter_saved_layout_data', $layout );
			
			
			$error = false;
			
			if ( empty( $layout['description'] ) ) {
				ITUtility::show_error_message( __( 'You must supply a Name.', 'it-l10n-Builder-Cohen' ) );
				$error = true;
			}
			
			if ( empty( $layout['width'] ) ) {
				ITUtility::show_error_message( __( 'Please supply a Width.', 'it-l10n-Builder-Cohen' ) );
				$error = true;
			}
			else if ( (string) intval( $layout['width'] ) != (string) $layout['width'] ) {
				ITUtility::show_error_message( __( 'The Width must be an integer number.', 'it-l10n-Builder-Cohen' ) );
				$error = true;
			}
			else if ( $layout['width'] < 50 ) {
				ITUtility::show_error_message( __( 'The Width must be at least 50 pixels. Please increase the Width.', 'it-l10n-Builder-Cohen' ) );
				$error = true;
			}
			
			if ( true === $error ) {
				$this->_cached_layout = $layout;
				$this->_modify_layout();
				
				return;
			}
			
			
			if ( empty( $layout['version'] ) )
				$layout['version'] = 1;
			else
				$layout['version']++;
			
			
			if ( ! empty( $_REQUEST['add_layout'] ) ) {
				foreach ( (array) $this->_options['layouts'] as $ex_layout ) {
					if ( strtolower( $layout['description'] ) === strtolower( $ex_layout['description'] ) ) {
						ITUtility::show_error_message( __( 'A layout with that Name already exists. Please choose a unique name.', 'it-l10n-Builder-Cohen' ) );
						
						$this->_cached_layout = $layout;
						$this->_modify_layout();
						
						return;
					}
				}
				
				$id = uniqid( '' );
				$layout['guid'] = $id;
				
				$_REQUEST['layout'] = $id;
				unset( $_REQUEST['add_layout'] );
				
				$this->_options['layouts'][$id] = $layout;
				
				ITUtility::show_status_message( sprintf( __( '%s created', 'it-l10n-Builder-Cohen' ), $layout['description'] ) );
			}
			else if ( ! empty( $_REQUEST['layout'] ) ) {
				$this->_options['layouts'][$_REQUEST['layout']] = $layout;
				
				ITUtility::show_status_message( sprintf( __( '%s updated', 'it-l10n-Builder-Cohen' ), $layout['description'] ) );
			}
			
			do_action( 'builder_editor_save_custom_settings', $layout );
			
			
			$this->_save();
			
			if ( empty( $_REQUEST['save_and_continue'] ) )
				$this->_list_layouts();
			else
				$this->_modify_layout();
		}
		
		function _modify_layout() {
			$defaults = array(
				'guid'        => '',
				'description' => '',
				'width'       => '960',
				'hide_widths' => 'no',
			);
			$defaults = apply_filters( 'builder_filter_layout_editor_default_values', $defaults );
			
			
			$layout_widths = array(
				'600'		=> __( 'Narrow (600 pixels)', 'it-l10n-Builder-Cohen' ),
				'780'		=> __( 'Medium (780 pixels)', 'it-l10n-Builder-Cohen' ),
				'960'		=> __( 'Wide (960 pixels)', 'it-l10n-Builder-Cohen' ),
			);
			$layout_widths = apply_filters( 'builder_filter_layout_editor_width_options', $layout_widths );
			
			
			foreach ( (array) $layout_widths as $width => $description ) {
				if ( (string) intval( $width ) != (string) $width )
					unset( $layout_widths[$width] );
			}
			
			$layout_widths['custom'] = __( 'Custom...', 'it-l10n-Builder-Cohen' );
			
			
			if ( builder_theme_supports( 'builder-extensions' ) ) {
				$extensions_data = apply_filters( 'builder_get_extensions_data', array() );
				
				$extensions = array( '' => __( '-- No Extension --', 'it-l10n-Builder-Cohen' ) );
				
				$extension_descriptions = array();
				
				foreach ( (array) $extensions_data as $extension => $extension_data ) {
					$extensions[$extension] = $extension_data['name'];
					
					
					$description = $extension_data['description'];
					
					if ( ! empty( $description ) )
						$description = "<p>$description</p>";
					
					if ( $extension_data['disable_theme_style'] )
						$description .= __( '<p><strong>Notice:</strong> This Extension replaces theme styling with its own.</p>', 'it-l10n-Builder-Cohen' );
					
					$extension_descriptions[] = '"' . str_replace( '"', '\\"', $extension ) . '": "' . str_replace( '"', '\\"', $description ) . '"';
				}
			}
			
/*			$layouts = array();
			foreach ( (array) $this->_options['layouts'] as $layout_id => $layout_data ) {
				if ( isset( $_REQUEST['layout'] ) && ( $layout_id == $_REQUEST['layout'] ) )
					continue;
				$layouts[$layout_id] = $layout_data['description'];
			}
			sort( $layouts );*/
			
			
			$layout = array();
			
			if ( isset( $this->_cached_layout ) && is_array( $this->_cached_layout ) )
				$layout = $this->_cached_layout;
			else if ( isset( $_REQUEST['layout'] ) && isset( $this->_options['layouts'][$_REQUEST['layout']] ) )
				$layout = $this->_options['layouts'][$_REQUEST['layout']];
			
			$layout = ITUtility::merge_defaults( $layout, $defaults );
			
			
			if ( isset( $layout['width'] ) && ( (string) intval( $layout['width'] ) == (string) $layout['width'] ) && ! isset( $layout_widths[$layout['width']] ) ) {
				$layout['custom_width'] = $layout['width'];
				$layout['width'] = 'custom';
			}
			
			if ( ! empty( $layout['extension'] ) && ( false !== strpos( $layout['extension'], '%WP_CONTENT_DIR%' ) ) )
				$layout['extension'] = basename( $layout['extension'] );
			
			
			$form = new ITForm( $layout );
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		
		<?php if ( ! empty( $_REQUEST['layout'] ) ) : ?>
			<h2><?php _e( 'Edit Layout', $this->_var ); ?></h2>
		<?php else : ?>
			<h2><?php _e( 'Add New Layout', $this->_var ); ?></h2>
		<?php endif; ?>
		
		<?php $form->start_form(); ?>
			<h3 class="title"><?php _e( 'Settings', 'it-l10n-Builder-Cohen' ); ?></h3>
			<table class="form-table">
				<tr><th scope="row"><label for="description"><?php _e( 'Name', 'it-l10n-Builder-Cohen' ); ?></label></th>
					<td>
						<?php $form->add_text_box( 'description', array( 'size' => '15', 'maxlength' => '15' ) ); ?>
						<?php ITUtility::add_tooltip( __( 'The name helps identify this Layout\'s widget areas. Choose a descriptive, short name.', 'it-l10n-Builder-Cohen' ) ); ?>
					</td>
				</tr>
				<tr><th scope="row"><label for="width"><?php _e( 'Width', 'it-l10n-Builder-Cohen' ); ?></label></th>
					<td>
						<?php $form->add_drop_down( 'width', $layout_widths ); ?>
						<?php ITUtility::add_tooltip( __( 'The width determines how wide the Layout is. Typically, a wider width is better for more complex Layouts that have multiple sidebars while a more narrow width is better for minimalistic Layouts such as one that does not use any sidebars.' ) ); ?>
						<div id="layout-width-custom" style="display:none;">
							<label>
								<?php _e( 'Custom Width', 'it-l10n-Builder-Cohen' ); ?>
								<?php $form->add_text_box( 'custom_width', array( 'size' => '4', 'maxlength' => '5' ) ); ?>
								<?php _e( 'pixels', 'it-l10n-Builder-Cohen' ); ?>
							</label>
						</div>
					</td>
				</tr>
				<?php if ( builder_theme_supports( 'builder-extensions' ) ) : ?>
					<tr><th scope="row"><label for="extension"><?php _e( 'Extension', 'it-l10n-Builder-Cohen' ); ?></label></th>
						<td>
							<?php $form->add_drop_down( 'extension', $extensions ); ?>
							<?php ITUtility::add_tooltip( __( 'Extensions can provide additional code that changes the content, provides additional features, or modifies the styling of the Layout.<br /><br />You can find Extensions in your theme\'s directory inside a directory named "extensions".', 'it-l10n-Builder-Cohen' ) ); ?>
							
							<div id="extension-details"></div>
						</td>
					</tr>
				<?php endif; ?>
				<tr><th scope="row"><label for="hide_widgets"><?php _e( 'Hide Widget Areas', 'it-l10n-Builder-Cohen' ); ?></label></th>
					<td>
						<?php $form->add_drop_down( 'hide_widgets', array( 'no' => __( 'No', 'it-l10n-Builder-Cohen' ), 'yes' => __( 'Yes', 'it-l10n-Builder-Cohen' ) ) ); ?>
						<?php ITUtility::add_tooltip( __( 'Use this option to hide this Layout\'s widget areas from the <strong>Appearance &gt; Widgets</strong> editor. This makes it easier to work with other Layout\'s widget areas.', 'it-l10n-Builder-Cohen' ) ); ?>
					</td>
				</tr>
				<?php do_action( 'builder_editor_add_custom_settings', $layout ); ?>
			</table>
			
			<h3 class="title">Design</h3>
			<table class="form-table layout-modules">
				<tr class="add-module-help"><td colspan="2"><?php _e( 'In order to start building your layout, please click the Add Module link below.', 'it-l10n-Builder-Cohen' ); ?></td></tr>
				<?php
					$position = 1;
					$max_id = 0;
					
					if ( isset( $layout['modules'] ) ) {
						foreach ( (array) $layout['modules'] as $id => $module ) {
							if ( false !== $this->_add_module_fields( $module, $id, $position ) )
								$position++;
							
							if ( $id > $max_id )
							$max_id = $id;
						}
					}
				?>
			</table>
			
			<p class="submit">
				<?php $form->add_submit( 'save', array( 'value' => __( 'Save Layout', 'it-l10n-Builder-Cohen' ), 'class' => 'button-primary' ) ); ?>
				<?php $form->add_submit( 'save_and_continue', array( 'value' => __( 'Save Layout and Continue Editing', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary' ) ); ?>
				<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary cancel' ) ); ?>
			</p>
			
			<input type="hidden" name="next-position" value="<?php echo $position; ?>" />
			<input type="hidden" name="current-position" value="0" />
			<input type="hidden" name="next-id" value="<?php echo ( $max_id + 1 ); ?>" />
			<?php $form->add_hidden( 'self-link', $this->_self_link ); ?>
			<?php if ( isset( $_REQUEST['layout'] ) ) $form->add_hidden( 'layout', $_REQUEST['layout'] ); ?>
			<?php if ( isset( $_REQUEST['layout'] ) ) $form->add_hidden( 'layout-guid', $layout['guid'] ); ?>
			<?php if ( isset( $_REQUEST['add_layout'] ) ) $form->add_hidden( 'add_layout', $_REQUEST['add_layout'] ); ?>
			<?php $form->add_hidden( 'base_url', "{$this->_plugin_url}" ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
			
			foreach ( (array) $this->_modules as $var => $module ) {
				echo "<table id=\"module-editor-$var\" style=\"display:none;\">";
				$this->_add_module_fields( array( 'module' => $var, 'data' => array() ), '%id%', '%position%' );
				echo '</table>';
				
				$form->add_hidden( "module-name-$var", $module->_name );
				$form->add_hidden( "module-editable-$var", ( method_exists( $module, 'edit' ) ) ? '1' : '0' );
				$form->add_hidden( "module-max-$var", $module->_max );
			}
			
			$module_image_paths = array();
			if ( is_dir( "$this->_plugin_path/modules" ) && ( $readdir = opendir( "$this->_plugin_path/modules" ) ) ) {
				while ( ( $module = readdir( $readdir ) ) !== false ) {
					if ( preg_match( '/^\.{1,2}$/', $module ) )
						continue;
					if ( is_dir( "$this->_plugin_path/modules/$module" ) && is_dir( "$this->_plugin_path/modules/$module/images" ) )
						$module_image_paths[] = "modules/$module/images";
				}
			}
			
			echo "<div class=\"preload-images\">\n";
			
			foreach ( (array) $module_image_paths as $path ) {
				if ( $readdir = opendir( "$this->_plugin_path/$path" ) ) {
					while ( ( $image = readdir( $readdir ) ) !== false ) {
						if ( is_file( "$this->_plugin_path/$path/$image" ) && preg_match ( '/\.(png|jpg|jpeg|gif)$/i', $image ) )
							echo "<img src=\"$this->_plugin_url/$path/$image\" alt=\"preload image\" />\n";
					}
				}
			}
			
			echo "</div>\n";
			
?>
	<script type="text/javascript">
		<?php if ( isset( $extension_descriptions ) ) : ?>
			var builder_extension_details = {<?php echo implode( ",\n", $extension_descriptions ); ?>};
		<?php endif; ?>
		
		init_layout_editor();
	</script>
<?php
			
		}
		
		function _add_module_fields( $module, $id, $position ) {
			if ( ! isset( $this->_modules[$module['module']] ) )
				return;
			
			
			$defaults = $this->_modules[$module['module']]->get_defaults();
			$data = array_merge( $defaults, $module['data'] );
			$form = new ITForm( $data, array( 'prefix' => "module-$id" ) );
			
			$layout_option = $this->_modules[$module['module']]->get_layout_option();
			$preview_image_url = $this->_modules[$module['module']]->get_preview_image( $data );
			
?>
	<tr class="module-row" id="module-<?php echo $id; ?>">
		<td class="preview-container">
			<div id="module-<?php echo $id; ?>-preview" class="module-<?php echo $this->_modules[$module['module']]->_var; ?>">
				<?php if ( ! empty( $preview_image_url ) ) : ?>
					<img src="<?php echo $preview_image_url; ?>" />
				<?php endif; ?>
			</div>
		</td>
		<td>
			<h4 class="module-name">
				<span class="module-name">
					<?php if ( isset( $module['data']['name'] ) ) : ?>
						<?php echo $data['name']; ?>
					<?php else : ?>
						<?php echo $this->_modules[$module['module']]->_name; ?>
					<?php endif; ?>
				</span>
			</h4>
			
			<div class="row-actions module-links"></div>
			
			<?php
				foreach ( (array) $data as $var => $val ) {
					$value_array = array( 'value' => $val );
					if ( $var === $layout_option )
						$value_array['class'] = 'layout-option';
					
					$form->add_hidden( $var, $value_array );
					echo "\n";
				}
				
				unset( $form->_args['prefix'] );
			?>
			
			<?php $form->add_hidden( "position-$id", array( 'value' => $position, 'class' => 'module-position' ) ); ?>
			<?php $form->add_hidden( "module-$id", array( 'value' => $module['module'] , 'class' => 'module-var' ) ); ?>
			<?php if ( isset( $module['guid'] ) ) $form->add_hidden( "module-guid-$id", $module['guid'] ); ?>
		</td>
	</tr>
<?php
			
			return true;
		}
		
		
		// Plugin Functions ///////////////////////////
		
		function _addPreviewGroup( $var ) {
			echo '<div id="' . $var . '_layout_editor_preview" class="layout_editor_preview">';
			
			if ( file_exists( $this->_plugin_path . '/images/' . $var . '_' . $this->_options['layouts'][$_REQUEST['layout']]['module'][$var] . '.gif' ) )
				echo '<img src="' . $this->_plugin_url .  '/images/' . $var . '_' . $this->_options['layouts'][$_REQUEST['layout']]['modules'][$var] . '.gif" />';
			
			echo "</div>\n";
		}
		
		function _generate_new_layout_id( $description ) {
			return uniqid();
		}
		
		function _add_layout_views() {
			global $wpdb;
			
			
			foreach ( (array) $this->_options['layouts'] as $id => $layout ) {
				$this->_options['layouts'][$id]['num_views'] = 0;
				$this->_options['layouts'][$id]['num_page_views'] = 0;
				$this->_options['layouts'][$id]['num_post_views'] = 0;
				$this->_options['layouts'][$id]['total_num_views'] = 0;
			}
			
			foreach ( (array) $this->_options['views'] as $id => $view_data ) {
				$layout = $view_data['layout'];
				
				if ( ! isset( $this->_options['layouts'][$layout] ) || ! is_array( $this->_options['layouts'][$layout] ) )
					continue;
				
				$this->_options['layouts'][$layout]['num_views']++;
				$this->_options['layouts'][$layout]['total_num_views']++;
			}
			
			
			$post_layouts = $wpdb->get_results( "SELECT {$wpdb->posts}.post_type, {$wpdb->postmeta}.meta_value AS layout, COUNT(*) AS count FROM {$wpdb->postmeta} LEFT JOIN {$wpdb->posts} ON {$wpdb->postmeta}.post_id={$wpdb->posts}.ID WHERE {$wpdb->postmeta}.meta_key='_custom_layout' GROUP BY meta_value, post_type" );
			
			foreach ( (array ) $post_layouts as $post_layout ) {
				if ( ! isset( $this->_options['layouts'][$post_layout->layout]["num_{$post_layout->post_type}_views"] ) )
					continue;
				
				if ( ! isset( $this->_options['layouts'][$post_layout->layout] ) || ! is_array( $this->_options['layouts'][$post_layout->layout] ) ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->postmeta} SET meta_value=%s WHERE meta_key='_custom_layout' AND meta_value=%s", $this->_options['default'], $post_layout->layout ) );
					continue;
				}
				
				$this->_options['layouts'][$post_layout->layout]["num_{$post_layout->post_type}_views"] += $post_layout->count;
				$this->_options['layouts'][$post_layout->layout]['total_num_views'] += $post_layout->count;
			}
		}
		
		function _orderedSort( $a, $b ) {
			return strcasecmp( $this->_options['layouts'][$a]['description'], $this->_options['layouts'][$b]['description'] );
		}
		
		function _parse_view_id( $view ) {
			$view = str_replace( '&', '-', $view );
			$view = str_replace( '|', '_', $view );
			return $view;
		}
		
		function _get_view_data( $view_id ) {
			$available_views = apply_filters( 'builder_get_available_views', array() );
			
			list( $temp_view_id ) = explode( '__', $view_id );
			if ( ! isset( $available_views[$temp_view_id] ) )
				return array();
			
			
			$layout_rules = array(
				'//INHERIT//' => __( 'Use this View\'s Active Layout', 'it-l10n-Builder-Cohen' ),
				''            => __( 'Use the site\'s Default Layout', 'it-l10n-Builder-Cohen' ),
			);
			
			
			$layout = $this->_options['views'][$view_id]['layout'];
			
			if ( isset( $this->_options['layouts'][$layout] ) )
				$layout_description = $this->_options['layouts'][$layout]['description'];
			else if ( isset( $layout_rules[$layout] ) )
				$layout_description = $layout_rules[$layout];
			else
				$layout_description = '';
			
			
			if ( strpos( $view_id, '__' ) ) {
				list( $view, $term_id ) = explode( '__', $view_id );
				
				if ( preg_match( '|^(builder_)?is_(.+)$|', $view, $matches ) ) {
					$type = $matches[2];
					$type_description = str_replace( '_', ' ', $type );
				}
				
				if ( 'tag' === $type ) {
					$type = 'post_tag';
					$type_description = 'tag';
				}
				
				if ( 'author' === $type ) {
					$author = get_userdata( $term_id );
					$term_name = "$author->display_name ($author->user_login)";
				}
				else
					$term_name = $this->_get_full_term_name( $term_id, $type );
				
				$name = "{$available_views[$view]['name']} - $term_name";
				
				$category_id = $tag_id = $author_id = null;
				if ( 'is_category' === $view )
					$category_id = $term_id;
				else if ( 'is_tag' === $view )
					$tag_id = $term_id;
				else if ( 'is_author' === $view )
					$author_id = $term_id;
				
				
				$view = array(
					'view_id'				=> $view_id,
					'view'					=> $view,
					'term_id'				=> $term_id,
					'category_id'			=> $category_id,
					'tag_id'				=> $tag_id,
					'author_id'				=> $author_id,
					'name'					=> $name,
					'description'			=> "View for $type_description $term_name",
					'layout'				=> $layout,
					'layout_description'	=> $layout_description,
				);
			}
			else {
				$view = array(
					'view_id'				=> $view_id,
					'view'					=> $view_id,
					'term_id'				=> null,
					'category_id'			=> null,
					'tag_id'				=> null,
					'author_id'				=> null,
					'name'					=> $available_views[$view_id]['name'],
					'description'			=> $available_views[$view_id]['description'],
					'layout'				=> $layout,
					'layout_description'	=> $layout_description,
				);
			}
			
			if ( isset( $this->_options['views'][$view_id]['extension'] ) )
				$view['extension'] = $this->_options['views'][$view_id]['extension'];
			else
				$view['extension'] = '';
			
			if ( isset( $this->_options['views'][$view_id]['extension_data'] ) )
				$view['extension_data'] = $this->_options['views'][$view_id]['extension_data'];
			else
				$view['extension_data'] = '';
			
			return $view;
		}
		
		function _get_full_term_name( $term_id, $type ) {
			$term = get_term( $term_id, $type );
			$full_name = $term->name;
			
			while ( $term->parent > 0 ) {
				$term = get_term( $term->parent, $term->taxonomy );
				$full_name = "$term->name \ $full_name";
			}
			
			return $full_name;
		}
	}
	
	new BuilderLayoutEditor();
}
