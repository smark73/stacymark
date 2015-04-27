<?php

/*
Code to handle exporting and importing of layouts and views data

Written by Chris Jean for iThemes.com
Version 1.1.1

Version History
	1.0.0 - 2011-07-01 - Chris Jean
		Added a force-upgrade path for layout settings
	1.0.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.0.2 - 2013-06-27 - Chris Jean
		Added more robust handling of Views that don't exist.
	1.1.0 - 2013-08-15 - Chris Jean
		Updated run_import() and associated functions to handle simplified requests and to allow for handling the importation of Layouts and Views differently.
	1.1.1 - 2013-10-21 - Chris Jean
		Fixed issue where imported Views were not being handled if they were set to replace.
*/


if ( ! class_exists( 'BuilderDataSourceLayoutsViews' ) ) {
	class BuilderDataSourceLayoutsViews extends BuilderDataSource {
		function get_name() {
			return 'Layouts and Views';
		}
		
		function get_var() {
			return 'layouts-views';
		}
		
		function get_version() {
			return builder_get_data_version( 'layout-settings' );
		}
		
		function get_layout_settings() {
			$storage = new ITStorage( 'layout_settings' );
			$layout_settings = $storage->load();
			
			if ( ! is_array( $layout_settings ) )
				$layout_settings = array();
			if ( ! isset( $layout_settings['layouts'] ) )
				$layout_settings['layouts'] = array();
			if ( ! isset( $layout_settings['views'] ) )
				$layout_settings['views'] = array();
			
			return $layout_settings;
		}
		
		function get_export_data() {
			$layout_settings = $this->get_layout_settings();
			
			$meta = array();
			
			
			$modules = apply_filters( 'builder_get_modules', array() );
			
			foreach ( (array) $layout_settings['layouts'] as $layout_id => $layout ) {
				foreach ( (array) $layout['modules'] as $module_id => $module ) {
					if ( isset( $modules[$module['module']] ) && method_exists( $modules[$module['module']], 'export' ) )
						$layout_settings['layouts'][$layout_id]['modules'][$module_id]['data'] = $modules[$module['module']]->export( $module['data'] );
				}
			}
			
			
			foreach ( (array) $layout_settings['views'] as $view_id => $guid ) {
				if ( false === strpos( $view_id, '__' ) )
					continue;
				
				list( $function, $id ) = split( '__', $view_id );
				
				if ( 'is_category' === $function )
					$meta['views'][$view_id] = get_category( $id );
				else if ( 'is_tag' === $function )
					$meta['views'][$view_id] = get_term( $id, 'post_tag' );
				else if ( 'is_author' === $function ) {
					$user_data = get_userdata( $id );
					
					$user = array(
						'ID'            => $user_data->ID,
						'user_login'    => $user_data->user_login,
						'user_nicename' => $user_data->user_nicename,
						'user_email'    => $user_data->user_email,
						'nickname'      => $user_data->nickname,
						'display_name'  => $user_data->display_name,
					);
					
					$meta['views'][$view_id] = $user;
				}
			}
			
			
			$data = array(
				'data'	=> $layout_settings,
				'meta'	=> $meta,
			);
			
			return $data;
		}
		
		function show_import_methods_form( $form, $info ) {
			$name = $this->get_name();
			
?>
	<p><label><?php $form->add_radio( 'method', 'add' ); ?> <?php _e( '<strong>Add Layouts:</strong> Add the Layouts from this export to this site\'s existing Layouts. The Views in this export will not be imported to this site.', 'it-l10n-Builder-Madison' ); ?></label></p>
	<p><label><?php $form->add_radio( 'method', 'custom' ); ?> <?php printf( __( '<strong>Custom:</strong> Selectively choose how to import the %s.', 'it-l10n-Builder-Madison' ), $name ); ?></label></p>
	<p><label><?php $form->add_radio( 'method', 'replace' ); ?> <?php printf( __( '<strong>Replace:</strong> Delete the %1$s from this site and replace with the %1$s in the export file.', 'it-l10n-Builder-Madison' ), $name ); ?></label></p>
	<p><label><?php $form->add_radio( 'method', 'skip' ); ?> <?php printf( __( '<strong>Skip:</strong> Do not import %1$s from the export file. The site\'s %1$s will remain unchanged.', 'it-l10n-Builder-Madison' ), $name ); ?></label></p>
<?php
			
		}
		
		function show_import_customizations_form( $form, $info, $data, $post_data ) {
			if ( 'custom' !== $post_data['method'] )
				return;
			
			
			$meta = $data['meta'];
			$data = $data['data'];
			
			$data['layouts'] = ITUtility::sort_array( $data['layouts'], 'description' );
			
			$layout_settings = $this->get_layout_settings();
			
			$layout_settings['layouts'] = ITUtility::sort_array( $layout_settings['layouts'], 'description' );
			
			
			echo '<h4>' . __( 'Select Layouts from the Import File to Add to Site', 'it-l10n-Builder-Madison' ) . "</h4>\n";
			echo "<div style='margin-left:20px;'>\n";
			echo '<p>' . __( 'Checking a layout below will add that layout from the import file to the site.', 'it-l10n-Builder-Madison' ) . "</p>\n";
			
			$form->add_input_group( 'keep_import_guids' );
			
			foreach ( (array) $data['layouts'] as $guid => $import_data ) {
				
?>
	<p><label><?php $form->add_check_box( $guid ); ?> <?php echo $import_data['description']; ?></label></p>
<?php
				
			}
			
			$form->remove_input_group();
			
			echo "</div>\n";
			
			
			echo '<h4>' . __( 'Select Layouts from the Site to Keep', 'it-l10n-Builder-Madison' ) . "</h4>\n";
			echo "<div style='margin-left:20px;'>\n";
			echo '<p>' . __( 'Unchecking one of the following layouts will remove it from the site.', 'it-l10n-Builder-Madison' ) . "</p>\n";
			
			$form->add_input_group( 'keep_site_guids' );
			$form->push_options();
			
			foreach ( (array) $layout_settings['layouts'] as $guid => $import_data ) {
				$form->set_option( $guid, 1 );
				
?>
	<p><label><?php $form->add_check_box( $guid ); ?> <?php echo $import_data['description']; ?></label></p>
<?php
				
			}
			
			$form->pop_options();
			$form->remove_input_group();
			
			echo "</div>\n";
			
			
			echo '<h4>' . __( 'Select Views from the Import File to Add to Site', 'it-l10n-Builder-Madison' ) . "</h4>\n";
			echo "<div style='margin-left:20px;'>\n";
			echo '<p>' . __( 'Checking a view below will add that view from the import file to the site.', 'it-l10n-Builder-Madison' ) . "</p>\n";
			
			$form->add_input_group( 'keep_import_views' );
			
			$descriptions = array();
			foreach ( array_keys( (array) $data['views'] ) as $id )
				$descriptions[$id] = $this->_get_view_description( $id, $data, $meta );
			
			$descriptions = ITUtility::sort_array( $descriptions, 'description' );
			
			foreach ( (array) $descriptions as $id => $description ) {
				
?>
	<?php if ( is_wp_error( $description ) ) : ?>
	<?php elseif ( false === $description['failed'] ) : ?>
		<?php $description = sprintf( __( '<strong>%1$s</strong> uses the export file\'s &quot;%2$s&quot; layout', 'it-l10n-Builder-Madison' ), $description['description'], $description['layout'] ); ?>
		<p><label><?php $form->add_check_box( $id ); ?> <?php echo $description; ?></label></p>
	<?php else : ?>
		<p><?php $form->add_check_box( $id, array( 'disabled' => 'disabled' ) ); ?> <?php printf( __( '<span style="color:red;">%s</span> &mdash; This view does not exist on the site.', 'it-l10n-Builder-Madison' ), $description['description'] ); ?></p>
	<?php endif; ?>
<?php
				
			}
			
			$form->remove_input_group();
			
			echo "</div>\n";
			
			
			echo '<h4>' . __( 'Select Views from the Site to Keep', 'it-l10n-Builder-Madison' ) . "</h4>\n";
			echo "<div style='margin-left:20px;'>\n";
			echo '<p>' . __( 'Unchecking one of the following views will remove it from the site.', 'it-l10n-Builder-Madison' ) . "</p>\n";
			
			$form->add_input_group( 'keep_site_views' );
			$form->push_options();
			
			$descriptions = array();
			foreach ( array_keys( (array) $data['views'] ) as $id )
				$descriptions[$id] = $this->_get_view_description( $id, $data, $meta );
			
			$descriptions = ITUtility::sort_array( $descriptions, 'description' );
			
			foreach ( (array) $descriptions as $id => $description ) {
				
?>
	<?php if ( ! is_wp_error( $description ) ) : ?>
		<?php $description = sprintf( __( '<strong>%1$s</strong> uses the site\'s &quot;%2$s&quot; layout', 'it-l10n-Builder-Madison' ), $description['description'], $description['layout'] ); ?>
		<?php $form->set_option( $id, 1 ); ?>
		<p><label><?php $form->add_check_box( $id ); ?> <?php echo $description; ?></label></p>
	<?php endif; ?>
<?php
				
			}
			
			$form->pop_options();
			$form->remove_input_group();
			
			echo "</div>\n";
		}
		
		function _get_view_description( $id, $data, $meta = null ) {
			$description = '';
			$failed = false;
			
			
			if ( ! isset( $this->_available_views ) )
				$this->_available_views = apply_filters( 'builder_get_available_views', array() );
			
			$layout = $data['layouts'][$data['views'][$id]['layout']]['description'];
			
			
			if ( isset( $this->_available_views[$id]['name'] ) )
				return array( 'description' => $this->_available_views[$id]['name'], 'layout' => $layout, 'failed' => false );
			
			
			if ( false !== strpos( $id, '|' ) ) {
				list( $function, $arg ) = explode( '|', $id );
				
				if ( 'builder_is_custom_post_type' == $function )
					$description = sprintf( __( 'Custom post type: %s', 'it-l10n-Builder-Madison' ), $arg );
				else if ( 'builder_is_custom_taxonomy' == $function )
					$description = sprintf( __( 'Custom taxonomy: %s', 'it-l10n-Builder-Madison' ), $arg );
				
				$failed = true;
			}
			
			if ( ! $failed && ( false !== strpos( $id, '__' ) ) )
				list( $function, $term_id ) = explode( '__', $id );
			
			if ( ! empty( $function ) && isset( $this->_available_views[$function]['name'] ) ) {
				$name = $this->_available_views[$function]['name'];
				
				if ( 'is_category' === $function ) {
					if ( is_null( $meta ) )
						$category = get_category( $term_id );
					else {
						$site_category_id = $this->_update_import_view_id( $id, $meta, true );
						
						if ( false !== $site_category_id )
							$category = get_category( $site_category_id );
						
						if ( empty( $category ) ) {
							$category =& $meta['views'][$id];
							$failed = true;
						}
					}
					
					if ( ! empty( $category->name ) )
						$description = "$name - {$category->name}";
				}
				else if ( 'is_tag' === $function ) {
					if ( is_null( $meta ) )
						$tag = get_term( $term_id, 'post_tag' );
					else {
						$site_tag_id = $this->_update_import_view_id( $id, $meta, true );
						
						if ( false !== $site_tag_id )
							$tag = get_term( $site_tag_id, 'post_tag' );
						
						if ( empty( $tag ) ) {
							$tag =& $meta['views'][$id];
							$failed = true;
						}
					}
					
					if ( ! empty( $tag->name ) )
						$description = "$name - {$tag->name}";
				}
				else if ( 'is_author' === $function ) {
					if ( is_null( $meta ) )
						$author = get_userdata( $term_id );
					else {
						$site_author_id = $this->_update_import_view_id( $id, $meta, true );
						
						if ( false !== $site_author_id )
							$author = get_userdata( $site_author_id );
						
						if ( ! $author )
							$author = (object) $meta['views'][$id];
					}
					
					if ( ! empty( $author->display_name ) )
						$description = "$name - {$author->display_name} ({$author->user_login})";
				}
			}
			
			if ( empty( $description ) ) {
				$description = $id;
				$failed = true;
			}
			
			return array( 'description' => $description, 'layout' => $layout, 'failed' => $failed );
		}
		
		function _update_import_view_id( $id, $meta, $return_term_id = false ) {
			if ( ! isset( $this->_available_views ) )
				$this->_available_views = apply_filters( 'builder_get_available_views', array() );
			
			if ( isset( $this->_available_views[$id] ) )
				return $id;
			if ( false === strpos( $id, '__' ) )
				return false;
			
			list( $function, $term_id ) = explode( '__', $id );
			
			if ( ! isset( $this->_available_views[$function]['name'] ) )
				return false;
			
			$name = $this->_available_views[$function]['name'];
			$description = '';
			
			$failed = false;
			
			if ( 'is_category' === $function ) {
				$category = get_term_by( 'slug', $meta['views'][$id]->slug, 'category' );
				
				if ( empty( $category ) )
					$category = get_term_by( 'name', $meta['views'][$id]->name, 'category' );
				
				if ( ! empty( $category->name ) )
					return ( $return_term_id ) ? $category->term_id : "is_category__{$category->term_id}";
			}
			else if ( 'is_tag' === $function ) {
				$tag = get_term_by( 'slug', $meta['views'][$id]->slug, 'post_tag' );
				
				if ( empty( $tag ) )
					$tag = get_term_by( 'name', $meta['views'][$id]->name, 'post_tag' );
				
				if ( ! empty( $tag->name ) )
					return ( $return_term_id ) ? $tag->term_id : "is_tag__{$tag->term_id}";
			}
			else if ( 'is_author' === $function ) {
				$author = get_user_by( 'login', $meta['views'][$id]['user_login'] );
				
				if ( ! $author )
					$author = get_user_by( 'email', $meta['views'][$id]['user_email'] );
				if ( ! $author )
					$author = get_user_by( 'slug', $meta['views'][$id]['user_nicename'] );
				if ( ! $author )
					$author = (object) $meta['views'][$id];
				
				if ( ! empty( $author->display_name ) )
					return ( $return_term_id ) ? $author->ID : "is_author__{$author->ID}";
			}
			
			return false;
		}
		
		function _get_active_layouts( $layout_settings, $export_data, $post_data ) {
			$active_layouts = array( 'import' => array(), 'site' => array() );
			
			if ( isset( $post_data['keep_import_guids'] ) ) {
				foreach ( (array) $post_data['keep_import_guids'] as $guid => $active )
					if ( ! empty( $active ) )
						$active_layouts['import'][] = $guid;
				
				foreach ( (array) $post_data['keep_site_guids'] as $guid => $active )
					if ( ! empty( $active ) )
						$active_layouts['site'][] = $guid;
			}
			else {
				foreach ( (array) array_keys( $export_data['layouts'] ) as $guid )
					$active_layouts['import'][] = $guid;
				
				foreach ( (array) array_keys( $layout_settings['layouts'] ) as $guid )
					$active_layouts['site'][] = $guid;
			}
			
			return $active_layouts;
		}
		
		function _get_active_views( $layout_settings, $export_data, $post_data ) {
			$active_views = array( 'import' => array(), 'site' => array() );
			
			
			if ( isset( $post_data['keep_import_guids'] ) ) {
				if ( ! isset( $post_data['keep_import_views'] ) )
					$post_data['keep_import_views'] = array();
				if ( ! isset( $post_data['keep_site_views'] ) )
					$post_data['keep_site_views'] = array();
				
				foreach ( (array) $post_data['keep_import_views'] as $id => $active )
					if ( ! empty( $active ) )
						$active_views['import'][] = $id;
				
				foreach ( (array) $post_data['keep_site_views'] as $id => $active )
					if ( ! empty( $active ) )
						$active_views['site'][] = $id;
			}
			else {
				if ( ( 'replace' === $post_data['method'] ) || ( 'replace' === $post_data['views_method'] ) ) {
					foreach ( (array) array_keys( $export_data['views'] ) as $id )
						$active_views['import'][] = $id;
				}
				else {
					foreach ( (array) array_keys( $layout_settings['views'] ) as $id )
						$active_views['site'][] = $id;
				}
			}
			
			return $active_views;
		}
		
		function _get_unique_layout_description( $description, $layouts ) {
			$layout_descriptions = array();
			
			foreach ( (array) $layouts as $guid => $layout_data )
				$layout_descriptions[] = $layout_data['description'];
			
			if ( ! in_array( $description, $layout_descriptions ) )
				return $description;
			
			
			$count = 1;
			
			while ( in_array( "$description - $count", $layout_descriptions ) )
				$count++;
			
			return "$description - $count";
		}
		
		function show_import_conflicts_form( $form, $info, $data, $post_data ) {
			$meta = $data['meta'];
			$data = $data['data'];
			
			
			$layout_settings = $this->get_layout_settings();
			
			if ( ! is_array( $layout_settings ) )
				$layout_settings = array();
			if ( ! isset( $layout_settings['layouts'] ) )
				$layout_settings['layouts'] = array();
			if ( ! isset( $layout_settings['views'] ) )
				$layout_settings['views'] = array();
			
			
			$active_layouts = $this->_get_active_layouts( $layout_settings, $data, $post_data );
			$active_views = $this->_get_active_views( $layout_settings, $data, $post_data );
			
			
			$site_layout_names = array();
			
			foreach ( (array) $active_layouts['site'] as $guid )
				$site_layout_names[$layout_settings['layouts'][$guid]['description']] = $guid;
			
			
			$layout_conflicts = $view_conflicts = array();
			
			if ( 'replace' !== $post_data['method'] ) {
				foreach ( (array) $active_layouts['import'] as $guid ) {
					if ( in_array( $guid, $active_layouts['site'] ) )
						$layout_conflicts[$guid]['guid'] = $guid;
					
					if ( isset( $site_layout_names[$data['layouts'][$guid]['description']] ) )
						$layout_conflicts[$guid]['description'] = $site_layout_names[$data['layouts'][$guid]['description']];
				}
				
				foreach ( (array) $active_views['import'] as $id ) {
					if ( in_array( $id, $active_views['site'] ) )
						$view_conflicts[$id]['id'] = $id;
				}
			}
			
			
/*			if ( 'replace' === $post_data['method'] )
				return;
			
			if ( ( 'add' === $post_data['method'] ) && empty( $layout_conflicts ) && empty( $view_conflicts ) )
				return;*/
			
			
			$setting_descriptions = array(
				'description'   => __( 'Layout Name', 'it-l10n-Builder-Madison' ),
				'width'         => __( 'Layout Width', 'it-l10n-Builder-Madison' ),
				'custom_width'  => __( 'Layout Width', 'it-l10n-Builder-Madison' ),
				'extension'     => __( 'Extension', 'it-l10n-Builder-Madison' ),
				'disable_style' => __( 'Extension Style', 'it-l10n-Builder-Madison' ),
				'modules'       => __( 'Modules', 'it-l10n-Builder-Madison' ),
			);
			
			
			
			$layout_options = array( '' => '&mdash; Remove View from site &mdash;' );
			
			foreach ( (array) $active_layouts as $source => $guids ) {
				foreach ( (array) $guids as $guid ) {
					$name = ( 'site' === $source ) ? $layout_settings['layouts'][$guid]['description'] : $data['layouts'][$guid]['description'];
					$layout_options["$source-$guid"] = ucfirst( $source ) . " Layout: $name";
				}
			}
			
			
			$modules = apply_filters( 'builder_get_modules', array() );
			
			$form->add_input_group( 'layout_guids' );
			
			foreach ( (array) $active_layouts['import'] as $guid ) {
				$import_data =& $data['layouts'][$guid];
				
				$form->push_options();
				$form->add_input_group( $guid );
				
				
				$form->add_input_group( 'modules' );
				
				$count = 0;
				
				ob_start();
				
				foreach ( (array) $import_data['modules'] as $num => $module ) {
					$form->push_options();
					$form->add_input_group( $num );
					
					if ( isset( $modules[$module['module']] ) && is_callable( array( $modules[$module['module']], 'show_conflicts_form' ) ) ) {
						if ( true === $modules[$module['module']]->show_conflicts_form( $form, $module['data'], $num ) )
							$count++;
					}
					
					$form->remove_input_group();
					$form->pop_options();
				}
				
				$module_conflicts = ob_get_contents();
				ob_end_clean();
				
				$form->remove_input_group();
				
				
				if ( ! empty( $module_conflicts ) ) {
					if ( $count > 0 )
						$layout_conflicts[$guid]['modules'] = $count;
					else
						echo $module_conflicts;
				}
				
				if ( ! isset( $layout_conflicts[$guid] ) ) {
					$form->remove_input_group();
					$form->pop_options();
					
					continue;
				}
				
				
				echo '<h4>' . sprintf( __( 'Resolve Layout &quot;%s&quot; Conflicts', 'it-l10n-Builder-Madison' ), $import_data['description'] ) . "</h4>\n";
				
				echo "<div style='margin-left:20px;'>\n";
				
				if ( ! empty( $layout_conflicts[$guid] ) ) {
					$form->add_input_group( 'conflicts' );
					$form->add_hiddens( $layout_conflicts[$guid] );
					$form->remove_input_group();
				}
				
				if ( isset( $layout_conflicts[$guid]['guid'] ) ) {
					$diff = array_diff_assoc( $layout_settings['layouts'][$guid], $import_data );
					
					if ( empty( $diff ) ) {
						echo "<p>" . sprintf( __( 'This Layout is a duplicate of the &quot;%s&quot; Layout that is currently on the site. All the settings are identical including the Layout\'s ID. Since each Layout must have a unique ID, please select an option to resolve this conflict.', 'it-l10n-Builder-Madison' ), $layout_settings['layouts'][$guid]['description'] ) . "</p>\n";
					}
					else {
						$differences = array_intersect( array_keys( $diff ), array_keys( $setting_descriptions ) );
						$differences = array_unique( $differences );
						
						$differences_descriptions = array();
						foreach ( (array) $differences as $difference )
							$differences_descriptions[] = $setting_descriptions[$difference];
						
						echo "<p>" . sprintf(
							__( 'This Layout is a duplicate of the &quot;%1$s&quot; Layout that is currently on the site. This means that both Layouts have the same ID and originated from the same Builder site. While these Layouts share the same IDs and a common origin, the %2$s for each do not match. Since each Layout must have a unique ID, please select an option to resolve this conflict.', count( $differences ), 'it-l10n-Builder-Madison' ),
							$layout_settings['layouts'][$guid]['description'],
							wp_sprintf_l( '%l', $differences_descriptions )
						) . "</p>\n";
					}
					
					$form->set_option( 'actions', 'new_guid' );
					
?>
	<div style="margin:20px;">
		<p><label><?php $form->add_radio( 'actions[]', 'new_guid' ); ?> <?php _e( '<strong>New ID:</strong> Create a new ID for the Layout being imported.', 'it-l10n-Builder-Madison' ); ?></label></p>
		<p><label><?php $form->add_radio( 'actions[]', 'replace' ); ?> <?php printf( __( '<strong>Replace:</strong> Remove this site\'s &quot;%s&quot; Layout and replace it with this one for the import.', 'it-l10n-Builder-Madison' ), $layout_settings['layouts'][$guid]['description'] ); ?></label></p>
		<p><label><?php $form->add_radio( 'actions[]', 'skip' ); ?> <?php _e( '<strong>Skip:</strong> Do not import this Layout.', 'it-l10n-Builder-Madison' ); ?></label></p>
	</div>
<?php
					
				}
				
				
				if ( isset( $layout_conflicts[$guid]['description'] ) ) {
					echo '<p>' . __( 'A Layout on the site already has a Layout with this name. Please choose a new name for this Layout.', 'it-l10n-Builder-Madison' ) . "</p>\n";
					
					$new_description = $import_data['description'];
					$count = 1;
					
					while ( in_array( "$new_description - $count", $site_layout_names ) )
						$count++;
					
					$new_description = "$new_description - $count";
					
					
					$form->set_option( 'new_description', $new_description );
					$form->set_option( 'actions', 'new_description' );
					
?>
	<div style="margin:20px;">
		<p><label><?php __( 'New Name:', 'it-l10n-Builder-Madison' ); ?> <?php $form->add_text_box( 'new_description' ); ?></label></p>
		<?php $form->add_hidden( 'actions[]' ); ?>
	</div>
<?php
					
				}
				
				
				if ( isset( $layout_conflicts[$guid]['modules'] ) )
					echo $module_conflicts;
				
				
				echo "</div>\n";
				
				$form->remove_input_group();
				$form->pop_options();
			}
			
			$form->remove_input_group();
			
			
			$form->add_input_group( 'view_ids' );
			
			foreach ( (array) $active_views['import'] as $id ) {
				if ( ! isset( $view_conflicts[$id] ) )
					continue;
				
				
				$import_data = $data['views'][$id]['layout'];
				
				$form->push_options();
				$form->add_input_group( $id );
				
				$view_name = $this->_get_view_description( $id, $data, $meta );
				
				echo '<h4>' . sprintf( __( 'Resolve View &quot;%s&quot; Conflicts', 'it-l10n-Builder-Madison' ), $view_name['description'] ) . "</h4>\n";
				
				echo "<div style='margin-left:20px;'>\n";
				
				if ( ! empty( $view_conflicts[$id] ) ) {
					$form->add_input_group( 'conflicts' );
					$form->add_hiddens( $view_conflicts[$id] );
					$form->remove_input_group();
				}
				
				if ( isset( $view_conflicts[$id]['id'] ) ) {
					echo "<p>" . sprintf( __( 'Both the site and the export data have a layout set for this View. The site uses the site\'s &quot;%1$s&quot; layout for this View. The export data uses the export\'s &quot;%2$s&quot; layout for this View. Please use the drop-down below to select what layout this view should use.', 'it-l10n-Builder-Madison' ), $layout_settings['layouts'][$layout_settings['views'][$id]['layout']]['description'], $data['layouts'][$import_data]['description'] ) . "</p>\n";
					
					if ( in_array( $import_data, $active_layouts['import'] ) )
						$form->set_option( 'set_layout', "import-$import_data" );
					else if ( in_array( $import_data, $active_layouts['site'] ) )
						$form->set_option( 'set_layout', "site-$import_data" );
					
?>
	<div style="margin:20px;">
		<p><label><?php _e( 'Select a Layout for this View:', 'it-l10n-Builder-Madison' ); ?> <?php $form->add_drop_down( 'set_layout', $layout_options ); ?></label></p>
	</div>
<?php
					
				}
				
				
				echo "</div>\n";
				
				$form->remove_input_group();
				$form->pop_options();
			}
			
			$form->remove_input_group();
			
			
			if ( 'custom' === $post_data['method'] ) {
				$current_default = ( isset( $layout_settings['layouts'][$layout_settings['default']]['description'] ) ) ? $layout_settings['layouts'][$layout_settings['default']]['description'] : '';
				$import_default = ( isset( $data['layouts'][$data['default']]['description'] ) ) ? $data['layouts'][$data['default']]['description'] : '';
				
				echo '<h4>' . __( 'Select the Default Layout', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				
				echo '<p>' . _e( 'The Default Layout is used for all views that do not have a specific layout set. You can use the drop-down below to select which Layout you want to use as the Default Layout.', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo '<p>' . sprintf( __( 'Note: The current site Default Layout is the site\'s &quot;%1$s&quot; Layout and the export data Default Layout is the export data\'s &quot;%2$s&quot; Layout.', 'it-l10n-Builder-Madison' ), $current_default, $import_default ) . "</p>\n";
				
				if ( in_array( $layout_settings['default'], $active_layouts['site'] ) )
					$form->set_option( 'default_layout', "site-{$layout_settings['default']}" );
				else if ( in_array( $data['default'], $active_layouts['import'] ) )
					$form->set_option( 'default_layout', "import-{$layout_settings['default']}" );
				else
					$form->set_option( 'default_layout', '' );
				
				unset( $layout_options[''] );
				
?>
<div style="margin:20px;">
	<p><label><?php _e( 'Select a Default Layout:', 'it-l10n-Builder-Madison' ); ?> <?php $form->add_drop_down( 'default_layout', $layout_options ); ?></label></p>
</div>
<?php
				
			}
			
			
//			ITUtility::print_js_script( 'jQuery(\'.builder_tooltip\').tooltip({track: false,delay: 0,showURL: false,showBody: " - ",fade: 250});' );
		}
		
		function _get_updated_layout_settings( $layout_settings, $export_data, $post_data, $meta, $attachments = array() ) {
			$layouts = $this->_get_updated_layouts( $layout_settings, $export_data, $post_data, $attachments );
			$views = $this->_get_updated_views( $layout_settings, $export_data, $post_data, $meta, $layouts );
			
			$layout_settings['layouts'] = $layouts['data'];
			$layout_settings['views'] = $views['data'];
			
			unset( $layouts['data'] );
			unset( $views['data'] );
			
			if ( ! empty( $post_data['default_layout'] ) ) {
				list( $source, $guid ) = explode( '-', $post_data['default_layout'] );
				
				if ( isset( $layouts['guids'][$source][$guid] ) && isset( $layout_settings['layouts'][$layouts['guids'][$source][$guid]] ) )
					$guid = $layouts['guids'][$source][$guid];
				else if ( isset( $layouts['guids']['site'][$layout_settings['default']] ) && isset( $layout_settings['layouts'][$layouts['guids']['site'][$layout_settings['default']]] ) )
					$guid = $layouts['guids']['site'][$layout_settings['default']];
				else if ( isset( $layouts['guids']['import'][$export_data['default']] ) && isset( $layout_settings['layouts'][$layouts['guids']['import'][$export_data['default']]] ) )
					$guid = $layouts['guids']['import'][$export_data['default']];
				
				if ( isset( $layout_settings['layouts'][$guid] ) )
					$layout_settings['default'] = $guid;
				else
					$layout_settings['default'] = key( $layout_settings['layouts'] );
			}
			else if ( 'replace' === $post_data['method'] ) {
				$layout_settings['default'] = $export_data['default'];
			}
			
			if ( ! isset( $layout_settings['layouts'][$layout_settings['default']] ) )
				$layout_settings['default'] = key( $layout_settings['layouts'] );
			
			$layout_settings['layouts'] = ITUtility::sort_array( $layout_settings['layouts'], 'description' );
			
			$info = array( 'layouts' => $layouts, 'views' => $views );
			
			
			$upgrade_data = array(
				'data'            => $layout_settings,
				'current_version' => builder_get_data_version( 'layout-settings' ),
			);
			
			require_once( dirname( __FILE__ ) . '/upgrade-storage.php' );
			
			$upgrade_data = apply_filters( 'it_storage_upgrade_layout_settings', $upgrade_data );
			
			$layout_settings = $upgrade_data['data'];
			
			
			return array( 'data' => $layout_settings, 'info' => $info );
		}
		
		function _get_updated_layouts( $layout_settings, $export_data, $post_data, $attachments ) {
			if ( ! isset( $post_data['layouts_method'] ) || ! in_array( $post_data['layouts_method'], array( 'add', 'replace', 'skip' ) ) )
				$post_data['layouts_method'] = $post_data['method'];
			
			
			$data = array();
			$actions = array(
				'kept'     => array(),
				'imported' => array(),
				'removed'  => array(),
				'skipped'  => array(),
				'renamed'  => array(),
			);
			$guids = array();
			
			$active_layouts = $this->_get_active_layouts( $layout_settings, $export_data, $post_data );
			
			
			if ( 'replace' !== $post_data['layouts_method'] ) {
				foreach ( (array) $active_layouts['site'] as $guid ) {
					$data[$guid] = $layout_settings['layouts'][$guid];
					$actions['kept'][] = $guid;
					$guids['site'][$guid] = $guid;
				}
			}
			
			
			$modules = apply_filters( 'builder_get_modules', array() );
			
			
			if ( 'skip' !== $post_data['layouts_method'] ) {
				foreach ( (array) $active_layouts['import'] as $guid ) {
					$layout_data = $export_data['layouts'][$guid];
					$layout_details = ( isset( $post_data['layout_guids'][$guid] ) ) ? $post_data['layout_guids'][$guid] : array();
					
					$final_guid = $guid;
					
					if ( isset( $layout_details['actions'] ) ) {
						if ( in_array( 'skip', $layout_details['actions'] ) )
							continue;
						else if ( in_array( 'replace', $layout_details['actions'] ) ) {
							unset( $data[$guid] );
							$actions['kept'] = array_diff( $actions['kept'], array( $guid ) );
							unset( $guids['site'][$guid] );
						}
						else {
							if ( in_array( 'new_guid', $layout_details['actions'] ) ) {
								$final_guid = uniqid( '' );
								
								while ( isset( $data[$final_guid] ) )
									$final_guid = uniqid( '' );
							}
							
							if ( in_array( 'new_description', $layout_details['actions'] ) ) {
								$description = ( ! empty( $layout_details['new_description'] ) ) ? $layout_details['new_description'] : $export_data['layouts'][$guid]['description'];
								$description = $this->_get_unique_layout_description( $description, $data );
								
								$layout_data['description'] = $description;
								
								$actions['renamed'][$final_guid] = $export_data['layouts'][$guid]['description'];
							}
						}
					}
					
					foreach ( (array) $layout_data['modules'] as $module_id => $module ) {
						if ( isset( $modules[$module['module']] ) && method_exists( $modules[$module['module']], 'import' ) ) {
							$module_post_data = ( isset( $post_data['layout_guids'][$guid]['modules'][$module_id] ) ) ? $post_data['layout_guids'][$guid]['modules'][$module_id] : array();
							$layout_data['modules'][$module_id]['data'] = $modules[$module['module']]->import( $module['data'], $attachments, $module_post_data );
						}
					}
					
					
					$data[$final_guid] = $layout_data;
					$actions['imported'][] = $final_guid;
					$guids['import'][$guid] = $final_guid;
				}
			}
			
			
/*			foreach ( (array) $data as $guid => $layout_data ) {
				foreach ( (array) $layout_data['modules'] as $module_id => $module ) {
					if ( isset( $modules[$module['module']] ) && method_exists( $modules[$module['module']], 'import' ) )
						$data[$guid]['modules'][$module_id]['data'] = $modules[$module['module']]->import( $module['data'], $attachments, array() );
				}
			}*/
			
			
			$actions['removed'] = array_diff( array_keys( $layout_settings['layouts'] ), $actions['kept'] );
			$actions['skipped'] = array_diff( array_keys( $export_data['layouts'] ), $actions['imported'] );
			
			
			return compact( 'data', 'actions', 'guids' );
		}
		
		function _get_updated_views( $layout_settings, $export_data, $post_data, $meta, $final_layout_settings ) {
			if ( ! isset( $post_data['views_method'] ) || ! in_array( $post_data['views_method'], array( 'add', 'replace', 'skip' ) ) )
				$post_data['views_method'] = $post_data['method'];
			
			
			$data = array();
			$actions = array(
				'kept'     => array(),
				'imported' => array(),
				'removed'  => array(),
				'skipped'  => array(),
				'failed'   => array(),
			);
			
			$active_views = $this->_get_active_views( $layout_settings, $export_data, $post_data );
			
			
			
			if ( 'replace' !== $post_data['views_method'] ) {
				foreach ( (array) $active_views['site'] as $id ) {
					if ( ! isset( $layout_settings['views'][$id] ) )
						continue;
					
					$data[$id] = $layout_settings['views'][$id];
					$actions['kept'][] = $id;
				}
			}
			
			
			if ( 'skip' !== $post_data['views_method'] ) {
				foreach ( (array) $active_views['import'] as $id ) {
					$view_data = $export_data['views'][$id];
					$view_details = ( isset( $post_data['view_ids'][$id] ) ) ? $post_data['view_ids'][$id] : array();
					
					$final_id = $this->_update_import_view_id( $id, $meta );
					
					if ( empty( $final_id ) ) {
						$actions['failed'][] = $id;
						continue;
					}
					
					if ( isset( $view_details['set_layout'] ) ) {
						unset( $data[$id] );
						$actions['kept'] = array_diff( $actions['kept'], array( $id ) );
						
						if ( empty( $view_details['set_layout'] ) ) {
							continue;
						}
						else {
							list( $source, $guid ) = explode( '-', $view_details['set_layout'] );
							
							$view_data = $final_layout_settings['guids'][$source][$guid];
						}
					}
					
					$data[$final_id] = $view_data;
					$actions['imported'][] = $final_id;
				}
			}
			
			$actions['removed'] = array_diff( array_keys( $layout_settings['views'] ), $actions['kept'] );
			$actions['skipped'] = array_diff( array_keys( $export_data['views'] ), $actions['imported'], $actions['failed'] );
			
			
			return compact( 'data', 'actions' );
		}
		
		function show_import_process( $form, $info, $data, $post_data ) {
			$meta = $data['meta'];
			$data = $data['data'];
			
			$layout_settings = $this->get_layout_settings();
			
			
			$updated_layout_settings = $this->_get_updated_layout_settings( $layout_settings, $data, $post_data, $meta );
			
			$final_layout_settings = $updated_layout_settings['info']['layouts'];
			$final_layout_settings['data'] = $updated_layout_settings['data']['layouts'];
			
			$final_view_settings = $updated_layout_settings['info']['views'];
			$final_view_settings['data'] = $updated_layout_settings['data']['views'];
			
			
			if ( ! empty( $final_layout_settings['actions']['kept'] ) ) {
				echo '<h4>' . __( 'Keeping Site Layouts', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo "<p>" . __( 'The following Layouts on the site will be kept:', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo "<ul>\n";
				
				foreach ( (array) $final_layout_settings['actions']['kept'] as $guid )
					echo "<li>{$final_layout_settings['data'][$guid]['description']}</li>\n";
				
				echo "</ul>\n";
				echo "</div>\n";
			}
			
			if ( ! empty( $final_layout_settings['actions']['imported'] ) ) {
				echo '<h4>' . __( 'Importing Layouts', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo "<p>" . __( 'The following Layouts from the export file will be imported:', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo "<ul>\n";
				
				foreach ( (array) $final_layout_settings['actions']['imported'] as $guid ) {
					$renamed = '';
					
					if ( isset( $final_layout_settings['actions']['renamed'][$guid] ) )
						$renamed = '&nbsp;&nbsp;&nbsp;<i>' . sprintf( __( '(renamed from &quot;%s&quot;)', 'it-l10n-Builder-Madison' ), $final_layout_settings['actions']['renamed'][$guid] ) . '</i>';
					
					echo "<li>{$final_layout_settings['data'][$guid]['description']}$renamed</li>\n";
				}
				
				echo "</ul>\n";
				echo "</div>\n";
			}
			
			if ( ! empty( $final_layout_settings['actions']['removed'] ) ) {
				echo '<h4>' . __( 'Removing Site Layouts', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo "<p>" . __( 'The following Layouts on the site will be removed:', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo "<ul>\n";
				
				$layout_settings['layouts'] = ITUtility::sort_array( $layout_settings['layouts'], 'description' );
				
				foreach ( (array) array_keys( $layout_settings['layouts'] ) as $guid ) {
					if ( in_array( $guid, $final_layout_settings['actions']['removed'] ) )
						echo "<li>{$layout_settings['layouts'][$guid]['description']}</li>\n";
				}
				
				echo "</ul>\n";
				echo "</div>\n";
			}
			
/*			if ( ! empty( $final_layout_settings['actions']['skipped'] ) ) {
				echo '<h4>'. __( 'Skipping Imported Layouts', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo "<p>" . __( 'The following Layouts from the export file will not be imported:', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo "<ul>\n";
				
				foreach ( (array) $final_layout_settings['actions']['skipped'] as $guid )
					echo "<li>{$data['layouts'][$guid]['description']}</li>\n";
				
				echo "</ul>\n";
				echo "</div>\n";
			}*/
			
			
			if ( ( 'add' !== $post_data['method'] ) && ! empty( $final_view_settings['data'] ) ) {
				echo '<h4>' . __( 'Final View Settings', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo "<p>" . __( 'The site will use the following Views:', 'it-l10n-Builder-Madison' ) . "</p>\n";
				echo "<ul>\n";
				
				$temp_layout_settings = $layout_settings;
				$temp_layout_settings['layouts'] = $final_layout_settings['data'];
				$temp_layout_settings['views'] = $final_view_settings['data'];
				
				foreach ( (array) $final_view_settings['data'] as $id => $guid ) {
					$description = $this->_get_view_description( $id, $temp_layout_settings );
					$description = sprintf( __( '<strong>%1$s</strong> uses the &quot;%2$s&quot; layout', 'it-l10n-Builder-Madison' ), $description['description'], $description['layout'] );
					echo "<li>$description</li>\n";
				}
				
				echo "</ul>\n";
				echo "</div>\n";
			}
			
			if ( 'add' !== $post_data['method'] ) {
				echo '<h4>' . __( 'Default Layout', 'it-l10n-Builder-Madison' ) . "</h4>\n";
				echo "<div style='margin-left:20px;'>\n";
				echo $final_layout_settings['data'][$updated_layout_settings['data']['default']]['description'];
				echo "</div>\n";
			}
		}
		
		function run_import( $info, $data, $post_data, $attachments, $return_data, $layout_settings ) {
			$meta = $data['meta'];
			$data = $data['data'];
			
			
			if ( ! is_array( $layout_settings ) ) {
				$layout_settings = $this->get_layout_settings();
			}
			else {
				if ( ! is_array( $layout_settings ) )
					$layout_settings = array();
				if ( ! isset( $layout_settings['layouts'] ) )
					$layout_settings['layouts'] = array();
				if ( ! isset( $layout_settings['views'] ) )
					$layout_settings['views'] = array();
			}
			
			
			$updated_layout_settings = $this->_get_updated_layout_settings( $layout_settings, $data, $post_data, $meta, $attachments );
			
			$storage = new ITStorage( 'layout_settings' );
			$storage->save( $updated_layout_settings['data'] );
			
			if ( $return_data )
				return $updated_layout_settings['data'];
			
			return true;
		}
		
		function _get_layout_description( $data ) {
			ob_start();
			
?>
	<div title="title - tooltip test" class="builder_tooltip"><strong><?php _e( 'Name:', 'it-l10n-Builder-Madison' ); ?></strong> <?php echo $data['description']; ?></div>
	<div><strong><?php _e( 'Width:', 'it-l10n-Builder-Madison' ); ?></strong> <?php echo $data['width']; ?> pixels</div>
	<div><strong><?php _e( 'Modules:', 'it-l10n-Builder-Madison' ); ?></strong></div>
	<ul>
		<?php foreach ( (array) $data['modules'] as $module ) : ?>
			<?php
				$description = ucwords( preg_replace( '/-+/', ' ', $module['module'] ) );
				
				if ( isset( $module['data']['sidebar'] ) ) {
					$sidebar_widths = $module['data']['sidebar_widths'];
					
					if ( 'custom' === $sidebar_widths )
						$sidebar_widths = $module['data']['custom_sidebar_widths'];
					
					if ( 'none' === $module['data']['sidebar'] )
						$description .= ' with no sidebars';
					else
						$description .= ' with ' . ucfirst( preg_replace( '/_+/', ' ', $module['data']['sidebar'] ) ) . " sidebars ($sidebar_widths)";
				}
				else if ( isset( $module['data']['widget_percents'] ) ) {
					$widget_widths = $module['data']['widget_percents'];
					
					if ( 'custom' === $widget_widths )
						$widget_widths = $module['data']['custom_widget_percents'];
					
					if ( 1 == $module['data']['type'] )
						$description .= ' 1 column';
					else
						$description .= " {$module['data']['type']} columns ($widget_widths)";
				}
			?>
			
			<li><?php echo $description; ?></li>
		<?php endforeach; ?>
	</ul>
<?php
			
			$description = ob_get_contents();
			ob_end_clean();
			
			return $description;
		}
	}
}
