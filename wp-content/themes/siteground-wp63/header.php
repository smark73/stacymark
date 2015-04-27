<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php bloginfo('name'); ?> <?php if ( is_single() ) { ?> &raquo; Blog Archive <?php } ?> <?php wp_title(); ?></title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />



<?php wp_head(); ?>
</head>
<body>
	<div class="body_bg">
	<div class="body_bg_1">

	
	<div id="headimg">
		<div id="headimg_1">
			<div id="headimg_2">
				<table cellspacing="0" cellpadding="0" style="float:right; height: 296px; width:430px;">
					<tr>
						<td style="text-align: left; vertical-align: middle;">
							<div id="headerimg">
								<h1><a href="<?php echo get_option('home'); ?>/"><?php bloginfo('name'); ?></a></h1>
								<div class="description"><?php bloginfo('description'); ?></div>	
							</div>
						</td>
					</tr>
				</table>
				<div class="clr"></div>
			</div>
		</div>
	</div>
	
	<div id="menu">
		<div id="menu_wrapper">
			<ul class="nav">
				<li class="page_item"><a href="<?php echo get_settings('home'); ?>/" title="Home">Home</a></li>
				<?php wp_list_pages('sort_column=menu_order&depth=1&title_li=');?>
			</ul>
		</div>
		<div class="clr"></div>
	</div>


	<div id="wrapper">
		<div id="page">
			<div id="holder">