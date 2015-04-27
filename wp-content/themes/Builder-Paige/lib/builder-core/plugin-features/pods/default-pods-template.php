<?php
/*
Template Name: Pods Template
*/


function render_content() {
	pods_content();
}

add_action( 'builder_layout_engine_render_content', 'render_content' );

do_action( 'builder_layout_engine_render', basename( __FILE__ ) );
