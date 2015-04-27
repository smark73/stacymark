<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2011-08-31 - Chris Jean
		Built from code in version 3.4.1 of lib/layout-engine/layout-engine.php
	1.0.1 - 2011-10-19 - Chris Jean
		Changed admin_bar_menu action priority to 99 in order to put the menu before the Search menu
*/


if ( ! class_exists( 'BuilderAdminBar' ) ) {
	class BuilderAdminBar {
		function BuilderAdminBar() {
			$menu_capability = apply_filters( 'it_builder_menu_capability', 'switch_themes' );
			
			if ( ! current_user_can( $menu_capability ) )
				return;
			
			add_action( 'builder_layout_engine_identified_layout', array( $this, 'layout_identified' ), 0, 2 );
			add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_menu_entry' ), 99 );
		}
		
		function layout_identified( $layout_id, $layout_settings ) {
			$this->_layout_id = $layout_id;
			$this->_layout_description = $layout_settings['layouts'][$layout_id]['description'];
		}
		
		function add_admin_bar_menu_entry( &$wp_admin_bar ) {
			$wp_admin_bar->add_menu( array( 'id' => 'builder', 'title' => 'Builder', 'href' => admin_url( 'admin.php?page=ithemes-builder-theme' ) ) );
			
			if ( ! empty( $this->_layout_id ) ) {
				$wp_admin_bar->add_menu( array( 'parent' => 'builder', 'id' => 'builder_edit_layout', 'title' => sprintf( __( 'Edit Layout (%s)', 'it-l10n-Builder-Cohen' ), $this->_layout_description ), 'href' => admin_url( 'admin.php?page=layout-editor&editor_tab=layouts&layout=' . $this->_layout_id ) ) );
				
				if ( 'on' != get_user_setting( 'widgets_access' ) )
					$wp_admin_bar->add_menu( array( 'parent' => 'builder', 'id' => 'builder_edit_widgets', 'title' => __( 'Manage Widgets for this Layout', 'it-l10n-Builder-Cohen' ), 'href' => admin_url( 'widgets.php?builder_layout_id=' . $this->_layout_id ) ) );
			}
			
			$wp_admin_bar->add_menu( array( 'parent' => 'builder', 'id' => 'builder_edit_settings', 'title' => __( 'Modify Builder Settings', 'it-l10n-Builder-Cohen' ), 'href' => admin_url( 'admin.php?page=theme-settings' ) ) );
			
			
			do_action_ref_array( 'builder_add_admin_bar_menu_entries', array( &$wp_admin_bar ) );
		}
	}
	
	new BuilderAdminBar();
}
