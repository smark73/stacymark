<?php

/*
Written by Chris Jean for iThemes.com
Version 1.2.3

Version History
	1.0.0 - 2010-10-05 - Chris Jean
		Release-ready
	1.1.0 - 2011-10-09 - Chris Jean
		Added edit link to widget output
	1.2.0 - 2011-11-23 - Chris Jean
		Added _format_content function
		Widget content is run through _format_content rather than directly
			through the the_content filter
		Added conditional check for the edit_post_link
	1.2.1 - 2012-08-06 - Chris Jean
		Added a tweak to set the global $post variable when rendering the content.
	1.2.2 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.2.3 - 2013-07-19 - Chris Jean
		Fixed a bug that caused duplicate Widget Style classes.
*/


if ( ! class_exists( 'ITWidgetContent' ) ) {
	class ITWidgetContent extends WP_Widget {
		function ITWidgetContent() {
			$widget_ops = array( 'classname' => 'widget-it-content', 'description' => __( 'Add "Widget Content" entries to a sidebar', 'it-l10n-Builder-Madison' ) );
			$control_ops = array( 'width' => 400 );
			
			$this->WP_Widget( 'it_widget_content', __( 'Widget Content', 'it-l10n-Builder-Madison' ), $widget_ops, $control_ops );
		}
		
		function widget( $args, $instance ) {
			if ( empty( $instance['entry_id'] ) )
				return;
			
			extract( $args );
			
			
			$post_backup = false;
			
			if ( isset( $GLOBALS['post'] ) )
				$post_backup = $GLOBALS['post'];
			
			
			$post = get_post( $instance['entry_id'] );
			$GLOBALS['post'] = $post;
			
			if ( empty( $post ) || ! isset( $post->post_content ) )
				return;
			
			
			$content = $this->_format_content( $post->post_content );
			
			
			if ( ! empty( $instance['style'] ) ) {
				$widget_styles = builder_get_widget_styles();
				
				if ( isset( $widget_styles[$instance['style']] ) && preg_match_all( '/<div[^>]* class=[\'"][^\'"]+/i', $before_widget, $matches, PREG_SET_ORDER ) ) {
					foreach ( $matches as $match ) {
						if ( false !== strpos( $match[0], 'background-wrapper' ) )
							$before_widget = preg_replace( '/' . preg_quote( $match[0], '/' ) . '/', "{$match[0]} {$instance['style']}-background-wrapper", $before_widget );
						else
							$before_widget = preg_replace( '/' . preg_quote( $match[0], '/' ) . '/', "{$match[0]} {$instance['style']}", $before_widget );
					}
				}
			}
			
			
			echo $before_widget;
			
			if ( ! empty( $instance['title'] ) )
				echo $before_title . $instance['title'] . $after_title;
			
?>
	<div class="widget-content clearfix">
		<?php echo $content; ?>
		
		<?php if ( 'yes' == builder_get_theme_setting( 'widget_content_edit_link' ) ) : ?>
			<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Madison' ), '<p class="edit-entry-link">', '</p>', $post->ID ); ?>
		<?php endif; ?>
	</div>
<?php
			
			echo $after_widget;
			
			
			if ( false !== $post_backup )
				$GLOBALS['post'] = $post_backup;
			else
				unset( $GLOBALS['post'] );
		}
		
		function _format_content( $content ) {
			if ( 'yes' == builder_get_theme_setting( 'widget_content_the_content_filter' ) )
				return apply_filters( 'the_content', $content );
			
			
			$content = wptexturize( $content );
			$content = convert_smilies( $content );
			$content = convert_chars( $content );
			$content = wpautop( $content );
			$content = shortcode_unautop( $content );
			$content = prepend_attachment( $content );
			$content = do_shortcode( $content );
			
			return $content;
		}
		
		function form( $instance ) {
			$defaults = array(
				'title'    => '',
				'entry_id' => '',
				'style'    => '',
			);
			
			$instance = wp_parse_args( (array) $instance, $defaults );
			
			$form = new ITForm( $instance, array( 'widget_instance' => $this ) );
			
			
			$posts = get_posts( array( 'post_type' => 'widget_content', 'numberposts' => '1000' ) );
			
			$entries = array();
			foreach ( (array) $posts as $post ) {
				$title = $post->post_title;
				
				if ( strlen( $title ) > 80 )
					$title = substr( $title, 0, 80 ) . '...';
				
				$entries[$post->ID] = $title;
			}
			asort( $entries );
			
			
			$styles = builder_get_widget_styles();
			
			if ( ! empty( $styles ) )
				$styles = array_merge( array( '' => 'Default' ), $styles );
			
?>
	<?php if ( ! empty( $entries ) ) : ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'it-l10n-Builder-Madison' ); ?></label>
			<?php $form->add_text_box( 'title', array( 'class' => 'widefat' ) ); ?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'entry_id' ); ?>"><?php _e( 'Widget Content Entry:', 'it-l10n-Builder-Madison' ); ?></label><br />
			<?php $form->add_drop_down( 'entry_id', $entries ); ?>
		</p>
		<?php if ( ! empty( $styles ) ) : ?>
			<p>
				<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Widget Style:', 'it-l10n-Builder-Madison' ); ?></label><br />
				<?php $form->add_drop_down( 'style', $styles ); ?>
			</p>
		<?php else : ?>
			<?php $form->add_hidden( 'style' ); ?>
		<?php endif; ?>
		<p>
			<em>Note: Use the <a href="<?php echo admin_url( 'edit.php?post_type=widget_content' ); ?>">Widget Content editor</a> to manage content for the Widget Content widgets.</em>
		</p>
	<?php else : ?>
		<p>
			This widget allows you to easily add advanced content to a sidebar.
		</p>
		<p>
			Currently, your site doesn't have any Widget Content entries. Use the <a href="<?php echo admin_url( 'edit.php?post_type=widget_content' ); ?>">Widget Content editor</a> to create new entries. Once you have created one or more new Widget Content entries, edit this widget again to select the desired entry and customize the widget.
		</p>
		<?php $form->add_hidden( 'title' ); ?>
		<?php $form->add_hidden( 'entry_id' ); ?>
		<?php $form->add_hidden( 'style' ); ?>
	<?php endif; ?>
<?php
			
		}
	}
}

register_widget( 'ITWidgetContent' );
