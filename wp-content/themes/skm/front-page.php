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
add_action( 'genesis_before_header', 'skm_cust_pg_hdr' );
function skm_cust_pg_hdr() {
    ?>


        <div class="ptgs-hdr">
            <div class="ptg-top-bar">
                <div class="two-thirds first ptg-top-bar-lft">
                    <a href="/" title="Stacy Mark - Paintings">
                        <span class="hdr-name">STACY MARK</span>
                        <span class="ptg-vsep"> | </span>
                        <span class="hdr-paintings">PAINTINGS</span>
                    </a>
                </div>
                
                <div class="one-third ptg-top-bar-rt">
                        <div class="sidexside">
                            <?php
                                wp_nav_menu( array(
                                    'menu'          => 'Paintings',
                                    'container'     => 'div',
                                    'container_class'   => 'genesis-nav-menu ptgs-menu',
                                    'container_id'      => '',
                                    'menu_class'        => 'menu',
                                    'menu_id'           => '',
                                    'echo'          => true,
                                    'fallback_cb'   => false,
                                    'items_wrap'        => '<ul id="%1$s" class="%2$s">%3$s</ul>',
                                    'before'          => '',
                                    'after'           => '',
                                    'link_before'     => '',
                                    'link_after'      => '',
                                    'depth'         => 0, 
                                    'walker'        => ''
                                ) );
                            ?>
                        </div>
                        <div class="sidexside">
                            <a class="ptg-contact-toggle">
                                Contact
                            </a>
                        </div>
                </div>
                
                <div class="clearfix"></div>
                
                <div class="ptg-contact web-contact-hide hidden">
                    <div class="ptg-contact-form">
                        <?php echo do_shortcode( '[gravityform id="2" title="false" description="false" ajax="true"]' );?>
                    </div>
                </div>
                
            </div>
            
        </div>

        <div class="hp-slider">
            <?php echo do_shortcode( '[metaslider id=135]' ); ?>
        </div>
    <?php
}

function page_loop(){
    ?>
            <div class="wrap">

                <div class="one-half first" style="background-color:rgba(255,255,255, 0.1);padding:20px;">
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
                <div class="one-half">
                    <img src="/wp-content/uploads/evening-on-the-hillside-hidden-path.jpg" alt="" style="width:auto;height:auto;max-height:300px;">
                </div>

            </div>

        

    <?php
        wp_reset_query();
    
}

add_action('genesis_after_footer', 'add_scripts_to_btm');
function add_scripts_to_btm() {
    ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){

            // START toggle ptg-contact in navbar
            //store our targets in vars
            var $ptgContactToggle = jQuery(document).find('.ptg-contact-toggle');
            var $ptgContact = jQuery(document).find('.ptg-contact');
            var $ptgContactForm = jQuery(document).find('.ptg-contact-form');

            //init search-form styles and classes
            $ptgContactForm.addClass('hidden');
            $ptgContactForm.css({opacity:0});
            //$ptgContact.hide();

            //toggle function
            $ptgContactToggle.click(function(){
                //ptg-contact-nav is hidden until first click (otherwise shows on slow page loads)
                $ptgContact.removeClass('hidden');
                //$ptgContact.show();
                //
                $ptgContact.toggleClass('web-contact-hide web-contact-show');
                
                if(($ptgContactForm).hasClass('hidden')){
                    var ptgContactWait;
                    clearTimeout(ptgContactWait);
                    ptgContactWait = setTimeout(function(){$ptgContactForm.toggleClass('hidden visible').animate({opacity:1});} , 100);
                } else {
                    $ptgContactForm.animate({opacity:0}).toggleClass('hidden visible');
                }
            });
            // END
        </script>
<?php
}
    

// genesis child theme
genesis();
