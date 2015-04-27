/*global jQuery */
/*!
* FitVids 1.0
*
* Copyright 2011, Chris Coyier - http://css-tricks.com + Dave Rupert - http://daverupert.com
* Credit to Thierry Koblentz - http://www.alistapart.com/articles/creating-intrinsic-ratios-for-video/
* Released under the WTFPL license - http://sam.zoy.org/wtfpl/
* Modified by Chris Jean of iThemes.com to be more universal
*
* Date: Thu Sept 01 18:00:00 2011 -0500
*/

(function( $ ) {
	$.fn.fitVidsMaxWidthMod = function( options ) {
		var settings = {
			customSelector: null
		}
		
		var div = document.createElement('div'),
		ref = document.getElementsByTagName('base')[0] || document.getElementsByTagName('script')[0];
		
		div.className = 'fit-vids-style';
		div.innerHTML = '&shy;<style>           \
			.fluid-width-video-wrapper {        \
				width: 100%;                    \
				position: relative;             \
				padding: 0;                     \
			}                                   \
			                                    \
			.fluid-width-video-wrapper iframe,  \
			.fluid-width-video-wrapper object,  \
			.fluid-width-video-wrapper embed {  \
				position: absolute;             \
				top: 0;                         \
				left: 0;                        \
				width: 100%;                    \
				height: 100%;                   \
			}                                   \
			</style>';
		
		ref.parentNode.insertBefore(div,ref);
		
		if ( options ) {
			$.extend( settings, options );
		}
		
		return this.each( function() {
			var selectors = [
				"iframe[src*='player.vimeo.com']",
				"iframe[src*='www.youtube.com']",
				"iframe[src*='www.kickstarter.com']",
				"object",
				"embed"
			];
			
			if (settings.customSelector) {
				selectors.push(settings.customSelector);
			}
			
			var $allVideos = $(this).find(selectors.join(','));
			
			$allVideos.each( function() {
				var $this = $(this);
				if ( ( ( 'embed' == this.tagName.toLowerCase() ) && $this.parent('object').length ) || $this.parent('.fluid-width-video-wrapper').length ) {
					return;
				}
				
				var height = ( ( 'object' == this.tagName.toLowerCase() ) || $this.attr( 'height' ) ) ? $this.attr('height') : $this.height();
				if ( ( 'undefined' == typeof height ) || String( height ).match(/[^\d]/) || height <= 0 ) {
					height = 0;
					
					if ( $this.parent().css('height') ) {
						height = $this.parent().css('height').replace(/px$/, '');
						
						if ( ! height.match(/^\d+/) )
							height = 0;
					}
					
					if ( height.match(/[^\d]/) || height <= 0 )
						height = $this.height();
				}
				
				var width = ( this.tagName.toLowerCase() == 'object' || $this.attr('width') ) ? $this.attr('width') : $this.width();
				if ( ( 'undefined' == typeof width ) || String( width ).match(/[^\d]/) || width <= 0 ) {
					width = 0;
					
					if ( $this.parent().css('width') ) {
						width = $this.parent().css('width').replace(/px$/, '');
						
						if ( ! width.match(/^\d+/) )
							width = 0;
					}
					
					if ( width.match(/[^\d]/) || width <= 0 )
						width = $this.width();
				}
				var aspectRatio = height / width;
				
				if ( $this.parent() && $this.parent().attr('id') && $this.parent().attr('id').match(/^jwplayer/) ) {
					$this.parent().css( { 'height': 'auto', 'width': 'auto', 'max-height': height + 'px', 'max-width': width + 'px' } );
				}
				
				if ( ! $this.attr('id') ) {
					var videoID = 'fitvid' + Math.floor( Math.random() * 999999 );
					$this.attr( 'id', videoID );
				}
				$wrapper = $this.wrap('<div class="fluid-width-video-wrapper"></div>').parent('.fluid-width-video-wrapper').css('padding-top', (aspectRatio * 100)+"%");
				$this.removeAttr('height').removeAttr('width');
				
				$outer_wrapper = $wrapper.wrap('<div class="fluid-width-video-container"></div>').parent('.fluid-width-video-container').css({'max-width': width + 'px', 'max-height': height + 'px'});
			});
		});
	}
})( jQuery );
