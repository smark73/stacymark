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
