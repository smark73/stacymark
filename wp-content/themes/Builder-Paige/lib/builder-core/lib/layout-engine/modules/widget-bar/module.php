<?php

/*
Written by Chris Jean for iThemes.com
Version 3.2.1

Version History
	See history.txt
*/



if ( ! class_exists( 'LayoutModuleWidgetBar' ) ) {
	class LayoutModuleWidgetBar extends LayoutModule {
		var $_name = '';
		var $_var = 'widget-bar';
		var $_description = '';
		var $_editor_width = 450;
		var $_has_sidebars = false;
		
		
		function LayoutModuleWidgetBar() {
			$this->_name = _x( 'Widget Bar', 'module', 'it-l10n-Builder-Paige' );
			$this->_description = __( 'This module can contain one to five widget locations that are displayed as columns.', 'it-l10n-Builder-Paige' );
			
			$this->LayoutModule();
		}
		
		function get_layout_option() {
			return 'type';
		}
		
		function _get_defaults( $defaults ) {
			$new_defaults = array(
				'type'                   => '3',
				'widget_percents'        => '33.333,33.333,33.333',
				'custom_widget_percents' => '',
			);
			
			return ITUtility::merge_defaults( $new_defaults, $defaults );
		}
		
		function _validate( $result ) {
			if ( 'custom' === $_POST['widget_percents'] ) {
				$result['data']['custom_widget_percents'] = preg_replace( '/[\s%]+/', '', $_POST['custom_widget_percents'] );
				
				if ( count( explode( ',', $_POST['custom_widget_percents'] ) ) != $_POST['type'] )
					$result['errors'][] = sprintf( __( 'You must supply %s custom widget width percentages separated by commas', 'it-l10n-Builder-Paige' ), $_POST['type'] );
				else if ( preg_match( '/[^0-9,\.]/', $result['data']['custom_widget_percents'] ) ) {
					$result['errors'][] = __( 'Only numbers can be used for custom widget width percentages', 'it-l10n-Builder-Paige' );
				}
				else if ( ( array_sum( explode( ',', $result['data']['custom_widget_percents'] ) ) > 102 ) || ( array_sum( explode( ',', $result['data']['custom_widget_percents'] ) ) < 98 ) )
					$result['errors'][] = __( 'The sum of the custom widget width percentages must equal 100', 'it-l10n-Builder-Paige' );
				else {
					foreach ( (array) explode( ',', $result['data']['custom_widget_percents'] ) as $percent ) {
						if ( $percent < 1 ) {
							$result['errors'][] = __( 'The minimum percentage width for a widget area is 1%. Please ensure that all of your custom widget width percentages are 1% or greater.', 'it-l10n-Builder-Paige' );
							break;
						}
					}
				}
			}
			
			return $result;
		}
		
		function _get_default_percents() {
			$default_percents = array(
				'1' => array(
					'100' => __( '100%', 'it-l10n-Builder-Paige' ),
				),
				'2' => array(
					'50,50'  => __( '50% / 50%', 'it-l10n-Builder-Paige' ),
					'80,20'  => __( '80% / 20%', 'it-l10n-Builder-Paige' ),
					'20,80'  => __( '20% / 80%', 'it-l10n-Builder-Paige' ),
					'70,30'  => __( '70% / 30%', 'it-l10n-Builder-Paige' ),
					'30,70'  => __( '30% / 70%', 'it-l10n-Builder-Paige' ),
					'60,40'  => __( '60% / 40%', 'it-l10n-Builder-Paige' ),
					'40,60'  => __( '40% / 60%', 'it-l10n-Builder-Paige' ),
					'custom' => __( 'Custom...', 'it-l10n-Builder-Paige' ),
				),
				'3' => array(
					'33.333,33.333,33.333' => __( '33% / 33% / 33%', 'it-l10n-Builder-Paige' ),
					'60,20,20'             => __( '60% / 20% / 20%', 'it-l10n-Builder-Paige' ),
					'20,60,20'             => __( '20% / 60% / 20%', 'it-l10n-Builder-Paige' ),
					'20,20,60'             => __( '20% / 20% / 60%', 'it-l10n-Builder-Paige' ),
					'50,25,25'             => __( '50% / 25% / 25%', 'it-l10n-Builder-Paige' ),
					'25,50,25'             => __( '25% / 50% / 25%', 'it-l10n-Builder-Paige' ),
					'25,25,50'             => __( '25% / 25% / 50%', 'it-l10n-Builder-Paige' ),
					'custom'               => __( 'Custom...', 'it-l10n-Builder-Paige' ),
				),
				'4' => array(
					'25,25,25,25' => __( '25% / 25% / 25% / 25%', 'it-l10n-Builder-Paige' ),
					'40,20,20,20' => __( '40% / 20% / 20% / 20%', 'it-l10n-Builder-Paige' ),
					'20,40,20,20' => __( '20% / 40% / 20% / 20%', 'it-l10n-Builder-Paige' ),
					'20,20,40,20' => __( '20% / 20% / 40% / 20%', 'it-l10n-Builder-Paige' ),
					'20,20,20,40' => __( '20% / 20% / 20% / 40%', 'it-l10n-Builder-Paige' ),
					'custom'      => __( 'Custom...', 'it-l10n-Builder-Paige' ),
				),
				'5' => array(
					'20,20,20,20,20' => __( '20% / 20% / 20%/ 20% / 20%', 'it-l10n-Builder-Paige' ),
					'custom'         => __( 'Custom...', 'it-l10n-Builder-Paige' ),
				),
			);
			
			return $default_percents;
		}
		
		function _start_table_edit( $form, $results = true ) {
			$types = array(
				'1' => '1',
				'2' => '2',
				'3' => '3',
				'4' => '4',
				'5' => '5',
			);
			
			if ( empty( $_REQUEST['type'] ) )
				$_REQUEST['type'] = '1';
			
			$default_percents = $this->_get_default_percents();
			
			if ( isset( $default_percents[$_REQUEST['type']] ) )
				$start_widget_percents = $default_percents[$_REQUEST['type']];
			else
				$start_widget_percents = '';
			
?>
	<tr><td><label for="type"><?php _e( 'Columns', 'it-l10n-Builder-Paige' ); ?></label></td>
		<td>
			<?php $form->add_drop_down( 'type', $types ); ?>
			<?php ITUtility::add_tooltip( __( 'Each column becomes a widget area that you can add widgets to.', 'it-l10n-Builder-Paige' ) ); ?>
		</td>
	</tr>
	<tr style="vertical-align:top;"><td><?php _e( 'Column Widths', 'it-l10n-Builder-Paige' ); ?></td>
		<td id="widget_percents_row">
			<?php $form->add_drop_down( 'widget_percents', $start_widget_percents ); ?>
			<?php ITUtility::add_tooltip( __( 'You can make the columns wider or narrower by using these options. To get more control over the widths used, select "Custom..." to put in your own widths.', 'it-l10n-Builder-Paige' ) ); ?>
			
			<div id="widget-percents-custom" style="display:none;">
				<?php $form->add_text_box( 'custom_widget_percents', array( 'size' => '20', 'maxlength' => '30' ) ); ?><br />
				<?php _e( 'Specify the percentage width of each widget area.', 'it-l10n-Builder-Paige' ); ?><br />
				<?php _e( 'For example, if you have three widget areas and want to separate them into 20%, 30%, and 50% widths, input <strong><code>20,30,50</code></strong> in the box above.', 'it-l10n-Builder-Paige' ); ?>
			</div>
		</td>
	</tr>
<?php
		
		}
		
		function _after_table_edit( $form, $results = true ) {
			$default_percents = $this->_get_default_percents();
			
?>
	<div id="percent-width-container" style="display:none;">
		<?php foreach ( (array) $default_percents as $id => $options ) : ?>
			<div id="percent-width-container-<?php echo $id; ?>">
				<?php $form->add_drop_down( "percent-width-$id", $options ); ?>
			</div>
		<?php endforeach; ?>
	</div>
	
	<script type="text/javascript">
		/* <![CDATA[ */
		init_widget_bar_editor();
		/* ]]> */
	</script>
<?php
			
		}
		
		function add_columns_data( $fields ) {
			if ( 'custom' === $fields['data']['widget_percents'] )
				$widths = $fields['data']['custom_widget_percents'];
			else
				$widths = $fields['data']['widget_percents'];
			
			$widths = explode( ',', $widths );
			$widths = builder_get_pixel_widths( $widths, $fields['widths']['container_width'] );
			
			$fields['columns'] = array();
			
			foreach ( $widths as $index => $width ) {
				$num = $index + 1;
				$fields['columns']["#builder-module-{$fields['guid']} .builder-module-column-$num-outer-wrapper"] = $width;
			}
			
			return $fields;
		}
		
		function render_contents( $fields ) {
			$data = $fields['data'];
			
			$data['type'] = (int) $data['type'];
			if ( ( $data['type'] < 1 ) )
				$data['type'] = 1;
			
			
			$columns = array();
			
			for ( $count = 0; $count < $data['type']; $count++ ) {
				$column = $fields;
				
				if ( '1' == $data['type'] )
					$side = 'single';
				else if ( 0 == $count )
					$side = 'left';
				else if ( ( $data['type'] - 1 ) == $count )
					$side = 'right';
				else
					$side = 'middle';
				
				$column['side'] = $side;
				
				$column['sidebar_wrapper'] = array(
					'class' => array(
						'widget-wrapper',
						$side,
						"widget-wrapper-$side",
						'widget-wrapper-' . ( $count + 1 ),
						'clearfix',
					),
				);
				
				$columns[] = $column;
			}
			
			
			$column_order = array_keys( $columns );
			
			
			if ( count( $column_order ) > 1 )
				$column_order = apply_filters( 'builder_module_filter_column_source_order', $column_order, $fields );
			
			
			foreach ( $column_order as $column ) {
				$columns[$column]['num'] = $column + 1;
				
				do_action( 'builder_module_render_sidebar_block', $columns[$column], $columns[$column]['side'] );
			}
		}
		
		function render_sidebar_block( $fields, $side ) {
			$this->_open_sidebar_wrappers( $fields, $side );
			
			do_action( 'builder_module_render_sidebar_block_contents', $fields, $side );
			
			$this->_close_sidebar_wrappers( $fields );
		}
		
		function render_sidebar_block_contents( $fields, $side ) {
			$name = $this->_get_sidebar_name( $fields['layout'], $fields['guid'], $fields['data'] );
			
			$this->_render_sidebar( $fields, "$name - {$fields['num']}", $fields['sidebar_wrapper'] );
		}
		
		function _register_sidebars( $module_data, $id, $layout, $name, $layout_id ) {
			for ( $count = 1; $count <= $module_data['data']['type']; $count++ )
				$this->_register_sidebar( "$name - $count", $module_data['guid'], $layout, $layout_id );
		}
	}
	
	new LayoutModuleWidgetBar();
}
