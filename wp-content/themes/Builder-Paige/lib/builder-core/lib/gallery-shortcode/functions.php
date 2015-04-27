<?php

/*
Gallery customization functions
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2013-02-14 - Chris Jean
		Built from code taken from lib/main/functions.php
*/


// Customize post gallery output
// Built from version 3.3.0
function builder_custom_post_gallery( $output, $attr ) {
	global $post, $wp_locale;
	
	static $instance = 0;
	$instance++;
	
	
	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( empty( $attr['orderby'] ) )
			unset( $attr['orderby'] );
	}
	
	$defaults = array(
		'builder'    => 'on',
		'captiontag' => 'dd',
		'columns'    => '',
		'exclude'    => '',
		'icontag'    => 'dt',
		'id'         => $post->ID,
		'include'    => '',
		'itemtag'    => 'dl',
		'link'       => 'attachment',
		'margin'     => '5px',
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'size'       => 'thumbnail',
	);
	$defaults = apply_filters( 'builder_filter_gallery_default_attributes', $defaults );
	
	$attr = shortcode_atts( $defaults, $attr );
	extract( $attr );
	
	if ( 'off' == $builder )
		return $output;
	
	if ( 'auto' != $columns ) {
		if ( ! is_numeric( $columns ) )
			$columns = '';
		else if ( $columns < 1 )
			$columns = 1;
		
		if ( empty( $columns ) ) {
			if ( 'auto' == builder_get_theme_setting( 'gallery_default_render_method' ) )
				$columns = 'auto';
			else
				$columns = 3;
		}
	}
	
	$id = intval( $id );
	if ( 'RAND' === $order )
		$orderby = 'none';
	
	if ( ! empty( $include ) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array( 'include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
		
		$attachments = array();
		foreach ( $_attachments as $key => $val )
			$attachments[$val->ID] = $_attachments[$key];
	}
	else if ( ! empty( $exclude ) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array( 'post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	}
	else {
		$attachments = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby ) );
	}
	
	if ( empty( $attachments ) )
		return '';
	
	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link( $att_id, $size, true ) . "\n";
		return $output;
	}
	
	$itemtag = tag_escape( $itemtag );
	$captiontag = tag_escape( $captiontag );
	$float = ( 'rtl' === $wp_locale->text_direction ) ? 'right' : 'left';
	
	$selector = "gallery-{$instance}";
	$size_class = sanitize_html_class( $size );
	
	if ( ( 'auto' != $columns ) && apply_filters( 'use_default_gallery_style', true, $attr ) ) {
		$itemwidth = 100 / $columns;
		
		ob_start();
		
?>
	<style type="text/css" scoped="scoped">
		#<?php echo $selector; ?> {
			clear: both;
		}
		#<?php echo $selector; ?> .gallery-item-wrapper {
			float: <?php echo $float; ?>;
			margin: 0;
			padding: 0;
			width: <?php echo $itemwidth; ?>%;
		}
		#ie6 #<?php echo $selector; ?> .gallery-item-wrapper,
		#ie7 #<?php echo $selector; ?> .gallery-item-wrapper {
			width: <?php echo floor( $itemwidth ); ?>%;
		}
		#<?php echo $selector; ?> .gallery-item {
			display: block;
			float: none;
			margin: <?php echo $margin; ?>;
			width: auto !important;
		}
		#ie6 #<?php echo $selector; ?> .gallery-item {
			display: inline;
		}
		#<?php echo $selector; ?> .gallery-icon a {
			display: block;
			line-height: 0;
		}
		#<?php echo $selector; ?> img {
			width: 100% !important;
			max-width: 100% !important;
			height: auto !important;
		}
		#<?php echo $selector; ?> .gallery-caption {
			overflow: hidden;
		}
		#ie6 #<?php echo $selector; ?> .gallery-caption {
			word-wrap: break-word;
		}
	</style>
<?php
		
		$output .= ob_get_clean();
	}
	
	$output .= "<div id='$selector' class='gallery galleryid-$id gallery-columns-$columns gallery-size-$size_class clearfix'>\n";
	
	$output = apply_filters( 'gallery_style', $output );
	
	
	$count = 0;
	
	foreach ( $attachments as $id => $attachment ) {
		if ( 'file' == $attr['link'] )
			$link = wp_get_attachment_link( $id, $size );
		else if ( 'none' == $attr['link'] )
			$link = wp_get_attachment_image( $id, $size );
		else
			$link = wp_get_attachment_link( $id, $size, true );
		
		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$gallery_item_wrapper_class = 'gallery-item-wrapper gallery-item-wrapper-with-caption';
			$gallery_item_class = 'gallery-item gallery-item-with-caption';
		}
		else {
			$gallery_item_wrapper_class = 'gallery-item-wrapper';
			$gallery_item_class = 'gallery-item';
		}
		
		if ( is_numeric( $columns ) )
			$output .= "<div class='$gallery_item_wrapper_class'>\n";
		
		$output .= "<{$itemtag} class='$gallery_item_class'>";
		$output .= "
			<$icontag class='gallery-icon'>
				$link
			</$icontag>";
		if ( $captiontag && trim( $attachment->post_excerpt ) ) {
			$output .= "
				<$captiontag class='gallery-caption'>
				" . wptexturize($attachment->post_excerpt) . "
				</$captiontag>";
		}
		$output .= "</{$itemtag}>\n";
		
		if ( is_numeric( $columns ) )
			$output .= "</div>\n";
		
		if ( is_numeric( $columns ) && ( 0 == ( ++$count % $columns ) ) )
			$output .= '<br style="clear: both" />';
	}
	
	if ( is_numeric( $columns ) && ( 0 != ( $count % $columns ) ) )
		$output .= '<br style="clear: both" />';
	
	$output .= "</div>\n";
	
	
	return $output;
}
