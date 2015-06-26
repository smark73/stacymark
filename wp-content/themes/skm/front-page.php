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
                <div class="grid-item grid-item--width2 grid-item-desc" style="background:rgba(255,255,255,.3);">
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

    <div id="gallery" class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="pswp__bg"></div>

        <div class="pswp__scroll-wrap">

            <div class="pswp__container">
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
                <div class="pswp__item"></div>
            </div>

            <div class="pswp__ui pswp__ui--hidden">

                <div class="pswp__top-bar">

                    <div class="pswp__counter"></div>

                    <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                    <button class="pswp__button pswp__button--share" title="Share"></button>
                    <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                    <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                    <div class="pswp__preloader">
                            <div class="pswp__preloader__icn">
                              <div class="pswp__preloader__cut">
                                <div class="pswp__preloader__donut"></div>
                              </div>
                            </div>
                    </div>
                </div>

                <!-- <div class="pswp__loading-indicator"><div class="pswp__loading-indicator__line"></div></div> -->

                <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                    <div class="pswp__share-tooltip">
                            <!-- <a href="#" class="pswp__share--facebook"></a>
                            <a href="#" class="pswp__share--twitter"></a>
                            <a href="#" class="pswp__share--pinterest"></a>
                            <a href="#" download class="pswp__share--download"></a> -->
                    </div>
                </div>

            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
            <div class="pswp__caption">
              <div class="pswp__caption__center">
              </div>
            </div>
          </div>

        </div>
    </div>
    
    <script type="text/javascript">
    (function() {

		var initPhotoSwipeFromDOM = function(gallerySelector) {

			var parseThumbnailElements = function(el) {
			    var thumbElements = el.childNodes,
			        numNodes = thumbElements.length,
			        items = [],
			        el,
			        childElements,
			        thumbnailEl,
			        size,
			        item;

			    for(var i = 0; i < numNodes; i++) {
			        el = thumbElements[i];

			        // include only element nodes 
			        if(el.nodeType !== 1) {
			          continue;
			        }

			        childElements = el.children;

			        size = el.getAttribute('data-size').split('x');

			        // create slide object
			        item = {
						src: el.getAttribute('href'),
						w: parseInt(size[0], 10),
						h: parseInt(size[1], 10),
						author: el.getAttribute('data-author')
			        };

			        item.el = el; // save link to element for getThumbBoundsFn

			        if(childElements.length > 0) {
			          item.msrc = childElements[0].getAttribute('src'); // thumbnail url
			          if(childElements.length > 1) {
			              item.title = childElements[1].innerHTML; // caption (contents of figure)
			          }
			        }


					var mediumSrc = el.getAttribute('data-med');
		          	if(mediumSrc) {
		            	size = el.getAttribute('data-med-size').split('x');
		            	// "medium-sized" image
		            	item.m = {
		              		src: mediumSrc,
		              		w: parseInt(size[0], 10),
		              		h: parseInt(size[1], 10)
		            	};
		          	}
		          	// original image
		          	item.o = {
		          		src: item.src,
		          		w: item.w,
		          		h: item.h
		          	};

			        items.push(item);
			    }

			    return items;
			};

			// find nearest parent element
			var closest = function closest(el, fn) {
			    return el && ( fn(el) ? el : closest(el.parentNode, fn) );
			};

			var onThumbnailsClick = function(e) {
			    e = e || window.event;
			    e.preventDefault ? e.preventDefault() : e.returnValue = false;

			    var eTarget = e.target || e.srcElement;

			    var clickedListItem = closest(eTarget, function(el) {
			        return el.tagName === 'A';
			    });

			    if(!clickedListItem) {
			        return;
			    }

			    var clickedGallery = clickedListItem.parentNode;

			    var childNodes = clickedListItem.parentNode.childNodes,
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
			        openPhotoSwipe( index, clickedGallery );
			    }
			    return false;
			};

			var photoswipeParseHash = function() {
				var hash = window.location.hash.substring(1),
			    params = {};

			    if(hash.length < 5) { // pid=1
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

			        galleryUID: galleryElement.getAttribute('data-pswp-uid'),

			        getThumbBoundsFn: function(index) {
			            // See Options->getThumbBoundsFn section of docs for more info
			            var thumbnail = items[index].el.children[0],
			                pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
			                rect = thumbnail.getBoundingClientRect(); 

			            return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
			        },

			        addCaptionHTMLFn: function(item, captionEl, isFake) {
						if(!item.title) {
							captionEl.children[0].innerText = '';
							return false;
						}
						captionEl.children[0].innerHTML = item.title +  '<br/><small>Painting by ' + item.author + '</small>';
						return true;
			        }
					
			    };


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
				    	options.index = parseInt(index, 10) - 1;
				    }
			    } else {
			    	options.index = parseInt(index, 10);
			    }

			    // exit if index not found
			    if( isNaN(options.index) ) {
			    	return;
			    }



				var radios = document.getElementsByName('gallery-style');
				for (var i = 0, length = radios.length; i < length; i++) {
				    if (radios[i].checked) {
				        if(radios[i].id == 'radio-all-controls') {

				        } else if(radios[i].id == 'radio-minimal-black') {
				        	options.mainClass = 'pswp--minimal--dark';
					        options.barsSize = {top:0,bottom:0};
							options.captionEl = false;
							options.fullscreenEl = false;
							options.shareEl = false;
							options.bgOpacity = 0.85;
							options.tapToClose = true;
							options.tapToToggleControls = false;
				        }
				        break;
				    }
				}

			    if(disableAnimation) {
			        options.showAnimationDuration = 0;
			    }

			    // Pass data to PhotoSwipe and initialize it
			    gallery = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options);

			    // see: http://photoswipe.com/documentation/responsive-images.html
				var realViewportWidth,
				    useLargeImages = false,
				    firstResize = true,
				    imageSrcWillChange;

				gallery.listen('beforeResize', function() {

					var dpiRatio = window.devicePixelRatio ? window.devicePixelRatio : 1;
					dpiRatio = Math.min(dpiRatio, 2.5);
				    realViewportWidth = gallery.viewportSize.x * dpiRatio;


				    if(realViewportWidth >= 1200 || (!gallery.likelyTouchDevice && realViewportWidth > 800) || screen.width > 1200 ) {
				    	if(!useLargeImages) {
				    		useLargeImages = true;
				        	imageSrcWillChange = true;
				    	}
				        
				    } else {
				    	if(useLargeImages) {
				    		useLargeImages = false;
				        	imageSrcWillChange = true;
				    	}
				    }

				    if(imageSrcWillChange && !firstResize) {
				        gallery.invalidateCurrItems();
				    }

				    if(firstResize) {
				        firstResize = false;
				    }

				    imageSrcWillChange = false;

				});

				gallery.listen('gettingData', function(index, item) {
				    if( useLargeImages ) {
				        item.src = item.o.src;
				        item.w = item.o.w;
				        item.h = item.o.h;
				    } else {
				        item.src = item.m.src;
				        item.w = item.m.w;
				        item.h = item.m.h;
				    }
				});

			    gallery.init();
			};

			// select all gallery elements
			var galleryElements = document.querySelectorAll( gallerySelector );
			for(var i = 0, l = galleryElements.length; i < l; i++) {
				galleryElements[i].setAttribute('data-pswp-uid', i+1);
				galleryElements[i].onclick = onThumbnailsClick;
			}

			// Parse URL and open gallery if it contains #&pid=3&gid=1
			var hashData = photoswipeParseHash();
			if(hashData.pid && hashData.gid) {
				openPhotoSwipe( hashData.pid,  galleryElements[ hashData.gid - 1 ], true, true );
			}
		};

		initPhotoSwipeFromDOM('.skm-gallery');

	})();

	</script>


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
