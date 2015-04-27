<?php

function render_content() {

?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : // The Loop ?>
					<?php the_post(); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<div class="entry-header clearfix">
								<h1 class="entry-title"><?php the_title(); ?></h1>
						</div>

						<!-- post content -->
						<div class="entry-content clearfix">
							<?php the_content(); ?>
						</div>

						<div class="entry-footer clearfix">
							<?php wp_link_pages( array( 'before' => '<p class="entry-utility"><strong>' . __( 'Pages:', 'it-l10n-Builder-Paige' ) . '</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
							<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Paige' ), '<p class="entry-utility edit-entry-link">', '</p>' ); ?>
						</div>
					</div>
					<!-- end .post -->

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
