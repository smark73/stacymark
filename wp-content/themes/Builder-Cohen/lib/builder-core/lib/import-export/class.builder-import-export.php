<?php

/*
Central manager for all Builder import/export operations

Written by Chris Jean for iThemes.com
Version 1.3.1

Version History
	1.0.0 - 2010-12-20 - Chris Jean
		Initial version
	1.0.1 - 2011-10-06 - Chris Jean
		Fixed a bug where invalid attachments would cause an import failure
	1.0.2 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.1.0 - 2013-07-01 - Chris Jean
		All serialized data now is stored and read in three different formats: JSON, serialize+base64, and serialize. When reading, it will read each format until it can successfully load the data. This avoids issues on systems that corrupt the data on read.
		Cleaned up named array definitions.
	1.2.0 - 2013-08-09 - Chris Jean
		Improved cleanup() to support cleanup of current cache when supplying no arguments.
	1.3.0 - 2013-08-15 - Chris Jean
		Updated run_import() to handle direct calls to import from the Setup page.
	1.3.1 - 2013-09-17 - Chris Jean
		Updated code to no longer use the deprecated get_theme_data() function.
*/


if ( ! class_exists( 'BuilderImportExport' ) ) {
	class BuilderImportExport {
		var $_guid = null;
		var $_data_sources = array();
		var $_attachments = array();
		var $_file_path = null;
		var $_errors = array();
		var $_cache = array();
		
		
		function BuilderImportExport( $guid_or_file = null ) {
			builder_set_minimum_memory_limit( '256M' );
			
			$this->_init_data_sources();
			
			if ( ! is_null( $guid_or_file ) ) {
				if ( is_file( $guid_or_file ) )
					$this->_guid = $this->_import_file( $guid_or_file );
				else
					$this->_guid = $guid_or_file;
				
				$this->_load_data();
			}
			else
				$this->_guid = $this->_generate_guid();
			
			$this->_cleanup_old_temp_directories();
		}
		
		function add_error( $wp_error ) {
			$this->_errors[] = $wp_error;
		}
		
		function had_error() {
			return ! empty( $this->_errors );
		}
		
		function get_errors() {
			return $this->_errors;
		}
		
		function get_guid() {
			if ( is_null( $this->_guid ) )
				return false;
			return $this->_guid;
		}
		
		function get_info() {
			if ( empty( $this->_cache['info'] ) ) {
				$file = BuilderImportExport::_get_export_path( $this->_guid );
				
				if ( is_wp_error( $file ) )
					return $file;
				
				$info = $this->_get_file_data( 'info.txt' );
				$info['file'] = $file;
				$info['url'] = ITFileUtility::get_url_from_file( $file );
				
				$this->_cache['info'] = $info;
			}
			
			return $this->_cache['info'];
		}
		
		function get_data( $data_source_var ) {
			return $this->_get_file_data( "data-sources/$data_source_var.txt" );
		}
		
		function get_attachments() {
			if ( empty( $this->_file_path ) || ! is_dir( $this->_file_path ) )
				return false;
			
			$info = $this->get_info();
			$attachments = array();
			
			foreach ( (array) $info['attachments'] as $id => $data )
				$attachments[$id] = "{$this->_file_path}/attachments/$id/{$data['file_name']}";
			
			return $attachments;
		}
		
		function get_data_sources() {
			$data_sources = array();
			
			foreach ( (array) $this->_data_sources as $var => $source )
				$data_sources[$var] = $source->get_name();
			
			asort( $data_sources );
			
			return $data_sources;
		}
		
		function get_data_source_export_description( $var ) {
			return $this->_data_sources[$var]->get_export_description();
		}
		
		function get_export( $name, $data_sources = array() ) {
			global $current_user;
			
			it_classes_load( 'it-zip.php' );
			
			$zip = new ITZip();
			
			
			if ( empty( $data_sources ) )
				$data_sources = array_keys( $this->_data_sources );
			
			$theme_data = get_file_data( get_stylesheet_directory() . '/style.css', array( 'name' => 'Theme Name' ), 'theme' );
			
			$export_path = $this->_get_export_path( $this->_guid, $name );
			
			$info = array(
				'name'               => $name,
				'guid'               => $this->_guid,
				'builder_version'    => $GLOBALS['it_builder_core_version'],
				'builder_theme_name' => $theme_data['name'],
				'timestamp'          => time(),
				'site_url'           => get_bloginfo( 'url' ),
				'site_wpurl'         => get_bloginfo( 'wpurl' ),
				'exported_by'        => $current_user->display_name,
				'data_sources'       => array(),
				'attachments'        => array(),
			);
			
			add_action( 'builder_import_export_add_attachment', array( $this, 'add_attachment' )  );
			
			foreach ( (array) $data_sources as $var ) {
				if ( ! isset( $this->_data_sources[$var] ) )
					continue;
				
				$data = $this->_data_sources[$var]->get_export_data();
				$this->_add_zip_file_content( $zip, "data-sources/$var.txt", $data );
				
				$data_source = array(
					'name'      => $this->_data_sources[$var]->get_name(),
					'version'   => $this->_data_sources[$var]->get_version(),
					'data_size' => strlen( serialize( $data ) ),
				);
				$info['data_sources'][$var] = $data_source;
				
				unset( $data );
			}
			
			
			$this->_attachments = array_unique( $this->_attachments );
			$attachments = $files = array();
			
			it_classes_load( 'it-file-utility.php' );
			
			foreach ( (array) $this->_attachments as $attachment_id ) {
				$url = wp_get_attachment_url( $attachment_id );
				
				if ( empty( $url ) )
					continue;
				
				$file = ITFileUtility::get_file_from_url( $url );
				
				$attachment = array();
				$attachment['post'] = get_post( $attachment_id );
				$attachment['file_name'] = basename( $file );
				$attachment['metadata_alt'] = get_metadata( 'post', $attachment_id, '_wp_attachment_image_alt' );
				
				if ( empty( $attachment['metadata_alt'] ) )
					unset( $attachment['metadata_alt'] );
				
				$attachments[$attachment_id] = $attachment;
				$files[$attachment_id] = $file;
			}
			
			$info['attachments'] = $attachments;
			
			
			$this->_add_zip_file_content( $zip, 'info.txt', $info );
			
			foreach ( (array) $files as $id => $file )
				$zip->add_file( $file, "attachments/$id/" );
			
			do_action( 'builder_exporter_modify_zip_content', $zip );
			
			
			$file = $zip->create_zip( array( 'file' => $export_path ) );
			
			if ( is_wp_error( $file ) )
				return $file;
			
			
			$this->_load_data();
			
			
			$info['file'] = $file;
			$info['url'] = ITFileUtility::get_url_from_file( $file );
			
			
			return $info;
		}
		
		function show_data_source_import_methods( $form ) {
			$info = $this->get_info();
			
			$form->add_input_group( 'data_sources' );
			
			foreach ( (array) $info['data_sources'] as $var => $data_source ) {
				echo "<h3>{$data_source['name']}</h3>\n";
				
				echo "<div style='margin-left:20px;'>\n";
				
				$form->push_options();
				$form->add_input_group( $var );
				
				$form->set_option( 'method', 'skip' );
				
				if ( isset( $this->_data_sources[$var] ) && is_callable( array( $this->_data_sources[$var], 'show_import_methods_form' ) ) )
					$this->_data_sources[$var]->show_import_methods_form( $form, $info['data_sources'][$var] );
				else
					echo "<p>" . __( 'The plugin or component that this data is for is not present on this site. This data will not be imported.', 'it-l10n-Builder-Cohen' ) . "</p>\n";
				
				$form->remove_input_group();
				$form->pop_options();
				
				echo "</div>\n";
			}
			
			$form->remove_input_group();
		}
		
		function show_data_source_import_customizations( $form ) {
			$info = $this->get_info();
			
			$submission_data = ITForm::get_post_data( true );
			
			$has_customizations = false;
			
			$form->add_input_group( 'data_sources' );
			
			foreach ( (array) $info['data_sources'] as $var => $data_source ) {
				if ( ! isset( $this->_data_sources[$var] ) )
					continue;
				
				$form->push_options();
				$form->add_input_group( $var );
				
				$data_source_has_customizations = false;
				
				if ( ( 'skip' !== $submission_data['data_sources'][$var]['method'] ) && isset( $this->_data_sources[$var] ) && is_callable( array( $this->_data_sources[$var], 'show_import_customizations_form' ) ) ) {
					ob_start();
					$this->_data_sources[$var]->show_import_customizations_form( $form, $info['data_sources'][$var], $this->get_data( $var ), $submission_data['data_sources'][$var] );
					$data_source_customizations = ob_get_contents();
					ob_end_clean();
					
					if ( ! empty( $data_source_customizations ) ) {
						echo "<h3>{$data_source['name']}</h3>\n";
						
						echo "<div style='margin-left:20px;'>\n";
						echo $data_source_customizations;
						echo "</div>\n";
						
						$has_customizations = true;
						$data_source_has_customizations = true;
					}
				}
				
				$form->add_hiddens( $submission_data['data_sources'][$var] );
				
				
				$form->remove_input_group();
				$form->pop_options();
			}
			
			$form->remove_input_group();
			
			return $has_customizations;
		}
		
		function show_data_source_import_conflicts( $form ) {
			$info = $this->get_info();
			
			$submission_data = ITForm::get_post_data( true );
			
			$has_conflicts = false;
			
			$form->add_input_group( 'data_sources' );
			
			foreach ( (array) $info['data_sources'] as $var => $data_source ) {
				if ( ! isset( $this->_data_sources[$var] ) )
					continue;
				
				$form->push_options();
				$form->add_input_group( $var );
				
				$data_source_has_conflicts = false;
				
				if ( ( 'skip' !== $submission_data['data_sources'][$var]['method'] ) && isset( $this->_data_sources[$var] ) && is_callable( array( $this->_data_sources[$var], 'show_import_conflicts_form' ) ) ) {
					ob_start();
					$this->_data_sources[$var]->show_import_conflicts_form( $form, $info['data_sources'][$var], $this->get_data( $var ), $submission_data['data_sources'][$var] );
					$data_source_conflicts = ob_get_contents();
					ob_end_clean();
					
					if ( ! empty( $data_source_conflicts ) ) {
						echo "<h3>{$data_source['name']}</h3>\n";
						
						echo "<div style='margin-left:20px;'>\n";
						echo $data_source_conflicts;
						echo "</div>\n";
						
						$has_conflicts = true;
						$data_source_has_conflicts = true;
					}
				}
				
				$form->add_hiddens( $submission_data['data_sources'][$var] );
				
				
				$form->remove_input_group();
				$form->pop_options();
			}
			
			$form->remove_input_group();
			
			return $has_conflicts;
		}
		
		function show_data_source_import_process( $form ) {
			$info = $this->get_info();
			
			$submission_data = ITForm::get_post_data( true );
			
			$form->add_input_group( 'data_sources' );
			
			foreach ( (array) $info['data_sources'] as $var => $data_source ) {
				$form->push_options();
				$form->add_input_group( $var );
				
				
				echo "<h3>{$data_source['name']}</h3>\n";
				echo "<div style='margin-left:20px;'>\n";
				
				if ( isset( $this->_data_sources[$var] ) ) {
					if ( 'skip' === $submission_data['data_sources'][$var]['method'] )
						echo '<p>' . sprintf( __( 'The %1$s data from the export file will not be imported to the site.', 'it-l10n-Builder-Cohen' ), $this->_data_sources[$var]->get_name() ) . "</p>\n";
					else {
						$has_custom_process = false;
						
						if ( is_callable( array( $this->_data_sources[$var], 'show_import_process' ) ) ) {
							ob_start();
							$this->_data_sources[$var]->show_import_process( $form, $info['data_sources'][$var], $this->get_data( $var ), $submission_data['data_sources'][$var] );
							$data_source_process = ob_get_contents();
							ob_end_clean();
							
							if ( ! empty( $data_source_process ) ) {
								echo $data_source_process;
								
								$has_custom_process = true;
							}
						}
						
						if ( false === $has_custom_process ) {
							if ( 'replace' === $submission_data['data_sources'][$var]['method'] )
								printf( __( 'The %1$s data on the site will be removed and replaced with the %1$s data from the export file.', 'it-l10n-Builder-Cohen' ), $this->_data_sources[$var]->get_name() );
							else
								_e( 'The data will be imported as selected.', 'it-l10n-Builder-Cohen' );
						}
					}
					
					$form->add_hiddens( $submission_data['data_sources'][$var] );
				}
				else {
					echo "<p>" . __( 'The plugin or component that this data is for is not present on this site. This data will not be imported.', 'it-l10n-Builder-Cohen' ) . "</p>\n";
				}
				
				echo "</div>\n";
				
				
				
				$form->remove_input_group();
				$form->pop_options();
			}
			
			$form->remove_input_group();
		}
		
		function run_import( $settings = array(), $use_post_data = true, $return_data = false, $db_data = false ) {
			$default_settings = array(
				'data_sources' => array(
					'layouts-views'  => array(
						'method' => 'add', // replace, add, custom, skip
					),
					'theme-settings' => array(
						'method' => 'skip', // replace, skip
					),
				),
			);
			$settings = ITUtility::merge_defaults( $settings, $default_settings );
			
			if ( $use_post_data ) {
				$post_data = ITForm::get_post_data( true );
				
				$settings = ITUtility::merge_defaults( $post_data, $settings );
			}
			
			
			it_classes_load( 'it-file-utility.php' );
			
			
			$info = $this->get_info();
			
			$errors = array();
			$results = array();
			
			$attachments = $this->get_attachments();
			$new_attachments = array();
			
			foreach ( (array) $attachments as $id => $file ) {
				$attachment = ITFileUtility::add_to_media_library( $file );
				$new_attachments[$id] = $attachment;
			}
			
			foreach ( (array) $info['data_sources'] as $var => $data_source ) {
				if ( isset( $this->_data_sources[$var] ) ) {
					if ( is_callable( array( $this->_data_sources[$var], 'run_import' ) ) ) {
						if ( ! isset( $settings['data_sources'][$var] ) )
							$settings['data_sources'][$var] = array();
						else if ( isset( $settings['data_sources'][$var]['method'] ) && ( 'skip' == $settings['data_sources'][$var]['method'] ) )
							continue;
						
						$db_settings = ( isset( $db_data[$var] ) ) ? $db_data[$var] : false;
						
						$result = $this->_data_sources[$var]->run_import( $info['data_sources'][$var], $this->get_data( $var ), $settings['data_sources'][$var], $new_attachments, $return_data, $db_settings );
						
						if ( $return_data )
							$results[$var] = $result;
						
						if ( false === $result )
							$errors[] = "$var:failed_run_import";
					}
					else
						$errors[] = "$var:missing_run_import";
				}
			}
			
			if ( $return_data )
				return $results;
			
			if ( ! empty( $errors ) )
				return $errors;
			
			return true;
		}
		
		function add_attachment( $id ) {
			if ( ! in_array( $id, $this->_attachments ) )
				$this->_attachments[] = $id;
		}
		
		function cleanup( $guid = false, $path = false ) {
			it_classes_load( 'it-file-utility.php' );
			
			if ( ! empty( $path ) )
				ITFileUtility::delete_directory( $path );
			else if ( ! empty( $this->_file_path ) )
				ITFileUtility::delete_directory( $this->_file_path );
			
			if ( ! empty( $guid ) )
				delete_transient( "builder-imex-path-$guid" );
			else if ( ! empty( $this->_guid ) )
				delete_transient( "builder-imex-path-{$this->_guid}" );
		}
		
		function _get_file_data( $file ) {
			if ( empty( $this->_file_path ) || ! is_dir( $this->_file_path ) )
				return false;
			
			return $this->_get_file_content( "{$this->_file_path}/$file" );
		}
		
		function _load_data() {
			if ( $this->had_error() )
				return false;
			
			if ( ! empty( $this->_file_path ) )
				return true;
			
			$path = get_transient( "builder-imex-path-{$this->_guid}" );
			
			if ( ( false !== $path ) && is_dir( $path ) ) {
				$this->_file_path = $path;
				return true;
			}
			
			
			$file = BuilderImportExport::_get_export_path( $this->_guid );
			
			if ( is_wp_error( $file ) ) {
				$this->add_error( $file );
				return false;
			}
			
			
			it_classes_load( 'it-file-utility.php' );
			it_classes_load( 'it-zip.php' );
			
			
			$path = ITFileUtility::create_writable_directory( array( 'name' => 'deleteme-builder-import-export-cache-temp', 'random' => true ) );
			
			if ( is_wp_error( $path ) )
				return $path;
			
			$result = ITZip::unzip( $file, $path );
			
			if ( is_wp_error( $result ) ) {
				$this->add_error( $results );
				return false;
			}
			
			
			$this->_file_path = $path;
			set_transient( "builder-imex-path-{$this->_guid}", $path, 600 );
			wp_schedule_single_event( time() + 660, 'builder_import_export_cleanup', array( $this->_guid, $path ) );
			
			return true;
		}
		
		function _import_file( $file ) {
			if ( $this->had_error() )
				return false;
			
			if ( ! is_file( $file ) ) {
				$this->add_error( new WP_Error( 'builder-import-export-no-file', __( 'Unable to find requested export file', 'it-l10n-Builder-Cohen' ) ) );
				return false;
			}
			
			it_classes_load( 'it-file-utility.php' );
			it_classes_load( 'it-zip.php' );
			
			
			$path = ITFileUtility::create_writable_directory( array( 'name' => 'deleteme-builder-import-export-upload-temp', 'random' => true ) );
			
			if ( is_wp_error( $path ) ) {
				$this->add_error( $path );
				return false;
			}
			
			$result = ITZip::unzip( $file, $path );
			
			if ( is_wp_error( $result ) || ! is_file( "$path/info.txt" ) || ! is_dir( "$path/data-sources" ) ) {
				ITFileUtility::delete_directory( $path );
				
				$this->add_error( new WP_Error( 'builder-import-export-invalid-export-format', __( 'The uploaded file is not a valid Builder export file. It cannot be imported.', 'it-l10n-Builder-Cohen' ) ) );
				return false;
			}
			
			
			$info = $this->_get_file_content( "$path/info.txt" );
			
			$zip = new ITZip();
			
			
			$export_path = $this->_get_export_path( $info['guid'], $info['name'] );
			$new_guid = false;
			
			if ( is_wp_error( $export_path ) ) {
				$info['guid'] = $this->_generate_guid();
				
				if ( false === $info['guid'] )
					return false;
				
				$export_path = $this->_get_export_path( $info['guid'], $info['name'] );
				$new_guid = true;
			}
			
			if ( true === $new_guid )
				$this->_add_zip_file_content( $zip, 'info.txt', $info );
			
			
			$files = ITFileUtility::get_flat_file_listing( $path );
			
			foreach ( (array) $files as $file ) {
				if ( ( true === $new_guid ) && ( 'info.txt' === $file ) )
					continue;
				
				$dir = dirname( $file );
				$dir = ( '.' === $dir ) ? '' : "$dir/";
				
				$zip->add_file( "$path/$file", $dir );
			}
			
			$file = $zip->create_zip( array( 'file' => $export_path ) );
			
			ITFileUtility::delete_directory( $path );
			
			
			return $info['guid'];
		}
		
		function _generate_guid() {
			$guid = uniqid( '', true );
			
			$file = $this->_get_export_path( $guid );
			$count = 0;
			
			while ( ! is_wp_error( $file ) ) {
				$guid = uniqid( '', true );
				
				$file = $this->_get_export_path( $guid );
				
				if ( ++$count > 5 ) {
					$this->add_error( new WP_Error( 'builder-import-export-cannot-generate-guid', 'Unable to create a unique export file path' ) );
					return false;
				}
			}
			
			return $guid;
		}
		
		function _get_export_path( $guid, $name = null ) {
			it_classes_load( 'it-file-utility.php' );
			
			if ( ! is_null( $name ) ) {
				$file_name = preg_replace( '/[^0-9a-z]+/', '-', strtolower( $name ) );
				$file_name = preg_replace( '/-+/', '-', $file_name );
				$file_name = trim( $file_name, '-' );
				
				$args = array(
					'name'		=> "builder-export-data/export-$guid/builder-export-$file_name",
					'extension'	=> 'zip',
					'rename'	=> false,
				);
				
				$file = ITFileUtility::create_writable_file( $args );
			}
			else {
				$file = ITFileUtility::locate_file( "builder-export-data/export-$guid/builder-export-*.zip" );
				
				if ( is_array( $file ) ) {
					if ( isset( $file[0] ) && is_file( $file[0] ) )
						$file = $file[0];
					else
						$file = new WP_Error( 'builder-import-export-cannot-locate-export', 'Unable to locate the needed export file' );
				}
			}
			
			return $file;
		}
		
		function _init_data_sources() {
			global $builder_import_export_data_sources;
			
			require_once( dirname( __FILE__ ) . '/class.builder-data-source.php' );
			
			foreach ( (array) $builder_import_export_data_sources as $source ) {
				if ( ! empty( $source['file'] ) && file_exists( $source['file'] ) )
					require_once( $source['file'] );
				if ( ! class_exists( $source['class'] ) )
					continue;
				
				
				$object = new $source['class']();
				$var = $object->get_var();
				
				$this->_data_sources[$var] = $object;
			}
		}
		
		function _cleanup_old_temp_directories() {
			it_classes_load( 'it-file-utility.php' );
			
			$directories = ITFileUtility::locate_file( 'deleteme-builder-import-export-*' );
			
			if ( is_wp_error( $directories ) )
				return;
			
			foreach ( (array) $directories as $directory ) {
				$stats = stat( $directory );
				
				if ( ( time() - 3600 ) > $stats['atime'] )
					ITFileUtility::delete_directory( $directory );
			}
		}
		
		function _add_zip_file_content( $zip, $filename, $content ) {
			$zip->add_content( $content, $filename );
			
			if ( is_callable( 'json_encode' ) )
				$zip->add_content( json_encode( $content ), "$filename.json" );
			
			if ( is_callable( 'base64_encode' ) )
				$zip->add_content( base64_encode( serialize( $content ) ), "$filename.base64" );
		}
		
		function _get_file_content( $filename ) {
			if ( is_file( "$filename.json" ) && is_callable( 'json_decode' ) ) {
				$data = json_decode( file_get_contents( "$filename.json" ), true );
				
				if ( null !== $data )
					return $data;
			}
			
			if ( is_file( "$filename.base64" ) && is_callable( 'base64_decode' ) ) {
				$data = @unserialize( base64_decode( file_get_contents( "$filename.base64" ) ) );
				
				if ( false !== $data )
					return $data;
			}
			
			
			$data = unserialize( file_get_contents( $filename ) );
			
			return $data;
		}
	}
}
