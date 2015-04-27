<?php
/**
 * @package WordPress
 * @subpackage Universe
 * @since Universe 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_url'); ?>/prettyPhoto.css" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<script src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.min.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/jquery.prettyPhoto.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/iSmallMenu.js"></script>
<script src="<?php bloginfo('template_url'); ?>/js/scripts.js"></script>

<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body class="page_bg">

<div id="parallax"></div>
<div id="foreground" class="stars"></div>
<div id="midground" class="stars"></div>
<div id="background" class="stars"></div>

<div id="wrapper">

	<div id="header">
		<div class="header_image">
			<h1><a href="<?php echo get_option('home'); ?>" class="logo"><?php bloginfo('name'); ?></a></h1>
			<h2><?php bloginfo('description');?></h2>
		</div>
	</div>
	<div id="navi">
		<ol class="main_menu">
			<li <?php if (is_home()){ echo 'class="f current_page_item"';}else{'class="f"';} ;?>><a href="<?php echo get_option('home'); ?>">Home</a></li>
			<?php wp_list_pages('sort_column=menu_order&title_li='); ?>
		</ol>
	</div>
	<div class="m_bg">
		<div class="main">
			<div class="main_t_bg">
				<div id="body">
