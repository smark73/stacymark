<?php

/*
Written by Chris Jean for iThemes.com
Version 1.4.3

Version History
	1.4.0 - 2013-01-09 - Chris Jean
		Removed class_exists check.
		Updated the menu registration code to make use of the menu_order filters.
	1.4.1 - 2013-01-10 - Chris Jean
		Fixed bug in new menu_order filtering code.
	1.4.2 - 2014-01-28 - Chris Jean
		Fixed unset index warning.
	1.4.3 - 2013-05-21 - Chris Jean
		Removed assign by reference.
*/


if ( ! empty( $_GET['it_start_here_height_callback'] ) ) {
	require_once( dirname( __FILE__ ) . '/iframe-resize-helper.html' );
	
	die;
}

class iThemesTutorials {
	var $_var = 'ithemes-tutorials';
	var $_name = 'iThemes Tutorials';
	var $_version = '1.1.7';
	var $_page = 'ithemes-tutorials';
	
	var $_plugin_url = '';
	var $_page_ref = '';
	
	
	function iThemesTutorials() {
		global $wp_theme_page_name;
		
		if ( ! empty( $wp_theme_page_name ) )
			$this->_page = $wp_theme_page_name;
		
		
		$this->_setVars();
		
		add_action( 'admin_menu', array( $this, 'add_pages' ), -10 );
		add_action( 'admin_init', array( $this, 'init' ) );
	}
	
	function init() {
		global $wp_version;
		
		
		if ( ! isset( $_REQUEST['page'] ) || ( $_REQUEST['page'] != $this->_page ) )
			return;
		
		if ( version_compare( $wp_version, '3.2.5', '>' ) )
			add_action( 'admin_head', array( $this, 'add_screen_meta' ) );
		else
			add_filter( 'contextual_help', array( $this, 'contextual_help' ), 10, 2 );
	}
	
	function add_screen_meta() {
		$help = $this->contextual_help( '' );
		
		if ( ! empty( $help ) && is_string( $help ) ) {
			$tab = array(
				'id'      => 'screen-info',
				'title'   => __( 'Screen Info' ),
				'content' => $help,
			);
			
			get_current_screen()->add_help_tab( $tab );
		}
		
		builder_set_help_sidebar();
	}
	
	function contextual_help( $text ) {
		ob_start();
		
?>
<p><?php _e( 'Welcome to Builder. Throughout the theme\'s back-end, you can use this help pulldown to get additional information about the page you are looking at.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php _e( 'You may also see question mark icons next to some of the options or descriptions. If you hover over one of these icons, you will get additional information about that specific option.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php _e( 'The videos on this page help get you up and running quickly. At the bottom of the page are some important links such as access to the iThemes support forum and member area.', 'it-l10n-Builder-Paige' ); ?></p>
<p><?php printf( __( 'After getting a feel for Builder from the videos, check out the <a href="%s">Layouts and Views</a> editor where you can start making modifications to your site to make it the site right for you.', 'it-l10n-Builder-Paige' ), admin_url( 'admin.php?page=layout-editor' ) ); ?></p>
<?php
		
		$content = ob_get_contents();
		ob_end_clean();
		
		if ( ! empty( $content ) )
			return $content;
		return $text;
	}
	
	function add_pages() {
		$GLOBALS['wp_theme_name'] = apply_filters( 'it_tutorials_top_menu_name', 'My Theme' );
		$menu_icon = apply_filters( 'it_tutorials_top_menu_icon', '' );
		
		global $wp_theme_name, $wp_theme_page_name, $wp_version;
		
		$tutorial_menu_name = __( 'Start Here', 'it-l10n-Builder-Paige' );
		$tutorial_menu_name = apply_filters( 'it_tutorials_menu_name', $tutorial_menu_name );
		
		$menu_capability = apply_filters( 'it_builder_menu_capability', 'switch_themes' );
		
		if ( ! empty( $wp_theme_page_name ) ) {
			$this->_page_ref = add_menu_page( $tutorial_menu_name, $wp_theme_name, $menu_capability, $this->_page, array( $this, 'index' ), $menu_icon );
			add_submenu_page( $this->_page, $tutorial_menu_name, $tutorial_menu_name, $menu_capability, $this->_page, array( $this, 'index' ) );
			
			if ( version_compare( $GLOBALS['wp_version'], '2.7.9', '>' ) ) {
				add_filter( 'custom_menu_order', array( $this, 'return_true' ) );
				add_filter( 'menu_order', array( $this, 'filter_menu_order' ) );
				
				
				$max_index = 0;
				
				foreach ( $GLOBALS['menu'] as $entry ) {
					if ( 0 !== strpos( $entry[2], 'separator' ) )
						continue;
					
					$index = substr( $entry[2], 9 );
					
					if ( is_numeric( $index ) && ( $index > $max_index ) )
						$max_index = $index;
				}
				
				$max_index++;
				$this->_separator_index = "separator$max_index";
				
				$GLOBALS['menu'][] = array( '', 'read', $this->_separator_index, '', 'wp-menu-separator' );
			}
		}
		else {
			$this->_page_ref = add_theme_page( sprintf( __( '%s Start Here', 'it-l10n-Builder-Paige' ), $wp_theme_name ), sprintf( __( '%s Start Here', 'it-l10n-Builder-Paige' ), $wp_theme_name ), $menu_capability, $this->_page, array( $this, 'index' ) );
		}
		
		
		add_action( 'admin_print_scripts-' . $this->_page_ref, array( $this, 'add_scripts' ) );
		add_action( 'admin_print_styles-' . $this->_page_ref, array( $this, 'add_styles' ) );
	}
	
	function return_true() {
		return true;
	}
	
	function filter_menu_order( $menu_order ) {
		$menu_order = array_reverse( array_reverse( $menu_order ) );
		
		$new_menu_order;
		
		foreach ( $menu_order as $index => $item ) {
			if ( isset( $menu_order[$index + 1] ) && ( 'themes.php' == $menu_order[$index + 1] ) ) {
				if ( 0 === strpos( $item, 'separator' ) ) {
					$new_menu_order[] = $this->_separator_index;
					$new_menu_order[] = $this->_page;
					$new_menu_order[] = $item;
				}
				else {
					$new_menu_order[] = $item;
					$new_menu_order[] = $this->_separator_index;
					$new_menu_order[] = $this->_page;
				}
			}
			else if ( ! in_array( $item, array( $this->_page, $this->_separator_index ) ) ) {
				$new_menu_order[] = $item;
			}
		}
		
		return $new_menu_order;
	}
	
	function add_scripts() {
		wp_enqueue_script( $this->_var . '-tutorials', $this->_plugin_url . '/js/tutorials.js' );
	}
	
	function add_styles() {
		wp_enqueue_style( $this->_var . '-tutorials', $this->_plugin_url . '/css/tutorials.css' );
	}
	
	function _setVars() {
		$this->_plugin_url = ITUtility::get_url_from_file( dirname( __FILE__ ) );
	}
	
	
	// Pages //////////////////////////////////////
	
	function index() {
		$filter_url = 'http://ithemes.com/tv/index.html';
		$filter_url = apply_filters( 'it_tutorials_filter_url', $filter_url );
		
?>
	<div class="wrap">
		<div id="start_here_frame_container">
			<iframe id="start_here_frame" src="<?php echo $filter_url ?>" frameborder="0" allowtransparency="true"></iframe>
		</div>
	</div>
<?php
		
	}
}

$GLOBALS['ithemes_tutorials'] = new iThemesTutorials();
