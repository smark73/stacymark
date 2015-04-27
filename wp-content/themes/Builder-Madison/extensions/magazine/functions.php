<?php

if ( is_admin() )
	return;

if ( ! class_exists( 'BuilderExtensionMagazineLayout' ) ) {
	class BuilderExtensionMagazineLayout {

		function BuilderExtensionMagazineLayout() {

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
					<div class="loop-content">
						<?php while ( have_posts() ) : // the loop ?>
							<?php the_post(); ?>

							<div <?php post_class('magazine-post-wrap'); ?>>
								<div class='magazine-post entry-content'>
									<div class="entry-header">
										<?php if ( has_post_thumbnail() ) : ?>
											<div class="entry-image">
												<a class="post-image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'it-magazine-thumb' ); ?></a>
											</div>
										<?php else : ?>

										<?php endif; ?>

										<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
										<div class="entry-meta">
											<?php printf( __( '%s', 'it-l10n-Builder-Madison' ), '<span class="the_date">' . get_the_date() . '</span>' ); ?>
										</div>
									</div>
									<div class="entry-content">
										<?php the_excerpt(); ?>
									</div>
								</div>
							</div>
						<?php endwhile; // end of one post ?>
					</div>
					<!-- Previous/Next page navigation -->
					<div class="loop-footer">
						<div class="loop-utility clearfix">
							<div class="alignleft"><?php previous_posts_link( __( '&larr; Previous Page' , 'it-l10n-Builder-Madison' ) ); ?></div>
							<div class="alignright"><?php next_posts_link( __( 'Next Page &rarr;', 'it-l10n-Builder-Madison' ) ); ?></div>
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
			return 40;
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

	$BuilderExtensionMagazineLayout = new BuilderExtensionMagazineLayout();
}