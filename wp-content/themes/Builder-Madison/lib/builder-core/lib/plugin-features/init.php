<?php

/*
Load plugin features.
Written by Chris Jean for iThemes.com
Version 1.5.0

Version History
	1.0.0 - 2011-05-19 - Chris Jean
		Release ready
	1.1.0 - 2011-08-04 - Chris Jean
		Added Shopp support
	1.2.0 - 2011-10-06 - Chris Jean
		Added Pods support
	1.3.0 - 2011-12-05 - Chris Jean
		Updated to use new BuilderPluginFeatures API
	1.4.0 - 2011-12-13 - Chris Jean
		Added Cart66 support
	1.5.0 - 2013-02-15 - Chris Jean
		Added All in One SEO Pack
		Added WordPress SEO
*/


require( dirname( __FILE__ ) . '/functions.php' );


$builder_plugin_features = array(
	'all-in-one-seo-pack' => defined( 'AIOSEOP_VERSION' ),
	'cart66'              => class_exists( 'Cart66' ),
	'gravity-forms'       => class_exists( 'RGForms' ),
	'jetpack'             => defined( 'JETPACK__VERSION' ),
	'pods'                => defined( 'PODS_VERSION' ),
	'shopp'               => class_exists( 'Shopp' ),
	'wordpress-seo'       => defined( 'WPSEO_VERSION' ),
	'wp-ecommerce'        => class_exists( 'WP_eCommerce' ),
);

new BuilderPluginFeatures( $builder_plugin_features );
