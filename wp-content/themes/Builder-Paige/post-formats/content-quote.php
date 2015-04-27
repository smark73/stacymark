<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Paige' ) ); ?>
	</div>

	<div class="edit-post-link"><?php edit_post_link('Edit'); ?></div>
	
</div>
<!-- end .post -->