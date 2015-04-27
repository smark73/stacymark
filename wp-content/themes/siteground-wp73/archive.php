<?php
/**
 * @package WordPress
 * @subpackage siteground-wp73
 * @since siteground-wp73 1.0
 */
?>
<?php get_header(); ?>

<!-- BEGIN content -->
<div id="content" class="archives_post">
	
	<?php 
	if (have_posts()) :
	while (have_posts()) : the_post();
	?>
	
	<div class="single">	
		<div class="post_header">
			<div class="date">
				<div class="post-day"><?php the_time('j') ?></div>
				<div class="post-month-year"><?php the_time('M y') ?></div>
			</div>
			<div class="fright">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
				<div class="postmetadata"><span class="left">category: <?php the_category(', ') ?></span>  <span class="comm"><?php comments_popup_link('no comments', '1 comment', '% comments'); ?></span><div class="clr"></div></div>
			</div>
		</div>	
		<div class="entry">
			<?php the_content('Read the rest of this entry &raquo;'); ?>
		</div>
			<?php the_tags('<div class="keywords">Tags: ', ', ', '</div>'); ?>
	</div>
	
	<!-- end post -->
	
	<?php endwhile; ?> 
	<div class="postnav">
		<?php next_posts_link('&laquo; Previous Entries'); ?> &nbsp; 
		<?php previous_posts_link('Next Entries &raquo;'); ?>
	</div>
	<?php endif; ?>
	
	</div>

<?php get_sidebar(); get_footer(); ?>
