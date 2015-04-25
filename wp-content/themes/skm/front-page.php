<?php


// Remove Page Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'skm_home_loop' );


/**
 * Home Content Area
 *
 */

function skm_home_loop() {
    echo "Stacy Mark";
}



// genesis child theme
genesis();

