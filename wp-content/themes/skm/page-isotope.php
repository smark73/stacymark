<?php

// Remove page header for front page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'skm_hdr_title' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );


// Remove Page Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'page_loop' );


// ADD to HEAD
add_action( 'genesis_meta', 'init_stuff' );
function init_stuff() {
    $src_iso = get_stylesheet_directory_uri() . '/isotope/dist/isotope.pkgd.min.js';
    wp_register_script('isotope', $src_iso);
    wp_enqueue_script('isotope', $src_iso, false);
}

// custom page header
add_action( 'genesis_before_header', 'skm_cust_pg_hdr' );
function skm_cust_pg_hdr() {
    ?>
        <div class="skm-title">
            <a href="/" title="Abstract Landscape Paintings" class="ptg-type-link">
                <span class="hdr-name">Abstract Landscapes</span>
            </a>
            <a href="/" title="Surreal Paintings" class="ptg-type-link">
                <span class="hdr-name">Surreal</span>
            </a>
        </div>
    <?php
}

function page_loop(){
    ?>
        <div class="skm-gallery">
            <div class="grid">
                <div class="grid-item grid-item--width2" style="background:rgba(255,255,255,.3);">
                    <div class="skm-title">
                        <a href="/" title="Stacy Mark - Paintings">
                            <span class="hdr-name">STACY MARK  <span style="font-size:0.8em;padding:0 5px">|</span>  </span><span class="hdr-paintings">Paintings</span>
                        </a>
                    </div>
                    <p id="bio-desc">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut vulputate commodo accumsan. Duis fermentum metus in diam luctus rutrum. Donec dictum, lorem ac placerat posuere, turpis magna finibus lorem, non m
                    </p>
                </div>

                <?php
                $thumbs = new WP_Query(array(
                    'category_name' => 'Painting',
                    'orderby' => 'rand',
                    ));

                // init vars to create 1 larger random image
                $el='';
                //$rand_num1 = rand(1,6);
                
                while ( $thumbs->have_posts() ) {
                    $thumbs->the_post();
                    global $post;
                    //wp_get_attachment_image_src($attachment_id) returns an array with
                    //[0] => url
                    //[1] => width
                    //[2] => height
                    //[3] => boolean: true if $url is a resized image, false if it is the original or if no image is available.

                    $full_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
                    $lg_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );

                    //echo $lg_img[1] . "x" . $lg_img[2];

                    // check image width & height
                    //if( $lg_img[1] >= $lg_img[2] ){
                        //wider than tall
                        //give class based on width
                        //$big_grid_class_x = 'grid-item--width2';
                        //var_dump($big_grid_class_x);
                    //} else {
                        //taller than wide
                        //give class based on height
                        //$big_grid_class_x = 'grid-item--height2';
                        //var_dump($big_grid_class_x);
                    //}

                    // first one, plus others we add to array, are given the grid-item--width2/height2 class
//                    $el+=1;
//                    $array_of_bigs = array(1);
//
//                    if( in_array( $el, $array_of_bigs) ){
//                        $big_grid_class = $big_grid_class_x;
//                        $wp_img_size = 'large';
//                    } else {
//                        $big_grid_class= 'normal-size';
//                        $wp_img_size = 'medium';
//                    }
//                    
                    // create 1 larger random image
                    $el+=1;
                    $big_grid_class_x = 'grid-item--width2';

                    if( $el == 1 ){
                        $big_grid_class = $big_grid_class_x;
                        $wp_img_size = 'large';
                    } else {
                        $big_grid_class= 'normal-size';
                        $wp_img_size = 'medium';
                    }
                    
                    //generate random background color for each grid-item
                    $rand_num2 = rand(2,6)/10;

                    //echo '<div class="grid-item ' . $big_grid_class .'" style="background:rgba(255,255,255,' . $rand_num . ');">';
                    //echo '<a href="' . $full_img[0] . '"data-size="' . $full_img[1] . 'x' . $full_img[2] . '" data-med="' . $lg_img[0] .'" data-med-size="' . $lg_img[1] . 'x' . $lg_img[2] . '" data-author="Stacy Mark" class="">' . get_the_post_thumbnail( $post->ID, $wp_img_size ) . '</a>';
                    echo '<div class="grid-item ' . $big_grid_class .'" style="background:rgba(255,255,255,' . $rand_num2 . ');">';
                    echo '<a href="' . $full_img[0] . '"data-size="' . $full_img[1] . 'x' . $full_img[2] . '" data-med="' . $lg_img[0] .'" data-med-size="' . $lg_img[1] . 'x' . $lg_img[2] . '" data-author="Stacy Mark">' . get_the_post_thumbnail( $post->ID, $wp_img_size ) . '</a>';
                    //echo the_title('<figure class="ptg-caption">', '</p>');
                    echo '</div>';


                }
                ?>

            </div>
        </div>

        <?php
            wp_reset_query();
    
}

    //add_action('genesis_after_footer', 'add_scripts_to_body_btm');
    function add_scripts_to_body_btm() {
    ?>
        <script type="text/javascript">
            $(document).ready(function(){
                //init isotope and assign to var
                var $grid = $('.grid').isotope({
                    itemSelector: '.grid-item',
                    layoutMode: 'masonry',
                    masonry: {
                        columnWidth: '.grid-sizer'
                    }
                });
                //layout grid after each image loads
                $grid.imagesLoaded().progress( function(){
                    $grid.isotope('layout');
                });
            });
        </script>
    <?php
    }
    

// genesis child theme
genesis();

