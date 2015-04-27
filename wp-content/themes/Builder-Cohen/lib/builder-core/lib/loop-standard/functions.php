<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-06-28
		Initial version
*/


if ( ! function_exists( 'dynamic_loop' ) ) {
	function dynamic_loop() {
		global $dynamic_loop_handlers;
		
		if ( empty( $dynamic_loop_handlers ) || ! is_array( $dynamic_loop_handlers ) )
			return false;
		
		ksort( $dynamic_loop_handlers );
		
		foreach ( (array) $dynamic_loop_handlers as $handlers ) {
			foreach ( (array) $handlers as $callback ) {
				list( $function, $args ) = $callback;
				if ( is_callable( $function ) && ( false != call_user_func_array( $function, $args ) ) )
					return true;
			}
		}
		
		return false;
	}
}

if ( ! function_exists( 'register_dynamic_loop_handler' ) ) {
	function register_dynamic_loop_handler( $function, $args = array(), $priority = 10 ) {
		global $dynamic_loop_handlers;
		
		if ( ! is_array( $args ) )
			$args = array();
		if ( ! is_numeric( $priority ) )
			$priority = 10;
		
		if ( ! isset( $dynamic_loop_handlers ) || ! is_array( $dynamic_loop_handlers ) )
			$dynamic_loop_handlers = array();
		
		if ( ! isset( $dynamic_loop_handlers[$priority] ) || ! is_array( $dynamic_loop_handlers[$priority] ) )
			$dynamic_loop_handlers[$priority] = array();
		
		$dynamic_loop_handlers[$priority][] = array( $function, $args );
	}
}
