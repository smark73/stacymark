function builder_set_start_here_iframe_height( height ) {
	if ( document.getElementById ) {
		var iframe = document.getElementById( 'start_here_frame' );
		
		if (iframe) {
			iframe.style.height = height + "px";
			iframe.style.marginTop = "0px";
		}
	}
}
