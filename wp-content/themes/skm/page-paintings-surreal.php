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
    <?php
}

function page_loop(){
    ?>
            <div class="skm-gallery grid">

                <?php
                $thumbs = new WP_Query(array(
                    'category_name' => 'Surreal',
                    'orderby' => 'rand',
                    ));

                // init vars to create 1 larger image
                $el='';
                
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

                    echo '<div class="grid-item ' . $big_grid_class . " " . $ptg_cats . '" style="background-color:rgba(255,255,255,' . $rand_num2 . ');">';
                    echo '<a href="' . $full_img[0] . '"data-size="' . $full_img[1] . 'x' . $full_img[2] . '" data-med="' . $lg_img[0] .'" data-med-size="' . $lg_img[1] . 'x' . $lg_img[2] . '" data-index="' . $ptg_index . '" data-author="Stacy Mark">' . get_the_post_thumbnail( $post->ID, $wp_img_size ) . '</a>';
                    //populate the caption, but give it hidden class here - still displays on full size
                    echo '<figure class="ptg-caption hidden">' . apply_filters( 'the_content', get_the_content() ) . '</figure>';
                    echo '</div>';


                }
                ?>

            </div>

        <!-- Root element of PhotoSwipe. Must have class pswp. -->
        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

            <!-- Background of PhotoSwipe. 
                 It's a separate element as animating opacity is faster than rgba(). -->
            <div class="pswp__bg"></div>

            <!-- Slides wrapper with overflow:hidden. -->
            <div class="pswp__scroll-wrap">

                <!-- Container that holds slides. 
                    PhotoSwipe keeps only 3 of them in the DOM to save memory.
                    Don't modify these 3 pswp__item elements, data is added later on. -->
                <div class="pswp__container">
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                    <div class="pswp__item"></div>
                </div>

                <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
                <div class="pswp__ui pswp__ui--hidden">

                    <div class="pswp__top-bar">

                        <!--  Controls are self-explanatory. Order can be changed. -->

                        <div class="pswp__counter"></div>

                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                        <button class="pswp__button pswp__button--share" title="Share"></button>

                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                        <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
                        <!-- element will get class pswp__preloader--active when preloader is running -->
                        <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                              <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                              </div>
                            </div>
                        </div>
                    </div>

                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                        <div class="pswp__share-tooltip"></div> 
                    </div>

                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                    </button>

                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                    </button>

                    <div class="pswp__caption">
                        <div class="pswp__caption__center"></div>
                    </div>

                </div>

            </div>

        </div>

    <?php
        wp_reset_query();
    
}

add_action('genesis_after_footer', 'add_scripts_to_btm');
function add_scripts_to_btm() {
    ?>
        <script type="text/javascript" src="/wp-content/themes/skm/js/ptg-scripts.js"></script>
<?php
}
    

// genesis child theme
genesis();
