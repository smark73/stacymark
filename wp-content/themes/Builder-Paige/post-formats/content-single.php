<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>

		<!-- title, meta, and date info -->
		<div class="entry-header clearfix">

			<div class="entry-meta-wrapper clearfix">

				<div class="entry-meta">
					<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Paige' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
				</div>

				<div class="entry-meta date">
					<span>&nbsp;<?php echo get_the_date(); ?>&nbsp;</span>
				</div>

			</div>

			<h1 class="entry-title clearfix">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>

		</div>

		<div class="it-featured-image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
			</a>
		</div>

	<?php else: ?>

		<!-- title, meta, and date info -->
		<div class="entry-header no-featured-image clearfix">

			<div class="entry-meta-wrapper clearfix">

				<div class="entry-meta">
					<?php printf( __( 'Posted by %s on', 'it-l10n-Builder-Paige' ), '<span class="author">' . builder_get_author_link() . '</span>' ); ?>
				</div>

				<div class="entry-meta date">
					<span>&nbsp;<?php echo get_the_date(); ?>&nbsp;</span>
				</div>

			</div>

			<h1 class="entry-title clearfix">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>

		</div>

	<?php endif; ?>

	<!-- post content -->
	<div class="entry-content clearfix">
		<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Paige' ) ); ?>
	</div>

	<!-- categories, tags and comments -->
	<div class="entry-footer clearfix">
		<div class="entry-meta alignright">
		<?php do_action( 'builder_comments_popup_link', '<div class="comments">', '</div>', __( '%s', 'it-l10n-Builder-Paige' ), __( 'No Comments', 'it-l10n-Builder-Paige' ), __( '1 Comment', 'it-l10n-Builder-Paige' ), __( '% Comments', 'it-l10n-Builder-Paige' ) ); ?>
		</div>
		<div class="entry-meta alignleft">
			<?php wp_link_pages( array( 'before' => '<div class="entry-utility entry-pages">' . __( 'Pages:', 'it-l10n-Builder-Paige' ) . '', 'after' => '</div>', 'next_or_number' => 'number' ) ); ?>
			<div class="categories"><?php printf( __( 'Categories : %s', 'it-l10n-Builder-Paige' ), get_the_category_list( ', ' ) ); ?></div>
			<?php the_tags( '<div class="tags">' . __( 'Tags : ', 'it-l10n-Builder-Paige' ), ', ', '</div>' ); ?>
		</div>
	</div>

</div>
