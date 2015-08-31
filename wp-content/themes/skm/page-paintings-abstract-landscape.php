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
                    //echo the_title('<figure class="ptg-caption">', '</p>');
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
        <script type="text/javascript">
            jQuery(document).ready(function($){
                
                // remove some jquery mobile styliing that interferes
                //$(document).bind('mobileinit',function(){
                    //$.mobile.keepNative = "select,input,button,a";
                //});
            
                //init isotope and assign to var
                var $grid = $('.grid').isotope({
                    itemSelector: '.grid-item',
                    layoutMode: 'masonry',
                    masonry: {
                        columnWidth: 300
                    }
                });
                
                //layout grid after each image loads
                //$grid.imagesLoaded().progress( function(){
                    //$grid.isotope('layout');
                //});
                
                // filter items on button click
                $('.ptgs-hdr').on( 'click', 'a', function() {
                  var filterValue = $(this).attr('data-filter');
                  // use filter function if class value matches
                  $grid.isotope({ filter: filterValue });
                  
                });
                
                
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
                
            
            //PhotoSwipe
            (function() {
            var initPhotoSwipeFromDOM = function(gallerySelector) {

                        // parse slide data (url, title, size ...) from DOM elements 
                        // (children of gallerySelector)
                        var parseThumbnailElements = function(el) {
                            var thumbElements = el.childNodes,
                                numNodes = thumbElements.length,
                                items = [],
                                divEl,
                                linkEl,
                                size,
                                item;

                            for(var i = 0; i < numNodes; i++) {

                                divEl = thumbElements[i]; // <div .grid-item> element

                                // include only element nodes 
                                if(divEl.nodeType !== 1) {
                                    continue;
                                }

                                linkEl = divEl.children[0]; // <a> element

                                if(linkEl.getAttribute('data-size')){
                                    size = linkEl.getAttribute('data-size').split('x');
                                }

                                // create slide object
                                item = {
                                    src: linkEl.getAttribute('href'),
                                    w: parseInt(size[0], 10),
                                    h: parseInt(size[1], 10)
                                };



                                if(divEl.children.length > 1) {
                                    // <figcaption> content
                                    item.title = divEl.children[1].innerHTML; 
                                }

                                if(linkEl.children.length > 0) {
                                    // <img> thumbnail element, retrieving thumbnail url
                                    item.msrc = linkEl.children[0].getAttribute('src');
                                } 

                                item.el = divEl; // save link to element for getThumbBoundsFn
                                items.push(item);
                            }

                            return items;
                        };

                        // find nearest parent element
                        var closest = function closest(el, fn) {
                            return el && ( fn(el) ? el : closest(el.parentNode, fn) );
                        };

                        // triggers when user clicks on thumbnail
                        var onThumbnailsClick = function(e) {
                            e = e || window.event;
                            e.preventDefault ? e.preventDefault() : e.returnValue = false;

                            var eTarget = e.target || e.srcElement;

                            // find root element of slide
                            var clickedListItem = closest(eTarget, function(el) {
                                return (el.tagName && el.tagName.toUpperCase() === 'DIV');
                            });

                            if(!clickedListItem) {
                                return;
                            }

                            // find index of clicked item by looping through all child nodes
                            // alternatively, you may define index via data- attribute
                            var clickedGallery = clickedListItem.parentNode,
                                childNodes = clickedListItem.parentNode.childNodes,
                                numChildNodes = childNodes.length,
                                nodeIndex = 0,
                                index;

                            for (var i = 0; i < numChildNodes; i++) {
                                if(childNodes[i].nodeType !== 1) { 
                                    continue; 
                                }

                                if(childNodes[i] === clickedListItem) {
                                    index = nodeIndex;
                                    break;
                                }
                                nodeIndex++;
                            }



                            if(index >= 0) {
                                // open PhotoSwipe if valid index found
                                openPhotoSwipe( index, clickedGallery );
                            }
                            return false;
                        };

                        // parse picture index and gallery index from URL (#&pid=1&gid=2)
                        var photoswipeParseHash = function() {
                            var hash = window.location.hash.substring(1),
                            params = {};

                            if(hash.length < 5) {
                                return params;
                            }

                            var vars = hash.split('&');
                            for (var i = 0; i < vars.length; i++) {
                                if(!vars[i]) {
                                    continue;
                                }
                                var pair = vars[i].split('=');  
                                if(pair.length < 2) {
                                    continue;
                                }           
                                params[pair[0]] = pair[1];
                            }

                            if(params.gid) {
                                params.gid = parseInt(params.gid, 10);
                            }

                            return params;
                        };

                        var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
                            var pswpElement = document.querySelectorAll('.pswp')[0],
                                gallery,
                                options,
                                items;

                            items = parseThumbnailElements(galleryElement);

                            // define options (if needed)
                            options = {

                                // define gallery index (for URL)
                                galleryUID: galleryElement.getAttribute('data-pswp-uid'),

                                getThumbBoundsFn: function(index) {
                                    // See Options -> getThumbBoundsFn section of documentation for more info
                                    var thumbnail = items[index].el.getElementsByTagName('img')[0], // find thumbnail
                                        pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                                        rect = thumbnail.getBoundingClientRect(); 

                                    return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
                                }

                            };

                            // PhotoSwipe opened from URL
                            if(fromURL) {
                                if(options.galleryPIDs) {
                                    // parse real index when custom PIDs are used 
                                    // http://photoswipe.com/documentation/faq.html#custom-pid-in-url
                                    for(var j = 0; j < items.length; j++) {
                                        if(items[j].pid == index) {
                                            options.index = j;
                                            break;
                                        }
                                    }
                                } else {
                                    // in URL indexes start from 1
                                    options.index = parseInt(index, 10) - 1;
                                }
                            } else {
                                options.index = parseInt(index, 10);
                            }

                            // exit if index not found
                            if( isNaN(options.index) ) {
                                return;
                            }

                            if(disableAnimation) {
                                options.showAnimationDuration = 0;
                            }

                            // Pass data to PhotoSwipe and initialize it
                            gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);
                            gallery.init();
                        };

                        // loop through all gallery elements and bind events
                        var galleryElements = document.querySelectorAll( gallerySelector );

                        for(var i = 0, l = galleryElements.length; i < l; i++) {
                            galleryElements[i].setAttribute('data-pswp-uid', i+1);
                            galleryElements[i].onclick = onThumbnailsClick;
                        }

                        // Parse URL and open gallery if it contains #&pid=3&gid=1
                        var hashData = photoswipeParseHash();
                        if(hashData.pid && hashData.gid) {
                            openPhotoSwipe( hashData.pid ,  galleryElements[ hashData.gid - 1 ], true, true );
                        }
                    };

                    // execute above function
                    initPhotoSwipeFromDOM('.skm-gallery');


            })();
            
        });
            
        </script>
<?php
}
    

// genesis child theme
genesis();
