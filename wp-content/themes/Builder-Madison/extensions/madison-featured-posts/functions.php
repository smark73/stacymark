<?php

function wip_extension_render_content() {
?>

	<?php // Creating a New Loop

	$args = array(
		'posts_per_page' => 9,
		'post_type' => 'post',
		'ignore_sticky_posts' => 1,
		'meta_query' => array( array( 'key' => '_thumbnail_id' ) )
	);

	$test_loop = new WP_Query( $args ); ?>

	<?php if ( $test_loop->have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while( $test_loop->have_posts() ) : ?>
					<?php $test_loop->the_post(); ?>
						<div id="post-<?php the_ID(); ?>" <?php post_class('madison-featured-post'); ?>>
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="it-featured-image">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
									</a>
								</div>
							<?php endif; ?>
							<h3 class="entry-title clearfix">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
						</div>
				<?php endwhile; // end of loop ?>
				<?php wp_reset_postdata(); // reset the loop ?>
			</div>

			<!-- Previous/Next page navigation -->
			<div class="loop-footer">
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page' , 'it-l10n-Builder-Madison' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Madison' ) ); ?></div>
				</div>
			</div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php

}

/**
 * Hook into the layout engine render to remove
 * the current content and replace it with our
 * new content.
*/
function wip_extension_render_change_content() {
	remove_action( 'builder_layout_engine_render_content', 'render_content' );
	add_action( 'builder_layout_engine_render_content', 'wip_extension_render_content' );
}
add_action( 'builder_layout_engine_render', 'wip_extension_render_change_content', 0 );