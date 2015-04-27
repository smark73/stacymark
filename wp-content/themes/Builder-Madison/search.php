<?php

function render_content() {
	$show_paging = false;

?>
	<div class="loop">
		<div class="loop-header">
			<h4 class="loop-title">
				<?php
					$title = sprintf( __( 'Search Results for "<em>%s</em>"', 'it-l10n-Builder-Madison' ), get_search_query() );

					if ( is_paged() )
						printf( '%s &ndash; Page %d', $title, get_query_var( 'paged' ) );
					else
						echo $title;
				?>
			</h4>
		</div>

		<div class="loop-content">
			<?php if ( have_posts() ) : ?>
				<?php $show_paging = true; ?>
				<?php while ( have_posts() ) : // The Loop ?>
					<?php the_post(); ?>

					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<div class="entry-header clearfix">
							<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

							<?php // Do not print author, comments, or date information for pages ?>
							<?php if ( 'page' !== $GLOBALS['post']->post_type ) : ?>
							<!-- meta, and date info -->
							<div class="entry-meta">
								<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Madison' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
							</div>

							<div class="entry-meta date">
								<a href="<?php the_permalink(); ?>">
									<span>&nbsp;<?php echo get_the_date(); ?></span>
								</a>
							</div>

							<div class="entry-meta">
								<?php do_action( 'builder_comments_popup_link', '<span class="comments">&nbsp; &middot; ', '</span>', __( '%s', 'it-l10n-Builder-Madison' ), __( 'No Comments', 'it-l10n-Builder-Madison' ), __( '1 Comment', 'it-l10n-Builder-Madison' ), __( '% Comments', 'it-l10n-Builder-Madison' ) ); ?>
							</div>
							<?php endif; ?>
						</div>

						<!-- post content -->
						<div class="entry-content clearfix">
							<?php the_excerpt(); ?>
						</div>

						<?php // Do not print category, tag, or comment information for pages ?>
						<?php if ( 'page' !== $GLOBALS['post']->post_type ) : ?>
							<!-- categories, tags and comments -->
							<div class="entry-footer clearfix">
							</div>
						<?php endif; ?>
					</div>
					<!-- end .post -->

					<?php comments_template(); // include comments template ?>
				<?php endwhile; // end of one post ?>
			<?php else : // do not delete ?>
				<div class="hentry">
					<div class="entry-content">
						<p><?php _e( 'No results found.', 'it-l10n-Builder-Madison' ); ?></p>

						<?php get_search_form(); ?>
					</div>
				</div>
			<?php endif; // do not delete ?>
		</div>

		<?php if ( $show_paging ) : ?>
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-Builder-Madison' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Madison' ) ); ?></div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php

}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );


?>
