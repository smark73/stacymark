<?php
/**
 * This file initializes the image grid extension,
 * but only loads when an extension is active.
 *
 * @author Justin Kopepasah
 * @package builder-core
*/

/**
 * Use the init hook to add image sizes for the
 * extension.
*/
function builder_core_extension_image_grid_add_image_size() {
	add_image_size( 'it-gallery-thumb', 400, 250, true );
	add_image_size( 'it-gallery-singular', 300, 250, true );
}
add_action( 'init', 'builder_core_extension_image_grid_add_image_size' );

/**
 * Enqueue specific scripts for the extension.
*/
function builder_core_extenstion_image_grid_enqueue_scripts() {
	$directory_uri = get_template_directory_uri() . '/lib/builder-core/extensions/image-grid/';

	wp_enqueue_script( 'colorbox', $directory_uri . 'js/colorbox.min.js', array( 'jquery' ), '1.4.29', true );
	wp_enqueue_script( 'builder-core-extension-image-grid', $directory_uri . 'js/extension.js', array( 'colorbox' ) );

	wp_enqueue_style( 'colorbox', $directory_uri . '/css/colorbox.min.css' );
}
add_action( 'wp_enqueue_scripts', 'builder_core_extenstion_image_grid_enqueue_scripts' );

/**
 * Set up the new content for the extension.
*/
function builder_core_extension_image_grid_render_content() {
	global $post, $wp_query;

	$directory_uri = get_template_directory_uri() . '/lib/builder-core/extensions/image-grid/';

	$args = array(
		'ignore_sticky_posts' => true,
		'posts_per_page'      => 9,
		'meta_key'            => '_thumbnail_id',
		'paged'               => get_query_var( 'paged' ),
	);

	$args = wp_parse_args( $args, $wp_query->query );

	query_posts( $args );
?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content extension-columns-wrapper">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php if ( has_post_thumbnail() ) : ?>
						<?php $gallery_url = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'large' ); ?>
						<div <?php post_class( 'extension-column grid_wrapper' ) ?>>
							<div class="extension-column-inner inner">
									<a href="<?php echo $gallery_url[0]; ?>" title="<?php the_title(); ?>" rel="gallery-images" class="gallery-image">
										<?php the_post_thumbnail( 'it-gallery-thumb', array( 'class' => 'extension-featured-image it-gallery-thumb' ) ); ?>
									</a>
									<a href="<?php the_permalink(); ?>" class="permalink"><?php the_title(); ?></a>

							</div>
						</div>
					<?php endif; ?>
				<?php endwhile; ?>
			</div>

			<div class="loop-footer">
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page' , 'it-l10n-Builder-Madison' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Madison' ) ); ?></div>
				</div>
			</div>
		</div>
	<?php else : ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; ?>
<?php

}

/**
 * Hook into the layout engine render to remove
 * the current content and replace it with our
 * new content, but only if we are not viewing a
 * singluar item.
*/
function builder_core_extension_image_grid_change_render_content() {
	if ( ! is_singular() ) {
		remove_action( 'builder_layout_engine_render_content', 'render_content' );
		add_action( 'builder_layout_engine_render_content', 'builder_core_extension_image_grid_render_content' );
	}
}
add_action( 'builder_layout_engine_render', 'builder_core_extension_image_grid_change_render_content', 0 );