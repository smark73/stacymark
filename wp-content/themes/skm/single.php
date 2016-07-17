<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0+
 * @link    http://my.studiopress.com/themes/genesis/
 */

// Remove Page Title
//remove_action( 'genesis_post_title', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'skm_ptg_loop' );


/**
 * Content Area
 *
 */

function skm_ptg_loop() {
    
    global $post;
    //print_r($post);
    
    echo '<div class="clearfix ptg-display">';

	    echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'full' ) . '</a>';
	    //get_template_part('templates/content', get_post_format());
	    //do_action('genesis_post_content');
	    //the_content();
	    //echo $post->post_title;
	    //$content = get_the_content();
	    //echo $content;
		//echo $post->post_content;
	    
	    echo '<section class="clearfix centered" style="color:#797471;padding: 5px 0 20px;">';

	    	$content = $post->post_content;
	    	echo strip_tags($content);
	    
	    echo '</section>';

	    do_action('genesis_post_content');

    echo '</div>';

}

//* This file handles single entries, but only exists for the sake of child theme forward compatibility.
genesis();
