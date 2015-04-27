<?php

/*
Written by Chris Jean for iThemes.com
Version 5.7.2

Version History
	5.0.0 - 2012-10-05 - Chris Jean
		Added support for responsive rendering mode.
		Added support for full width modules rendering mode.
		Added background wrappers.
		Added id attributes to each modules' wrappers.
		Cleaned up style attribute output.
	5.1.0 - 2012-10-12 - Chris Jean
		Added stylesheet generation code.
		Removed remains of inline styles.
	5.2.0 - 2012-10-17 - Chris Jean
		Added support for theme support options to control responsive margins.
	5.3.0 - 2012-10-18 - Chris Jean
		Added builder_get_layout_width filter.
	5.4.0 - 2012-10-18 - Chris Jean
		Added support for the column-min-width and enable-breakpoints options of builder-responsive.
	5.5.0 - 2012-10-19 - Chris Jean
		Added support for the enable-fluid-images option of builder-responsive.
	5.5.1 - 2012-10-22 - Chris Jean
		Removed "width: auto" for fluid images.
	5.5.2 - 2012-10-25 - Chris Jean
		Fixed issue with proper support of builder-percentage-widths.
	5.5.3 - 2012-12-03 - Chris Jean
		Improved efficiency of the render_modules function.
		Added commented code that can add before and after classes for alternate module styles.
	5.6.0 - 2012-12-14 - Chris Jean
		Removed check for legacy full width modules as that code has moved to layout-selector.php.
		Added styling for fluid audio elements when enable-fluid-images is true.
		Fixed logical issue that prevents builder-full-width-modules from rendering the site properly.
	5.7.0 - 2013-04-22 - Chris Jean
		Added builder-module-style-before-after-classes theme support.
	5.7.1 - 2013-06-24 - Chris Jean
		Added a check to see if the page is currently being output buffered. If it is, then the flush() function will not be called.
	5.7.2 - 2013-10-24 - Chris Jean
		Added a minimum Container width when the theme is not responsive and is using full width modules.
*/


if ( ! class_exists( 'BuilderLayoutEngine' ) ) {
	class BuilderLayoutEngine {
		var $_layout_id = false;
		var $_layout = false;
		var $_modules = array();
		var $_current_module = null;
		var $_current_area_width = null;
		var $_legacy_theme_template = false;
		
		
		function BuilderLayoutEngine() {
			$this->_modules = apply_filters( 'builder_get_modules', array() );
			
			
			add_action( 'get_header', array( $this, 'identify_legacy_theme_template' ), -9999 );
			
			add_action( 'builder_layout_engine_identified_layout', array( $this, 'layout_identified' ), 0, 2 );
			
			add_action( 'builder_layout_engine_render_layout', array( $this, 'render' ), 10, 3 );
			
			add_action( 'builder_layout_engine_render_header', 'get_header', 10, 0 );
			add_action( 'builder_layout_engine_render_header', array( $this, 'print_body_tag' ), 16 );
			
			add_action( 'builder_layout_engine_render_container', array( $this, 'render_container' ) );
			add_action( 'builder_layout_engine_render_container_contents', array( $this, 'render_container_contents' ) );
			
			add_action( 'builder_layout_engine_render_modules', array( $this, 'render_modules' ) );
			add_action( 'builder_module_render', array( $this, 'render_module' ) );
			add_action( 'builder_module_render_contents', array( $this, 'render_module_contents' ) );
			add_action( 'builder_module_render_element_block', array( $this, 'render_module_element_block' ) );
			add_action( 'builder_module_render_sidebar_block', array( $this, 'render_module_sidebar_block' ), 10, 2 );
			add_action( 'builder_module_render_sidebar_block_contents', array( $this, 'render_module_sidebar_block_contents' ), 10, 2 );
			
			add_action( 'builder_finish', array( $this, 'render_finish' ) );
			
			add_action( 'builder_layout_engine_set_current_module', array( $this, 'set_current_module' ) );
			add_action( 'builder_layout_engine_set_current_area_width', array( $this, 'set_current_area_width' ) );
			
			
			add_filter( 'builder_layout_engine_get_current_module', array( $this, 'get_current_module' ) );
			add_filter( 'builder_layout_engine_get_current_area_width', array( $this, 'get_current_area_width' ) );
			
			add_filter( 'builder_get_layout_width', array( $this, 'get_container_width' ), 0 );
			add_filter( 'builder_get_container_width', array( $this, 'get_container_width' ), 0 );
			
			add_filter( 'builder_get_layout_style_rules', array( $this, 'get_style' ), 0, 3 );
		}
		
		function identify_legacy_theme_template() {
			if ( did_action( 'builder_layout_engine_render' ) )
				return;
			
			
			$this->_legacy_theme_template_file = '';
			$backtrace = debug_backtrace();
			
			if ( isset( $backtrace[3] ) && isset( $backtrace[3]['file'] ) )
				$this->_legacy_theme_template_file = $backtrace[3]['file'];
			
			$this->_legacy_theme_template = true;
			ob_start();
			
			add_action( 'get_footer', array( $this, 'render_legacy_theme_template' ), -9999 );
		}
		
		function render_legacy_theme_template() {
			if ( isset( $this->_legacy_theme_template_content ) )
				return;
			
			$content = ob_get_contents();
			ob_end_clean();
			
			
			list( $head, $content ) = preg_split( '|<\s*/\s*head\s*>|i', $content, 2 );
			
			$this->_legacy_theme_template_head = "$head</head>\n";
			$this->_legacy_theme_template_content = $content;
			
			
			remove_action( 'get_header', array( $this, 'identify_legacy_theme_template' ) );
			remove_action( 'builder_layout_engine_render_header', 'get_header' );
			remove_action( 'get_footer', array( $this, 'render_legacy_theme_template' ) );
			
			add_action( 'builder_layout_engine_render_header', array( $this, 'render_legacy_theme_template_head' ) );
			add_action( 'builder_layout_engine_render_content', array( $this, 'render_legacy_theme_template_content' ) );
			
			do_action( 'builder_layout_engine_render', basename( $this->_legacy_theme_template_file ) );
			
			exit;
		}
		
		function render_legacy_theme_template_head() {
			echo $this->_legacy_theme_template_head;
		}
		
		function render_legacy_theme_template_content() {
			echo $this->_legacy_theme_template_content;
		}
		
		function layout_identified( $layout_id, $layout_settings ) {
			$this->_layout_id = $layout_id;
			$this->_layout =& $layout_settings['layouts'][$layout_id];
		}
		
		function set_current_module( $module ) {
			$this->_current_module = $module;
		}
		
		function get_current_module( $module ) {
			return $this->_current_module;
		}
		
		function set_current_area_width( $width ) {
			$this->_current_area_width = $width;
		}
		
		function get_current_area_width( $width ) {
			return $this->_current_area_width;
		}
		
		function get_style( $stylesheet = '', $layout_id = '', $layout = array() ) {
			if ( ! empty( $layout_id ) )
				$this->layout_identified( $layout_id, $layout );
			
			
			$layout_width = apply_filters( 'builder_get_container_width', $this->_layout['width'] );
			
			
			if ( builder_theme_supports( 'builder-responsive', 'enable-fluid-images' ) ) {
				$stylesheet .= "img, video, .wp-caption {\n";
				$stylesheet .= "\t-moz-box-sizing: border-box;\n";
				$stylesheet .= "\t-webkit-box-sizing: border-box;\n";
				$stylesheet .= "\tbox-sizing: border-box;\n";
				$stylesheet .= "\tmax-width: 100%;\n";
				$stylesheet .= "\theight: auto !important;\n";
				$stylesheet .= "}\n";
				
				$stylesheet .= "audio {\n";
				$stylesheet .= "\tmax-width: 100%;\n";
				$stylesheet .= "}\n";
			}
			
			$stylesheet .= ".builder-container-outer-wrapper {\n";
			
			if ( ! builder_theme_supports( 'builder-full-width-modules' ) )
				$stylesheet .= "\tmax-width: {$layout_width}px;\n";
			
			if ( builder_theme_supports( 'builder-responsive' ) ) {
				$stylesheet .= "\twidth: 100%;\n";
			}
			else if ( builder_theme_supports( 'builder-full-width-modules' ) ) {
				$stylesheet .= "\twidth: 100%;\n";
				$stylesheet .= "\tmin-width: {$layout_width}px;\n";
			}
			else {
				$stylesheet .= "\twidth: {$layout_width}px;\n";
			}
			
			$stylesheet .= "}\n";
			
			if ( builder_theme_supports( 'builder-full-width-modules' ) ) {
				$stylesheet .= "#ie6 .builder-module-outer-wrapper,\n";
				$stylesheet .= "#ie7 .builder-module-outer-wrapper,\n";
				$stylesheet .= "#ie8 .builder-module-outer-wrapper {\n";
				$stylesheet .= "\twidth: {$layout_width}px;\n";
				$stylesheet .= "}\n";
			}
			else {
				$stylesheet .= "#ie6 .builder-container-outer-wrapper,\n";
				$stylesheet .= "#ie7 .builder-container-outer-wrapper,\n";
				$stylesheet .= "#ie8 .builder-container-outer-wrapper {\n";
				$stylesheet .= "\twidth: {$layout_width}px;\n";
				$stylesheet .= "}\n";
			}
			
			
			if ( builder_theme_supports( 'builder-responsive', 'enable-auto-margins', true ) ) {
				$tablet_margin = builder_theme_supports( 'builder-responsive', 'tablet-auto-margin' );
				$mobile_margin = builder_theme_supports( 'builder-responsive', 'mobile-auto-margin' );
				
				$tablet_width = builder_theme_supports( 'builder-responsive', 'tablet-width' );
				$mobile_width = builder_theme_supports( 'builder-responsive', 'mobile-width' );
				
				if ( 'layout-width' == $tablet_width )
					$tablet_width = $layout_width . 'px';
				if ( 'layout-width' == $mobile_width )
					$mobile_width = $layout_width . 'px';
				
				if ( builder_theme_supports( 'builder-full-width-modules' ) ) {
					$stylesheet .= "@media screen and (max-width: $tablet_width) {\n";
					$stylesheet .= "\t.builder-module-background-wrapper {\n";
					$stylesheet .= "\t\tpadding-left: $tablet_margin;\n";
					$stylesheet .= "\t\tpadding-right: $tablet_margin;\n";
					$stylesheet .= "\t}\n";
					$stylesheet .= "}\n";
					
					$stylesheet .= "@media screen and (max-width: $mobile_width) {\n";
					$stylesheet .= "\t.builder-module-background-wrapper {\n";
					$stylesheet .= "\t\tpadding-left: $mobile_margin;\n";
					$stylesheet .= "\t\tpadding-right: $mobile_margin;\n";
					$stylesheet .= "\t}\n";
					$stylesheet .= "}\n";
				}
				else {
					$stylesheet .= "@media screen and (max-width: $tablet_width) {\n";
					$stylesheet .= "\t.builder-container {\n";
					$stylesheet .= "\t\tmargin: 0 $tablet_margin;\n";
					$stylesheet .= "\t}\n";
					$stylesheet .= "}\n";
					
					$stylesheet .= "@media screen and (max-width: $mobile_width) {\n";
					$stylesheet .= "\t.builder-container {\n";
					$stylesheet .= "\t\tmargin: 0 $mobile_margin;\n";
					$stylesheet .= "\t}\n";
					$stylesheet .= "}\n";
				}
			}
			
			
			foreach ( $this->_layout['modules'] as $fields ) {
				$fields = apply_filters( 'builder_module_filter_render_fields', $fields );
				
				if ( ! isset( $this->_modules[$fields['module']] ) || ! is_callable( array( $this->_modules[$fields['module']], '_get_style' ) ) )
					continue;
				
				$stylesheet .= call_user_func( array( $this->_modules[$fields['module']], '_get_style' ), $fields );
			}
			
			return $stylesheet;
		}
		
		function render( $view, $layout_id, $layout ) {
			$class_prefix = apply_filters( 'builder_module_filter_css_prefix', '' );
			
			$this->_layout_id = $layout_id;
			$this->_layout =& $layout;
			
			$render_data = array(
				'layout_id'    => $layout_id,
				'layout'       => $layout['description'],
				'class_prefix' => $class_prefix,
			);
			
			do_action( 'builder_layout_engine_render_header', $render_data );
			do_action( 'builder_layout_engine_render_container', $render_data );
			do_action( 'builder_finish', $render_data );
		}
		
		function render_finish() {
			echo "\n</body>\n</html>";
		}
		
		function render_container( $render_data ) {
			$this->_render_container_start();
			
			do_action( 'builder_layout_engine_render_container_contents', $render_data );
			
			$this->_render_container_end();
		}
		
		function render_container_contents( $render_data ) {
			do_action( 'builder_layout_engine_render_modules', $render_data );
		}
		
		function render_modules( $render_data ) {
			$class_prefix = $render_data['class_prefix'];
			
			
			$modules = array();
			$module_positions = array();
			$module_counts = array();
			$module_count = 0;
			$footer_position = 0;
			
			foreach ( (array) $this->_layout['modules'] as $module ) {
				if ( ! isset( $this->_modules[$module['module']] ) || ! method_exists( $this->_modules[$module['module']], 'render' ) )
					continue;
				
				if ( empty( $module['data']['style'] ) )
					$module['data']['style'] = 'default-module-style';
				
				$modules[] = array_merge( $module, $render_data );
				
				
				$module_count++;
				
				if ( 'footer' === $module['module'] )
					$footer_position = $module_count;
				
				if ( ! isset( $module_counts[$module['module']] ) )
					$module_counts[$module['module']] = 0;
				$module_counts[$module['module']]++;
				
				$module_positions[$module_count] = $module['module'];
			}
			
			
			$module_use_count = array();
			$module_count = 0;
			
			$id = 1;
			$last_id = count( $modules );
			
			if ( builder_theme_supports( 'builder-module-style-before-after-classes' ) )
				$module_style_before_after_classes = true;
			else
				$module_style_before_after_classes = false;
			
			
			foreach ( $modules as $module_index => $module ) {
				if ( ! isset( $module_use_count[$module['module']] ) )
					$module_use_count[$module['module']] = 0;
				$module_use_count[$module['module']]++;
				
				$module_count++;
				
				if ( 1 === $last_id )
					$module_location = 'single';
				else if ( $module_count === $last_id )
					$module_location = 'bottom';
				else if ( 1 === $module_count )
					$module_location = 'top';
				else
					$module_location = 'middle';
				
				
				$module['id'] = $id;
				
				
				$module['inner_wrapper']['class'] = array(
					$class_prefix,
					"$class_prefix-{$module['module']}",
					"$class_prefix-$id",
					"$class_prefix-{$module['module']}-{$module_use_count[$module['module']]}",
					"$class_prefix-$module_location",
				);
				
				if ( $id === $last_id )
					$module['inner_wrapper']['class'][] = "$class_prefix-last";
				
				if ( $module_use_count[$module['module']] === $module_counts[$module['module']] )
					$module['inner_wrapper']['class'][] = "$class_prefix-{$module['module']}-last";
				
				if ( isset( $modules[$module_index + 1] ) ) {
					$module['inner_wrapper']['class'][] = "$class_prefix-before-{$modules[$module_index + 1]['module']}";
					
					if ( $module_style_before_after_classes )
						$module['inner_wrapper']['class'][] = "$class_prefix-before-{$modules[$module_index + 1]['data']['style']}";
				}
				if ( ( $id === $last_id ) && ( 0 === $footer_position ) ) {
					$module['inner_wrapper']['class'][] = "$class_prefix-before-footer";
				}
				if ( $module_index > 0 ) {
					$module['inner_wrapper']['class'][] = "$class_prefix-after-{$modules[$module_index - 1]['module']}";
					
					if ( $module_style_before_after_classes )
						$module['inner_wrapper']['class'][] = "$class_prefix-after-{$modules[$module_index - 1]['data']['style']}";
				}
				
				$module['inner_wrapper']['class'][] = $module['data']['style'];
				
				
				foreach ( $module['inner_wrapper']['class'] as $class ) {
					$module['background_wrapper']['class'][] = "$class-background-wrapper";
					$module['outer_wrapper']['class'][] = "$class-outer-wrapper";
				}
				
				
				$module['inner_wrapper']['class'][] = 'clearfix';
				
				$module['background_wrapper']['id'] = "$class_prefix-{$module['guid']}-background-wrapper";
				$module['outer_wrapper']['id'] = "$class_prefix-{$module['guid']}-outer-wrapper";
				$module['inner_wrapper']['id'] = "$class_prefix-{$module['guid']}";
				
				
				do_action( 'builder_module_render', $module );
				
				$id++;
			}
			
			
			if ( 0 === $footer_position ) {
				do_action( 'get_footer' );
				do_action( 'wp_footer' );
			}
		}
		
		function render_module( $fields ) {
			do_action( 'builder_layout_engine_set_current_module', $fields['module'] );
			
			do_action( "builder_module_render_{$fields['module']}", $fields );
			
			do_action( 'builder_layout_engine_set_current_module', null );
		}
		
		function render_module_contents( $fields ) {
			do_action( "builder_module_render_contents_{$fields['module']}", $fields );
		}
		
		function render_module_element_block( $fields ) {
			do_action( "builder_module_render_element_block_{$fields['module']}", $fields );
		}
		
		function render_module_sidebar_block( $fields, $side ) {
			do_action( "builder_module_render_sidebar_block_{$fields['module']}", $fields, $side );
		}
		
		function render_module_sidebar_block_contents( $fields, $side ) {
			do_action( "builder_module_render_sidebar_block_contents_{$fields['module']}", $fields, $side );
		}
		
		function print_body_tag( $render_data ) {
			// Figure out if output buffering is enabled or not.
			// The flush should be bypassed if the page is being output buffered.
			$output_buffering_level = ob_get_level();
			$is_buffered = ( $output_buffering_level > 1 ) || ( ! @ini_get( 'output_buffering' ) && ( $output_buffering_level > 0 ) );
			
			if ( ! $is_buffered && builder_theme_supports( 'builder-header-flush' ) && ( ! defined( 'BUILDER_DISABLE_FLUSH' ) || ( false === BUILDER_DISABLE_FLUSH ) ) )
				flush();
			
			$attributes = array(
				'id' => array(
					"builder-layout-{$this->_layout['guid']}",
				),
			);
			
			if ( builder_theme_supports( 'builder-responsive' ) )
				$class = 'builder-responsive';
			else
				$class = 'builder-static';
			
			$attributes['class'] = get_body_class( $class );
			
			$attributes = apply_filters( 'builder_filter_body_attributes', $attributes );
			
			ITUtility::print_open_tag( 'body', $attributes );
		}
		
		function get_container_width( $width ) {
			if ( 'custom' === $this->_layout['width'] )
				$width = intval( $this->_layout['custom_width'] );
			else
				$width = intval( $this->_layout['width'] );
			
			return $width;
		}
		
		function _render_container_start() {
			$background_wrapper_attributes = array(
				'class' => array(
					'builder-container-background-wrapper',
				),
			);
			
			$background_wrapper_attributes = apply_filters( 'builder_filter_container_background_wrapper_attributes', $background_wrapper_attributes );
			
			
			$width = apply_filters( 'builder_get_container_width', 0 );
			
			$outer_wrapper_attributes = array(
				'class' => array(
					'builder-container-outer-wrapper',
				),
				'style' => array(),
			);
			
			$outer_wrapper_attributes = apply_filters( 'builder_filter_container_outer_wrapper_attributes', $outer_wrapper_attributes );
			
			if ( empty( $outer_wrapper_attributes['style'] ) )
				unset( $outer_wrapper_attributes['style'] );
			
			
			$inner_wrapper_attributes = array(
				'class' => array( 'builder-container' ),
				'id'    => "builder-container-{$this->_layout['guid']}"
			);
			
			$inner_wrapper_attributes = apply_filters( 'builder_filter_container_inner_wrapper_attributes', $inner_wrapper_attributes );
			
			
			ITUtility::print_open_tag( 'div', $background_wrapper_attributes );
			ITUtility::print_open_tag( 'div', $outer_wrapper_attributes );
			ITUtility::print_open_tag( 'div', $inner_wrapper_attributes );
		}
		
		function _render_container_end() {
			echo "\n</div>\n</div>\n</div>\n\n";
		}
	}
	
	new BuilderLayoutEngine();
}
