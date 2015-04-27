<?php
/**
 * @package WordPress
 * @subpackage siteground-wp73
 * @since siteground-wp73 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body class="page_bg <?php body_class( $class ); ?>">
<div id="pagelight">
<div id="wrapper">

	<div id="header">
		<h1><a href="<?php home_url(); ?>" class="logo"><?php bloginfo('name'); ?></a></h1>
		<h2><?php bloginfo('description');?></h2>
	</div>
	
	<div id="topMenu">
		<div class="leftBg">
			<div class="rightBg">
				<ul>
					<?php wp_list_pages('title_li=&depth=1'); ?>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="main_m_bg">
		<div class="main_b_bg">
			<div class="main_t_bg">