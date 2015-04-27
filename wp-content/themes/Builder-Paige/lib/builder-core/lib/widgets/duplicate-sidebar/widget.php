<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.3

Version History
	1.0.0 - 2010-10-05 - Chris Jean
		Release-ready
	1.0.1 - 2010-12-15 - Chris Jean
		Removed the "Inactive Widgets" sidebar from the drop-down
	1.0.2 - 2011-12-20 - Chris Jean
		Updated sidebar listing to exclude Inactive Widgets and Inactive Sidebars
		Now groups sidebars into Layout Sidebars and Non-Layout Sidebars
		Uses full_name of sidebar if available
	1.0.3 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! class_exists( 'ITDuplicateSidebarWidgets' ) ) {
	class ITDuplicateSidebarWidgets extends WP_Widget {
		var $_registered_sidebars = array();
		
		function ITDuplicateSidebarWidgets() {
			$widget_ops = array( 'classname' => 'it_duplicate_sidebar', 'description' => __( 'Duplicate another sidebar&#8217;s widgets', 'it-l10n-Builder-Paige' ) );
			$control_ops = array( 'width' => 350 );
			
			$this->WP_Widget( 'it_duplicate_sidebar', __( 'Duplicate Sidebar', 'it-l10n-Builder-Paige' ), $widget_ops, $control_ops );
			
			if ( ! defined( 'DOING_AJAX' ) )
				add_action( 'admin_head-widgets.php', array( $this, 'store_registered_sidebars' ), 0 );
		}
		
		function widget( $args, $instance ) {
			global $it_storage_cache_layout_settings, $builder_current_sidebar;
			global $wp_registered_sidebars;
			
			if ( in_array( $instance['sidebar'], $builder_current_sidebar ) )
				return;
			
			list( $module_guid, $num ) = explode( '-', $instance['sidebar'] );
			
			$layout = null;
			
			foreach ( (array) $it_storage_cache_layout_settings['layouts'] as $layout_id => $layout_data ) {
				foreach ( (array) $layout_data['modules'] as $module ) {
					if ( $module['guid'] === $module_guid ) {
						$layout = $layout_id;
						break 2;
					}
				}
			}
			
			if ( ! is_null( $layout ) ) {
				global $builder_current_sidebar;
				
				do_action( 'builder_sidebar_register_layout_sidebars', $layout );
				
				if ( ! is_array( $builder_current_sidebar ) )
					$builder_current_sidebar = array();
				
				$builder_current_sidebar[] = $instance['sidebar'];
				
				dynamic_sidebar( $instance['sidebar'] );
				
				array_pop( $builder_current_sidebar );
			}
		}
		
		function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, array( 'sidebar' => '' ) );
			
			$form = new ITForm( $instance, array( 'widget_instance' => $this ) );
			
			if ( empty( $this->_registered_sidebars ) )
				$this->_registered_sidebars = get_transient( 'it-cached-registered-sidebars' );
			if ( ! is_array( $this->_registered_sidebars ) )
				$this->_registered_sidebars = array();
			
			unset( $this->_registered_sidebars['wp_inactive_widgets'] );
			
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'sidebar' ); ?>"><?php _e( 'Sidebar to Duplicate:', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_drop_down( 'sidebar', $this->_registered_sidebars ); ?>
	</p>
<?php
			
		}
		
		function store_registered_sidebars() {
			global $wp_registered_sidebars;
			
			$sidebars = array();
			
			foreach ( (array) $wp_registered_sidebars as $sidebar ) {
				$index = ( ! empty( $sidebar['layout'] ) ) ? __( 'Layout Sidebars', 'it-l10n-Builder-Paige' ) : __( 'Non-Layout Sidebars', 'it-l10n-Builder-Paige' );;
				
				if ( preg_match( '/inactive-sidebar/', $sidebar['class'] ) )
					continue;
				
				if ( ! empty( $sidebar['full_name'] ) )
					$sidebars[$index][$sidebar['id']] = $sidebar['full_name'];
				else
					$sidebars[$index][$sidebar['id']] = $sidebar['name'];
			}
			
			ksort( $sidebars );
			
			foreach ( $sidebars as $index => $data ) {
				asort( $data );
				$this->_registered_sidebars[$index] = $data;
			}
			
			set_transient( 'it-cached-registered-sidebars', $this->_registered_sidebars, 300 );
		}
	}
}

register_widget( 'ITDuplicateSidebarWidgets' );
