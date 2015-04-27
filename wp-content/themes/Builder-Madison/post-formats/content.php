<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- title, meta, and date info -->
	<div class="entry-header clearfix">

		<h3 class="entry-title clearfix">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="entry-meta-wrapper clearfix">
			<div class="entry-meta">
				<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Madison' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
			</div>

			<div class="entry-meta date">
				<a href="<?php the_permalink(); ?>">
					<span>&nbsp;<?php echo get_the_date(); ?></span>
				</a>
			</div>

			<div class="entry-meta">
				<?php do_action( 'builder_comments_popup_link', '<span class="comments">&nbsp; &middot; ', '</span>', __( '%s', 'it-l10n-Builder-Madison' ), __( 'No Comments', 'it-l10n-Builder-Madison' ), __( '1 Comment', 'it-l10n-Builder-Madison' ), __( '% Comments', 'it-l10n-Builder-Madison' ) ); ?>
			</div>
		</div>

		<?php if ( has_post_thumbnail() ) : ?>
			<div class="it-featured-image">
				<a href="<?php the_permalink(); ?>">
					<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
				</a>
			</div>
		<?php endif; ?>

	</div>

	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Continue Reading &rarr;', 'it-l10n-Builder-Madison' ) ); ?>
	</div>

	<!-- categories, tags and comments -->
	<div class="entry-footer clearfix">
		<?php edit_post_link( __( 'Edit this entry.', 'it-l10n-Builder-Madison' ), '<div class="entry-utility edit-entry-link">', '</div>' ); ?>
	</div>
</div>