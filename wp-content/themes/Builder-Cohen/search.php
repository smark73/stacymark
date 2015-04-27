<?php

function render_content() {
	$show_paging = false;
	
?>
	<div class="loop">
		<div class="loop-header">
			<h4 class="loop-title">
				<?php
					$title = sprintf( __( 'Search Results for "<em>%s</em>"', 'it-l10n-Builder-Cohen' ), get_search_query() );
					
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
								<div class="entry-meta">
									<?php printf( __( 'By %s', 'it-l10n-Builder-Cohen' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
									<?php do_action( 'builder_comments_popup_link', '<span class="comments">&middot; ', '</span>', __( 'Comments %s', 'it-l10n-Builder-Cohen' ), __( '(0)', 'it-l10n-Builder-Cohen' ), __( '(1)', 'it-l10n-Builder-Cohen' ), __( '(%)', 'it-l10n-Builder-Cohen' ) ); ?>
								</div>
								
								<div class="entry-meta date">
									<span class="weekday"><?php the_time( 'l' ); ?><span class="weekday-comma">,</span></span>
									<span class="month"><?php the_time( 'F' ); ?></span>
									<span class="day"><?php the_time( 'j' ); ?><span class="day-suffix"><?php the_time( 'S' ); ?></span><span class="day-comma">,</span></span>
									<span class="year"><?php the_time( 'Y' ); ?></span>
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
								<?php do_action( 'builder_comments_popup_link', '<div class="entry-meta alignright"><span class="comments">', '</span></div>', __( 'Comments %s', 'it-l10n-Builder-Cohen' ), __( '(0)', 'it-l10n-Builder-Cohen' ), __( '(1)', 'it-l10n-Builder-Cohen' ), __( '(%)', 'it-l10n-Builder-Cohen' ) ); ?>
								<div class="entry-meta alignleft">
									<div class="categories"><?php printf( __( 'Categories : %s', 'it-l10n-Builder-Cohen' ), get_the_category_list( ', ' ) ); ?></div>
									<?php the_tags( '<div class="tags">' . __( 'Tags : ', 'it-l10n-Builder-Cohen' ), ', ', '</div>' ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
					<!-- end .post -->
					
					<?php comments_template(); // include comments template ?>
				<?php endwhile; // end of one post ?>
			<?php else : // do not delete ?>
				<div class="hentry">
					<div class="entry-content">
						<p><?php _e( 'No results found.', 'it-l10n-Builder-Cohen' ); ?></p>
						
						<?php get_search_form(); ?>
					</div>
				</div>
			<?php endif; // do not delete ?>
		</div>
		
		<?php if ( $show_paging ) : ?>
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-Builder-Cohen' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Cohen' ) ); ?></div>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php
	
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );


?>
