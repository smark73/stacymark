<?php

/*
Written by Chris Jean for iThemes.com
Version 0.0.2

Version History
	0.0.1 - 2010-11-03 - Chris Jean
		Initial version
	0.0.2 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! class_exists( 'BuilderSEOPostEditor' ) ) {
	class BuilderSEOPostEditor {
		var $_var = 'theme-settings-seo';
		
		
		function BuilderSEOPostEditor() {
			add_action( 'admin_menu', array( $this, 'add_post_boxes' ) );
			add_action( 'save_post', array( $this, 'save_post_box_data' ) );
		}
		
		function add_post_boxes() {
			$types = array(
				'descriptions',
				'keywords',
				'robots',
				'titles',
			);
			
			$content_types = array();
			
			foreach ( (array) $types as $type ) {
				if ( ! empty( $this->_options["enable_custom_{$type}_pages"] ) && ! isset( $content_types['page'] ) ) {
					add_meta_box( $this->_var, 'SEO Options', array( $this, 'meta_box_index' ), 'page', 'advanced', 'high' );
					$content_types['page'] = true;
				}
				if ( ! empty( $this->_options["enable_custom_{$type}_posts"] ) && ! isset( $content_types['post'] ) ) {
					add_meta_box( $this->_var, 'SEO Options', array( $this, 'meta_box_index' ), 'post', 'advanced', 'high' );
					$content_types['post'] = true;
				}
			}
		}
		
		
		// Post Boxes //////////////////////////////////////
		
		function post_box_index() {
			global $post;
			
			
			$form = new ITForm( $this->_parse_meta_box_data( $post->ID ) );
			
			$robots = array(
				''					=> '',
				'index,follow'		=> 'index, follow',
				'noindex,follow'	=> 'noindex, follow',
				'index,nofollow'	=> 'index, nofollow',
				'noindex,nofollow'	=> 'noindex, nofollow',
			);
			
?>
	<dl>
		<?php if ( ! empty( $this->_options["enable_custom_titles_{$post->post_type}s"] ) ) : ?>
			<dt><label for='<?php echo "{$this->_var}_title"; ?>'>Title</label></dt>
			<dd><?php $form->add_text_box( "{$this->_var}_title", array( 'style' => 'width:350px;' ) ); ?></dd>
		<?php endif; ?>
		
		<?php if ( ! empty( $this->_options["enable_custom_descriptions_{$post->post_type}s"] ) ) : ?>
			<dt><label for='<?php echo "{$this->_var}_description"; ?>'>Description (max 150 characters)</label></dt>
			<dd><?php $form->add_text_area( "{$this->_var}_description", array( 'style' => 'width:350px;' ) ); ?></dd>
		<?php endif; ?>
		
		<?php if ( ! empty( $this->_options["enable_custom_robots_{$post->post_type}s"] ) ) : ?>
			<dt><label for='<?php echo "{$this->_var}_robots"; ?>'>Robots</label></dt>
			<dd><?php $form->add_drop_down( "{$this->_var}_robots", $robots ); ?></dd>
		<?php endif; ?>
		
		<?php if ( ! empty( $this->_options["enable_custom_keywords_{$post->post_type}s"] ) ) : ?>
			<dt><label for='<?php echo "{$this->_var}_keywords"; ?>'>Keywords (comma separated list)</label></dt>
			<dd><?php $form->add_text_box( "{$this->_var}_keywords", array( 'style' => 'width:350px;' ) ); ?></dd>
		<?php endif; ?>
	</dl>
	
	<?php $form->add_hidden( "{$this->_var}_save", '1' ); ?>
	<?php $form->add_hidden( "{$this->_var}_nonce", wp_create_nonce( plugin_basename( __FILE__ ) ) ); ?>
<?php
			
		}
		
		function _parse_post_box_data( $post_id ) {
			$vars = array(
				'title',
				'description',
				'robots',
				'keywords',
			);
			
			$data = array();
			
			foreach ( (array) $vars as $var ) {
				$name = "{$this->_var}_$var";
				$data[$name] = htmlspecialchars( stripcslashes( get_post_meta( $post_id, "_$name", true ) ) );
			}
			
			return $data;
		}
		
		function save_post_box_data( $post_id ) {
			if ( ! isset( $_POST["{$this->_var}_nonce"] ) || ! wp_verify_nonce( $_POST["{$this->_var}_nonce"], plugin_basename( __FILE__ ) ) )
				return $post_id;
			
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
				return $post_id;
			
			if ( empty( $_POST["{$this->_var}_save"] ) )
				return $post_id;
			
			if ( 'page' === $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return $post_id;
			}
			else {
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return $post_id;
			}
			
			
			foreach ( (array) $_POST as $var => $val ) {
				if ( ! preg_match( "/^{$this->_var}_/", $var ) )
					continue;
				
				update_post_meta( $post_id, "_$var", $val );
			}
			
			
			return $post_id;
		}
	}
}

?>
<?php

/*
Written by Chris Jean for iThemes.com
Version 0.0.2

Version History
	0.0.1 - 2010-04-13
		Beta release ready
	0.0.2 - 2010-04-15
		Added _suppress_errors variable
*/


if ( ! class_exists( 'BuilderSEOEditor' ) ) {
	class BuilderSEOEditor extends ITCoreClass {
		var $_var = 'builder_seo';
		var $_page_title = 'Builder SEO and Meta Options';
		var $_menu_title = 'SEO & Meta';
		var $_access_level = 'edit_themes';
		var $_page_var = 'builder-seo';
		var $_menu_priority = '12';
		var $_suppress_errors = true;
		
		
		function BuilderSEOEditor() {
			$this->ITCoreClass();
			
			$this->_file = __FILE__;
			
		}
		
		function init() {
			ITCoreClass::init();
			
			add_action( 'admin_menu', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
		}
		
		function add_meta_boxes() {
			$types = array(
				'descriptions',
				'keywords',
				'robots',
				'titles',
			);
			
			$content_types = array();
			
			foreach ( (array) $types as $type ) {
				if ( ! empty( $this->_options["enable_custom_{$type}_pages"] ) && ! isset( $content_types['page'] ) ) {
					add_meta_box( $this->_var, 'SEO Options', array( $this, 'meta_box_index' ), 'page', 'advanced', 'high' );
					$content_types['page'] = true;
				}
				if ( ! empty( $this->_options["enable_custom_{$type}_posts"] ) && ! isset( $content_types['post'] ) ) {
					add_meta_box( $this->_var, 'SEO Options', array( $this, 'meta_box_index' ), 'post', 'advanced', 'high' );
					$content_types['post'] = true;
				}
			}
		}
		
		function add_admin_scripts() {
			ITCoreClass::add_admin_scripts();
			
			wp_enqueue_script( "{$this->_var}-seo", "{$this->_plugin_url}/js/seo-editor.js", array( 'jquery', 'postbox' ) );
		}
		
		function add_admin_styles() {
			ITCoreClass::add_admin_styles();
			
			wp_enqueue_style( "{$this->_var}-seo", "{$this->_plugin_url}/css/seo-editor.css" );
		}
		
		function set_defaults( $defaults ) {
			$new_defaults = array(
				'show_editor_information'	=> '1',
				'show_editor_advanced'		=> '',
				
				'description_setting'								=> 'default',
				'enable_automatic_descriptions_pages'				=> '1',
				'enable_automatic_descriptions_posts'				=> '1',
				'enable_automatic_descriptions_custom_post_types'	=> '1',
				'enable_custom_descriptions_pages'					=> '1',
				'enable_custom_descriptions_posts'					=> '1',
				'enable_custom_descriptions_custom_post_types'		=> '1',
				'other_views_description'							=> '',
				
				'title_setting'								=> 'default',
				'enable_custom_titles_pages'				=> '1',
				'enable_custom_titles_posts'				=> '1',
				'enable_custom_titles_custom_post_types'	=> '1',
				'title_type'								=> 'simple',
				'simple_title_style'						=> 'title_name',
				'simple_title_separator'					=> '::',
				'simple_title_separator_custom'				=> '',
				'page_number_listing_format'				=> ' %sep% Page %page-number%',
				'separator_format'							=> '::',
				
				'robots_setting'							=> 'default',
				'enable_dmoz'								=> '',
				'enable_yahoo_directory'					=> '',
				'enable_archive'							=> '1',
				'enable_snippet'							=> '1',
				'enable_custom_robots_pages'				=> '1',
				'enable_custom_robots_posts'				=> '1',
				'enable_custom_robots_custom_post_types'	=> '1',
				'indexing_setting'							=> 'default',
				
				'keywords_setting'							=> 'default',
				'enable_custom_keywords_pages'				=> '1',
				'enable_custom_keywords_posts'				=> '1',
				'enable_custom_keywords_custom_post_types'	=> '1',
				'post_keywords'								=> 'categories_and_tags',
			);
			
			$views_data = $this->_get_views_data();
			
			foreach ( (array) $views_data as $view ) {
				if ( ! empty( $view['robots_default'] ) )
					$new_defaults["robots_views_{$view['priority']}_{$view['function']}"] = $view['robots_default'];
				
				if ( ! empty( $view['title_default'] ) )
					$new_defaults["title_views_{$view['priority']}_{$view['function']}"] = $view['title_default'];
			}
			
			$this->_defaults = array_merge( $defaults, $new_defaults );
			
			
			return $this->_defaults;
		}
		
		
	}
	
	new BuilderSEOPostEditor();
}


?>
