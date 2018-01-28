<?php

// Remove page header for page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
//remove_action( 'genesis_header', 'skm_hdr_title' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );


// Remove Page Title
//remove_action( 'genesis_post_title', 'genesis_do_post_title' );
//remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'page_loop' );



// custom page header
add_action( 'genesis_before_header', 'skm_cust_pg_hdr' );
function skm_cust_pg_hdr() {
    ?>
        <div class="ptgs-hdr">
            <div class="ptg-top-bar">
            
                <div class="ptg-top-bar-lft">
                    <a href="/" title="Stacy Mark - Paintings">
                        <span class="hdr-name">Stacy Mark</span><span class="hdr-vsep">|</span><span class="hdr-art">ART</span>
                    </a>
                    </div>
                </div>
                
                <div class="ptg-top-bar-rt">
                    <?php get_template_part('templates/paintings-menu') ?>
                </div>
                
                <div class="clearfix"></div>

            </div>
        </div>
    <?php
}

function page_loop(){
    ?>
            <div class="skm-gallery grid">

                <?php
                $thumbs = new WP_Query(array(
                    'category_name' => 'Abstract Landscape',
                    'orderby' => 'rand',
                    ));

                // init vars to create 1 larger image
                $el=0;
                
                while ( $thumbs->have_posts() ) {
                    $thumbs->the_post();
                    global $post;
                    
                    // get categories to add as classes for sorting with isotope
                    $post_cats = wp_get_post_categories( $post->ID );
                    
                    $ptg_cats = '';
                    
                    foreach( $post_cats as $c ){
                        $cat = get_category( $c );
                        $ptg_cats .= $cat->slug;
                        $ptg_cats .= " ";
                    }
                    
                    //wp_get_attachment_image_src($attachment_id) returns an array with
                    //[0] => url
                    //[1] => width
                    //[2] => height
                    //[3] => boolean: true if $url is a resized image, false if it is the original or if no image is available.
                    $full_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                    $lg_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
  
                    // create 1 larger image
                    $el+=1;
                    $big_grid_class_x = 'grid-item--width2';
                    if( $el <= 2 ){
                        $big_grid_class = $big_grid_class_x;
                        $wp_img_size = 'large';
                    } else {
                        $big_grid_class= 'normal-size';
                        $wp_img_size = 'medium';
                    }
                    
                    //generate random background color for each grid-item
                    $rand_num2 = rand(23,37)/100;
                    
                    //populate the data-index attr with the array of indexes
                    $ptg_index = $el - 1;
                    
                    //$ptg_info = '<div class="ptg-info-overlay hidden">' . 
                            //the_title(false) . 
                            //apply_filters( 'the_content', get_the_content() ) . 
                            //'</div>';
                    
                    echo '<div class="grid-item ' . $big_grid_class . " " . $ptg_cats . '" style="background-color:rgba(255,255,255,' . $rand_num2 . ');">';
                    echo '<a href="' . $full_img[0] . '"data-size="' . $full_img[1] . 'x' . $full_img[2] . '" data-med="' . $lg_img[0] .'" data-med-size="' . $lg_img[1] . 'x' . $lg_img[2] . '" data-index="' . $ptg_index . '" data-author="Stacy Mark">' . get_the_post_thumbnail( $post->ID, $wp_img_size ) . '</a>';
                    //populate the caption, but give it hidden class here - still displays on full size
                    echo '<figure class="ptg-caption hidden">' . apply_filters( 'the_content', get_the_content() ) . '</figure>';
                    echo '</div>';


                }
                ?>

            </div>

            <?php get_template_part('templates/photoswipe'); ?>

    <?php
        wp_reset_query();
    
}

    

// genesis child theme
genesis();
