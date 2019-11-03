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

    <div class="hdr-wrap__home">
        <div class="ptgs-hdr">
            <div class="ptg-top-bar">
            
                <div class="ptg-top-bar-title">
                    <a href="/" title="Stacy Mark - Paintings">
                        <span class="hdr-name">Paintings by Stacy Mark</span>
                    </a>
                </div>
                
                <div class="ptg-top-bar-links">
                    <?php get_template_part('templates/paintings-menu') ?>
                </div>
                

            </div>
        </div>
    </div>

    <div class="home-spacer">
        <h3 class="home-spacer-txt">Oil, Acrylic, and Mixed Media works from 2006 - present.</h3>
        <p class="home-spacer-txt-sm">Inquiries are welcome, but paintings are not currently for sale.</p> 
    </div>

    <div class="home-slider">
        <?php
            // show proper meta slider for live or local site
            if (live_or_local() == 'local'){
                //dev
                echo do_shortcode( '[metaslider id=197]' );
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
            <div class="wrap home-content-wrap">

                <div class="home-content-left">
                    <h2>ABSTRACT LANDSCAPES</h2>
                    <p>Multiple subtle layers of paint applied and eroded away create a balance of color and light that create the highly atmospheric abstract landscape paintings.</p>
                    <p><a href="/paintings-abstract-landscape">View &raquo;</a></p>
                </div>

                <div class="home-content-right">
                    <h2>REALISM, SURREALISM, AND OTHERS</h2>
                    <p>Additionally, paintings of cityscapes, realism, and surrealism occasionally find their way on and off my easel.</p>
                    <p><a href="/paintings-surreal">View &raquo;</a></p>
                </div>

            </div>

        

    <?php
        wp_reset_query();
    
}

function page_loop_NOTUSED(){
    ?>
            <div class="wrap content-body">

                <div class="content-body-home">
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
