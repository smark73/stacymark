<?php

/*
Generators for web analytics JavaScript code

Written by Chris Jean for iThemes.com
Version 1.1.0

Version History
	1.0.0 - 2010-12-15 - Chris Jean
		Release ready
	1.1.0 - 2011-12-09 - Chris Jean
		Added GoSquared generator
*/


/*
function builder_add_universal_analytics_functions( $content ) {
	ob_start();
	
	require_once( dirname( dirname( __FILE__ ) ) . '/js/universal-analytics.js.php' );
	
	$script = ob_get_contents();
	ob_end_clean();
	
	if ( ! empty( $content ) )
		$content .= "\n";
	
	return $content . $script;
}
add_filter( 'builder_filter_javascript_content', 'builder_add_universal_analytics_functions', 20 );
*/

function builder_generate_google_analytics_code( $content ) {
	if ( ! builder_get_theme_setting( 'google_analytics_enable' ) )
		return $content;
	
	$account_id = builder_get_theme_setting( 'google_analytics_account_id' );
	
	if ( empty( $account_id ) )
		return $content;
	
	ob_start();
	
?>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', "<?php echo $account_id; ?>"]);
_gaq.push(['_trackPageview']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
<?php
	
	$script = ob_get_contents();
	ob_end_clean();
	
	if ( ! empty( $content ) )
		$content .= "\n";
	
	return $content . $script;
}
add_filter( 'builder_filter_javascript_content', 'builder_generate_google_analytics_code' );


function builder_generate_woopra_code( $content ) {
	if ( ! builder_get_theme_setting( 'woopra_enable' ) )
		return $content;
	
	$woo_settings = $woo_actions = array();
	
	
	$domain = builder_get_theme_setting( 'woopra_domain' );
	
	if ( ! empty( $domain ) )
		$woo_settings[] = "domain:'$domain'";
	else {
		$domain = get_option( 'home' );
		
		if ( preg_match( '|//([^/]+)|', $domain, $match ) )
			$woo_settings[] = "domain:'" . addslashes( $match[1] ) . "'";
	}
	
	
	if ( ! empty( $woo_settings ) )
		$woo_settings = 'var woo_settings = {' . implode( ', ', $woo_settings ) . "};\n";
	else
		$woo_settings = '';
	
	if ( ! empty( $woo_actions ) )
		$woo_actions = 'var woo_actions = [' . implode( ',', $woo_actions ) . "];\n";
	else
		$woo_actions = '';
	
	ob_start();
	
?>
<?php echo $woo_settings; ?>
<?php echo $woo_actions; ?>
(function(){
var wsc = document.createElement('script');
wsc.src = document.location.protocol+'//static.woopra.com/js/woopra.js';
wsc.type = 'text/javascript';
wsc.async = true;
var ssc = document.getElementsByTagName('script')[0];
ssc.parentNode.insertBefore(wsc, ssc);
})();
<?php
	
	$script = ob_get_contents();
	ob_end_clean();
	
	if ( ! empty( $content ) )
		$content .= "\n";
	
	return $content . $script;
}
add_filter( 'builder_filter_javascript_content', 'builder_generate_woopra_code' );


function builder_generate_gosquared_code( $content ) {
	if ( ! builder_get_theme_setting( 'gosquared_enable' ) )
		return $content;
	
	
	$site_token = builder_get_theme_setting( 'gosquared_site_token' );
	
	if ( empty( $site_token ) )
		return $content;
	
	
	ob_start();
	
?>
var GoSquared={};
GoSquared.acct = "<?php echo $site_token; ?>";

(function(w){
	w._gstc_lt=+(new Date); var d=document;
	var g = d.createElement("script"); g.type = "text/javascript"; g.async = true; g.src = "//d1l6p2sc9645hc.cloudfront.net/tracker.js";
	var s = d.getElementsByTagName("script")[0]; s.parentNode.insertBefore(g, s);
})(window);
<?php
	
	$script = ob_get_contents();
	ob_end_clean();
	
	if ( ! empty( $content ) )
		$content .= "\n";
	
	return $content . $script;
}
add_filter( 'builder_filter_javascript_content', 'builder_generate_gosquared_code' );
