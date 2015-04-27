<?php

/*
Written by Chris Jean for iThemes.com
Version 0.0.5

Version History
	0.0.1 - 2010-04-13
		Beta release ready
	0.0.2 - 2010-04-15
		Removed error_log status messages
		Added suppress_errors to ITStorage
	0.0.3 - 2010-05-19
		Added checks to not modify output for feeds
	0.0.4 - 2010-05-26
		Fixed PHP 4 compatibility issue
	0.0.5 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! class_exists( 'BuilderSEOFrontend' ) ) {
	class BuilderSEOFrontend {
		var $_var = 'builder-theme-settings';
		
		var $_head = '';
		var $_meta_tags = array();
		var $_parsable_meta = array( 'robots', 'description', 'keywords' );
		
		var $_original_title = null;
		var $_current_title = null;
		var $_found_titles = array();
		
		
		function BuilderSEOFrontend() {
			$this->_storage = new ITStorage2( $this->_var );
			
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'template_redirect', array( $this, 'replace_default_builder_title' ) );
		}
		
		function init() {
			$this->_options = $this->_storage->load();
			$this->_options = $this->_options['seo'];
			
			remove_action( 'builder_add_meta_data', 'builder_seo_options' );
			
			remove_all_filters( 'wp_title' );
			
			
			if ( 'default' === $this->_options['title_setting'] )
				add_filter( 'wp_title', array( $this, 'cache_original_title' ), -1000, 3 );
			
			
			add_action( 'template_redirect', array( $this, 'buffer_head_start' ), 0 );
			add_action( 'builder_layout_engine_render_header', array( $this, 'buffer_head_end' ), 11 );
			
			/*
				************* IMPORTANT ************
				add check to not filter robots if the following condition occurs:
				if ( '0' == get_option('blog_public') )
				this means that the blog is private
			*/
		}
		
		function replace_default_builder_title() {
			if ( is_feed() )
				return;
			
			remove_action( 'builder_add_title', 'builder_add_title' );
			add_action( 'builder_add_title', array( $this, 'default_builder_title' ) );
		}
		
		function default_builder_title() {
			
?>
	<!-- The Title -->
	<title><?php wp_title(); ?></title>
<?php
			
		}
		
		function buffer_head_start() {
			if ( is_feed() )
				return;
			
			ob_start( array( $this, 'filter_head' ) );
		}
		
		function buffer_head_end() {
			if ( is_feed() )
				return;
			
			ob_end_flush();
			
//			echo "<p>custom_title: " . htmlentities( $this->_custom_title ) . "</p>\n";
//			echo "<p>title_setting: " . htmlentities( $this->_options['title_setting'] ) . "</p>\n";
//			echo "<p>original_title: " . htmlentities( $this->_original_title ) . "</p>\n";
//			echo "<p>current_title: " . htmlentities( $this->_current_title ) . "</p>\n";
		}
		
		function filter_head( $head ) {
			global $post;
			
			
			$head_sections = explode( '</head>', $head );
			
			$this->_head = "{$head_sections[0]}\n";
			$this->_head_end = "\n</head>{$head_sections[1]}";
			
			
			$this->_parse_meta_tags();
			$this->_parse_custom_meta();
			
			foreach ( (array) $this->_parsable_meta as $meta ) {
				$meta_setting = $this->_options["{$meta}_setting"];
				
				if ( empty( $meta_setting ) )
					continue;
				
				if ( ( 'default' === $meta_setting ) && isset( $this->_meta_tags[$meta] ) )
					continue;
				
				if ( isset( $this->_meta_tags[$meta] ) )
					$this->_remove_meta_tag( $meta );
				
				if ( 'robots' !== $meta ) {
					if ( ! empty( $this->_custom_meta[$meta] ) )
						$meta_content = $this->_custom_meta[$meta];
					else
						$meta_content = call_user_func( array( $this, "_get_{$meta}_meta_content" ) );
				}
				else {
					$meta_content = ( isset( $this->_custom_meta[$meta] ) ) ? $this->_custom_meta[$meta] : '';
					$meta_content = call_user_func( array( $this, "_get_{$meta}_meta_content" ), $meta_content );
				}
				
				$this->_print_meta_tag( $meta, $meta_content );
			}
			
			
			if ( ! empty( $this->_custom_meta['title'] ) || ! empty( $this->_options['title_setting'] ) && ( ( 'default' !== $this->_options['title_setting'] ) || ( $this->_original_title === $this->_current_title ) ) )
				$this->_replace_title_tag( $this->_custom_meta['title'] );
			
			
			$this->_head .= $this->_head_end;
			
			return $this->_head;
		}
		
		function _get_description_meta_content() {
			$description = term_description();
			if ( ! is_string( $description ) )
				$description = '';
			
			if ( empty( $description ) )
				$description = $this->_get_content_description();
			
			if ( empty( $description ) )
				$description = $this->_options['other_views_description'];
			
			if ( empty( $description ) )
				$description = get_bloginfo( 'description' );
			
			return preg_replace( '/\s{2,}/', ' ', preg_replace( "/[\n\r]+/", ' ', trim( strip_tags( $description ) ) ) );
		}
		
		function _get_content_description() {
			if ( ! is_singular() )
				return '';
			
			
			global $post;
			
			$content = $post->post_excerpt;
			
			if ( empty( $content ) )
				$content = $post->post_content;
			
			$content = strip_tags( $content );
			
			$content = trim( strip_tags( str_replace( '><', '> <', $content ) ) );
			$content = preg_replace( '/\s{2,}/', ' ', preg_replace( "/[\n\r]+/", ' ', $content ) );
			
			if ( preg_match( '/^(.{20,149}[\.\?!]|.{20,150}\s)/', $content, $matches ) )
				$content = $matches[1];
			else
				$content = substr( $content, 0, 150 );
			
			return trim( $content );
		}
		
		function _get_robots_meta_content( $custom_robots = '' ) {
			$robots = array();
			
			if ( '0' == get_option( 'blog_public' ) )
				$robots[] = 'noindex,nofollow';
			else if ( ! empty( $custom_robots ) )
				$robots[] = $custom_robots;
			else if ( 'custom' === $this->_options['indexing_setting'] )
				$robots[] = $this->_get_custom_robots();
			
			if ( empty( $robots ) )
				$robots[] = $this->_get_default_robots();
			
			if ( empty( $this->_options['enable_dmoz'] ) )
				$robots[] = 'noodp';
			if ( empty( $this->_options['enable_yahoo_directory'] ) )
				$robots[] = 'noydir';
			if ( empty( $this->_options['enable_archive'] ) )
				$robots[] = 'noarchive';
			if ( empty( $this->_options['enable_snippet'] ) )
				$robots[] = 'nosnippet';
			
			$content = implode( ',', $robots );
			
			return $content;
		}
		
		function _get_default_robots() {
			$robots = 'index,follow';
			
			if ( is_category() || is_tag() || is_author() || is_date() || is_search() || ( is_home() && ( get_query_var('paged') > 1 ) ) )
				$robots = 'noindex,follow';
			
			return $robots;
		}
		
		function _get_custom_robots() {
			$robots_views = $this->_options['robots_views'];
			
			ksort( $robots_views );
			
			foreach ( (array) $robots_views as $functions ) {
				foreach ( (array) $functions as $function => $content ) {
					if ( function_exists( $function ) && ( true === $function() ) )
						return $content;
				}
			}
			
			return '';
		}
		
		function _get_keywords_meta_content() {
			if ( ! is_singular() && is_home() && ! empty( $this->_options['home_keywords'] ) )
				return $this->_options['home_keywords'];
			
			if ( ! is_singular() || empty( $this->_options['post_keywords'] ) )
				return '';
			
			
			global $post;
			
			$taxes = array();
			
			if ( in_array( $this->_options['post_keywords'], array( 'categories_and_tags', 'categories' ) ) )
				$taxes = array_merge( $taxes, get_the_terms( $post->ID, 'category' ) );
			if ( in_array( $this->_options['post_keywords'], array( 'categories_and_tags', 'tags' ) ) )
				$taxes = array_merge( $taxes, get_the_terms( $post->ID, 'post_tag' ) );
			
			$keywords = array();
			
			foreach ( (array) $taxes as $tax )
				$keywords[] = $tax->name;
			sort( $keywords );
			$keywords = implode( ',', $keywords );
			
			return $keywords;
		}
		
		function _parse_custom_meta() {
			if ( ! is_singular() || isset( $this->_custom_meta ) )
				return;
			
			
			global $post;
			
			$this->_custom_meta = array();
			
			$types = array(
				'title',
				'description',
				'robots',
				'keywords',
			);
			
			foreach ( (array) $types as $type ) {
				$data = get_post_meta( $post->ID, "_{$this->_var}_$type", true );
				
				if ( ! empty( $data ) )
					$this->_custom_meta[$type] = $data;
			}
		}
		
		function _remove_meta_tag( $meta ) {
			$this->_head = preg_replace( '/\s*<meta[^>]+name=[\'"]*' . preg_quote( $meta, '/' ) . '[\'"\s][^>]*>/', '', $this->_head );
		}
		
		function _print_meta_tag( $meta, $meta_content ) {
			if ( empty( $meta_content ) )
				return;
			
			$attributes = array(
				'name'		=> $meta,
				'content'	=> $meta_content,
			);
			
			$this->_head .= "\t" . ITUtility::get_open_tag( 'meta', $attributes ) . "\n";
		}
		
		function _replace_title_tag( $title ) {
			if ( empty( $title ) )
				$title = $this->_generate_title();
			
			if ( isset( $this->_found_titles[0] ) )
				$this->_head = preg_replace( '/' . preg_quote( $this->_found_titles[0], '/' ) . '/', "<title>$title</title>", $this->_head );
		}
		
		function cache_original_title( $title, $sep, $sel_loc ) {
			if ( is_null( $this->_original_title ) )
				$this->_original_title = $title;
			
			return $title;
		}
		
		function _generate_title() {
			if ( 'simple' === $this->_options['title_type'] )
				$title = $this->_generate_simple_title();
			else
				$title = $this->_generate_custom_title();
			
			$this->_title = $title;
			
			return $title;
		}
		
		function _generate_simple_title() {
			if ( 'custom' === $this->_options['simple_title_separator'] )
				$separator = $this->_options['simple_title_separator_custom'];
			else
				$separator = $this->_options['simple_title_separator'];
			
			$separator = html_entity_decode( $separator );
			
			return $this->_wp_title( $separator, $this->_options['simple_title_style'] );
		}
		
		function _generate_custom_title() {
			$title_views = $this->_options['title_views'];
			
			ksort( $title_views );
			
			foreach ( (array) $title_views as $functions ) {
				foreach ( (array) $functions as $function => $format ) {
					if ( function_exists( $function ) && ( true === $function() ) )
						return $this->_replace_custom_title_vars( $format );
				}
			}
		}
		
		function _replace_custom_title_vars( $title ) {
			global $post, $paged;
			
			
			$vars = array(
				'%title%'				=> $this->_wp_title( $this->_options['separator_format'], 'title' ),
				'%blog-title%'			=> get_bloginfo( 'name' ),
				'%search-terms%'		=> strip_tags( get_query_var( 's' ) ),
				'%sep%'					=> $this->_options['separator_format'],
				'%date%'				=> '',
				'%alt-date%'			=> '',
				'%author%'				=> '',
				'%page-number%'			=> '',
				'%page-number-listing%'	=> '',
				'%##%'					=> '',
			);
			
			if ( isset( $post ) ) {
				$author = get_userdata( $post->post_author );
				$vars['%author%'] = $author->user_nicename;
				
				$categories = get_the_category();
				$category = reset( $categories );
				$vars['%category%'] = $category->cat_name;
				
				$vars['%categories%'] = array();
				foreach ( (array) $categories as $category ) {
					if ( ! empty( $vars['%categories%'] ) )
						$vars['%categories%'] .= ', ';
					$vars['%categories%'] .= $category->cat_name;
				}
				
				$vars['%date%'] = get_the_time( 'F-j-Y' );
				$vars['%alt-date%'] = get_the_time( 'j-F-Y' );
			}
			
			if ( isset( $paged ) && $paged > 1 ) {
				$vars['%page-number%'] = $paged;
				$vars['%page-number-listing%'] = $this->_options['page_number_listing_format'];
				
				foreach ( (array) $vars as $var => $val )
					$vars['%page-number-listing%'] = str_replace( $var, $val, $vars['%page-number-listing%'] );
				
				if ( false === strpos( $title, '%page-number-listing%' ) ) {
					if ( false !== strpos( $title, '%title%' ) )
						$title = preg_replace( '/(%title%[^\s]*)/', '$1%page-number-listing%', $title );
					else if ( false !== strpos( $title, '%search-terms%' ) )
						$title = preg_replace( '/(%search-terms%[^\s]*)/', '$1%page-number-listing%', $title );
					else
						$title .= ' %page-number-listing%';
				}
			}
			
			
			foreach ( (array) $vars as $var => $val )
				$title = str_replace( $var, $val, $title );
			
			return html_entity_decode( $title );
		}
		
		
		function _parse_meta_tags() {
			$data = preg_replace( '/<!--.*-->/', '', $this->_head );
			
			$this->_parsed_data = $data;
			
			if ( preg_match_all( "/<\/?meta\s+(name|content)\s*=\s*('[^']*'|\"[^\"]*\"|[^'\">\s]*)[^>]*(name|content)\s*=\s*('[^']*'|\"[^\"]*\"|[^'\">\s]*)\s*\/*>/i", $data, $matches, PREG_SET_ORDER ) ) {
				$this->_matches = $matches;
				foreach ( (array) $matches as $match ) {
					foreach ( (array) $match as $var => $val ) {
						$val = $this->_remove_attribute_quotes( $val );
						
						$match[$var] = $val;
					}
					
					$meta_tag = array(
						$match[1]	=> $match[2],
						$match[3]	=> $match[4],
					);
					
					if ( isset( $meta_tag['name'] ) )
						$this->_meta_tags[trim( strtolower( $meta_tag['name'] ) )] = ( isset( $meta_tag['content'] ) ) ? $meta_tag['content'] : '';
				}
			}
			
			if ( preg_match_all( '|<\s*title[^>]*>(.*?)</\s*title[^>]*>|', $data, $matches, PREG_SET_ORDER ) ) {
				$this->_current_title = $matches[0][1];
				
				foreach ( (array) $matches as $match )
					$this->_found_titles[] = ( isset( $match[0] ) ) ? $match[0] : '';
			}
		}
		
		function _remove_attribute_quotes( $text ) {
			if ( preg_match( '/^\'[^\']*\'$/', $text ) )
				return preg_replace( '/^\'([^\']*)\'$/', '$1', $text );
			
			if ( preg_match( '/^\"[^\"]*\"$/', $text ) ) 
				return preg_replace( '/^\"([^\"]*)\"$/', '$1', $text );
			
			return $text;
		}
		
		// Customized from version 2.9.2 of WordPress
		function _wp_title( $sep, $blog_title_position = false ) {
			global $wpdb, $wp_locale, $wp_query;
			
			$cat = get_query_var( 'cat' );
			$tag = get_query_var( 'tag_id' );
			$category_name = get_query_var( 'category_name' );
			$author = get_query_var( 'author' );
			$author_name = get_query_var( 'author_name' );
			$m = get_query_var( 'm' );
			$year = get_query_var( 'year' );
			$monthnum = get_query_var( 'monthnum' );
			$day = get_query_var( 'day' );
			$search = get_query_var( 's' );
			$title = '';
			
			$t_sep = '%WP_TITILE_SEP%';
			
			if ( ! empty( $cat ) ) {
				if ( ! stristr( $cat, '-' ) )
					$title = apply_filters( 'single_cat_title', get_the_category_by_ID( $cat ) );
			}
			else if ( ! empty( $category_name ) ) {
				if ( stristr( $category_name, '/' ) ) {
					$category_name = explode( '/', $category_name );
					
					if ( $category_name[count( $category_name ) - 1] )
						$category_name = $category_name[count( $category_name ) - 1];
					else
						$category_name = $category_name[count( $category_name ) - 2];
				}
				
				$cat = get_term_by( 'slug', $category_name, 'category', OBJECT, 'display' );
				if ( $cat )
					$title = apply_filters( 'single_cat_title', $cat->name );
			}
			
			if ( ! empty( $tag ) ) {
				$tag = get_term( $tag, 'post_tag', OBJECT, 'display' );
				if ( is_wp_error( $tag ) )
					return $tag;
				if ( ! empty( $tag->name ) )
					$title = apply_filters( 'single_tag_title', $tag->name );
			}
			
			if ( ! empty( $author ) ) {
				$title = get_userdata( $author );
				$title = $title->display_name;
			}
			if ( ! empty( $author_name ) ) {
				$title = $wpdb->get_var( $wpdb->prepare( "SELECT display_name FROM $wpdb->users WHERE user_nicename = %s", $author_name ) );
			}
			
			if ( ! empty( $m ) ) {
				$my_year = substr( $m, 0, 4 );
				$my_month = $wp_locale->get_month( substr( $m, 4, 2 ) );
				$my_day = intval( substr( $m, 6, 2 ) );
				$title = "$my_year" . ( $my_month ? "$t_sep$my_month" : "" ) . ( $my_day ? "$t_sep$my_day" : "" );
			}
			
			if ( ! empty( $year ) ) {
				$title = $year;
				if ( ! empty( $monthnum ) )
					$title .= "$t_sep" . $wp_locale->get_month( $monthnum );
				if ( ! empty( $day ) )
					$title .= "$t_sep" . zeroise( $day, 2 );
			}
			
			if ( is_single() || ( is_home() && !is_front_page() ) || ( is_page() && !is_front_page() ) ) {
				$post = $wp_query->get_queried_object();
				$title = strip_tags( apply_filters( 'single_post_title', $post->post_title ) );
			}
			
			if ( is_tax() ) {
				$taxonomy = get_query_var( 'taxonomy' );
				$tax = get_taxonomy( $taxonomy );
				$tax = $tax->label;
				$term = $wp_query->get_queried_object();
				$term = $term->name;
				$title = "$tax$t_sep$term";
			}
			
			if ( is_search() ) {
				$title = sprintf( __( 'Search Results %1$s %2$s' ), $t_sep, strip_tags( $search ) );
			}
			
			if ( is_404() ) {
				$title = __( 'Page not found' );
			}
			
			$prefix = '';
			if ( ! empty( $title ) )
				$prefix = " $sep ";
			
			if ( empty( $title ) ) {
				$title = get_bloginfo( 'name' );
			}
			else if ( 'title' === $blog_title_position ) {
				$title_array = explode( $t_sep, $title );
				$title_array = array_reverse( $title_array );
				$title = implode( " $sep ", $title_array );
			}
			else if ( 'name_title' === $blog_title_position ) {
				$title_array = explode( $t_sep, $title );
				$title = get_bloginfo( 'name' ) . " $sep " . implode( " $sep ", $title_array );
			}
			else {
				$title_array = explode( $t_sep, $title );
				$title_array = array_reverse( $title_array );
				$title = implode( " $sep ", $title_array ) . " $sep " . get_bloginfo( 'name' );
			}
			
			return $title;
		}
	}
	
	new BuilderSEOFrontend();
}


?>
