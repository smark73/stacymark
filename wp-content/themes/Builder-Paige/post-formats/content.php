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

			<h3 class="entry-title clearfix">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

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

			<h3 class="entry-title clearfix">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>

		</div>

	<?php endif; ?>

	<div class="entry-content clearfix">
		<?php the_excerpt(); ?>
		<a class="excerpt-read-more" href="<?php the_permalink(); ?>">Read More</a>
	</div>

</div>