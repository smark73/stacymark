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

        <div class="thing2" style="position:relative;width:200px;height:200px;background:#ccc;margin-top:100px;z-index:100;">

            <div style="position:absolute;top:50px;left:0;z-index:300;text-align:center;">

                <?php //get_template_part('templates/paintings-menu') ?>

                <ul style="margin:auto;width:200px;text-align:center;">
                    <li style="width:100%;padding:10px 0;">
                        <a href="">aewhilf</a>
                    </li>
                    <li style="width:100%;padding:10px 0;">
                        <a href="">dfhulesh</a>
                    </li>
                    <li style="width:100%;padding:10px 0;">
                        <a href="">asdfflseih</a>
                    </li>
                </ul>
                <p style="width:100%;font-size:1em;font-weight:500;margin-top:30px;">PAINTINGS</p>


            </div>

            <!--div style="width:200px;height:200px;border-radius:50%;background:#efe0d1;position:absolute;top:-100px;left:0;z-index:200;"></div-->
            <div style="width:200px;height:200px;border-radius:50%;background:#ccc;position:absolute;top:100px;left:0;z-index:200;"></div>

        </div>

    <?php
}


    

// genesis child theme
genesis();
