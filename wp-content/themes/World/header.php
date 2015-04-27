<?php
/**
 * @package WordPress
 * @subpackage World
 * @since World 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />

<?php if (get_option('sg_color_scheme')==''): ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_orange.css" />
<?php endif; ?>
<?php if (get_option('sg_color_scheme')=='Orange'): ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_orange.css" />
<?php endif; ?>
<?php if (get_option('sg_color_scheme')=='Red'): ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_red.css" />
<?php endif; ?>
<?php if (get_option('sg_color_scheme')=='Blue'): ?>
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_blue.css" />
<?php endif; ?>
<?php if (get_option('sg_color_scheme')=='Green'): ?>	
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_green.css" />
<?php endif; ?>
<?php if (get_option('sg_color_scheme')=='Purple'): ?>	
	<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/scheme_purple.css" />
<?php endif; ?>

<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<link href='http://fonts.googleapis.com/css?family=Cookie' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>
<?php // wp_head(); ?>

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>


<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>


<?php if (get_option('sg_enable_fancy')==''): ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/jquery.fancybox.css?v=2.0.6" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.fancybox.pack.js?v=2.0.6"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox({ wrapCSS    : 'fancybox-custom', closeClick : true, helpers : { title : { type : 'inside' },	overlay : { css : { 'background-color' : '#eee' } } } }); });
	</script>
<?php endif; ?>
<?php if (get_option('sg_enable_fancy')=='Yes'): ?>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/js/jquery.fancybox.css?v=2.0.6" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery.fancybox.pack.js?v=2.0.6"></script>
	<script type="text/javascript">
		$(document).ready(function() {
			$(".fancybox").fancybox({ wrapCSS    : 'fancybox-custom', closeClick : true, helpers : { title : { type : 'inside' },	overlay : { css : { 'background-color' : '#eee' } } } }); });
	</script>
<?php endif; ?>

<?php if (get_option('sg_enable_back')==''): ?>
	<script type="text/javascript">
		jQuery(function() { jQuery(window).scroll(function() { if(jQuery(this).scrollTop() != 0) { jQuery('#toTop').fadeIn();} else { jQuery('#toTop').fadeOut(); } });
		jQuery('#toTop').click(function() { jQuery('body,html').animate({scrollTop:0},800); });	});
	</script>	
<?php endif; ?>
<?php if (get_option('sg_enable_back')=='Yes'): ?>
	<script type="text/javascript">
		jQuery(function() { jQuery(window).scroll(function() { if(jQuery(this).scrollTop() != 0) { jQuery('#toTop').fadeIn();} else { jQuery('#toTop').fadeOut(); } });
		jQuery('#toTop').click(function() { jQuery('body,html').animate({scrollTop:0},800); });	});
	</script>
<?php endif; ?>

</head>
<body <?php body_class('page_bg'); ?>> 
<div id="wrapper">

	<div id="header">
		<div class="header_image">

		<?php if (get_option('sg_logo')==''): ?>
			<a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>">
			
			<img src="<?php header_image(); ?>" height="<?php echo get_custom_header()->height; ?>" width="<?php echo get_custom_header()->width; ?>" alt="" /></a>
		<?php else: ?>
			<a href="<?php echo home_url(); ?>" title="<?php bloginfo('name'); ?>"><img src="<?php echo stripslashes(get_option('sg_logo')); ?>" alt="<?php bloginfo('description'); ?>" /></a>
		<?php endif; ?>
		
		
		
		
		
			<h1><a href="<?php echo home_url(); ?>" class="logo"><?php bloginfo('name'); ?></a></h1>
			<h2><?php bloginfo('description');?></h2>
		</div>
	</div>
	<div id="navi">
		<ol class="main_menu">
			<li <?php if (is_home()){ echo 'class="f current_page_item"';}else{'class="f"';} ;?>><a href="<?php echo home_url(); ?>">Home</a></li>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
		</ol>
	</div>
	<div class="breadcrumbs">
	
	<?php // Breadcrumb navigation
	if (get_option('sg_enable_brd') == 'Yes') {
	    if (is_page() && !is_front_page() || is_single() || is_category()) {
	        echo '<ul>';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">';?><img src="<?php bloginfo('template_directory'); ?>/images/home.png" width="13" height="12"  alt="<?php bloginfo('description');?>"/><?php echo '</a></li>';
	 
	        if (is_page()) {
	            $ancestors = get_post_ancestors($post);
	            if ($ancestors) { $ancestors = array_reverse($ancestors);
	                foreach ($ancestors as $crumb) {
	                    echo '<li><a href="'.get_permalink($crumb).'">'.get_the_title($crumb).'</a></li>';}}}
	 
	        if (is_single()) {$category = get_the_category();
	            echo '<li><a href="'.get_category_link($category[0]->cat_ID).'">'.$category[0]->cat_name.'</a></li>';}
	 
	        if (is_category()) {
	            $category = get_the_category();
	            echo '<li>'.$category[0]->cat_name.'</li>';}
	        
	     
	        // Current page
	        if (is_page() || is_single()) {
	            echo '<li class="current">'.get_the_title().'</li>';}
	        echo '</ul>';}
	        elseif (is_front_page()) {
	        // Front page
	        echo '<ul>';
	        echo '<li class="front_page"><a href="'.get_bloginfo('url').'">';?><img src="<?php bloginfo('template_directory'); ?>/images/home.png" width="13" height="12"  alt="<?php bloginfo('description');?>"/><?php echo '</a></li>';
	        echo '<li class="current">You are home</li>';
	        echo '</ul>';
	    }
	    
	   }
	?>
	</div>
	<div class="clr"></div>
		<div class="main">
			<div id="body">