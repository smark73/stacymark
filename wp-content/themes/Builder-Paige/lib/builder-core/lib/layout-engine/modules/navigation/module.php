<?php

/*
Written by Chris Jean for iThemes.com
Version 2.5.1

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleNavigation' ) ) {
	class LayoutModuleNavigation extends LayoutModule {
		var $_name = '';
		var $_var = 'navigation';
		var $_description = '';
		var $_editor_width = 550;
		var $_has_sidebars = false;
		
		
		function LayoutModuleNavigation() {
			if ( builder_theme_supports( 'builder-navigation-module-sidebars' ) )
				$this->_has_sidebars = true;
			
			$this->_name = _x( 'Navigation', 'module', 'it-l10n-Builder-Paige' );
			$this->_description = __( 'This module adds a horizontal navigation bar. Category and Page navigations are available.', 'it-l10n-Builder-Paige' );
			
			$this->LayoutModule();
		}
		
		function export( $data ) {
			if ( is_numeric( $data['type'] ) )
				$data['type'] = wp_get_nav_menu_object( $data['type'] );
			
			return $data;
		}
		
		function _get_import_menu_default( $data, $force_value = false ) {
			if ( is_string( $data['type'] ) )
				return ( true === $force_value ) ? $data['type'] : true;
			if ( is_array( $data['type'] ) )
				$data['type'] = (object) $data['type'];
			
			
			$default = false;
			
			$slug_menu = wp_get_nav_menu_object( $data['type']->slug );
			
			if ( false !== $slug_menu ) {
				if ( ( $data['type']->term_id === $slug_menu->term_id ) || ( strtolower( $data['type']->name ) === strtolower( $slug_menu->name ) ) )
					return ( true === $force_value ) ? $slug_menu->term_id : true;
				
				$default = $slug_menu->term_id;
			}
			
			if ( false === $default ) {
				$name_menu = wp_get_nav_menu_object( $data['type']->name );
				
				if ( false !== $name_menu )
					$default = $name_menu->term_id;
			}
			
			return $default;
		}
		
		function show_conflicts_form( $form, $data ) {
			$default = $this->_get_import_menu_default( $data );
			
			if ( true === $default )
				return;
			
			if ( false !== $default )
				$form->set_option( 'type', $default );
			
			$types = $this->_get_menu_types();
			
			if ( is_string( $data['type'] ) && isset( $types[$data['type']] ) )
				$original_type = $types[$data['type']];
			else
				$original_type = "Custom Menu - {$data['type']->name}";
			
			echo '<p>' . sprintf( __( 'A new Navigation Type must be selected for the Navigation module (original setting: %s).', 'it-l10n-Builder-Paige' ), $original_type ) . "</p>\n";
			echo "<div style='margin-left:20px;'>\n";
			$form->add_drop_down( 'type', $types );
			echo "</div>\n";
			
			
			return true;
		}
		
		function import( $data, $attachments, $post_data ) {
			if ( isset( $post_data['type'] ) )
				$data['type'] = $post_data['type'];
			else
				$data['type'] = $this->_get_import_menu_default( $data, true );
			
			$valid_types = $this->_get_menu_types();
			
			if ( ! isset( $valid_types[$data['type']] ) ) {
				$defaults = $this->get_defaults();
				$data['type'] = $defaults['type'];
			}
			
			
			return $data;
		}
		
		function get_layout_option() {
			return 'type';
		}
		
		function _get_custom_preview_image_name( $data ) {
			if ( is_numeric( $data['type'] ) )
				return "type_custom.gif";
			
			return '';
		}
		
		function _get_defaults( $defaults ) {
			$new_defaults = array(
				'type'		=> 'pages',
				'style'		=> '',
			);
			
			if ( builder_theme_supports( 'builder-navigation-module-sidebars' ) )
				$new_defaults['sidebar'] = '';
			
			return array_merge( $defaults, $new_defaults );
		}
		
		function validate() {
			return true;
		}
		
		function _before_table_edit( $form, $results = true ) {
			
?>
	<p><?php printf( __( 'The Layout Module offers a number of different options for adding navigation elements to your layout. To get the most power out of this feature, use WordPress\' built-in <a href="%s" target="_blank">Menu Editor</a> to create your menus. You can then select to use one of these menus in the Navigation Module.', 'it-l10n-Builder-Paige' ), admin_url( 'nav-menus.php' ) ); ?></p>
	<p><?php printf( __( 'The Legacy Menu Types are present as a fallback in case you have not configured any menus in the <a href="%1$s" target="_blank">Menu Editor</a>. The Builder menu types can be configured in the <a href="%2$s" target="_blank">Builder\'s Theme Settings</a>. The WordPress Pages option uses WordPress\' built-in <code>wp_list_pages</code> function to show a listing of all of your site\'s pages.', 'it-l10n-Builder-Paige' ), admin_url( 'nav-menus.php' ), admin_url( 'admin.php?page=theme-settings#theme-settings-basic-menu_builder' ) ); ?></p>
<?php
			
		}
		
		function _start_table_edit( $form, $results = true ) {
			$types = $this->_get_menu_types();
			
?>
	<tr><td><label for="type"><?php _e( 'Navigation Type', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td><?php $form->add_drop_down( 'type', $types ); ?>
		</td>
	</tr>
<?php
			
		}
		
		function _pre_render_validate() {
			if ( ! is_numeric( $this->_data['type'] ) && ! is_string( $this->_data['type'] ) )
				return false;
			
			return true;
		}
		
		function _modify_module_inner_wrapper_fields( $fields ) {
			if ( ! is_numeric( $this->_data['type'] ) )
				$fields['attributes']['class'][] = "{$fields['class_prefix']}-navigation-{$this->_data['type']}";
			else {
				$fields['attributes']['class'][] = "{$fields['class_prefix']}-navigation-custom-menu";
				$fields['attributes']['class'][] = "{$fields['class_prefix']}-navigation-custom-menu-id-{$this->_data['type']}";
			}
			
			return $fields;
		}
		
		function _render( $fields ) {
			$data = $fields['data'];
			
			
			if ( is_numeric( $data['type'] ) ) {
				if ( function_exists( 'is_nav_menu' ) && is_nav_menu( $data['type'] ) ) {
					$menu = wp_get_nav_menu_object( $data['type'] );
					
					wp_nav_menu( array( 'menu' => $data['type'], 'container_class' => "menu-{$menu->slug}-container {$fields['class_prefix']}-navigation-menu-wrapper" ) );
				}
			}
			else {
				echo "<div class='{$fields['class_prefix']}-navigation-menu-wrapper'>\n";
				echo "<ul class='menu'>\n";
				
				if ( 'pages' === $data['type'] ) {
					$include_pages_setting = builder_get_theme_setting( 'include_pages' );
					
					if ( ! empty( $include_pages_setting ) ) {
						if ( in_array( 'home', (array) $include_pages_setting ) ) {
							$classes = 'home';
							
							if ( is_front_page() )
								$classes .= ' current_page_item';
							
							$link = get_option( 'home' );
							$link = ITUtility::fix_url( $link );
							
							echo "<li class='$classes'><a href='$link'>" . __( 'Home', 'it-l10n-Builder-Paige' ) . "</a></li>\n";
						}
						
						if ( in_array( 'site_name', (array) $include_pages_setting ) ) {
							$classs = 'site-name';
							
							if ( is_front_page() )
								$classes .= ' current_page_item';
							
							$link = get_option( 'home' );
							$link = ITUtility::fix_url( $link );
							$link_text = get_bloginfo( 'name' );
							
							echo "<li class='$classes'><a href='$link'>$link_text</a></li>\n";
						}
						
						$include_page_ids = array();
						
						foreach ( (array) $include_pages_setting as $include_page_id ) {
							if ( is_numeric( $include_page_id ) )
								$include_page_ids[] = $include_page_id;
						}
						
						$exclude_page_ids = apply_filters( 'wp_list_pages_excludes', array() );
						
						$include_page_ids = array_diff( (array) $include_page_ids, (array) $exclude_page_ids );
						
						if ( ! empty( $include_page_ids ) ) {
							$include = implode( ',', (array) $include_page_ids );
							$my_pages = "title_li=&depth=0&include=$include";
							
							wp_list_pages( $my_pages );
						}
					}
				}
				else if ( 'categories' === $data['type'] ) {
					$include_cats_setting = builder_get_theme_setting( 'include_cats' );
					
					if ( ! empty( $include_cats_setting ) ) {
						$include = implode( ',', (array) $include_cats_setting );
						$my_cats = "title_li=&depth=0&include=$include";
						
						wp_list_categories( $my_cats );
					}
				}
				else {
					wp_list_pages( 'title_li=' );
				}
				
				echo "</ul>\n</div>\n";
			}
		}
		
		function _get_menu_types() {
			$types = array();
			
			if ( function_exists( 'wp_get_nav_menus' ) ) {
				$menu_objects = wp_get_nav_menus();
				$menus = array();
				
				foreach ( (array) $menu_objects as $menu )
					$menus[$menu->term_id] = $menu->name;
				
				$types[__( 'Menus' )] = $menus;
			}
			
			$types[__( 'Legacy Menu Types', 'it-l10n-Builder-Paige' )] = array(
				'categories' => __( 'Builder Settings Categories', 'it-l10n-Builder-Paige' ),
				'pages'      => __( 'Builder Settings Pages', 'it-l10n-Builder-Paige' ),
				'wp_legacy'  => __( 'WordPress Pages', 'it-l10n-Builder-Paige' ),
			);
			
			return $types;
		}
	}
	
	new LayoutModuleNavigation();
}
