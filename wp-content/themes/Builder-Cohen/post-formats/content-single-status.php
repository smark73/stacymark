<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-header clearfix">

	<!-- meta, and date info -->
	<div class="entry-meta">
		<?php printf( __( 'Posted by %s', 'it-l10n-Builder-Cohen' ), '<span class="author">' . builder_get_author_link() . '</span>&nbsp;' ); ?>
	</div>

	<div class="entry-meta date"> on
		<a href="<?php the_permalink(); ?>">
			<span><?php echo get_the_date(); ?></span>
		</a>
	</div>

	</div>
	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Cohen' ) ); ?>
	</div>

	<div class="edit-post-link"><?php edit_post_link('Edit'); ?></div>

</div>
<!-- end .post -->