<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-header clearfix">

		<div class="entry-meta-wrapper clearfix">

			<div class="entry-meta">
				<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Paige' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
			</div>

			<div class="entry-meta date">
				<span>&nbsp;<?php echo get_the_date(); ?>&nbsp;</span>
			</div>

		</div>

	</div>

	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Paige' ) ); ?>
	</div>

	<div class="edit-post-link"><?php edit_post_link('Edit'); ?></div>

</div>
<!-- end .post -->