<?php

/*
Written by Chris Jean for iThemes.com
Version 1.1.1

Version History
	1.0.0 - 2010-12-15 - Chris Jean
		Release ready
	1.1.0 - 2011-08-04 - Chris Jean
		Added error handling
	1.1.1 - 2011-10-06 - Chris Jean
		Removed the by-reference passing of $this in method_exists functions
*/


if ( ! class_exists( 'ITThemeSettingsTab' ) ) {
	class ITThemeSettingsTab {
		var $_var = '';
		var $_name = '';
		
		var $_show_quick_links = true;
		var $_refresh_settings_on_save = true;
		var $_errors = array();
		
		
		function ITThemeSettingsTab( &$parent ) {
			$this->_parent =& $parent;
			$this->_options =& $parent->_options;
			
			add_action( 'builder_theme_settings_loaded', array( $this, 'register_tab' ) );
			
			add_filter( "it_storage_get_defaults_builder-theme-settings", array( $this, 'set_defaults' ) );
		}
		
		
		function init() {
			if ( ! method_exists( $this, '_init' ) )
				return;
			
			$this->_init();
		}
		
		function reset_handler() {
			if ( ! method_exists( $this, '_reset' ) )
				return;
			
			$this->_reset();
			
			if ( true === $this->_refresh_settings_on_save )
				$this->_refresh_settings();
			
			$redirect = "{$this->_parent->_self_link}&reset=true";
			wp_redirect( $redirect );
		}
		
		function save_handler() {
			if ( ! method_exists( $this, '_save' ) )
				return;
			
			
			$this->_save();
			
			if ( true === $this->_refresh_settings_on_save )
				$this->_refresh_settings();
			
			
			$redirect = "{$this->_parent->_self_link}&updated=true";
			
			if ( ! empty( $this->_errors ) ) {
				$redirect .= '&errors=';
				
				foreach ( (array) $this->_errors as $error ) {
					$redirect .= $error->get_error_code();
					
					set_transient( 'it_bt_' . $error->get_error_code(), $error->get_error_message(), 3600 );
				}
			}
			
			
			wp_redirect( $redirect );
		}
		
		function index() {
			if ( is_callable( array( $this, '_pre_index' ) ) )
				$this->_pre_index();
			
			if ( isset( $_GET['show_video'] ) ) {
				$this->_show_video( $_GET['show_video'], $_GET['video_width'], $_GET['video_height'] );
				return;
			}
			
			$this->_register_meta_boxes();
			$this->_editor();
		}
		
		function set_defaults( $defaults ) {
			return $defaults;
		}
		
		
		// Helper Functions //////////////////////////////////////
		
		function _refresh_settings() {
			$GLOBALS['wp_theme_options'] = $this->_options;
			do_action( 'it_cache_rebuild_cache_builder-core' );
		}
		
		function _print_editor_tabs() {
			echo "<h2 class='nav-tab-wrapper'>";
			
			foreach ( (array) $this->_parent->_tab_order as $var ) {
				$link = $this->_parent->_tabless_self_link . '&editor_tab=' . urlencode( $var );
				$class = 'nav-tab' . ( ( $var === $this->_parent->_active_tab ) ? ' nav-tab-active' : '' );
				$class .= ( is_null( $this->_parent->_editor_tabs[$var]['file'] ) ) ? ' nav-tab-deactivated' : '';
				$name = $this->_parent->_editor_tabs[$var]['name'];
				
				if ( ! is_null( $this->_parent->_editor_tabs[$var]['file'] ) )
					echo "<a class='$class' href='$link'>$name</a>";
				else
					echo "<span class='$class'>$name</span>";
			}
			
			echo "</h2>\n";
		}
		
		function _register_meta_boxes() {
			$boxes = builder_get_settings_editor_boxes( $this->_parent->_active_tab );
			
			foreach ( (array) $boxes as $var => $args )
				$this->_add_meta_box( $var, $args );
		}
		
		function _add_meta_box( $var, $args ) {
			$default_args = array(
				'title'			=> '',
				'callback'		=> '',
				'page'			=> $this->_var,
				'context'		=> 'meta',
				'priority'		=> 'default',
				'callback_args'	=> null,
			);
			
			if ( true === $args['_builtin'] ) {
				if ( is_null( $args['callback'] ) ) {
					$method_var = str_replace( '-', '_', $var );
					$args['callback'] = array( $this, "meta_box_$method_var" );
				}
			}
			else {
				$args['callback_args'] = array( 'callback' => $args['callback'] );
				$args['callback'] = array( $this, 'custom_meta_box_handler' );
			}
			
			$args = array_merge( $default_args, $args );
			
			$this->_meta_boxes["{$this->_var}-$var"] = $args;
		}
		
		function custom_meta_box_handler( $object, $box ) {
			call_user_func( $box['args']['callback'], $this->_form );
		}
		
		function legacy_custom_meta_box_handler() {
			echo "<table class='form-table'>\n";
			
			do_action( 'builder_custom_settings', $this->_form );
			
			echo "</table>\n";
		}
		
		function _print_meta_box_quick_links() {
			if ( true !== $this->_show_quick_links )
				return;
			
			$links = array();
			
			foreach ( (array) array_keys( $this->_meta_boxes ) as $id )
				$links[$id] = $this->_meta_boxes[$id]['title'];
			
			natcasesort( $links );
			
?>
	<div class="quick-links">
		<h4>Quick Links</h4>
		
		<ul>
			<?php foreach ( (array) $links as $id => $link_name ) : ?>
				<li><a href="#<?php echo $id; ?>"><?php echo $link_name; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<br style="clear:both;" />
<?php
			
		}
		
		function _print_meta_boxes( $form ) {
			// Arguments passed are the meta_boxes array to be filtered along with the page that should be used and the default context
			$this->_meta_boxes = apply_filters( "builder_filter_theme_settings_{$this->_parent->_active_tab}_meta_boxes", $this->_meta_boxes, $this->_var, 'left' );
			
			$this->_print_meta_box_quick_links();
			
			foreach ( (array) $this->_meta_boxes as $id => $args )
				call_user_func_array( 'add_meta_box', array_merge( array( $id ), array_values( $args ) ) );
			
?>
	<div class="metabox-holder">
		<?php do_meta_boxes( $this->_var, 'meta', $form ); ?>
	</div>
	
	<div class="clear"></div>
<?php
		}
		
		function _init_meta_boxes() {
			echo "<script>jQuery(function() { postboxes.add_postbox_toggles('{$this->_var}', {} ); });</script>\n";
		}
		
		function _show_video( $video_id, $width, $height ) {
			
?>
	<div style="text-align:center;">
		<object width="<?php echo $width; ?>" height="<?php echo $height; ?>"><param name="movie" value="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/<?php echo $video_id; ?>?fs=1&autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<?php echo $width; ?>" height="<?php echo $height; ?>"></embed></object>
	</div>
<?php
			
		}
	}
}
