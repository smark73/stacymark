<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="it-featured-image">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'index_thumbnail', array( 'class' => 'index-thumbnail' ) ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="entry-wrapper">
		<!-- title, meta, and date info -->
		<div class="entry-header clearfix">

			<h1 class="entry-title">
				<!-- Use this instead? <h3 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h3> -->
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h1>

		</div>

		<!-- post content -->
		<div class="entry-content clearfix">
			<?php the_content( __( 'Read More &rarr;', 'it-l10n-Builder-Cohen' ) ); ?>
		</div>

	</div>

</div>
<!-- end .post -->