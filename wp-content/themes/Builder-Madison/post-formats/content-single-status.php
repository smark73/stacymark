<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-header clearfix">

	<h3 class="entry-title">
		<?php the_title(); ?>
	</h3>

	<!-- meta, and date info -->
	<div class="entry-meta">
		<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Madison' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
	</div>

	<div class="entry-meta date">
			<span>&nbsp;<?php echo get_the_date(); ?></span>
	</div>

	</div>
	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Madison' ) ); ?>
	</div>

	<div class="entry-footer clearfix">
		<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Madison' ), '<div class="entry-utility edit-entry-link">', '</div>' ); ?>
	</div>

</div>
<!-- end .post -->