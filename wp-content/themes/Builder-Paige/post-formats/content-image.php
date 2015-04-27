<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-header clearfix">

		<h3 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

	</div>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="it-featured-image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
			</a>
		</div>
	<?php endif; ?>

	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Paige' ) ); ?>
	</div>

	<div class="edit-post-link"><?php edit_post_link('Edit'); ?></div>

</div>