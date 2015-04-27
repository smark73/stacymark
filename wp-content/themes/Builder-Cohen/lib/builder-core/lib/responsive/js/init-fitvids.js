/*
Init the FitVids script.
Written by Chris Jean for iThemes.com
Version 1.0

Version History
	1.0 - 2012-10-09 - Chris Jean
		Initial version
*/


jQuery(document).ready(
	function() {
		if ( 'function' == typeof jQuery.fn.fitVids ) {
			jQuery('body').fitVids();
		}
		else if ( 'function' == typeof jQuery.fn.fitVidsMaxWidthMod ) {
			jQuery('body').fitVidsMaxWidthMod();
		}
	}
);
