<?php

function render_content() {
	// Load the not_found.php file, by default
	do_action( 'builder_template_show_not_found' );
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
