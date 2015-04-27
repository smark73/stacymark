<?php


function render_content() {

?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : // The Loop ?>
					<?php the_post(); ?>
						<?php get_template_part('post-formats/content-single', get_post_format()); ?>
						<!-- end .post -->

						<div class="loop-footer">
							<!-- Previous/Next page navigation -->
							<div class="loop-utility clearfix">
								<div class="alignleft"><?php next_post_link( '%link', __( '&larr; Next Post', 'it-l10n-Builder-Paige' ), FALSE ); ?></div>
								<div class="alignright"><?php previous_post_link( '%link', __( 'Previous Post &rarr;', 'it-l10n-Builder-Paige' ), FALSE ); ?></div>
							</div>
						</div>

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
