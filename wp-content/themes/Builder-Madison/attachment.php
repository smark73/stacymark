<?php

function render_content() {
	global $post;
	
?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : // the loop ?>
					<?php the_post(); ?>
					
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<div class="entry-header clearfix">
							<h1 class="entry-title">
								<?php if ( 0 != $post->post_parent ) : ?>
									<a href="<?php echo get_permalink( $post->post_parent ); ?>" rev="attachment"><?php echo get_the_title( $post->post_parent ); ?></a> &raquo; <?php the_title(); ?>
								<?php else : ?>
									<?php the_title(); ?>
								<?php endif; ?>
							</h1>
						</div>
						
						<!-- post content -->
						<div class="entry-content clearfix">
							<div class="entry-attachment">
								<p class="attachment">
									<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
										<?php echo get_attachment_icon(); ?>
										<?php //echo wp_get_attachment_image( $post->ID, array( $max_width, $max_width * 2 ) ); // max $content_width wide or high. ?>
									</a>
								</p>
							</div>
							
							<?php the_content(); ?>
						</div>
					</div>
					<!--end .post-->
					
					<?php comments_template(); // include comments template ?>
				<?php endwhile; // end of one post ?>
			</div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}


add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );


?>
