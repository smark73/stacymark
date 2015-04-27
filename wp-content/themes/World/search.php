<?php
/**
 * @package WordPress
 * @subpackage World
 * @since World 1.0
 */
?>
<?php get_header(); ?>

<!-- BEGIN content -->
<div id="content">

	<div class="buffer">
	
	<?php 
	if (have_posts()) : ?>
	<h2 class="title">Search Results for "<strong><?php the_search_query(); ?></strong>"</h2>
	<?php
	while (have_posts()) : the_post();
	?>

	<!-- begin post -->
	<div class="single">
		<div class="date_holder">
			<div class="date">
				<div class="post-day"><?php the_time('j') ?></div>
				<div class="post-month-year"><?php the_time('M') ?></div>
			</div>
		</div>
		<div style="padding:0 10px">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="postmetadata">
			Author: <span class="orange"><?php the_author() ?></span> | Category: <span class="orange"><?php the_category(', ') ?></span><span class="comm"><?php comments_popup_link('no comments', '1 comment', '% comments'); ?></span><?php the_tags(' | Tags: ', ', ', ''); ?><div class="clr"></div>
			</div>
			<div class="entry">
				<?php the_excerpt(); ?>
			</div>
		</div>
	</div>
	<!-- end post -->
	<?php endwhile; ?>
	<p class="postnav">
		<?php next_posts_link('&laquo; Previous Entries'); ?> &nbsp; 
		<?php previous_posts_link('Next Entries &raquo;'); ?>
	</p>

	<?php else : ?>
	
	<h2 class="title">Sorry, but nothing matched your search criteria.<br />Please try again with some different keywords.</h2>

	<?php endif; ?>
	
	
	</div>

<?php get_sidebar(); get_footer(); ?>