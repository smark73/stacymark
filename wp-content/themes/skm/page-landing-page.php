<?php

// Remove page header for front page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'child_custom_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

// use just name for header
add_action( 'genesis_header', 'lp_hdr' );

// Remove Page Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'lp_loop' );

//* FOOTER Customization
remove_action( 'genesis_footer', 'skm_custom_footer' );
add_action( 'genesis_footer', 'lp_footer' );


function lp_hdr() {
    ?>

        <div class="lp-hdr">
            <div class="one-third first">
                <a href="/landing-page" title="Business Logo">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hyundai-dealer-logo.png" alt="Logo" title="Logo">
                </a>
            </div>
            <div class="two-thirds second">
                <h1>
                    <span>THE ALL NEW</span><span class="autoyear">2018</span> <span class="autoname">Hyundai Sonata</span>
                </h1>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php
}

function lp_loop(){
    ?>

    <div class="lp-mid-wrap">

        <div class="one-half first lp-left">
            <div class="cta-wrap">
                <div class="cta-top-arrow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cta-top-arrow.png" style="width:auto;height:auto;"></div>
                <div class="cta">
                    <p><span class="underline">Right now</span>, we're offering</p>
                    <h1><span class="savings">25%</span><div class="offmsrp">OFF MSRP</div></h1>
                    <p class="reservenow">Reserve your savings now!</p>
                    <form action="#">
                    <input type="text" value="" placeholder="Name"/>
                    <input type="text" value="" placeholder="Email"/>
                    <button type="submit" name="save-25" id="save-25" title="SAVE 25% NOW">Save 25%</button>
                    </form>
                    <p class="info-safe">We respect your privacy.  Your information is safe with us!</p>
                </div>
            </div>
            <div class="cta-btm-arrow"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/cta-btm-arrow.png" style="width:auto;height:auto;"></div>
        </div>
        <div class="lp-right one-half">
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/2015-hyundai-sonata.jpg" class="autoimage" alt="SAVE 25% on the 2015 Hyundai Sonota Now" title="SAVE 25% on the 2015 Hyundai Sonota Now">
            <br/>
            <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hyundai-logo.jpg" alt="Hyundai" title="Hyundai">
        </div>

        <div class="clearfix"></div>

    </div>
    
    <?php
}

function lp_footer() {
    ?>
    <div class="lp-foot-wrap">
        
        <div class="lp-foot-a">
            
            <div class="one-half first lp-details">
                <h3>Details of the Promotion</h3>
                <p>
                    25% Off MSRP includes HMA Rebate and Dealer Discounts. Available to all consumers on all 2018 model year Sonatas.
                </p>
                <h4>Available right now at Your Hyundai Dealer!</h4>
                <div class="clearfix"></div>
            </div>
            
            <div class="dealer-info one-half">
                <a href="/landing-page" title="Business Name">
                    <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/hyundai-dealer-logo.png" alt="Business Name" title="Business Name">
                </a>
                <br/>
                <p class="sales"><span>Sales:</span> <a href="tel:1-123-456-7890">(123) 456-7890</a></p>
                <p class="service"><span>Service:</span> <a href="tel:1-123-456-7890">(123) 456-7890</a></p>
                <div class="clearfix"></div>
            </div>
            
            <div class="clearfix"></div>
        </div>
        
        <div class="lp-foot-b">
            <p>Copyright © 2018 Business Name - All Rights Reserved </p>
        </div>
        <div class="clearfix"></div>
        
    </div>

    <?php
}

// genesis child theme
genesis();

