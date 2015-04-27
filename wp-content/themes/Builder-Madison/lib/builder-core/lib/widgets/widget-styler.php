<?php

/*
Modifies all widgets to support widget styles
Written by Chris Jean for iThemes.com
Version 0.0.1

Version History
	0.0.1 - 2010-11-02
		Initial test version
*/


if ( ! class_exists( 'BuilderWidgetStyler' ) ) {
	class BuilderWidgetStyler {
		var $_original_callbacks = array();
		
		
		function BuilderWidgetStyler() {
			add_action( 'sidebar_admin_setup', array( $this, 'bootstrap_widget_callbacks' ) );
			add_action( 'builder_post_widget_editor', array( $this, 'add_widget_style_dropdown' ) );
			
			add_filter( 'widget_update_callback', array( $this, 'update_widget' ), 10, 4 );
		}
		
		function bootstrap_widget_callbacks() {
			global $wp_registered_widgets, $wp_registered_widget_controls;
			
			foreach ( (array) $wp_registered_widgets as $id => $widget ) {
//				if ( ! isset( $wp_registered_widget_controls[$id] ) )
//					wp_register_widget_control( $id, $widget['name'], array( $this, 'false_widget_control' ) );
				
				$control = $wp_registered_widget_controls[$id];
				
				$wp_registered_widget_controls[$id]['callback'] = array( $this, 'modify_widget_editor' );
				$wp_registered_widget_controls[$id]['params'][0]['builder_cached_widget'] = array( $control['name'], $control['callback'], $id, $widget['params'][0]['number'] );
			}
		}
		
		function modify_widget_editor() {
			$params = func_get_args();
			
			error_log( 'params: ' . print_r( $params, true ) );
			
			list( $name, $callback, $id, $number ) = $params[0]['builder_cached_widget'];
			unset( $params[0]['builder_cached_widget'] );
			
			$args = compact( 'name', 'id', 'number' );
			
			do_action( 'builder_pre_widget_editor', $args );
			
			if ( is_callable( $callback ) )
				call_user_func_array( $callback, $params );
			
			do_action( 'builder_post_widget_editor', $args );
		}
		
		function add_widget_style_dropdown( $args ) {
			$styles = builder_get_widget_styles();
			
			echo "<pre>Args: " . print_r( $args, true ) . "</pre>\n";
			
			it_classes_load( 'it-form.php' );
			
			$form = new ITForm();
			
			$description = apply_filters( 'builder_filter_widget_style_input_description', __( 'Your Builder child theme offers different widget styles. Select one from the drop down for a different look for this widget.', 'it-l10n-Builder-Madison' ) . '<br />' );
			
?>
	<p>
		<label><?php echo $description; ?> <?php $form->add_drop_down( 'it-style', $styles ); ?><br /></label>
	</p>
<?php
			
		}
		
		function update_widget( $instance, $new_instance, $old_instance, $widget ) {
			
		}
		
		function false_widget_control() {}
	}
	
	new BuilderWidgetStyler();
}
