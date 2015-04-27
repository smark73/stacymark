<?php

if ( is_admin() )
	return;

if ( ! class_exists( 'BuilderExtensionTeasersRight' ) ) {
	class BuilderExtensionTeasersRight {

		function BuilderExtensionTeasersRight() {

			// Include the file for setting the image sizes
			require_once( dirname( __FILE__ ) . '/lib/image-size.php' );

			// Helpers
			it_classes_load( 'it-file-utility.php' );
			$this->_base_url = ITFileUtility::get_url_from_file( dirname( __FILE__ ) );

			// Calling only if not on a singular
			if ( ! is_singular() ) {
				add_action( 'builder_layout_engine_render', array( &$this, 'change_render_content' ), 0 );
			}
		}

		function extension_render_content() {
			add_filter( 'excerpt_length', array( &$this, 'excerpt_length' ) );
			add_filter( 'excerpt_more', array( &$this, 'excerpt_more' ) );
		?>
			<?php if ( have_posts() ) : ?>
				<div class="loop">
					<div class="teasers-loop-wrapper">
					<div class="loop-content">
						<?php while ( have_posts() ) : // the loop ?>
							<?php the_post(); ?>

							<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<div class="entry-content teasers">
									<?php if ( has_post_thumbnail() ) : ?>
										<a href="<?php the_permalink(); ?>" class="teasers-thumb-wrapper">
											<?php the_post_thumbnail( 'it-teaser-thumb', array( 'class' => 'alignright teaser-thumb' ) ); ?>
										</a>
									<?php else : ?>

									<?php endif; ?>

									<div class="teasers-entry-wrapper">

									<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

									<div class="entry-meta">
										<?php printf( __( 'Posted on %s', 'it-l10n-Builder-Cohen' ), '<span class="the_date">' . get_the_date() . '</span>' ); ?>
										<?php printf( __( 'by %s', 'it-l10n-Builder-Cohen' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
										<?php do_action( 'builder_comments_popup_link', '<span class="comments"> | ', '</span>', __( '%s Comments', 'it-l10n-Builder-Cohen' ), __( '0', 'it-l10n-Builder-Cohen' ), __( '1', 'it-l10n-Builder-Cohen' ), __( '%', 'it-l10n-Builder-Cohen' ) ); ?>
									</div>
									<?php the_excerpt(); ?>

									</div>

								</div>
							</div>
							<!-- end .post -->
						<?php endwhile; // end of one post ?>
					</div>
					</div>
					<!-- Previous/Next page navigation -->
					<div class="loop-footer">
						<div class="loop-utility clearfix">
							<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page' , 'it-l10n-Builder-Cohen' ) ); ?></div>
							<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Cohen' ) ); ?></div>
						</div>
					</div>
				</div>
			<?php else : // do not delete ?>
				<?php do_action( 'builder_template_show_not_found' ); ?>
			<?php endif; // do not delete ?>
		<?php
			remove_filter( 'excerpt_length', array( &$this, 'excerpt_length' ) );
			remove_filter( 'excerpt_more', array( &$this, 'excerpt_more' ) );
		}

		function excerpt_length( $length ) {
			return 60;
		}

		function excerpt_more( $more ) {
			global $post;
			return '...<p><a href="'. get_permalink( $post->ID ) . '" class="more-link">Read More&rarr;</a></p>';
		}

		function change_render_content() {
			remove_action( 'builder_layout_engine_render_content', 'render_content' );
			add_action( 'builder_layout_engine_render_content', array( &$this, 'extension_render_content' ) );
		}

	} // end class

	$BuilderExtensionTeasersRight = new BuilderExtensionTeasersRight();
}
