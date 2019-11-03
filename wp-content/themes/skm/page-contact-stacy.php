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
    <div class="hdr-wrap__contact">
        <div class="ptgs-hdr">
            <div class="ptg-top-bar">
            
                <div class="ptg-top-bar-title">
                    <a href="/" title="Stacy Mark - Paintings">
                        <span class="hdr-name">Paintings by Stacy Mark</span>
                    </a>
                    </div>
                </div>
                
                <div class="ptg-top-bar-links">
                    <?php get_template_part('templates/paintings-menu') ?>
                </div>

            </div>
        </div>
    </div>
    <?php
}


function page_loop(){

     while (have_posts()) : the_post();
        ?>
        <div class="contact-pg-img">
            <?php the_content(); ?>
         </div>
        <?php
    endwhile;
}


    

// genesis child theme
genesis();
