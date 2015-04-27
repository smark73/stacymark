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
    
    $args = array(
        'category_name' => 'Painting',
        'posts_per_page' => 1,
    );
    
    $main_ptg = new WP_Query( $args );
    
    if( $main_ptg->have_posts() ) {
        while ( $main_ptg->have_posts() ) {
            $main_ptg->the_post();
            global $post;
            echo '<div class="clearfix ptg-display">';
            echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'full' ) . '</a>';
            get_template_part('templates/content', get_post_format());
            do_action('genesis_post_content');
            echo '</div>';
        }
    }
    wp_reset_query();
    

       
}



// genesis child theme
genesis();

