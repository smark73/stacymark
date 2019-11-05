// Modified http://paulirish.com/2009/markup-based-unobtrusive-comprehensive-dom-ready-execution/
// Only fires on body class (working off strictly WordPress body_class)

var SkmSite = {
  // All pages
  common: {
    init: function() {
        
        jQuery(function($) {

            $(document).ready(function() {

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

//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1haW4uanMiLCJwdGctc2NyaXB0cy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FDdEVBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoic2NyaXB0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLy8gTW9kaWZpZWQgaHR0cDovL3BhdWxpcmlzaC5jb20vMjAwOS9tYXJrdXAtYmFzZWQtdW5vYnRydXNpdmUtY29tcHJlaGVuc2l2ZS1kb20tcmVhZHktZXhlY3V0aW9uL1xyXG4vLyBPbmx5IGZpcmVzIG9uIGJvZHkgY2xhc3MgKHdvcmtpbmcgb2ZmIHN0cmljdGx5IFdvcmRQcmVzcyBib2R5X2NsYXNzKVxyXG5cclxudmFyIFNrbVNpdGUgPSB7XHJcbiAgLy8gQWxsIHBhZ2VzXHJcbiAgY29tbW9uOiB7XHJcbiAgICBpbml0OiBmdW5jdGlvbigpIHtcclxuICAgICAgICBcclxuICAgICAgICBqUXVlcnkoZnVuY3Rpb24oJCkge1xyXG5cclxuICAgICAgICAgICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgICAgICAgICB9KTtcclxuICAgICAgICB9KTtcclxuICAgICAgICBcclxuICAgIH0sXHJcbiAgICBmaW5hbGl6ZTogZnVuY3Rpb24oKSB7IH1cclxuICB9LFxyXG5cclxuICAvLyBIb21lIHBhZ2VcclxuICBob21lOiB7XHJcbiAgICBpbml0OiBmdW5jdGlvbigpIHtcclxuXHJcbiAgICAgICAgalF1ZXJ5KGZ1bmN0aW9uKCQpIHtcclxuXHJcbiAgICAgICAgICAgICQoZG9jdW1lbnQpLnJlYWR5KGZ1bmN0aW9uKCkge1xyXG5cclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIH0pO1xyXG4gICAgfVxyXG4gIH0sXHJcblxyXG4gIC8vIEFib3V0IHBhZ2VcclxuICBhYm91dDoge1xyXG4gICAgaW5pdDogZnVuY3Rpb24oKSB7XHJcblxyXG4gICAgICAgIGpRdWVyeShmdW5jdGlvbigkKSB7XHJcblxyXG4gICAgICAgICAgICAkKGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigpIHtcclxuXHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgIH1cclxuICB9XHJcblxyXG59O1xyXG5cclxudmFyIFVUSUwgPSB7XHJcbiAgZmlyZTogZnVuY3Rpb24oZnVuYywgZnVuY25hbWUsIGFyZ3MpIHtcclxuICAgIHZhciBuYW1lc3BhY2UgPSBTa21TaXRlO1xyXG4gICAgZnVuY25hbWUgPSAoZnVuY25hbWUgPT09IHVuZGVmaW5lZCkgPyAnaW5pdCcgOiBmdW5jbmFtZTtcclxuICAgIGlmIChmdW5jICE9PSAnJyAmJiBuYW1lc3BhY2VbZnVuY10gJiYgdHlwZW9mIG5hbWVzcGFjZVtmdW5jXVtmdW5jbmFtZV0gPT09ICdmdW5jdGlvbicpIHtcclxuICAgICAgbmFtZXNwYWNlW2Z1bmNdW2Z1bmNuYW1lXShhcmdzKTtcclxuICAgIH1cclxuICB9LFxyXG4gIGxvYWRFdmVudHM6IGZ1bmN0aW9uKCkge1xyXG5cclxuICAgIFVUSUwuZmlyZSgnY29tbW9uJyk7XHJcblxyXG4gICAgalF1ZXJ5LmVhY2goZG9jdW1lbnQuYm9keS5jbGFzc05hbWUucmVwbGFjZSgvLS9nLCAnXycpLnNwbGl0KC9cXHMrLyksZnVuY3Rpb24oaSxjbGFzc25tKSB7XHJcbiAgICAgIFVUSUwuZmlyZShjbGFzc25tKTtcclxuICAgIH0pO1xyXG5cclxuICAgIFVUSUwuZmlyZSgnY29tbW9uJywgJ2ZpbmFsaXplJyk7XHJcbiAgfVxyXG59O1xyXG5cclxualF1ZXJ5KGRvY3VtZW50KS5yZWFkeShVVElMLmxvYWRFdmVudHMpO1xyXG4iLCIvKiBcclxuICogVG8gY2hhbmdlIHRoaXMgbGljZW5zZSBoZWFkZXIsIGNob29zZSBMaWNlbnNlIEhlYWRlcnMgaW4gUHJvamVjdCBQcm9wZXJ0aWVzLlxyXG4gKiBUbyBjaGFuZ2UgdGhpcyB0ZW1wbGF0ZSBmaWxlLCBjaG9vc2UgVG9vbHMgfCBUZW1wbGF0ZXNcclxuICogYW5kIG9wZW4gdGhlIHRlbXBsYXRlIGluIHRoZSBlZGl0b3IuXHJcbiAqL1xyXG5cclxualF1ZXJ5KGRvY3VtZW50KS5yZWFkeShmdW5jdGlvbigkKXtcclxuXHJcbiAgICAvLyByZW1vdmUgc29tZSBqcXVlcnkgbW9iaWxlIHN0eWxpaW5nIHRoYXQgaW50ZXJmZXJlc1xyXG4gICAgLy8kKGRvY3VtZW50KS5iaW5kKCdtb2JpbGVpbml0JyxmdW5jdGlvbigpe1xyXG4gICAgICAgIC8vJC5tb2JpbGUua2VlcE5hdGl2ZSA9IFwic2VsZWN0LGlucHV0LGJ1dHRvbixhXCI7XHJcbiAgICAvL30pO1xyXG5cclxuICAgIC8vaW5pdCBpc290b3BlIGFuZCBhc3NpZ24gdG8gdmFyXHJcbiAgICB2YXIgJGdyaWQgPSAkKCcuZ3JpZCcpLmlzb3RvcGUoe1xyXG4gICAgICAgIGl0ZW1TZWxlY3RvcjogJy5ncmlkLWl0ZW0nLFxyXG4gICAgICAgIGxheW91dE1vZGU6ICdtYXNvbnJ5JyxcclxuICAgICAgICBtYXNvbnJ5OiB7XHJcbiAgICAgICAgICAgIGNvbHVtbldpZHRoOiAzMDBcclxuICAgICAgICB9XHJcbiAgICB9KTtcclxuXHJcbiAgICAvL2xheW91dCBncmlkIGFmdGVyIGVhY2ggaW1hZ2UgbG9hZHNcclxuICAgIC8vJGdyaWQuaW1hZ2VzTG9hZGVkKCkucHJvZ3Jlc3MoIGZ1bmN0aW9uKCl7XHJcbiAgICAgICAgLy8kZ3JpZC5pc290b3BlKCdsYXlvdXQnKTtcclxuICAgIC8vfSk7XHJcblxyXG4gICAgLy8gZmlsdGVyIGl0ZW1zIG9uIGJ1dHRvbiBjbGlja1xyXG4gICAgJCgnLnB0Z3MtaGRyJykub24oICdjbGljaycsICdhJywgZnVuY3Rpb24oKSB7XHJcbiAgICAgIHZhciBmaWx0ZXJWYWx1ZSA9ICQodGhpcykuYXR0cignZGF0YS1maWx0ZXInKTtcclxuICAgICAgLy8gdXNlIGZpbHRlciBmdW5jdGlvbiBpZiBjbGFzcyB2YWx1ZSBtYXRjaGVzXHJcbiAgICAgICRncmlkLmlzb3RvcGUoeyBmaWx0ZXI6IGZpbHRlclZhbHVlIH0pO1xyXG5cclxuICAgIH0pO1xyXG5cclxuXHJcblxyXG4vL1Bob3RvU3dpcGVcclxuKGZ1bmN0aW9uKCkge1xyXG52YXIgaW5pdFBob3RvU3dpcGVGcm9tRE9NID0gZnVuY3Rpb24oZ2FsbGVyeVNlbGVjdG9yKSB7XHJcblxyXG4gICAgICAgICAgICAvLyBwYXJzZSBzbGlkZSBkYXRhICh1cmwsIHRpdGxlLCBzaXplIC4uLikgZnJvbSBET00gZWxlbWVudHMgXHJcbiAgICAgICAgICAgIC8vIChjaGlsZHJlbiBvZiBnYWxsZXJ5U2VsZWN0b3IpXHJcbiAgICAgICAgICAgIHZhciBwYXJzZVRodW1ibmFpbEVsZW1lbnRzID0gZnVuY3Rpb24oZWwpIHtcclxuICAgICAgICAgICAgICAgIHZhciB0aHVtYkVsZW1lbnRzID0gZWwuY2hpbGROb2RlcyxcclxuICAgICAgICAgICAgICAgICAgICBudW1Ob2RlcyA9IHRodW1iRWxlbWVudHMubGVuZ3RoLFxyXG4gICAgICAgICAgICAgICAgICAgIGl0ZW1zID0gW10sXHJcbiAgICAgICAgICAgICAgICAgICAgZGl2RWwsXHJcbiAgICAgICAgICAgICAgICAgICAgbGlua0VsLFxyXG4gICAgICAgICAgICAgICAgICAgIHNpemUsXHJcbiAgICAgICAgICAgICAgICAgICAgaXRlbTtcclxuXHJcbiAgICAgICAgICAgICAgICBmb3IodmFyIGkgPSAwOyBpIDwgbnVtTm9kZXM7IGkrKykge1xyXG5cclxuICAgICAgICAgICAgICAgICAgICBkaXZFbCA9IHRodW1iRWxlbWVudHNbaV07IC8vIDxkaXYgLmdyaWQtaXRlbT4gZWxlbWVudFxyXG5cclxuICAgICAgICAgICAgICAgICAgICAvLyBpbmNsdWRlIG9ubHkgZWxlbWVudCBub2RlcyBcclxuICAgICAgICAgICAgICAgICAgICBpZihkaXZFbC5ub2RlVHlwZSAhPT0gMSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGxpbmtFbCA9IGRpdkVsLmNoaWxkcmVuWzBdOyAvLyA8YT4gZWxlbWVudFxyXG5cclxuICAgICAgICAgICAgICAgICAgICBpZihsaW5rRWwuZ2V0QXR0cmlidXRlKCdkYXRhLXNpemUnKSl7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHNpemUgPSBsaW5rRWwuZ2V0QXR0cmlidXRlKCdkYXRhLXNpemUnKS5zcGxpdCgneCcpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgLy8gY3JlYXRlIHNsaWRlIG9iamVjdFxyXG4gICAgICAgICAgICAgICAgICAgIGl0ZW0gPSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHNyYzogbGlua0VsLmdldEF0dHJpYnV0ZSgnaHJlZicpLFxyXG4gICAgICAgICAgICAgICAgICAgICAgICB3OiBwYXJzZUludChzaXplWzBdLCAxMCksXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGg6IHBhcnNlSW50KHNpemVbMV0sIDEwKVxyXG4gICAgICAgICAgICAgICAgICAgIH07XHJcblxyXG5cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaWYoZGl2RWwuY2hpbGRyZW4ubGVuZ3RoID4gMSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyA8ZmlnY2FwdGlvbj4gY29udGVudFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBpdGVtLnRpdGxlID0gZGl2RWwuY2hpbGRyZW5bMV0uaW5uZXJIVE1MOyBcclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGlmKGxpbmtFbC5jaGlsZHJlbi5sZW5ndGggPiAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIDxpbWc+IHRodW1ibmFpbCBlbGVtZW50LCByZXRyaWV2aW5nIHRodW1ibmFpbCB1cmxcclxuICAgICAgICAgICAgICAgICAgICAgICAgaXRlbS5tc3JjID0gbGlua0VsLmNoaWxkcmVuWzBdLmdldEF0dHJpYnV0ZSgnc3JjJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaXRlbS5lbCA9IGRpdkVsOyAvLyBzYXZlIGxpbmsgdG8gZWxlbWVudCBmb3IgZ2V0VGh1bWJCb3VuZHNGblxyXG4gICAgICAgICAgICAgICAgICAgIGl0ZW1zLnB1c2goaXRlbSk7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgcmV0dXJuIGl0ZW1zO1xyXG4gICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgLy8gZmluZCBuZWFyZXN0IHBhcmVudCBlbGVtZW50XHJcbiAgICAgICAgICAgIHZhciBjbG9zZXN0ID0gZnVuY3Rpb24gY2xvc2VzdChlbCwgZm4pIHtcclxuICAgICAgICAgICAgICAgIHJldHVybiBlbCAmJiAoIGZuKGVsKSA/IGVsIDogY2xvc2VzdChlbC5wYXJlbnROb2RlLCBmbikgKTtcclxuICAgICAgICAgICAgfTtcclxuXHJcbiAgICAgICAgICAgIC8vIHRyaWdnZXJzIHdoZW4gdXNlciBjbGlja3Mgb24gdGh1bWJuYWlsXHJcbiAgICAgICAgICAgIHZhciBvblRodW1ibmFpbHNDbGljayA9IGZ1bmN0aW9uKGUpIHtcclxuICAgICAgICAgICAgICAgIGUgPSBlIHx8IHdpbmRvdy5ldmVudDtcclxuICAgICAgICAgICAgICAgIGUucHJldmVudERlZmF1bHQgPyBlLnByZXZlbnREZWZhdWx0KCkgOiBlLnJldHVyblZhbHVlID0gZmFsc2U7XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIGVUYXJnZXQgPSBlLnRhcmdldCB8fCBlLnNyY0VsZW1lbnQ7XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gZmluZCByb290IGVsZW1lbnQgb2Ygc2xpZGVcclxuICAgICAgICAgICAgICAgIHZhciBjbGlja2VkTGlzdEl0ZW0gPSBjbG9zZXN0KGVUYXJnZXQsIGZ1bmN0aW9uKGVsKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuIChlbC50YWdOYW1lICYmIGVsLnRhZ05hbWUudG9VcHBlckNhc2UoKSA9PT0gJ0RJVicpO1xyXG4gICAgICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICAgICAgaWYoIWNsaWNrZWRMaXN0SXRlbSkge1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAvLyBmaW5kIGluZGV4IG9mIGNsaWNrZWQgaXRlbSBieSBsb29waW5nIHRocm91Z2ggYWxsIGNoaWxkIG5vZGVzXHJcbiAgICAgICAgICAgICAgICAvLyBhbHRlcm5hdGl2ZWx5LCB5b3UgbWF5IGRlZmluZSBpbmRleCB2aWEgZGF0YS0gYXR0cmlidXRlXHJcbiAgICAgICAgICAgICAgICB2YXIgY2xpY2tlZEdhbGxlcnkgPSBjbGlja2VkTGlzdEl0ZW0ucGFyZW50Tm9kZSxcclxuICAgICAgICAgICAgICAgICAgICBjaGlsZE5vZGVzID0gY2xpY2tlZExpc3RJdGVtLnBhcmVudE5vZGUuY2hpbGROb2RlcyxcclxuICAgICAgICAgICAgICAgICAgICBudW1DaGlsZE5vZGVzID0gY2hpbGROb2Rlcy5sZW5ndGgsXHJcbiAgICAgICAgICAgICAgICAgICAgbm9kZUluZGV4ID0gMCxcclxuICAgICAgICAgICAgICAgICAgICBpbmRleDtcclxuXHJcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IG51bUNoaWxkTm9kZXM7IGkrKykge1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKGNoaWxkTm9kZXNbaV0ubm9kZVR5cGUgIT09IDEpIHsgXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRpbnVlOyBcclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIGlmKGNoaWxkTm9kZXNbaV0gPT09IGNsaWNrZWRMaXN0SXRlbSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpbmRleCA9IG5vZGVJbmRleDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgIG5vZGVJbmRleCsrO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuXHJcblxyXG4gICAgICAgICAgICAgICAgaWYoaW5kZXggPj0gMCkge1xyXG4gICAgICAgICAgICAgICAgICAgIC8vIG9wZW4gUGhvdG9Td2lwZSBpZiB2YWxpZCBpbmRleCBmb3VuZFxyXG4gICAgICAgICAgICAgICAgICAgIG9wZW5QaG90b1N3aXBlKCBpbmRleCwgY2xpY2tlZEdhbGxlcnkgKTtcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgfTtcclxuXHJcbiAgICAgICAgICAgIC8vIHBhcnNlIHBpY3R1cmUgaW5kZXggYW5kIGdhbGxlcnkgaW5kZXggZnJvbSBVUkwgKCMmcGlkPTEmZ2lkPTIpXHJcbiAgICAgICAgICAgIHZhciBwaG90b3N3aXBlUGFyc2VIYXNoID0gZnVuY3Rpb24oKSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgaGFzaCA9IHdpbmRvdy5sb2NhdGlvbi5oYXNoLnN1YnN0cmluZygxKSxcclxuICAgICAgICAgICAgICAgIHBhcmFtcyA9IHt9O1xyXG5cclxuICAgICAgICAgICAgICAgIGlmKGhhc2gubGVuZ3RoIDwgNSkge1xyXG4gICAgICAgICAgICAgICAgICAgIHJldHVybiBwYXJhbXM7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgdmFyIHZhcnMgPSBoYXNoLnNwbGl0KCcmJyk7XHJcbiAgICAgICAgICAgICAgICBmb3IgKHZhciBpID0gMDsgaSA8IHZhcnMubGVuZ3RoOyBpKyspIHtcclxuICAgICAgICAgICAgICAgICAgICBpZighdmFyc1tpXSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjb250aW51ZTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIHBhaXIgPSB2YXJzW2ldLnNwbGl0KCc9Jyk7ICBcclxuICAgICAgICAgICAgICAgICAgICBpZihwYWlyLmxlbmd0aCA8IDIpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgY29udGludWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfSAgICAgICAgICAgXHJcbiAgICAgICAgICAgICAgICAgICAgcGFyYW1zW3BhaXJbMF1dID0gcGFpclsxXTtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICBpZihwYXJhbXMuZ2lkKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgcGFyYW1zLmdpZCA9IHBhcnNlSW50KHBhcmFtcy5naWQsIDEwKTtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICByZXR1cm4gcGFyYW1zO1xyXG4gICAgICAgICAgICB9O1xyXG5cclxuICAgICAgICAgICAgdmFyIG9wZW5QaG90b1N3aXBlID0gZnVuY3Rpb24oaW5kZXgsIGdhbGxlcnlFbGVtZW50LCBkaXNhYmxlQW5pbWF0aW9uLCBmcm9tVVJMKSB7XHJcbiAgICAgICAgICAgICAgICB2YXIgcHN3cEVsZW1lbnQgPSBkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcucHN3cCcpWzBdLFxyXG4gICAgICAgICAgICAgICAgICAgIGdhbGxlcnksXHJcbiAgICAgICAgICAgICAgICAgICAgb3B0aW9ucyxcclxuICAgICAgICAgICAgICAgICAgICBpdGVtcztcclxuXHJcbiAgICAgICAgICAgICAgICBpdGVtcyA9IHBhcnNlVGh1bWJuYWlsRWxlbWVudHMoZ2FsbGVyeUVsZW1lbnQpO1xyXG5cclxuICAgICAgICAgICAgICAgIC8vIGRlZmluZSBvcHRpb25zIChpZiBuZWVkZWQpXHJcbiAgICAgICAgICAgICAgICBvcHRpb25zID0ge1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAvLyBkZWZpbmUgZ2FsbGVyeSBpbmRleCAoZm9yIFVSTClcclxuICAgICAgICAgICAgICAgICAgICBnYWxsZXJ5VUlEOiBnYWxsZXJ5RWxlbWVudC5nZXRBdHRyaWJ1dGUoJ2RhdGEtcHN3cC11aWQnKSxcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgZ2V0VGh1bWJCb3VuZHNGbjogZnVuY3Rpb24oaW5kZXgpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gU2VlIE9wdGlvbnMgLT4gZ2V0VGh1bWJCb3VuZHNGbiBzZWN0aW9uIG9mIGRvY3VtZW50YXRpb24gZm9yIG1vcmUgaW5mb1xyXG4gICAgICAgICAgICAgICAgICAgICAgICB2YXIgdGh1bWJuYWlsID0gaXRlbXNbaW5kZXhdLmVsLmdldEVsZW1lbnRzQnlUYWdOYW1lKCdpbWcnKVswXSwgLy8gZmluZCB0aHVtYm5haWxcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHBhZ2VZU2Nyb2xsID0gd2luZG93LnBhZ2VZT2Zmc2V0IHx8IGRvY3VtZW50LmRvY3VtZW50RWxlbWVudC5zY3JvbGxUb3AsXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICByZWN0ID0gdGh1bWJuYWlsLmdldEJvdW5kaW5nQ2xpZW50UmVjdCgpOyBcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiB7eDpyZWN0LmxlZnQsIHk6cmVjdC50b3AgKyBwYWdlWVNjcm9sbCwgdzpyZWN0LndpZHRofTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgfTtcclxuXHJcbiAgICAgICAgICAgICAgICAvLyBQaG90b1N3aXBlIG9wZW5lZCBmcm9tIFVSTFxyXG4gICAgICAgICAgICAgICAgaWYoZnJvbVVSTCkge1xyXG4gICAgICAgICAgICAgICAgICAgIGlmKG9wdGlvbnMuZ2FsbGVyeVBJRHMpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gcGFyc2UgcmVhbCBpbmRleCB3aGVuIGN1c3RvbSBQSURzIGFyZSB1c2VkIFxyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBodHRwOi8vcGhvdG9zd2lwZS5jb20vZG9jdW1lbnRhdGlvbi9mYXEuaHRtbCNjdXN0b20tcGlkLWluLXVybFxyXG4gICAgICAgICAgICAgICAgICAgICAgICBmb3IodmFyIGogPSAwOyBqIDwgaXRlbXMubGVuZ3RoOyBqKyspIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGlmKGl0ZW1zW2pdLnBpZCA9PSBpbmRleCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaW5kZXggPSBqO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGJyZWFrO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgLy8gaW4gVVJMIGluZGV4ZXMgc3RhcnQgZnJvbSAxXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaW5kZXggPSBwYXJzZUludChpbmRleCwgMTApIC0gMTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9IGVsc2Uge1xyXG4gICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuaW5kZXggPSBwYXJzZUludChpbmRleCwgMTApO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIC8vIGV4aXQgaWYgaW5kZXggbm90IGZvdW5kXHJcbiAgICAgICAgICAgICAgICBpZiggaXNOYU4ob3B0aW9ucy5pbmRleCkgKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgIGlmKGRpc2FibGVBbmltYXRpb24pIHtcclxuICAgICAgICAgICAgICAgICAgICBvcHRpb25zLnNob3dBbmltYXRpb25EdXJhdGlvbiA9IDA7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgLy8gUGFzcyBkYXRhIHRvIFBob3RvU3dpcGUgYW5kIGluaXRpYWxpemUgaXRcclxuICAgICAgICAgICAgICAgIGdhbGxlcnkgPSBuZXcgUGhvdG9Td2lwZSggcHN3cEVsZW1lbnQsIFBob3RvU3dpcGVVSV9EZWZhdWx0LCBpdGVtcywgb3B0aW9ucyk7XHJcbiAgICAgICAgICAgICAgICBnYWxsZXJ5LmluaXQoKTtcclxuICAgICAgICAgICAgfTtcclxuXHJcbiAgICAgICAgICAgIC8vIGxvb3AgdGhyb3VnaCBhbGwgZ2FsbGVyeSBlbGVtZW50cyBhbmQgYmluZCBldmVudHNcclxuICAgICAgICAgICAgdmFyIGdhbGxlcnlFbGVtZW50cyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoIGdhbGxlcnlTZWxlY3RvciApO1xyXG5cclxuICAgICAgICAgICAgZm9yKHZhciBpID0gMCwgbCA9IGdhbGxlcnlFbGVtZW50cy5sZW5ndGg7IGkgPCBsOyBpKyspIHtcclxuICAgICAgICAgICAgICAgIGdhbGxlcnlFbGVtZW50c1tpXS5zZXRBdHRyaWJ1dGUoJ2RhdGEtcHN3cC11aWQnLCBpKzEpO1xyXG4gICAgICAgICAgICAgICAgZ2FsbGVyeUVsZW1lbnRzW2ldLm9uY2xpY2sgPSBvblRodW1ibmFpbHNDbGljaztcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgLy8gUGFyc2UgVVJMIGFuZCBvcGVuIGdhbGxlcnkgaWYgaXQgY29udGFpbnMgIyZwaWQ9MyZnaWQ9MVxyXG4gICAgICAgICAgICB2YXIgaGFzaERhdGEgPSBwaG90b3N3aXBlUGFyc2VIYXNoKCk7XHJcbiAgICAgICAgICAgIGlmKGhhc2hEYXRhLnBpZCAmJiBoYXNoRGF0YS5naWQpIHtcclxuICAgICAgICAgICAgICAgIG9wZW5QaG90b1N3aXBlKCBoYXNoRGF0YS5waWQgLCAgZ2FsbGVyeUVsZW1lbnRzWyBoYXNoRGF0YS5naWQgLSAxIF0sIHRydWUsIHRydWUgKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH07XHJcblxyXG4gICAgICAgIC8vIGV4ZWN1dGUgYWJvdmUgZnVuY3Rpb25cclxuICAgICAgICBpbml0UGhvdG9Td2lwZUZyb21ET00oJy5za20tZ2FsbGVyeScpO1xyXG5cclxuXHJcbn0pKCk7XHJcblxyXG4vLyQoJy5ncmlkLWl0ZW0gYScpLm9uKCdtb3VzZW92ZXInLCBmdW5jdGlvbigpe1xyXG4vLyAgICAvLyRwdGdJbmZvID0gJCh0aGlzKS5jaGlsZHJlbignLnB0Zy1pbmZvLW92ZXJsYXknKTtcclxuLy8gICAgLy8kcHRnSW5mby50b2dnbGVDbGFzcygndmlzaWJsZSBoaWRkZW4nKTtcclxuLy8gICAgJCh0aGlzKS5jaGlsZHJlbignLnB0Zy1pbmZvLW92ZXJsYXknKS50b2dnbGVDbGFzcygndmlzaWJsZSBoaWRkZW4nKTtcclxuLy99KTtcclxuLy8kKCcuZ3JpZC1pdGVtIGEnKS5vbignbW91c2VvdXQnLCBmdW5jdGlvbigpe1xyXG4vLyAgICAvLyRwdGdJbmZvID0gJCh0aGlzKS5jaGlsZHJlbignLnB0Zy1pbmZvLW92ZXJsYXknKTtcclxuLy8gICAgLy8kcHRnSW5mby50b2dnbGVDbGFzcygndmlzaWJsZSBoaWRkZW4nKTtcclxuLy8gICAgJCh0aGlzKS5jaGlsZHJlbignLnB0Zy1pbmZvLW92ZXJsYXknKS50b2dnbGVDbGFzcygndmlzaWJsZSBoaWRkZW4nKTtcclxuLy99KTtcclxuXHJcbn0pO1xyXG4iXX0=
