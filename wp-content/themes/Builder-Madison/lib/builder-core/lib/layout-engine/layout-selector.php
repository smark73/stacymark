<?php

/*
Written by Chris Jean for iThemes.com
Version 1.3.5

Version History
	1.2.0 - 2012-10-22 - Chris Jean
		Added action "builder_print_render_comments" to allow other code to add comments.
	1.3.0 - 2012-12-14 - Chris Jean
		Added legacy full width modules detection code as doing so in layout-engine.php was too late.
		Fixed issue with the wp-signup.php page on multisite that prevented Layout selection.
	1.3.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.3.2 - 2013-06-28 - Chris Jean
		Added details that wp-signup.php is a WP core file and not an actual template to the Builder Layout details comment.
		Updated setup_layout() to not treat the signup page as a post or page.
	1.3.3 - 2013-07-17 - Chris Jean
		Removed legacy-templates loader as legacy templates are no longer a concern under Builder 5.
	1.3.4 - 2013-08-22 - Chris Jean
		Added Builder core version to builder_debug comment output.
	1.3.5 - 2013-11-21 - Chris Jean
		Added categtory, tag, etc-specific entries to the _layout_functions array.
*/


if ( ! class_exists( 'BuilderLayoutSelector' ) ) {
	class BuilderLayoutSelector {
		var $_layout_id = null;
		var $_view_id = null;
		var $_view = null;
		var $_var = 'layout_settings';
		
		var $_layout_functions = array();
		var $_layout_views = array();
		
		
		function BuilderLayoutSelector() {
			$this->_storage = new ITStorage( $this->_var );
			
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'builder_force_layout_selection', array( $this, 'setup_layout' ) );
			add_action( 'template_redirect', array( $this, 'setup_layout' ), -10 );
			add_action( 'builder_layout_engine_render_header', array( $this, 'print_render_comments' ), 15 );
			
			add_action( 'builder_layout_engine_render', array( $this, 'render' ) );
			add_action( 'builder_module_render_element_block_contents', array( $this, 'render_module_element_block_contents' ) );
			
			add_filter( 'builder_get_current_layout', array( $this, 'get_current_layout' ), 0 );
			add_filter( 'builder_get_current_layout_id', array( $this, 'get_current_layout_id' ), 0 );
			
			add_filter( 'body_class', array( $this, 'filter_body_classes' ) );
		}
		
		function init() {
			$this->_layout_settings = $this->_storage->load();
		}
		
		function get_current_layout( $layout ) {
			if ( empty( $this->_layout_id ) )
				$this->setup_layout();
			
			return $this->_layout_settings['layouts'][$this->_layout_id];
		}
		
		function get_current_layout_id( $id ) {
			if ( empty( $this->_layout_id ) )
				$this->setup_layout();
			
			return $this->_layout_id;
		}
		
		function render( $view ) {
			if ( empty( $this->_layout_id ) )
				$this->setup_layout();
			
			$this->_view = strtolower( preg_replace( '/-+/', '-', preg_replace( '/[^a-z0-9\-\.]/i', '-', $view ) ) );
			
			if ( 'wp-signup.php' == $this->_view )
				$this->_view .= ' (WordPress core file)';
			
			$stylesheet_path = builder_stylesheet_directory();
			$template_path = builder_template_directory();
			
			do_action( 'builder_layout_engine_render_layout', $view, $this->_layout_id, $this->_layout_settings['layouts'][$this->_layout_id] );
		}
		
		function render_module_element_block_contents( $fields ) {
			do_action( "builder_module_render_element_block_contents_{$fields['module']}", $fields );
		}
		
		function filter_body_classes( $classes ) {
			$classes[] = "builder-template-" . preg_replace( '/\.php.*/', '', $this->_view );
			
			foreach ( (array) $this->_layout_views as $view )
				$classes[] = "builder-view-$view";
			
			return $classes;
		}
		
		function print_render_comments( $render_data ) {
			$menu_capability = apply_filters( 'it_builder_menu_capability', 'switch_themes' );
			
			if ( ! current_user_can( $menu_capability ) && empty( $_GET['builder_debug'] ) )
				return;
			
			
			$class_prefix = apply_filters( 'builder_module_filter_css_prefix', '' );
			
			echo "<!--\n";
			
			if ( ! empty( $_GET['builder_debug'] ) )
				echo "\tBuilder Core Version: {$GLOBALS['it_builder_core_version']}\n\n";
			
			echo "\tLayout:               {$this->_layout_settings['layouts'][$this->_layout_id]['description']}\n";
			echo "\tTemplate File:        $this->_view\n";
			
			if ( ! empty( $this->_view_id ) )
				echo "\tActive View Function: {$this->_view_id}\n";
			
			echo "\tView" . ( ( 1 !== count( $this->_layout_views ) ) ? 's:' : ': ' ) . '                ' . implode( ', ', $this->_layout_views ) . "\n";
			echo "\tView Function" . ( ( 1 !== count( $this->_layout_functions ) ) ? 's:' : ': ' ) . '       ' . implode( ', ', $this->_layout_functions ) . "\n\n";
			
			echo "\tTemplate Class (body): .builder-template-" . preg_replace( '/\.php.*/', '', $this->_view ) . "\n";
			
			foreach ( (array) $this->_layout_views as $view )
				echo "\tView Class (body):     .builder-view-$view\n";
			echo "\n";
			
			echo "\tLayout ID (body):         #builder-layout-{$this->_layout_settings['layouts'][$this->_layout_id]['guid']}\n";
			echo "\tContainer ID (container): #builder-container-{$this->_layout_settings['layouts'][$this->_layout_id]['guid']}\n\n";
			
			echo "\tModule IDs:\n";
			
			foreach ( (array) $this->_layout_settings['layouts'][$this->_layout_id]['modules'] as $id => $module )
				printf( "\t\t%-12s #%s-%s\n", "{$module['module']}:", $class_prefix, $module['guid'] );
			
			do_action( 'builder_print_render_comments' );
			
			echo "-->\n";
		}
		
		function setup_layout() {
			global $post, $wp_the_query;
			
			if ( ! builder_is_signup_page() && ( is_single() || is_page() ) ) {
				if ( is_object( $post ) ) {
					$this->_layout_id = get_post_meta( $post->ID, '_custom_layout', true );
					
					if ( ! empty( $this->_layout_id ) && ( ! isset( $this->_layout_settings['layouts'][$this->_layout_id] ) || ! is_array( $this->_layout_settings['layouts'][$this->_layout_id] ) ) )
						$this->_layout_id = null;
					
					if ( ! empty( $this->_layout_id ) ) {
						$this->_layout_functions[] = 'custom_layout';
						$this->_layout_views = array( $post->post_type, "$post->post_type-$post->ID" );
						$this->_view_id = "post:{$post->ID}";
					}
				}
			}
			
			if ( empty( $this->_layout_id ) || ! is_array( $this->_layout_settings['layouts'][$this->_layout_id] ) ) {
				$available_views = apply_filters( 'builder_get_available_views', array() );
				
				$priority = 0;
				
				foreach ( $available_views as $function => $view ) {
					if ( $this->_is_current_view( $function ) ) {
						if ( ( $view['priority'] > $priority ) && ! empty( $this->_layout_settings['views'][$function] ) && ( '//INHERIT//' != $this->_layout_settings['views'][$function]['layout'] ) ) {
							$this->_layout_id = $this->_layout_settings['views'][$function]['layout'];
							$this->_view_id = $function;
							$priority = $view['priority'];
						}
						
						$this->_layout_functions[] = $function;
						$this->_layout_views[] = strtolower( str_replace( ' ', '-', $available_views[$function]['name'] ) );
					}
				}
			}
			
			
			if ( ! empty( $wp_the_query->query_vars['cat'] ) ) {
				$this->_layout_functions[] = "is_category__{$wp_the_query->query_vars['cat']}";
				$this->_layout_views[] = "category-{$wp_the_query->query_vars['cat']}";
				
				if ( isset( $this->_layout_settings['views']["is_category__{$wp_the_query->query_vars['cat']}"] ) && ( '//INHERIT//' != $this->_layout_settings['views']["is_category__{$wp_the_query->query_vars['cat']}"]['layout'] ) ) {
					$this->_layout_id = $this->_layout_settings['views']["is_category__{$wp_the_query->query_vars['cat']}"]['layout'];
					$this->_view_id = "is_category__{$wp_the_query->query_vars['cat']}";
				}
			}
			else if ( ! empty( $wp_the_query->query_vars['tag_id'] ) ) {
				$this->_layout_functions[] = "is_tag__{$wp_the_query->query_vars['tag_id']}";
				$this->_layout_views[] = "tag-{$wp_the_query->query_vars['tag_id']}";
				
				if ( isset( $this->_layout_settings['views']["is_tag__{$wp_the_query->query_vars['tag_id']}"] ) && ( '//INHERIT//' != $this->_layout_settings['views']["is_tag__{$wp_the_query->query_vars['tag_id']}"]['layout'] ) ) {
					$this->_layout_id = $this->_layout_settings['views']["is_tag__{$wp_the_query->query_vars['tag_id']}"]['layout'];
					$this->_view_id = "is_tag__{$wp_the_query->query_vars['tag_id']}";
				}
			}
			else if ( ! empty( $wp_the_query->query_vars['author'] ) ) {
				$this->_layout_functions[] = "is_author__{$wp_the_query->query_vars['author']}";
				$this->_layout_views[] = "author-{$wp_the_query->query_vars['author']}";
				
				if ( isset( $this->_layout_settings['views']["is_author__{$wp_the_query->query_vars['author']}"] ) && ( '//INHERIT//' != $this->_layout_settings['views']["is_author__{$wp_the_query->query_vars['author']}"]['layout'] ) ) {
					$this->_layout_id = $this->_layout_settings['views']["is_author__{$wp_the_query->query_vars['author']}"]['layout'];
					$this->_view_id = "is_author__{$wp_the_query->query_vars['author']}";
				}
			}
			else if ( ! builder_is_signup_page() && isset( $post ) && ! empty( $post->post_type ) ) {
				$this->_layout_functions[] = "builder_is_custom_post_type__{$post->post_type}";
				$this->_layout_views[] = "$post->post_type-$post->ID";
				
				if ( isset( $this->_layout_settings['views']["builder_is_custom_post_type__{$post->post_type}"] ) && ( '//INHERIT//' != $this->_layout_settings['views']["builder_is_custom_post_type__{$post->post_type}"]['layout'] ) ) {
					$this->_layout_id = $this->_layout_settings['views']["builder_is_custom_post_type__{$post->post_type}"]['layout'];
					$this->_view_id = "builder_is_custom_post_type__{$post->post_type}";
				}
			}
			
			
			$original_layout_id = $this->_layout_id;
			
			$this->_layout_id = apply_filters( 'builder_filter_current_layout', $this->_layout_id );
			
			if ( $this->_layout_id !== $original_layout_id )
				$this->_layout_functions[] = 'filter';
			
			
			if ( empty( $this->_layout_settings['layouts'][$this->_layout_id] ) ) {
				$this->_layout_id = $this->_layout_settings['default'];
				$this->_layout_functions[] = 'default';
			}
			
			if ( empty( $this->_layout_views ) )
				$this->_layout_views[] = 'default';
			
			
			do_action_ref_array( 'builder_layout_engine_identified_view', array( $this->_view_id, $this->_layout_settings, $this->_layout_views, $this->_layout_functions ) );
			do_action_ref_array( 'builder_layout_engine_identified_layout', array( $this->_layout_id, $this->_layout_settings, $this->_layout_views, $this->_layout_functions ) );
			
			do_action( 'builder_sidebar_register_layout_sidebars', $this->_layout_id );
			
			
			$layout_width = apply_filters( 'builder_get_container_width', $this->_layout_settings['layouts'][$this->_layout_id]['width'] );
			
			if ( empty( $layout_width ) ) {
				add_theme_support( 'builder-full-width-modules' );
				add_theme_support( 'builder-full-width-modules-legacy' );
			}
		}
		
		function _is_current_view( $function ) {
			$args = explode( '|', $function );
			$function = array_shift( $args );
			
			if ( ! function_exists( $function ) )
				return false;
			
			return call_user_func_array( $function, $args );
		}
	}
	
	new BuilderLayoutSelector();
}
