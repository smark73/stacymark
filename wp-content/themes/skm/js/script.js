// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)

var SkmSite = {
  // All pages
  common: {
    init: function() {
        
        jQuery(function($) {

            $(document).ready(function() {

                // START Paintings menu
                var $ptgsMenuBox = jQuery(document).find('.ptgs-menu-box');

                $ptgsMenuBox.mouseenter(function(){
                    jQuery(this).stop().animate({top: -50}, 100);
                });

                $ptgsMenuBox.mouseleave(function(){
                    jQuery(this).stop().animate({top: -230}, 100);
                });
                // END

            });
        });
        
    },
    finalize: function() { }
  },

  // Home page
  home: {
    init: function() {

        jQuery(function($) {

            $(document).ready(function() {

            });

        });
    }
  },

  // About page
  about: {
    init: function() {

        jQuery(function($) {

            $(document).ready(function() {

            });
        });

    }
  }

};

var UTIL = {
  fire: function(func, funcname, args) {
    var namespace = SkmSite;
    funcname = (funcname === undefined) ? 'init' : funcname;
    if (func !== '' && namespace[func] && typeof namespace[func][funcname] === 'function') {
      namespace[func][funcname](args);
    }
  },
  loadEvents: function() {

    UTIL.fire('common');

    jQuery.each(document.body.className.replace(/-/g, '_').split(/\s+/),function(i,classnm) {
      UTIL.fire(classnm);
    });

    UTIL.fire('common', 'finalize');
  }
};

jQuery(document).ready(UTIL.loadEvents);

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

//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiLCJwdGctc2NyaXB0cy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDbEZBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoic2NyaXB0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8gTW9kaWZpZWQgaHR0cDovL3BhdWxpcmlzaC5jb20vMjAwOS9tYXJrdXAtYmFzZWQtdW5vYnRydXNpdmUtY29tcHJlaGVuc2l2ZS1kb20tcmVhZHktZXhlY3V0aW9uL1xyXG4vLyBPbmx5IGZpcmVzIG9uIGJvZHkgY2xhc3MgKHdvcmtpbmcgb2ZmIHN0cmljdGx5IFdvcmRQcmVzcyBib2R5X2NsYXNzKVxyXG5cclxudmFyIFNrbVNpdGUgPSB7XHJcbiAgLy8gQWxsIHBhZ2VzXHJcbiAgY29tbW9uOiB7XHJcbiAgICBpbml0OiBmdW5jdGlvbigpIHtcclxuICAgICAgICBcclxuICAgICAgICBqUXVlcnkoZnVuY3Rpb24oJCkge1xyXG5cclxuICAgICAgICAgICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gU1RBUlQgUGFpbnRpbmdzIG1lbnVcclxuICAgICAgICAgICAgICAgIHZhciAkcHRnc01lbnVCb3ggPSBqUXVlcnkoZG9jdW1lbnQpLmZpbmQoJy5wdGdzLW1lbnUtYm94Jyk7XHJcblxyXG4gICAgICAgICAgICAgICAgJHB0Z3NNZW51Qm94Lm1vdXNlZW50ZXIoZnVuY3Rpb24oKXtcclxuICAgICAgICAgICAgICAgICAgICBqUXVlcnkodGhpcykuc3RvcCgpLmFuaW1hdGUoe3RvcDogLTUwfSwgMTAwKTtcclxuICAgICAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgICAgICRwdGdzTWVudUJveC5tb3VzZWxlYXZlKGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgICAgICAgICAgICAgalF1ZXJ5KHRoaXMpLnN0b3AoKS5hbmltYXRlKHt0b3A6IC0yMzB9LCAxMDApO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgICAgICAgICAvLyBFTkRcclxuXHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG4gICAgICAgIFxyXG4gICAgfSxcclxuICAgIGZpbmFsaXplOiBmdW5jdGlvbigpIHsgfVxyXG4gIH0sXHJcblxyXG4gIC8vIEhvbWUgcGFnZVxyXG4gIGhvbWU6IHtcclxuICAgIGluaXQ6IGZ1bmN0aW9uKCkge1xyXG5cclxuICAgICAgICBqUXVlcnkoZnVuY3Rpb24oJCkge1xyXG5cclxuICAgICAgICAgICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgfSk7XHJcbiAgICB9XHJcbiAgfSxcclxuXHJcbiAgLy8gQWJvdXQgcGFnZVxyXG4gIGFib3V0OiB7XHJcbiAgICBpbml0OiBmdW5jdGlvbigpIHtcclxuXHJcbiAgICAgICAgalF1ZXJ5KGZ1bmN0aW9uKCQpIHtcclxuXHJcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCkge1xyXG5cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgfVxyXG4gIH1cclxuXHJcbn07XHJcblxyXG52YXIgVVRJTCA9IHtcclxuICBmaXJlOiBmdW5jdGlvbihmdW5jLCBmdW5jbmFtZSwgYXJncykge1xyXG4gICAgdmFyIG5hbWVzcGFjZSA9IFNrbVNpdGU7XHJcbiAgICBmdW5jbmFtZSA9IChmdW5jbmFtZSA9PT0gdW5kZWZpbmVkKSA/ICdpbml0JyA6IGZ1bmNuYW1lO1xyXG4gICAgaWYgKGZ1bmMgIT09ICcnICYmIG5hbWVzcGFjZVtmdW5jXSAmJiB0eXBlb2YgbmFtZXNwYWNlW2Z1bmNdW2Z1bmNuYW1lXSA9PT0gJ2Z1bmN0aW9uJykge1xyXG4gICAgICBuYW1lc3BhY2VbZnVuY11bZnVuY25hbWVdKGFyZ3MpO1xyXG4gICAgfVxyXG4gIH0sXHJcbiAgbG9hZEV2ZW50czogZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgVVRJTC5maXJlKCdjb21tb24nKTtcclxuXHJcbiAgICBqUXVlcnkuZWFjaChkb2N1bWVudC5ib2R5LmNsYXNzTmFtZS5yZXBsYWNlKC8tL2csICdfJykuc3BsaXQoL1xccysvKSxmdW5jdGlvbihpLGNsYXNzbm0pIHtcclxuICAgICAgVVRJTC5maXJlKGNsYXNzbm0pO1xyXG4gICAgfSk7XHJcblxyXG4gICAgVVRJTC5maXJlKCdjb21tb24nLCAnZmluYWxpemUnKTtcclxuICB9XHJcbn07XHJcblxyXG5qUXVlcnkoZG9jdW1lbnQpLnJlYWR5KFVUSUwubG9hZEV2ZW50cyk7XHJcbiIsIi8qIFxyXG4gKiBUbyBjaGFuZ2UgdGhpcyBsaWNlbnNlIGhlYWRlciwgY2hvb3NlIExpY2Vuc2UgSGVhZGVycyBpbiBQcm9qZWN0IFByb3BlcnRpZXMuXHJcbiAqIFRvIGNoYW5nZSB0aGlzIHRlbXBsYXRlIGZpbGUsIGNob29zZSBUb29scyB8IFRlbXBsYXRlc1xyXG4gKiBhbmQgb3BlbiB0aGUgdGVtcGxhdGUgaW4gdGhlIGVkaXRvci5cclxuICovXHJcblxyXG5qUXVlcnkoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCQpe1xyXG5cclxuICAgIC8vIHJlbW92ZSBzb21lIGpxdWVyeSBtb2JpbGUgc3R5bGlpbmcgdGhhdCBpbnRlcmZlcmVzXHJcbiAgICAvLyQoZG9jdW1lbnQpLmJpbmQoJ21vYmlsZWluaXQnLGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgLy8kLm1vYmlsZS5rZWVwTmF0aXZlID0gXCJzZWxlY3QsaW5wdXQsYnV0dG9uLGFcIjtcclxuICAgIC8vfSk7XHJcblxyXG4gICAgLy9pbml0IGlzb3RvcGUgYW5kIGFzc2lnbiB0byB2YXJcclxuICAgIHZhciAkZ3JpZCA9ICQoJy5ncmlkJykuaXNvdG9wZSh7XHJcbiAgICAgICAgaXRlbVNlbGVjdG9yOiAnLmdyaWQtaXRlbScsXHJcbiAgICAgICAgbGF5b3V0TW9kZTogJ21hc29ucnknLFxyXG4gICAgICAgIG1hc29ucnk6IHtcclxuICAgICAgICAgICAgY29sdW1uV2lkdGg6IDMwMFxyXG4gICAgICAgIH1cclxuICAgIH0pO1xyXG5cclxuICAgIC8vbGF5b3V0IGdyaWQgYWZ0ZXIgZWFjaCBpbWFnZSBsb2Fkc1xyXG4gICAgLy8kZ3JpZC5pbWFnZXNMb2FkZWQoKS5wcm9ncmVzcyggZnVuY3Rpb24oKXtcclxuICAgICAgICAvLyRncmlkLmlzb3RvcGUoJ2xheW91dCcpO1xyXG4gICAgLy99KTtcclxuXHJcbiAgICAvLyBmaWx0ZXIgaXRlbXMgb24gYnV0dG9uIGNsaWNrXHJcbiAgICAkKCcucHRncy1oZHInKS5vbiggJ2NsaWNrJywgJ2EnLCBmdW5jdGlvbigpIHtcclxuICAgICAgdmFyIGZpbHRlclZhbHVlID0gJCh0aGlzKS5hdHRyKCdkYXRhLWZpbHRlcicpO1xyXG4gICAgICAvLyB1c2UgZmlsdGVyIGZ1bmN0aW9uIGlmIGNsYXNzIHZhbHVlIG1hdGNoZXNcclxuICAgICAgJGdyaWQuaXNvdG9wZSh7IGZpbHRlcjogZmlsdGVyVmFsdWUgfSk7XHJcblxyXG4gICAgfSk7XHJcblxyXG5cclxuXHJcbi8vUGhvdG9Td2lwZVxyXG4oZnVuY3Rpb24oKSB7XHJcbnZhciBpbml0UGhvdG9Td2lwZUZyb21ET00gPSBmdW5jdGlvbihnYWxsZXJ5U2VsZWN0b3IpIHtcclxuXHJcbiAgICAgICAgICAgIC8vIHBhcnNlIHNsaWRlIGRhdGEgKHVybCwgdGl0bGUsIHNpemUgLi4uKSBmcm9tIERPTSBlbGVtZW50cyBcclxuICAgICAgICAgICAgLy8gKGNoaWxkcmVuIG9mIGdhbGxlcnlTZWxlY3RvcilcclxuICAgICAgICAgICAgdmFyIHBhcnNlVGh1bWJuYWlsRWxlbWVudHMgPSBmdW5jdGlvbihlbCkge1xyXG4gICAgICAgICAgICAgICAgdmFyIHRodW1iRWxlbWVudHMgPSBlbC5jaGlsZE5vZGVzLFxyXG4gICAgICAgICAgICAgICAgICAgIG51bU5vZGVzID0gdGh1bWJFbGVtZW50cy5sZW5ndGgsXHJcbiAgICAgICAgICAgICAgICAgICAgaXRlbXMgPSBbXSxcclxuICAgICAgICAgICAgICAgICAgICBkaXZFbCxcclxuICAgICAgICAgICAgICAgICAgICBsaW5rRWwsXHJcbiAgICAgICAgICAgICAgICAgICAgc2l6ZSxcclxuICAgICAgICAgICAgICAgICAgICBpdGVtO1xyXG5cclxuICAgICAgICAgICAgICAgIGZvcih2YXIgaSA9IDA7IGkgPCBudW1Ob2RlczsgaSsrKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGRpdkVsID0gdGh1bWJFbGVtZW50c1tpXTsgLy8gPGRpdiAuZ3JpZC1pdGVtPiBlbGVtZW50XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIGluY2x1ZGUgb25seSBlbGVtZW50IG5vZGVzIFxyXG4gICAgICAgICAgICAgICAgICAgIGlmKGRpdkVsLm5vZGVUeXBlICE9PSAxKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgbGlua0VsID0gZGl2RWwuY2hpbGRyZW5bMF07IC8vIDxhPiBlbGVtZW50XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGlmKGxpbmtFbC5nZXRBdHRyaWJ1dGUoJ2RhdGEtc2l6ZScpKXtcclxuICAgICAgICAgICAgICAgICAgICAgICAgc2l6ZSA9IGxpbmtFbC5nZXRBdHRyaWJ1dGUoJ2RhdGEtc2l6ZScpLnNwbGl0KCd4Jyk7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgICAgICAvLyBjcmVhdGUgc2xpZGUgb2JqZWN0XHJcbiAgICAgICAgICAgICAgICAgICAgaXRlbSA9IHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgc3JjOiBsaW5rRWwuZ2V0QXR0cmlidXRlKCdocmVmJyksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHc6IHBhcnNlSW50KHNpemVbMF0sIDEwKSxcclxuICAgICAgICAgICAgICAgICAgICAgICAgaDogcGFyc2VJbnQoc2l6ZVsxXSwgMTApXHJcbiAgICAgICAgICAgICAgICAgICAgfTtcclxuXHJcblxyXG5cclxuICAgICAgICAgICAgICAgICAgICBpZihkaXZFbC5jaGlsZHJlbi5sZW5ndGggPiAxKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIDxmaWdjYXB0aW9uPiBjb250ZW50XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGl0ZW0udGl0bGUgPSBkaXZFbC5jaGlsZHJlblsxXS5pbm5lckhUTUw7IFxyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaWYobGlua0VsLmNoaWxkcmVuLmxlbmd0aCA+IDApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gPGltZz4gdGh1bWJuYWlsIGVsZW1lbnQsIHJldHJpZXZpbmcgdGh1bWJuYWlsIHVybFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBpdGVtLm1zcmMgPSBsaW5rRWwuY2hpbGRyZW5bMF0uZ2V0QXR0cmlidXRlKCdzcmMnKTtcclxuICAgICAgICAgICAgICAgICAgICB9IFxyXG5cclxuICAgICAgICAgICAgICAgICAgICBpdGVtLmVsID0gZGl2RWw7IC8vIHNhdmUgbGluayB0byBlbGVtZW50IGZvciBnZXRUaHVtYkJvdW5kc0ZuXHJcbiAgICAgICAgICAgICAgICAgICAgaXRlbXMucHVzaChpdGVtKTtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gaXRlbXM7XHJcbiAgICAgICAgICAgIH07XHJcblxyXG4gICAgICAgICAgICAvLyBmaW5kIG5lYXJlc3QgcGFyZW50IGVsZW1lbnRcclxuICAgICAgICAgICAgdmFyIGNsb3Nlc3QgPSBmdW5jdGlvbiBjbG9zZXN0KGVsLCBmbikge1xyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGVsICYmICggZm4oZWwpID8gZWwgOiBjbG9zZXN0KGVsLnBhcmVudE5vZGUsIGZuKSApO1xyXG4gICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgLy8gdHJpZ2dlcnMgd2hlbiB1c2VyIGNsaWNrcyBvbiB0aHVtYm5haWxcclxuICAgICAgICAgICAgdmFyIG9uVGh1bWJuYWlsc0NsaWNrID0gZnVuY3Rpb24oZSkge1xyXG4gICAgICAgICAgICAgICAgZSA9IGUgfHwgd2luZG93LmV2ZW50O1xyXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCA/IGUucHJldmVudERlZmF1bHQoKSA6IGUucmV0dXJuVmFsdWUgPSBmYWxzZTtcclxuXHJcbiAgICAgICAgICAgICAgICB2YXIgZVRhcmdldCA9IGUudGFyZ2V0IHx8IGUuc3JjRWxlbWVudDtcclxuXHJcbiAgICAgICAgICAgICAgICAvLyBmaW5kIHJvb3QgZWxlbWVudCBvZiBzbGlkZVxyXG4gICAgICAgICAgICAgICAgdmFyIGNsaWNrZWRMaXN0SXRlbSA9IGNsb3Nlc3QoZVRhcmdldCwgZnVuY3Rpb24oZWwpIHtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm4gKGVsLnRhZ05hbWUgJiYgZWwudGFnTmFtZS50b1VwcGVyQ2FzZSgpID09PSAnRElWJyk7XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgICAgICAgICBpZighY2xpY2tlZExpc3RJdGVtKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIC8vIGZpbmQgaW5kZXggb2YgY2xpY2tlZCBpdGVtIGJ5IGxvb3BpbmcgdGhyb3VnaCBhbGwgY2hpbGQgbm9kZXNcclxuICAgICAgICAgICAgICAgIC8vIGFsdGVybmF0aXZlbHksIHlvdSBtYXkgZGVmaW5lIGluZGV4IHZpYSBkYXRhLSBhdHRyaWJ1dGVcclxuICAgICAgICAgICAgICAgIHZhciBjbGlja2VkR2FsbGVyeSA9IGNsaWNrZWRMaXN0SXRlbS5wYXJlbnROb2RlLFxyXG4gICAgICAgICAgICAgICAgICAgIGNoaWxkTm9kZXMgPSBjbGlja2VkTGlzdEl0ZW0ucGFyZW50Tm9kZS5jaGlsZE5vZGVzLFxyXG4gICAgICAgICAgICAgICAgICAgIG51bUNoaWxkTm9kZXMgPSBjaGlsZE5vZGVzLmxlbmd0aCxcclxuICAgICAgICAgICAgICAgICAgICBub2RlSW5kZXggPSAwLFxyXG4gICAgICAgICAgICAgICAgICAgIGluZGV4O1xyXG5cclxuICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgbnVtQ2hpbGROb2RlczsgaSsrKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYoY2hpbGROb2Rlc1tpXS5ub2RlVHlwZSAhPT0gMSkgeyBcclxuICAgICAgICAgICAgICAgICAgICAgICAgY29udGludWU7IFxyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaWYoY2hpbGROb2Rlc1tpXSA9PT0gY2xpY2tlZExpc3RJdGVtKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGluZGV4ID0gbm9kZUluZGV4O1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgbm9kZUluZGV4Kys7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG5cclxuXHJcbiAgICAgICAgICAgICAgICBpZihpbmRleCA+PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgLy8gb3BlbiBQaG90b1N3aXBlIGlmIHZhbGlkIGluZGV4IGZvdW5kXHJcbiAgICAgICAgICAgICAgICAgICAgb3BlblBob3RvU3dpcGUoIGluZGV4LCBjbGlja2VkR2FsbGVyeSApO1xyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGZhbHNlO1xyXG4gICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgLy8gcGFyc2UgcGljdHVyZSBpbmRleCBhbmQgZ2FsbGVyeSBpbmRleCBmcm9tIFVSTCAoIyZwaWQ9MSZnaWQ9MilcclxuICAgICAgICAgICAgdmFyIHBob3Rvc3dpcGVQYXJzZUhhc2ggPSBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgIHZhciBoYXNoID0gd2luZG93LmxvY2F0aW9uLmhhc2guc3Vic3RyaW5nKDEpLFxyXG4gICAgICAgICAgICAgICAgcGFyYW1zID0ge307XHJcblxyXG4gICAgICAgICAgICAgICAgaWYoaGFzaC5sZW5ndGggPCA1KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHBhcmFtcztcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICB2YXIgdmFycyA9IGhhc2guc3BsaXQoJyYnKTtcclxuICAgICAgICAgICAgICAgIGZvciAodmFyIGkgPSAwOyBpIDwgdmFycy5sZW5ndGg7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKCF2YXJzW2ldKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB2YXIgcGFpciA9IHZhcnNbaV0uc3BsaXQoJz0nKTsgIFxyXG4gICAgICAgICAgICAgICAgICAgIGlmKHBhaXIubGVuZ3RoIDwgMikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcclxuICAgICAgICAgICAgICAgICAgICB9ICAgICAgICAgICBcclxuICAgICAgICAgICAgICAgICAgICBwYXJhbXNbcGFpclswXV0gPSBwYWlyWzFdO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIGlmKHBhcmFtcy5naWQpIHtcclxuICAgICAgICAgICAgICAgICAgICBwYXJhbXMuZ2lkID0gcGFyc2VJbnQocGFyYW1zLmdpZCwgMTApO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIHJldHVybiBwYXJhbXM7XHJcbiAgICAgICAgICAgIH07XHJcblxyXG4gICAgICAgICAgICB2YXIgb3BlblBob3RvU3dpcGUgPSBmdW5jdGlvbihpbmRleCwgZ2FsbGVyeUVsZW1lbnQsIGRpc2FibGVBbmltYXRpb24sIGZyb21VUkwpIHtcclxuICAgICAgICAgICAgICAgIHZhciBwc3dwRWxlbWVudCA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoJy5wc3dwJylbMF0sXHJcbiAgICAgICAgICAgICAgICAgICAgZ2FsbGVyeSxcclxuICAgICAgICAgICAgICAgICAgICBvcHRpb25zLFxyXG4gICAgICAgICAgICAgICAgICAgIGl0ZW1zO1xyXG5cclxuICAgICAgICAgICAgICAgIGl0ZW1zID0gcGFyc2VUaHVtYm5haWxFbGVtZW50cyhnYWxsZXJ5RWxlbWVudCk7XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gZGVmaW5lIG9wdGlvbnMgKGlmIG5lZWRlZClcclxuICAgICAgICAgICAgICAgIG9wdGlvbnMgPSB7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIGRlZmluZSBnYWxsZXJ5IGluZGV4IChmb3IgVVJMKVxyXG4gICAgICAgICAgICAgICAgICAgIGdhbGxlcnlVSUQ6IGdhbGxlcnlFbGVtZW50LmdldEF0dHJpYnV0ZSgnZGF0YS1wc3dwLXVpZCcpLFxyXG5cclxuICAgICAgICAgICAgICAgICAgICBnZXRUaHVtYkJvdW5kc0ZuOiBmdW5jdGlvbihpbmRleCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBTZWUgT3B0aW9ucyAtPiBnZXRUaHVtYkJvdW5kc0ZuIHNlY3Rpb24gb2YgZG9jdW1lbnRhdGlvbiBmb3IgbW9yZSBpbmZvXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHZhciB0aHVtYm5haWwgPSBpdGVtc1tpbmRleF0uZWwuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ2ltZycpWzBdLCAvLyBmaW5kIHRodW1ibmFpbFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgcGFnZVlTY3JvbGwgPSB3aW5kb3cucGFnZVlPZmZzZXQgfHwgZG9jdW1lbnQuZG9jdW1lbnRFbGVtZW50LnNjcm9sbFRvcCxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHJlY3QgPSB0aHVtYm5haWwuZ2V0Qm91bmRpbmdDbGllbnRSZWN0KCk7IFxyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgcmV0dXJuIHt4OnJlY3QubGVmdCwgeTpyZWN0LnRvcCArIHBhZ2VZU2Nyb2xsLCB3OnJlY3Qud2lkdGh9O1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgICAgIC8vIFBob3RvU3dpcGUgb3BlbmVkIGZyb20gVVJMXHJcbiAgICAgICAgICAgICAgICBpZihmcm9tVVJMKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgaWYob3B0aW9ucy5nYWxsZXJ5UElEcykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBwYXJzZSByZWFsIGluZGV4IHdoZW4gY3VzdG9tIFBJRHMgYXJlIHVzZWQgXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIGh0dHA6Ly9waG90b3N3aXBlLmNvbS9kb2N1bWVudGF0aW9uL2ZhcS5odG1sI2N1c3RvbS1waWQtaW4tdXJsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGZvcih2YXIgaiA9IDA7IGogPCBpdGVtcy5sZW5ndGg7IGorKykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgaWYoaXRlbXNbal0ucGlkID09IGluZGV4KSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgb3B0aW9ucy5pbmRleCA9IGo7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBpbiBVUkwgaW5kZXhlcyBzdGFydCBmcm9tIDFcclxuICAgICAgICAgICAgICAgICAgICAgICAgb3B0aW9ucy5pbmRleCA9IHBhcnNlSW50KGluZGV4LCAxMCkgLSAxO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAgICAgb3B0aW9ucy5pbmRleCA9IHBhcnNlSW50KGluZGV4LCAxMCk7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gZXhpdCBpZiBpbmRleCBub3QgZm91bmRcclxuICAgICAgICAgICAgICAgIGlmKCBpc05hTihvcHRpb25zLmluZGV4KSApIHtcclxuICAgICAgICAgICAgICAgICAgICByZXR1cm47XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgaWYoZGlzYWJsZUFuaW1hdGlvbikge1xyXG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuc2hvd0FuaW1hdGlvbkR1cmF0aW9uID0gMDtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAvLyBQYXNzIGRhdGEgdG8gUGhvdG9Td2lwZSBhbmQgaW5pdGlhbGl6ZSBpdFxyXG4gICAgICAgICAgICAgICAgZ2FsbGVyeSA9IG5ldyBQaG90b1N3aXBlKCBwc3dwRWxlbWVudCwgUGhvdG9Td2lwZVVJX0RlZmF1bHQsIGl0ZW1zLCBvcHRpb25zKTtcclxuICAgICAgICAgICAgICAgIGdhbGxlcnkuaW5pdCgpO1xyXG4gICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgLy8gbG9vcCB0aHJvdWdoIGFsbCBnYWxsZXJ5IGVsZW1lbnRzIGFuZCBiaW5kIGV2ZW50c1xyXG4gICAgICAgICAgICB2YXIgZ2FsbGVyeUVsZW1lbnRzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCggZ2FsbGVyeVNlbGVjdG9yICk7XHJcblxyXG4gICAgICAgICAgICBmb3IodmFyIGkgPSAwLCBsID0gZ2FsbGVyeUVsZW1lbnRzLmxlbmd0aDsgaSA8IGw7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgZ2FsbGVyeUVsZW1lbnRzW2ldLnNldEF0dHJpYnV0ZSgnZGF0YS1wc3dwLXVpZCcsIGkrMSk7XHJcbiAgICAgICAgICAgICAgICBnYWxsZXJ5RWxlbWVudHNbaV0ub25jbGljayA9IG9uVGh1bWJuYWlsc0NsaWNrO1xyXG4gICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAvLyBQYXJzZSBVUkwgYW5kIG9wZW4gZ2FsbGVyeSBpZiBpdCBjb250YWlucyAjJnBpZD0zJmdpZD0xXHJcbiAgICAgICAgICAgIHZhciBoYXNoRGF0YSA9IHBob3Rvc3dpcGVQYXJzZUhhc2goKTtcclxuICAgICAgICAgICAgaWYoaGFzaERhdGEucGlkICYmIGhhc2hEYXRhLmdpZCkge1xyXG4gICAgICAgICAgICAgICAgb3BlblBob3RvU3dpcGUoIGhhc2hEYXRhLnBpZCAsICBnYWxsZXJ5RWxlbWVudHNbIGhhc2hEYXRhLmdpZCAtIDEgXSwgdHJ1ZSwgdHJ1ZSApO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfTtcclxuXHJcbiAgICAgICAgLy8gZXhlY3V0ZSBhYm92ZSBmdW5jdGlvblxyXG4gICAgICAgIGluaXRQaG90b1N3aXBlRnJvbURPTSgnLnNrbS1nYWxsZXJ5Jyk7XHJcblxyXG5cclxufSkoKTtcclxuXHJcbi8vJCgnLmdyaWQtaXRlbSBhJykub24oJ21vdXNlb3ZlcicsIGZ1bmN0aW9uKCl7XHJcbi8vICAgIC8vJHB0Z0luZm8gPSAkKHRoaXMpLmNoaWxkcmVuKCcucHRnLWluZm8tb3ZlcmxheScpO1xyXG4vLyAgICAvLyRwdGdJbmZvLnRvZ2dsZUNsYXNzKCd2aXNpYmxlIGhpZGRlbicpO1xyXG4vLyAgICAkKHRoaXMpLmNoaWxkcmVuKCcucHRnLWluZm8tb3ZlcmxheScpLnRvZ2dsZUNsYXNzKCd2aXNpYmxlIGhpZGRlbicpO1xyXG4vL30pO1xyXG4vLyQoJy5ncmlkLWl0ZW0gYScpLm9uKCdtb3VzZW91dCcsIGZ1bmN0aW9uKCl7XHJcbi8vICAgIC8vJHB0Z0luZm8gPSAkKHRoaXMpLmNoaWxkcmVuKCcucHRnLWluZm8tb3ZlcmxheScpO1xyXG4vLyAgICAvLyRwdGdJbmZvLnRvZ2dsZUNsYXNzKCd2aXNpYmxlIGhpZGRlbicpO1xyXG4vLyAgICAkKHRoaXMpLmNoaWxkcmVuKCcucHRnLWluZm8tb3ZlcmxheScpLnRvZ2dsZUNsYXNzKCd2aXNpYmxlIGhpZGRlbicpO1xyXG4vL30pO1xyXG5cclxufSk7XHJcbiJdLCJzb3VyY2VSb290IjoiL3NvdXJjZS8ifQ==
