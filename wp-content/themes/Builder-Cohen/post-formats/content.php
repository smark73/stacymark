<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<!-- title, meta, and date info -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="it-featured-image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
			</a>
		</div>
	<?php endif; ?>


	<div class="entry-header-wrapper">


		<div class="entry-meta-wrapper clearfix">

			<div class="entry-meta">
				<?php printf( __( 'Posted by %s', 'it-l10n-Builder-Cohen' ), '<span class="author">' . builder_get_author_link() . '</span>&nbsp;' ); ?>
			</div>

			<div class="entry-meta date">
				<span>&middot; <?php echo get_the_date(); ?>&nbsp;</span>
			</div>

			<div class="entry-meta">
				<?php do_action( 'builder_comments_popup_link', '<span class="comments">&middot; ', '</span>', __( '%s', 'it-l10n-Builder-Cohen' ), __( 'No Comments', 'it-l10n-Builder-Cohen' ), __( '1 Comment', 'it-l10n-Builder-Cohen' ), __( '% Comments', 'it-l10n-Builder-Cohen' ) ); ?>
			</div>

		</div>


		<div class="entry-header clearfix">

			<h3 class="entry-title clearfix">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

		</div>

	</div>

	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Cohen' ) ); ?>
	</div>

	<!-- categories, tags and comments -->
	<div class="entry-footer clearfix">
		<div class="entry-meta alignright">
		<?php do_action( 'builder_comments_popup_link', '<div class="comments">', '</div>', __( '%s', 'it-l10n-Builder-Cohen' ), __( 'No Comments', 'it-l10n-Builder-Cohen' ), __( '1 Comment', 'it-l10n-Builder-Cohen' ), __( '% Comments', 'it-l10n-Builder-Cohen' ) ); ?>
		</div>
		<div class="entry-meta alignleft">
			<?php wp_link_pages( array( 'before' => '<div class="entry-utility entry-pages">' . __( 'Pages:', 'it-l10n-Builder-Cohen' ) . '', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
			<div class="categories"><?php printf( __( 'Categories : %s', 'it-l10n-Builder-Cohen' ), get_the_category_list( ', ' ) ); ?></div>
			<?php the_tags( '<div class="tags">' . __( 'Tags : ', 'it-l10n-Builder-Cohen' ), ', ', '</div>' ); ?>
		</div>
	</div>
</div>