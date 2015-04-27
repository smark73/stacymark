<?php
/**
 * @package WordPress
 * @subpackage World
 * @since World 1.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php bloginfo('name'); ?> <?php wp_title(); ?></title>

<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<link href='http://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css'>

<script src="<?php bloginfo('template_url'); ?>/js/jquery-1.4.min.js"></script>

<?php if (function_exists('wp_enqueue_script') && function_exists('is_singular')) : ?>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php endif; ?>
<?php wp_head(); ?>

<style type="text/css">
#searchform #s {
    background: none repeat scroll 0 0 #FFFFFF;
    border: 1px solid #171717;
    border-radius: 5px 5px 5px 5px;
    color: #171717;
    float: left;
    font-family: Arial,Tahoma,sans-serif;
    font-size: 12px;
    height: 22px;
    overflow: hidden;
    padding-left: 4px;
    padding-right: 4px;
    width: 220px;
}
.screen-reader-text {
	display: none;
}
#searchform #searchsubmit {
    background: url("<?php bloginfo('template_url'); ?>/images/search_btn.png") no-repeat scroll left top transparent;
    border: medium none;
    font-size: 14px;
    font-weight: 700;
    height: 26px;
    margin: 0 0 0 10px;
    text-indent: -99999px;
    width: 26px;
}    
</style>

</head>
<body style="background:#FCFCFC">
<div class="">

	<div style="width: 680px;height:360px;margin: 250px auto 0;text-align: center;">
		<h2 style="font-family:'Raleway',Arial;color: #171717;font-weight: 400;font-size: 30px;line-height: 30px;">The page you are looking for cannot be found.<br />Please try searching for it:</h2>

		<div style="width:270px;display: block; margin: 30px auto;">
		<?php get_search_form(); ?>
		</div>
	</div>


<?php wp_footer(); ?>
</body>
</html>