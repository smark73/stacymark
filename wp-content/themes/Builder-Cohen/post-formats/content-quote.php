<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-wrapper">
		<!-- post content -->
		<div class="entry-content clearfix">
			<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Cohen' ) ); ?>
			<a class="post-infin-link" href="<?php the_permalink() ?>">&infin;</a>
		</div>
	</div>

</div>
<!-- end .post -->