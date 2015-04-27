<?php
/**
 * @package WordPress
 * @subpackage Universe
 * @since Universe 1.0
 */
?>
<?php get_header(); ?>

<!-- BEGIN content -->
<div id="content">

	<div class="buffer">
	
	
	<?php 
	if (have_posts()) :
	while (have_posts()) : the_post();
	?>

	<h2 class="title">Search Results for <strong><?php the_search_query(); ?></strong></h2>
	
	<!-- begin post -->
	<div class="single">
		<div class="date_holder">
			<div class="date">
				<div class="post-day"><?php the_time('j') ?></div>
				<div class="post-month-year"><?php the_time('M y') ?></div>
			</div>
		</div>
		<div style="padding:0 20px 60px">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<div class="postmetadata"><span class="left">category: <?php the_category(', ') ?></span>  <span class="comm"><?php comments_popup_link('няма коментари', '1 коментар', '% коментари'); ?></span><div class="clr"></div></div>
		
			<div class="entry">
				<?php the_content('Виж цялата публикация &raquo;'); ?>
			</div>
			<?php the_tags('<div class="keywords">Tags: ', ', ', '</div>'); ?>
		</div>
	</div>
	
	
	<p class="postnav">
		<?php next_posts_link('&laquo; Previous Entries'); ?> &nbsp; 
		<?php previous_posts_link('Next Entries &raquo;'); ?>
	</p>
	
	<?php endwhile; ?>

	<?php else : ?>
	
	<h2 style="text-shadow:1px 1px #fff;line-height:normal;margin:50px 0;text-align:center;color:#1A569F;font-size:16px;font-family:Arial,Tahoma,Verdana,sans-serif;">Sorry, but nothing matched your search criteria.<br />Please try again with some different keywords.</h2>

	<?php endif; ?>
	
	
	</div>

<?php get_sidebar(); get_footer(); ?>
