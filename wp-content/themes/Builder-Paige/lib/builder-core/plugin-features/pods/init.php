<?php

require_once( dirname( __FILE__ ) . '/functions.php' );

add_filter( 'pods_page_templates', 'builder_pods_integration_filter_page_templates' );
add_action( 'template_redirect', 'builder_pods_integration_replace_template_redirect', -20 );
