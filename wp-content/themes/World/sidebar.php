<?php
/**
 * @package WordPress
 * @subpackage World
 * @since World 1.0
 */
?>
</div>
<!-- END content -->

<!-- BEGIN sidebar -->

<!-- Displays the Social Bar if enabled in the admin panel -->
<?php if (get_option('sg_enable_soc') == 'Yes'): ?>
<div id="socialbar">
	<h2><?php echo stripslashes(get_option('sg_social_title')); ?></h2>
	
	<?php if (get_option('sg_facebook_url')): ?>
		<a href="<?php echo stripslashes(get_option('sg_facebook_url')); ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/facebook.png" /></a>
	<?php endif; ?>
	
	<?php if (get_option('sg_twitter_url')): ?>
		<a href="<?php echo stripslashes(get_option('sg_twitter_url')); ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" /></a>
	<?php endif; ?>
	
	<?php if (get_option('sg_google_url')): ?>
		<a href="<?php echo stripslashes(get_option('sg_google_url')); ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/images/google.png" /></a>
	<?php endif; ?>
	
	<?php if (get_option('sg_rss_url')): ?>
		<a href="<?php echo stripslashes(get_option('sg_rss_url')); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/rss.png" /></a>
	<?php endif; ?>
	
</div>
<?php endif; ?>
<!-- End of the social Bar -->


<div id="sidebar">

<?php if ( !function_exists('dynamic_sidebar')
|| !dynamic_sidebar(1) ) : ?>

	<?php get_search_form(); ?>
	
	<div class="clr"></div>

	<!-- begin categories -->
	<h2>Categories</h2>
	<ul><?php wp_list_categories(); ?></ul>
	<!-- end categories -->

	<!-- begin archives -->
	<h2>Archive</h2>
	<ul><?php wp_get_archives('type=monthly'); ?></ul>
	<!-- end archives -->
	
	<!-- begin blogroll -->
	<?php wp_list_bookmarks('category_before=&category_after=&title_before=<h2>&title_after=</h2>'); ?>
	<!-- end blogroll -->

<?php endif; ?>

</div>
<!-- END sidebar -->