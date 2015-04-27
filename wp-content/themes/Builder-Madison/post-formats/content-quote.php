<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-wrapper">
		<!-- post content -->
		<div class="entry-content clearfix">
			<a class="post-infin-link" href="<?php the_permalink() ?>">&infin;</a>
			<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Madison' ) ); ?>
		</div>
	</div>

	<div class="entry-footer clearfix">
		<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Madison' ), '<div class="entry-utility edit-entry-link">', '</div>' ); ?>
	</div>


</div>
<!-- end .post -->