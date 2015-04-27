<?php

function render_content() {
	global $post;
	
	$area_width = apply_filters( 'builder_layout_engine_get_current_area_width', null );
	$max_width = $area_width - 50;
	
?>
	<?php if ( have_posts() ) : ?>
		<div class="loop">
			<div class="loop-content">
				<?php while ( have_posts() ) : // the loop ?>
					<?php the_post(); ?>
					
					<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<!-- title, meta, and date info -->
						<div class="entry-header clearfix">
							<h1 class="entry-title">
								<?php if ( 0 != $post->post_parent ) : ?>
									<a href="<?php echo get_permalink( $post->post_parent ); ?>" rev="attachment"><?php echo get_the_title( $post->post_parent ); ?></a> &raquo; <?php the_title(); ?>
								<?php else : ?>
									<?php the_title(); ?>
								<?php endif; ?>
							</h1>
						</div>
						
						<!-- post content -->
						<div class="entry-content clearfix">
							<div class="entry-attachment">
								<p class="attachment">
									<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
										<?php echo wp_get_attachment_image( $post->ID, array( $max_width, $max_width * 2 ) ); // max $content_width wide or high. ?>
									</a>
								</p>
							</div>
							
							<?php the_content(); ?>
							
							<div class="photometa clearfix">
								<div class="EXIF">
									<h4><?php _e( 'Image Data', 'it-l10n-Builder-Paige' ); ?></h4>
									
									<?php if ( is_attachment() ) : ?>
										<?php
											$meta = wp_get_attachment_metadata( $post->ID );
											$image_meta = $meta['image_meta'];
											
											if ( ! empty( $image_meta['created_timestamp'] ) )
												$image_meta['created_timestamp'] = date( 'l, F j, Y, g:i a', $image_meta['created_timestamp'] );
											if ( ! empty( $image_meta['aperture'] ) )
												$image_meta['aperture'] = 'f/' . $image_meta['aperture'];
											if ( ! empty( $image_meta['focal_length'] ) )
												$image_meta['focal_length'] .= 'mm';
											if ( ! empty( $image_meta['shutter_speed'] ) )
												$image_meta['shutter_speed'] = number_format( $image_meta['shutter_speed'], 2 ) . ' sec';
											
											$meta_fields = array(
												'camera'            => __( 'Camera', 'it-l10n-Builder-Paige' ),
												'created_timestamp' => __( 'Date Taken', 'it-l10n-Builder-Paige' ),
												'aperture'          => __( 'Aperture', 'it-l10n-Builder-Paige' ),
												'focal_length'      => __( 'Focal Length', 'it-l10n-Builder-Paige' ),
												'iso'               => __( 'ISO', 'it-l10n-Builder-Paige' ),
												'shutter_speed'     => __( 'Shutter Speed', 'it-l10n-Builder-Paige' ),
												'credit'            => __( 'Credit', 'it-l10n-Builder-Paige' ),
												'copyright'         => __( 'Copyright', 'it-l10n-Builder-Paige' ),
												'title'             => __( 'Title', 'it-l10n-Builder-Paige' ),
											);
										?>
										
										<table>
											<tr>
												<th scope="row"><?php _e( 'Dimensions', 'it-l10n-Builder-Paige' ); ?></th>
												<td><?php echo "{$meta['width']}px &times; {$meta['height']}px"; ?></td>
											</tr>
											
											<?php foreach ( (array) $meta_fields as $field => $description ) : ?>
												<?php if ( empty( $image_meta[$field] ) ) continue; ?>
												
												<tr>
													<th scope="row"><?php echo $description; ?></th>
													<td><?php echo $image_meta[$field]; ?></td>
												</tr>
											<?php endforeach; ?>
										</table>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<!-- end .post -->
					
					<?php comments_template(); // include comments template ?>
				<?php endwhile; // end of one post ?>
			</div>
			
			<div class="loop-footer">
				<!-- Previous/Next page navigation -->
				<div class="loop-utility clearfix">
					<div class="alignleft"><?php previous_posts_link( __( '&laquo; Previous Page', 'it-l10n-Builder-Paige' ) ); ?></div>
					<div class="alignright"><?php next_posts_link( __( 'Next Page &raquo;', 'it-l10n-Builder-Paige' ) ); ?></div>
				</div>
			</div>
		</div>
	<?php else : // do not delete ?>
		<?php do_action( 'builder_template_show_not_found' ); ?>
	<?php endif; // do not delete ?>
<?php
	
}



add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
