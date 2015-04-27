<?php
/**
 * @package WordPress
 * @subpackage siteground-wp73
 * @since siteground-wp73 1.0
 */
?>


<!-- BEGIN sidebar -->
<div id="sidebar">

<?php if ( !function_exists('dynamic_sidebar')
|| !dynamic_sidebar(2) ) : ?>

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