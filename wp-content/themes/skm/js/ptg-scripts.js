/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

//$('.grid-item a').on('mouseover', function(){
//    //$ptgInfo = $(this).children('.ptg-info-overlay');
//    //$ptgInfo.toggleClass('visible hidden');
//    $(this).children('.ptg-info-overlay').toggleClass('visible hidden');
//});
//$('.grid-item a').on('mouseout', function(){
//    //$ptgInfo = $(this).children('.ptg-info-overlay');
//    //$ptgInfo.toggleClass('visible hidden');
//    $(this).children('.ptg-info-overlay').toggleClass('visible hidden');
//});

});
