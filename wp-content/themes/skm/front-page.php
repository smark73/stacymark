<?php

// Remove page header for front page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
//remove_action( 'genesis_header', 'skm_hdr_title' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );


// Remove Page Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'page_loop' );



// custom page header
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

        <div class="hp-slider">
            <?php
                // show proper meta slider for live or local site
                if (live_or_local() == 'local'){
                    //dev
                    echo do_shortcode( '[metaslider id=135]' );
                } else {
                    //live
                    echo do_shortcode( '[metaslider id=197]' );
                }
            ?>
        </div>
    <?php
}
add_action( 'genesis_before_header', 'skm_cust_pg_hdr' );



function page_loop(){
    ?>
            <div class="wrap">

                <div class="" style="background-color:rgba(255,255,255, 0.1);padding:20px;">
                    <?php
                    $home_post_args = array(
                        'post_type' => 'page',
                        'pagename' => 'home',
                    );
                    $hp_query = new WP_Query( $home_post_args );
                    if( $hp_query->have_posts() ){
                        while ( $hp_query->have_posts() ) {
                            $hp_query->the_post();
                            global $post;
                            the_content();
                        }
                    }
                    ?>
                </div>

            </div>

        

    <?php
        wp_reset_query();
    
}

    

// genesis child theme
genesis();
