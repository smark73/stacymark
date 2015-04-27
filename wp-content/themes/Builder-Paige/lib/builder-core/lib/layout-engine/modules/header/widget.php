<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.1

Version History
	1.0.0 - 2011-06-29 - Chris Jean
		Release-ready
	1.0.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! class_exists( 'BuilderHeaderWidget' ) ) {
	class BuilderHeaderWidget extends WP_Widget {
		function BuilderHeaderWidget() {
			$widget_ops = array( 'classname' => 'widget-builder-header', 'description' => __( 'Add site title and description to your site', 'it-l10n-Builder-Paige' ) );
			$control_ops = array( 'width' => 300 );
			
			$this->WP_Widget( 'it_builder_header_widget', __( 'Header', 'it-l10n-Builder-Paige' ), $widget_ops, $control_ops );
		}
		
		function widget( $args, $instance ) {
			if ( empty( $instance['show_site_title'] ) && empty( $instance['show_tagline'] ) )
				return;
			
			
			extract( $args );
			
			
			$content = '';
			$link = get_bloginfo( 'url' );
			
			if ( ! empty( $instance['show_site_title'] ) ) {
				$tag = ( builder_is_home() ) ? $instance['home_title_tag'] : $instance['site_title_tag'];
				$title = ( empty( $instance['custom_site_title'] ) ) ? get_bloginfo( 'title' ) : $instance['custom_site_title'];
				
				$content .= "<$tag class='site-title'><a href=\"$link\">$title</a></$tag>";
			}
			
			if ( ! empty( $instance['show_tagline'] ) ) {
				$tag = ( builder_is_home() ) ? $instance['home_tagline_tag'] : $instance['site_tagline_tag'];
				$tagline = ( empty( $instance['custom_tagline'] ) ) ? get_bloginfo( 'description' ) : $instance['custom_tagline'];
				
				$content .= "<$tag class='site-tagline'><a href=\"$link\">$tagline</a></$tag>";
			}
			
			
			echo $before_widget;
			
?>
	<div class="widget-content clearfix">
		<?php echo $content; ?>
	</div>
<?php
			
			echo $after_widget;
		}
		
		function form( $instance ) {
			$defaults = array(
				'show_site_title'   => '1',
				'custom_site_title' => '',
				'site_title_tag'    => 'div',
				'home_title_tag'    => 'h1',
				'show_tagline'      => '1',
				'custom_tagline'    => '',
				'site_tagline_tag'  => 'div',
				'home_tagline_tag'  => 'div',
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults );
			
			$form = new ITForm( $instance, array( 'widget_instance' => $this ) );
			
			
			$use_default_tag_settings_options = array(
				'1' => __( 'Yes' ),
				''  => __( 'No, use custom tag settings', 'it-l10n-Builder-Paige' ),
			);
			
			$generic_tag_options = array(
				'div' => '<div> (Recommended)',
				'h1'  => '<h1> (Not Recommended)',
				'h2'  => '<h2>',
				'h3'  => '<h3>',
				'h4'  => '<h4>',
				'h5'  => '<h5>',
				'h6'  => '<h6>',
			);
			
			$home_title_tag_options = array(
				'div' => '<div>',
				'h1'  => '<h1> (Recommended)',
			);
			$home_title_tag_options = array_merge( $generic_tag_options, $home_title_tag_options );
			
			
			foreach ( $generic_tag_options as $tag => $description )
				$generic_tag_options[$tag] = htmlentities( $description );
			foreach ( $home_title_tag_options as $tag => $description )
				$home_title_tag_options[$tag] = htmlentities( $description );
			
			
			$styles = builder_get_widget_styles();
			
			if ( ! empty( $styles ) )
				$styles = array_merge( array( '' => 'Default' ), $styles );
			
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_site_title' ); ?>"><?php printf ( __( 'Show Site Title (configured in <a href="%s">Settings > General</a>):', 'it-l10n-Builder-Paige' ), admin_url( 'options-general.php' ) ); ?></label><br />
		<?php $form->add_yes_no_drop_down( 'show_site_title' ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'custom_site_title' ); ?>"><?php _e( 'Customize Site Title (leave blank for default Site Title):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_text_box( 'custom_site_title' ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'show_tagline' ); ?>"><?php printf ( __( 'Show Tagline (configured in <a href="%s">Settings > General</a>):', 'it-l10n-Builder-Paige' ), admin_url( 'options-general.php' ) ); ?></label><br />
		<?php $form->add_yes_no_drop_down( 'show_tagline' ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'custom_tagline' ); ?>"><?php _e( 'Customize Tagline (leave blank for default Tagline):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_text_box( 'custom_tagline' ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'home_title_tag' ); ?>"><?php _e( 'Site Title tag (when on the home page):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_drop_down( 'home_title_tag', $home_title_tag_options ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'home_tagline_tag' ); ?>"><?php _e( 'Tagline tag (when on the home page):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_drop_down( 'home_tagline_tag', $generic_tag_options ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'site_title_tag' ); ?>"><?php _e( 'Site Title tag (on other site views):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_drop_down( 'site_title_tag', $generic_tag_options ); ?>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'site_tagline_tag' ); ?>"><?php _e( 'Tagline tag (on other site views):', 'it-l10n-Builder-Paige' ); ?></label><br />
		<?php $form->add_drop_down( 'site_tagline_tag', $generic_tag_options ); ?>
	</p>
	<?php if ( ! empty( $styles ) ) : ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Widget Style:', 'it-l10n-Builder-Paige' ); ?></label><br />
			<?php $form->add_drop_down( 'style', $styles ); ?>
		</p>
	<?php else : ?>
		<?php $form->add_hidden( 'style' ); ?>
	<?php endif; ?>
<?php
			
		}
	}
}

register_widget( 'BuilderHeaderWidget' );
