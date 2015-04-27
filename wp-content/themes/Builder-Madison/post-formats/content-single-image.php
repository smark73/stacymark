<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="entry-header clearfix">

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="it-featured-image">
						<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
				</div>
			<?php endif; ?>

		<h3 class="entry-title clearfix">
			<?php the_title(); ?>
		</h3>

		<div class="entry-meta date">
			<span><?php echo get_the_date(); ?>&nbsp;</span>
		</div>

	</div>

	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Madison' ) ); ?>
	</div>

	<div class="entry-footer clearfix">
		<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Madison' ), '<div class="entry-utility edit-entry-link">', '</div>' ); ?>
	</div>
</div>