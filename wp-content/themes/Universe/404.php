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
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<script src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.min.js"></script>

<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>
<?php wp_head(); ?>
</head>
<body class="page_bg">

<div id="parallax">
	<div id="foreground" class="stars"></div>
	<div id="midground" class="stars"></div>
	<div id="background" class="stars"></div>
</div>

<div class="error-404-box">
	<div style="position:relative;width:580px;height:360px;">
		<h2>The page you are looking for cannot be found.<br />Please try searching for it:</h2>
		<?php get_search_form(); ?>
	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
