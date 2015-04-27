<?php

if ( is_admin() )
	return;
	
if ( ! class_exists( 'BuilderExtensionSlider' ) ) {
	class BuilderExtensionSlider {
		
		function BuilderExtensionSlider() {
			
			// Include the file for setting the image sizes
			require_once( dirname( __FILE__ ) . '/lib/image-size.php' );
			
			// Helpers
			it_classes_load( 'it-file-utility.php' );
			$this->_base_url = ITFileUtility::get_url_from_file( dirname( __FILE__ ) );
			
			// Printing necessary scripts
			add_action( 'wp_enqueue_scripts',  array( &$this, 'print_scripts' ) );
			
			// Calling only if not on a singular
			if ( ! is_singular() ) {
				add_action( 'builder_layout_engine_render', array( &$this, 'change_render_content' ), 0 );
			}
			
		}
		
		function print_scripts() {
			wp_enqueue_script( 'slides', "$this->_base_url/js/slides.min.jquery.js", array( 'jquery' ) );
			wp_enqueue_script( 'builder-extension-slider', "$this->_base_url/js/slides-starter.js", array( 'slides' ) );
		}
		
		function extension_render_content() {
			global $wp_query, $paged, $post_organizer, $more, $post;
			
			// Removing the default more link.
			$more = 0;
			
			add_filter( 'excerpt_length', array( &$this, 'excerpt_length' ) );
			add_filter( 'excerpt_more', array( &$this, 'excerpt_more' ) );
			
			$post_organizer = array();
			
		?>
			<div class="loop">
				<?php
					$args = array(
						'posts_per_page' => 6,
						'order'          => 'date',
						'meta_key'       => '_thumbnail_id'
					);
			
					$args = wp_parse_args( $args, $wp_query->query );
					unset( $args[ 'paged' ] );
					$posts = get_posts( $args );
				?>
				<?php if ( get_query_var( 'paged' ) <= 1 ) : // This calls the slider only on the front page. ?>
					<div class="loop-header">
						<div id="slides">
							<div class="slides_container slides-thumb-wrapper">
								<?php foreach ( $posts as $post ) : ?>
									<?php setup_postdata( $post ); ?>
									<?php array_push( $post_organizer, $post->ID ); ?>
							
									<div class="slide">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'it-slider-thumb' ); ?>
											</a>
										<?php else : ?>
											<?php edit_post_link( 'Add a feature image', '<img width="615" height="300" src="' . $this->_base_url . '/images/it-slider-thumb.jpg" class="it-slider-thumb no-thumb" /></a><span class="add_feature_image">', '</span>' ) ; ?>
										<?php endif; ?>
								
										<div class="caption entry-meta" style="bottom:0">
											<p><?php the_title(); ?></p>
										</div>
									</div>
								<?php endforeach; // end of one post ?>
							</div>
						</div>
					</div>
				<?php else: 
				foreach ( $posts as $post ) {
					array_push( $post_organizer, $post->ID );
				}
				?>	
				<?php endif; ?>
				<?php
					// this is for the second query
					$temp = $wp_query;
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var('paged') : 1;
					$posts_per_page = get_option( 'posts_per_page' );
					$my_offset = 6;
					
					$args = array(
						'paged' => get_query_var( 'paged' ),
						'posts_per_page' => $posts_per_page,
						'numberposts' => $posts_per_page,
						'post__not_in' => $post_organizer,
					);
					
					$args = wp_parse_args( $args, $wp_query->query );
					
					query_posts( $args );
					$total_posts = ( $posts_per_page * $wp_query->max_num_pages ) - $my_offset;
					$max_num_pages = round( $total_posts / $posts_per_page );
				?>
				<?php if ( have_posts() ) : ?>
					<div class="loop-content older-posts">
						<?php while ( have_posts() ) : the_post(); ?>
							<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<h3 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="entry-content index">
									<?php if ( has_post_thumbnail() ) : ?> 
										<a href="<?php the_permalink(); ?>" class="index-thumb-wrapper">
											<?php the_post_thumbnail( 'it-teaser-thumb', array( 'class' => 'alignleft' ) ); ?>
										</a>
									<?php else : ?>
										<?php edit_post_link( '<img width="120" height="120" src="' . $this->_base_url . '/images/no-feature-image.jpg" class="it-teaser-thumb alignleft" />', '<div class="add_feature_image_thumb">', '</div>' ) ; ?>
									<?php endif; ?>
									<?php the_excerpt(); ?>
								</div>
								<div class="entry-footer">
									<?php printf( __( 'Posted on %s', 'it-l10n-Builder-Madison' ), '<span class="the_date">' . get_the_date() . '</span>' ); ?>
									<?php printf( __( 'by %s', 'it-l10n-Builder-Madison' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
									<?php do_action( 'builder_comments_popup_link', '<span class="comments">', '</span>', __( '%s Comments', 'it-l10n-Builder-Madison' ), __( '0', 'it-l10n-Builder-Madison' ), __( '1', 'it-l10n-Builder-Madison' ), __( '%', 'it-l10n-Builder-Madison' ) ); ?>
								</div>
							</div>
							<!-- end .post -->
						<?php endwhile; ?>
					</div>
					
					<div class="loop-footer">
						<div class="loop-utility clearfix">
							<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-Builder-Madison' ) ); ?></div>
							<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Madison' ), $max_num_pages ); ?></div>
						</div>
					</div>	
				</div>
			<?php else : // do not delete ?>
				<?php do_action( 'builder_template_show_not_found' ); ?>
			<?php endif; // do not delete ?>
		<?php
			$wp_query = null; $wp_query = $temp;
			remove_filter( 'excerpt_length', array( &$this, 'excerpt_length' ) );
			remove_filter( 'excerpt_more', array( &$this, 'excerpt_more' ) );
		}
				
		function excerpt_length( $length ) {
			return 55;
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
	
	$BuilderExtensionSlider = new BuilderExtensionSlider();
}
