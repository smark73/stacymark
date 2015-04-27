<?php

/*
Universal functions for use by analytics tools
Written by Chris Jean for iThemes.com
Version 0.0.1

Version History
	0.0.1 - 2010-12-13 - Chris Jean
		Work in progress
*/


if ( ! function_exists( 'builder_get_theme_setting' ) )
	return;
if ( ! builder_get_theme_setting( 'google_analytics_enable' ) && ! builder_get_theme_setting( 'woopra_enable' ) )
	return;

?>
function builder_analytics_click_handler( e ) {
	var target = (e.srcElement) ? e.srcElement : e.target;
	if(cElem.tagName == "A"){
		var link=cElem;
		var _download = link.pathname.match(/(?:doc|eps|jpg|png|svg|xls|ppt|pdf|xls|zip|txt|vsd|vxd|js|css|rar|exe|wma|mov|avi|wmv|mp3)($|\&)/);
		var ev=false;
		if(_download && (link.href.toString().indexOf('woopra-ns.com')<0)){
			ev=new WoopraEvent('download',{});
			ev.addProperty('url',link.href);
			ev.fire();
			pntr.sleep(100);
		}
		if (!_download&&link.hostname != location.host && link.hostname.indexOf('javascript')==-1 && link.hostname!=''){
			ev=new WoopraEvent('exit',{});
			ev.addProperty('url',link.href);
			ev.fire();
			pntr.sleep(400);
		}
	}

}
<?php

if ( builder_get_theme_setting( 'google_analytics_enable' ) && builder_get_theme_setting( 'google_analytics_action_tracker_download_links' ) ) {
	
?>
function builder_ga_track_event( category, action, label, value ) {
	try {
		var ga_args = ['_trackEvent', category, action];
		
		if ( 'undefined' !== typeof( label ) ) {
			ga_args.push( label );
			
			if ( 'undefined' !== typeof( value ) )
				ga_args.push( value );
		}
		
		_gaq.push(ga_args);
	}catch(err){}
}
<?php
	
}

//builder_track_event('Download', {type: 'PDF', file: '/trunk/file.pdf'});

?>
if ( document.addEventListener ) {
	document.addEventListener( 'click', builder_analytics_click_handler, false );
} 
else {
	document.attachEvent( 'onclick', builder_analytics_click_handler );
}
