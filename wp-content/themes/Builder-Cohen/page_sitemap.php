<?php

/*
Template Name: Sitemap Template
*/


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
							
							<div class="archive-left">
							
								<h4><?php _e( 'Pages', 'it-l10n-Builder-Cohen' ); ?></h4>
								<ul>
									<?php wp_list_pages( 'title_li=&depth=0' ); ?>
								</ul>
								
								<h4><?php _e( 'Recent Blog Posts', 'it-l10n-Builder-Cohen' ); ?></h4>
								<ul>
									<?php wp_get_archives( array( 'type' => 'postbypost', 'limit' => 10 ) ); ?>
								</ul>
							
							</div>
							
							<div class="archive-right">

								<h4><?php _e( 'Posts by Month', 'it-l10n-Builder-Cohen' ); ?></h4>
								<ul>
									<?php wp_get_archives( 'type=monthly&limit=12' ); ?>
								</ul>
								
								<h4><?php _e( 'Posts by Categories', 'it-l10n-Builder-Cohen' ); ?></h4>
								<ul>
									<?php wp_list_categories( 'title_li=&depth=0' ); ?>
								</ul>
							
							</div>
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


?>
