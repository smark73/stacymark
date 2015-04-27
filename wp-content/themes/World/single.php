<?php
/**
 * @package WordPress
 * @subpackage Team
 * @since World 1.0
 */
?>
<?php get_header(); ?>
<!-- BEGIN content -->
<div id="content">
	<div class="buffer">
	
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	<?php /* If this is a category archive */ if (is_category()) { ?>
	<h2 class="title">Archive for the <strong><?php single_cat_title(); ?></strong> Category</h2>
	<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
	<h2 class="title">Posts Tagged <strong><?php single_tag_title(); ?></strong></h2>
	<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
	<h2 class="title">Archive for <?php the_time('F jS, Y'); ?></h2>
	<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
	<h2 class="title">Archive for <?php the_time('F, Y'); ?></h2>
	<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
	<h2 class="title">Archive for <?php the_time('Y'); ?></h2>
	<?php /* If this is an author archive */ } elseif (is_author()) { ?>
	<h2 class="title">Author Archive</h2>
	<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
	<h2 class="title">Blog Archives</h2>
	<?php } ?>
	
	<?php 
	if (have_posts()) : the_post();
	?>

	<!-- begin post -->
	<div class="single">
		<div class="date_holder">
			<div class="date">
				<div class="post-day"><?php the_time('j') ?></div>
				<div class="post-month-year"><?php the_time('M') ?></div>
			</div>
		</div>
		<div style="padding:0 10px 40px">
			<h2><?php the_title(); ?></h2>
			<div class="postmetadata">
			Author: <span class="orange"><?php the_author() ?></span> | Category: <span class="orange"><?php the_category(', ') ?></span><span class="comm"><?php comments_popup_link('no comments', '1 comment', '% comments'); ?></span><?php the_tags(' | Tags: ', ', ', ''); ?><div class="clr"></div>
			</div>
			<div class="entry">
				<?php the_content(); ?>
			</div>
		</div>
	</div>
	<!-- end post -->
	
	<?php comments_template(); ?>
	<!-- end comments -->
	
	<?php endif; ?>
	</div>
<?php get_sidebar(); get_footer(); ?>