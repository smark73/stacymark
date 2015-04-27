<?php

/*
Written by Chris Jean for iThemes.com
Version 1.0.3

Version History
	1.0.0 - 2010-11-03 - Chris Jean
		Initial version
	1.0.1 - 2013-05-21 - Chris Jean
		Removed assign by reference.
	1.0.2 - 2013-06-27 - Chris Jean
		Added check for missing import file to prevent errors when the file is missing.
	1.0.3 - 2013-12-02 - Chris Jean
		Updated calls to screen_icon() to ITUtility::screen_icon().
*/


if ( ! class_exists( 'ITThemeSettingsTabImportExport' ) ) {
	class ITThemeSettingsTabImportExport extends ITThemeSettingsTab {
		var $_var = 'import-export';
		
		
		function _init() {
			$this->_storage = new ITStorage2( 'builder-exports', array( 'version' => builder_get_data_version( 'builder-exports' ), 'autoload' => false ) );
			$this->_exports = $this->_storage->load();
			
			if ( ! is_array( $this->_exports['exports'] ) )
				$this->_exports['exports'] = array();
			
			uasort( $this->_exports['exports'], array( $this, '_sort_exports' ) );
			
			if ( ! empty( $_REQUEST['action'] ) )
				$action = $_REQUEST['action'];
			else if ( ! empty( $_REQUEST['action2'] ) )
				$action = $_REQUEST['action2'];
			
			$cancel = isset( $_REQUEST['cancel'] );
			
			if ( ! empty( $action ) ) {
				if ( ( 'export' === $action ) && ( false === $cancel ) )
					$this->_export();
				else if ( ( 'import' === $action ) && ( false === $cancel ) )
					$this->_import();
				else if ( 'import_methods' === $action )
					$this->_parent->_nonce = "import_methods_guid_{$_REQUEST['guid']}";
				else if ( 'import_customize' === $action )
					$this->_parent->_nonce = "import_customize_guid_{$_REQUEST['guid']}";
				else if ( 'import_conflicts' === $action )
					$this->_parent->_nonce = "import_conflicts_guid_{$_REQUEST['guid']}";
				else if ( 'import_confirm' === $action )
					$this->_parent->_nonce = "import_confirm_guid_{$_REQUEST['guid']}";
				else if ( 'import_run' === $action ) {
					$this->_parent->_nonce = "import_run_guid_{$_REQUEST['guid']}";
					
					if ( false === $cancel )
						add_action( 'admin_init', array( $this, 'run_import' ) );
				}
				else if ( ( 'delete' === $action ) && ( false === $cancel ) )
					$this->_delete();
			}
			
			if ( true === $cancel )
				unset( $_REQUEST['action'] );
			
			builder_add_settings_editor_box( __( 'Site Exports', 'it-l10n-Builder-Cohen' ), null, array( 'var' => 'site_exports', '_builtin' => true, 'tab' => 'import-export' ) );
			builder_add_settings_editor_box( __( 'Export Data', 'it-l10n-Builder-Cohen' ), null, array( 'var' => 'export', '_builtin' => true, 'tab' => 'import-export' ) );
			builder_add_settings_editor_box( __( 'Import Data', 'it-l10n-Builder-Cohen' ), null, array( 'var' => 'import', '_builtin' => true, 'tab' => 'import-export' ) );
		}
		
		function add_admin_scripts() {
			it_classes_load( 'it-file-utility.php' );
			
			wp_enqueue_script( "{$this->_var}-jquery-toolips", ITFileUtility::get_url_from_file( dirname( __FILE__ ) . '/js/tooltip.js' ), array( 'jquery' ) );
		}
		
		function contextual_help( $text, $screen ) {
			ob_start();
			
?>
	<p><?php _e( 'The tools offered here allow you to export settings from or import settings into your Builder theme. This is helpful for transferring specific settings, including Layouts, from one Builder site to another Builder site.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'Exports can also be used as a basic backup. Create a full export once you have your Builder theme configured the way you want it, save the export to your computer, and if you mess up your Layouts at a later time, you can import the Layouts as you originally had them.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The Site Exports section lists all your current exports. Each time you create an export or import an export file, the export will be listed in the Site Exports section. This allows you to easily keep track of the exports and download them as needed. You can also select to import a listed export. This can be helpful to quickly replace Layouts that have been accidentally modified or removed.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The Export Data section allows you to quickly generate an export.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( 'The Import Data section allows you to import an export that you previously download from this or another site. Once you select the export file and click the Import button, you will be given a series of options that help you import the settings as desired.', 'it-l10n-Builder-Cohen' ); ?></p>
	<p><?php _e( '<strong>Note:</strong> The exports created here will only include settings specific to Builder. No WordPress settings, WordPress content, or data from other themes or plugins will be included.', 'it-l10n-Builder-Cohen' ); ?></p>
<?php
			
			$text = ob_get_contents();
			ob_end_clean();
			
			return $text;
		}
		
		function _editor() {
			$action = ( ! empty( $_REQUEST['action'] ) ) ? $_REQUEST['action'] : '';
			
			if ( 'import_methods' === $action )
				$this->_show_import_methods_screen();
			else if ( 'import_customize' === $action )
				$this->_show_import_customize_screen();
			else if ( 'import_conflicts' === $action )
				$this->_show_import_conflicts_screen();
			else if ( 'import_confirm' === $action )
				$this->_show_import_confirm_screen();
			else
				$this->_editor_main();
		}
		
		function _editor_main() {
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$exporter = new BuilderImportExport();
			$this->_data_sources = $exporter->get_data_sources();
			
			if ( ! empty( $_REQUEST['imported'] ) && isset( $this->_exports['exports'][$_REQUEST['imported']] ) ) {
				ITUtility::show_status_message( sprintf( __( 'Successfully imported &quot;%s&quot;', 'it-l10n-Builder-Cohen' ), $this->_exports['exports'][$_REQUEST['imported']]['name'] ) );
				
				if ( ! empty( $_REQUEST['errors'] ) ) {
					foreach ( (array) explode( ',', $_REQUEST['errors'] ) as $error ) {
						list( $var, $error ) = explode( ':', $error );
						ITUtility::show_error_message( sprintf( __( 'Problem occurred when importing %1$s. Error code: %2$s', 'it-l10n-Builder-Cohen' ), $this->_data_sources[$var], $error ) );
					}
				}
			}
			else {
				if ( ! empty( $this->_errors ) ) {
					foreach ( (array) $this->_errors as $error )
						ITUtility::show_error_message( $error );
				}
			}
			
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		<?php $this->_print_editor_tabs(); ?>
		
		<p><?php _e( 'For information about this page, please click the "Help" button at the top right.', 'it-l10n-Builder-Cohen' ); ?></p>
		
		<?php $this->_print_meta_boxes( null ); ?>
		
		<form style="display:none" method="get" action="">
			<p>
				<?php wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false ); ?>
				<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
			</p>
		</form>
	</div>
	
	<?php $this->_init_meta_boxes(); ?>
<?php
			
		}
		
		
		// Basic Meta Boxes //////////////////////////////////////
		
		function meta_box_site_exports() {
			$form = new ITForm();
			
			if ( ! empty( $_GET['exported'] ) && isset( $this->_exports['exports'][$_GET['exported']]['name'] ) ) {
				ITUtility::show_status_message( sprintf( __( 'Created export: %s', 'it-l10n-Builder-Cohen' ), $this->_exports['exports'][$_GET['exported']]['name'] ) );
			}
			if ( ! empty( $_GET['deleted'] ) ) {
				if ( $_GET['deleted'] > 1 )
					ITUtility::show_status_message( sprintf( __( '%d exports deleted.', 'it-l10n-Builder-Cohen' ), $_GET['deleted'] ) );
				else if ( $_GET['deleted'] > 0 )
					ITUtility::show_status_message( __( 'Export deleted.', 'it-l10n-Builder-Cohen' ) );
			}
			
?>
	<?php $form->start_form( array(), 'site_exports' ); ?>
		<p><?php _e( 'The following listing shows export files created on this site or imported from another site. These exports can be imported into this site (in order to revert changes made to the site\'s settings) or can be downloaded for importing into another site.', 'it-l10n-Builder-Cohen' ); ?></p>
		<hr />
		
		<?php if ( empty( $this->_exports['exports'] ) ) : ?>
			<p><?php printf( __( 'No exports have been created on or imported. Please use either the <a href="%1$s">Export Data</a> or <a href="%2$s">Import Data</a> options to load data.', 'it-l10n-Builder-Cohen' ), '#import-export-export', '#import-export-import' ); ?></p>
		<?php else: ?>
			<?php ob_start(); ?>
				<tr class="thead">
					<th scope="col" class="check-column"><input type="checkbox" id="check-all-groups" /></th>
					<th><?php _e( 'Name', 'it-l10n-Builder-Cohen' ); ?></th>
					<th><?php _e( 'Export Date and Time', 'it-l10n-Builder-Cohen' ); ?></th>
					<th title="<?php _e( 'The types of data contained inside the export.', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Contents', 'it-l10n-Builder-Cohen' ); ?></th>
					<th title="<?php _e( 'This is the version of Builder that created the export.', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Builder Version', 'it-l10n-Builder-Cohen' ); ?></th>
					<th title="<?php _e( 'This is the site that created the export.', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Source Site', 'it-l10n-Builder-Cohen' ); ?></th>
					<th title="<?php _e( 'This is the user who created the export.', 'it-l10n-Builder-Cohen' ); ?>"><?php _e( 'Exported By', 'it-l10n-Builder-Cohen' ); ?></th>
					<th><?php _e( 'Download', 'it-l10n-Builder-Cohen' ); ?></th>
				</tr>
			<?php
				$header = ob_get_contents();
				ob_end_clean();
			?>
			
			<div class="tablenav top">
				<div class="alignleft actions">
					<?php $form->add_drop_down( 'action', array( '' => __( 'Bulk Actions' ), 'delete' => __( 'Delete' ) ) ); ?>
					<?php $form->add_submit( 'bulk_action', array( 'class' => 'button-secondary action', 'value' => __( 'Apply' ) ) ); ?>
				</div>
			</div>
			
			<table cellspacing="0" class="widefat fixed" id="site-exports">
				<thead>
					<?php echo $header; ?>
				</thead>
				<tfoot>
					<?php echo $header; ?>
				</tfoot>
				<tbody>
					<?php $count = 0; ?>
					<?php foreach ( (array) $this->_exports['exports'] as $guid => $info ) : ?>
						<?php
							$timestamp = gmdate( 'Y-m-d H:i:s', ( $info['timestamp'] + ( get_option( 'gmt_offset' ) * 3600 ) ) );
							
							$import_link = wp_nonce_url( "{$this->_parent->_self_link}&amp;action=import&amp;guid=$guid", "import_guid_$guid" );
							$delete_link = wp_nonce_url( "{$this->_parent->_self_link}&amp;action=delete&amp;guid=$guid", "delete_guid_$guid" );
							
							
							$data_sources = array();
							
							foreach ( (array) $info['data_sources'] as $source )
								$data_sources[] = $source['name'];
							
							natcasesort( $data_sources );
							$contents = implode( ', ', $data_sources );
							
							$class = ( $count++ % 2 ) ? '' : 'alternate';
						?>
						<tr class="<?php echo $class; ?>" id="entry-<?php echo $guid; ?>">
							<th scope="row" class="check-column"><input type="checkbox" name="guid[]" class="administrator exports" value="<?php echo $guid; ?>" /></th>
							<td>
								<strong><a title="<?php echo esc_attr( sprintf( __( 'Import data from %s', 'it-l10n-Builder-Cohen' ), $info['name'] ) ); ?>" href="<?php echo esc_html( $import_link ); ?>"><?php echo $info['name']; ?></a></strong>
								<br/>
								<div class="row-actions">
									<span class="import"><a title="<?php echo esc_attr( sprintf( __( 'Import data from %s', 'it-l10n-Builder-Cohen' ), $info['name'] ) ); ?>" href="<?php echo esc_html( $import_link ); ?>"><?php _e( 'Import', 'it-l10n-Builder-Cohen' ); ?></a> | </span>
									<span class="delete"><a href="<?php echo esc_attr( $delete_link ); ?>" onclick='return showNotice.warn();'><?php _e( 'Delete', 'it-l10n-Builder-Cohen' ); ?></a></span>
								</div>
							</td>
							<td><?php echo $timestamp; ?></td>
							<td><?php echo $contents; ?></td>
							<td><?php echo $info['builder_version']; ?></td>
							<td><a href="<?php echo esc_attr( $info['site_url'] ); ?>"><?php echo $info['site_url']; ?></a></td>
							<td><?php echo $info['exported_by']; ?></td>
							<td><a href="<?php echo esc_attr( $info['url'] ); ?>"><?php echo basename( $info['file'] ); ?></a></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			
			<div class="tablenav bottom">
				<div class="alignleft actions">
					<?php $form->add_drop_down( 'action2', array( '' => __( 'Bulk Actions' ), 'delete' => __( 'Delete' ) ) ); ?>
					<?php $form->add_submit( 'bulk_action', array( 'class' => 'button-secondary action', 'value' => __( 'Apply' ) ) ); ?>
				</div>
			</div>
		<?php endif; ?>
		
		<?php $form->add_hidden_no_save( 'editor_tab', $this->_parent->_active_tab ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		function meta_box_export() {
			$options = array();
			
			if ( ! empty( $_GET['export_error'] ) ) {
				$errors = explode( ',', $_GET['export_error'] );
				
				if ( in_array( 'name', $errors ) )
					ITUtility::show_inline_error_message( 'You must supply a name for the export.' );
			}
			
			foreach ( (array) $this->_data_sources as $var => $name )
				$options['data_sources'][] = $var;
			
			$form = new ITForm( $options );
			
?>
	<?php $form->start_form( array(), 'export_data' ); ?>
		<p><?php _e( 'A Builder export file can be used to easily migrate settings, layouts, and views to another site. For example, transferring settings from a development site to a production site. Export files can also be used as very basic backups of Builder settings.', 'it-l10n-Builder-Cohen' ); ?></p>
		<hr />
		
		<p><?php _e( 'Create a name for this export:', 'it-l10n-Builder-Cohen' ); ?></p>
		<ul class="no-bullets">
			<li><label for="name"><?php $form->add_text_box( 'name' ); ?></label></li>
		</ul>
		
		<p><?php _e( 'Select what the export file should contain:', 'it-l10n-Builder-Cohen' ); ?></p>
		<ul class="no-bullets">
			<?php foreach ( (array) $this->_data_sources as $var => $name ) : ?>
				<li><label for="data_sources-<?php echo $var; ?>"><?php $form->add_multi_check_box( 'data_sources', $var ); ?> <?php echo $name; ?></li>
			<?php endforeach; ?>
		</ul>
		
		<p>
			<?php $form->add_submit( 'export', array( 'value' => 'Create Export', 'class' => 'button-secondary' ) ); ?>
		</p>
		
		<?php $form->add_hidden_no_save( 'action', 'export' ); ?>
		<?php $form->add_hidden_no_save( 'editor_tab', $this->_parent->_active_tab ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		function meta_box_import() {
			$form = new ITForm();
			
?>
	<?php $form->start_form( array(), 'import_data' ); ?>
		<p><?php _e( 'Importing a Builder export file allows you to apply/add some or all of the export data to the site. Importing the data also also adds the uploaded export file to the list of Site Exports for use later.', 'it-l10n-Builder-Cohen' ); ?></p>
		<hr />
		
		<p><?php printf( __( 'Select export file to import:', 'it-l10n-Builder-Cohen' ) ); ?></p>
		<ul class="no-bullets">
			<li><?php $form->add_file_upload( 'import_file' ); ?></li>
		</ul>
		<br />
		
		<p>
			<?php $form->add_submit( 'import', array( 'value' => 'Import', 'class' => 'button-secondary' ) ); ?>
		</p>
		
		<?php $form->add_hidden_no_save( 'action', 'import' ); ?>
		<?php $form->add_hidden_no_save( 'editor_tab', $this->_parent->_active_tab ); ?>
	<?php $form->end_form(); ?>
<?php
			
		}
		
		
		// Importer Screens //////////////////////////////////////
		
		function _show_import_methods_screen() {
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$import = new BuilderImportExport( $_REQUEST['guid'] );
			
			$info = $import->get_info();
			
			$details = array(
				'name'            => __( 'Name', 'it-l10n-Builder-Cohen' ),
				'timestamp'       => __( 'Date and Time', 'it-l10n-Builder-Cohen' ),
				'exported_by'     => __( 'Exported By', 'it-l10n-Builder-Cohen' ),
				'builder_version' => __( 'Builder Version', 'it-l10n-Builder-Cohen' ),
				'site_url'        => __( 'Site Address', 'it-l10n-Builder-Cohen' ),
			);
			
			
			$form = new ITForm( array(), true );
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		<h2>Basic Import Options</h2>
		
		<?php if ( is_wp_error( $info ) ) : ?>
			<?php ITUtility::show_error_message( __( 'Error: The selected input file could not be found. Please select a different file to import.', 'it-l10n-Builder-Cohen' ) ); ?>
			
			<p><a href="<?php echo esc_url( $this->_parent->_self_link ); ?>"><?php _e( '&larr; Back to Import/Export Settings', 'it-l10n-Builder-Cohen' ); ?></a></p>
		<?php else : ?>
			<p><?php _e( 'Selected export file details:', 'it-l10n-Builder-Cohen' ); ?></p>
			
			<table style="text-align:left;margin-left:20px;">
				<?php foreach( (array) $details as $var => $description ) : ?>
					<?php
						$val = $info[$var];
						
						if ( 'timestamp' === $var )
							$val = gmdate( 'Y-m-d H:i:s', ( $info['timestamp'] + ( get_option( 'gmt_offset' ) * 3600 ) ) );
						if ( 'site_url' === $var )
							$val = "<a href='" . esc_attr( $val ) . "'>$val</a>";
					?>
					<tr><th scope="row" style="padding-right:20px;"><?php echo $description; ?></th>
						<td><?php echo $val; ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
			<br />
			
			<p><?php _e( 'Use the following options to select how to import each type of data contained in the export:', 'it-l10n-Builder-Cohen' ); ?></p>
			
			<?php $form->start_form( array(), "import_customize_guid_{$_REQUEST['guid']}" ); ?>
				<?php $import->show_data_source_import_methods( $form ); ?>
				
				<p class="submit">
					<?php $form->add_submit( 'next', __( 'Next Step', 'it-l10n-Builder-Cohen' ) ); ?>
					<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary' ) ); ?>
				</p>
				
				<?php $form->add_hidden_no_save( 'guid', $_REQUEST['guid'] ); ?>
				<?php $form->add_hidden_no_save( 'action', 'import_customize' ); ?>
				<?php $form->add_hidden_no_save( 'editor_tab', 'import-export' ); ?>
			<?php $form->end_form(); ?>
		<?php endif; ?>
	</div>
<?php
			
		}
		
		function _show_import_customize_screen() {
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$import = new BuilderImportExport( $_REQUEST['guid'] );
			
			$form = new ITForm( array(), array( 'compact_used_inputs' => true ) );
			
			
			ob_start();
			$has_customizations = $import->show_data_source_import_customizations( $form );
			$import_customizations = ob_get_contents();
			ob_end_clean();
			
			if ( false === $has_customizations ) {
				$this->_show_import_conflicts_screen( $import );
				return;
			}
			
			
			$info = $import->get_info();
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		<h2>Customize Import Options</h2>
		
		<p><?php _e( 'The following options allow you to customize how certain types of data are imported into the site. Any conflicts that may occur can be resolved in the next screen.', 'it-l10n-Builder-Cohen' ); ?></p>
		
		<?php $form->start_form( array(), "import_conflicts_guid_{$_REQUEST['guid']}" ); ?>
			<?php echo $import_customizations; ?>
			
			<p class="submit">
				<?php $form->add_submit( 'next', __( 'Next Step', 'it-l10n-Builder-Cohen' ) ); ?>
				<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary' ) ); ?>
			</p>
			
			<?php $form->add_hidden_no_save( 'guid', $_REQUEST['guid'] ); ?>
			<?php $form->add_hidden_no_save( 'action', 'import_conflicts' ); ?>
			<?php $form->add_hidden_no_save( 'editor_tab', 'import-export' ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
			
		}
		
		function _show_import_conflicts_screen() {
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$import = new BuilderImportExport( $_REQUEST['guid'] );
			
			$form = new ITForm( array(), true );
			
			
			ob_start();
			$has_conflicts = $import->show_data_source_import_conflicts( $form );
			$import_conflicts = ob_get_contents();
			ob_end_clean();
			
			if ( false === $has_conflicts ) {
				$this->_show_import_confirm_screen( $import );
				return;
			}
			
			
			$info = $import->get_info();
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		<h2>Resolve Import Conflicts</h2>
		
		<p><?php //_e( ' ?></p>
		
		<?php $form->start_form( array(), "import_confirm_guid_{$_REQUEST['guid']}" ); ?>
			<?php echo $import_conflicts; ?>
			
			<p class="submit">
				<?php $form->add_submit( 'next', __( 'Next Step', 'it-l10n-Builder-Cohen' ) ); ?>
				<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary' ) ); ?>
			</p>
			
			<?php $form->add_hidden_no_save( 'guid', $_REQUEST['guid'] ); ?>
			<?php $form->add_hidden_no_save( 'action', 'import_confirm' ); ?>
			<?php $form->add_hidden_no_save( 'editor_tab', 'import-export' ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
			
		}
		
		function _show_import_confirm_screen( $import = null ) {
			if ( is_null( $import ) ) {
				require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
				$import = new BuilderImportExport( $_REQUEST['guid'] );
			}
			
			$info = $import->get_info();
			
			$details = array(
				'name'				=> __( 'Name', 'it-l10n-Builder-Cohen' ),
				'timestamp'			=> __( 'Date and Time', 'it-l10n-Builder-Cohen' ),
				'exported_by'		=> __( 'Exported By', 'it-l10n-Builder-Cohen' ),
				'builder_version'	=> __( 'Builder Version', 'it-l10n-Builder-Cohen' ),
				'site_url'			=> __( 'Site Address', 'it-l10n-Builder-Cohen' ),
			);
			
			
			$form = new ITForm( array(), true );
			
?>
	<div class="wrap">
		<?php ITUtility::screen_icon(); ?>
		<h2>Confirm Import Options</h2>
		
		<p><?php _e( 'Selected export file details:', 'it-l10n-Builder-Cohen' ); ?></p>
		
		<table style="text-align:left;margin-left:20px;">
			<?php foreach( (array) $details as $var => $description ) : ?>
				<?php
					$val = $info[$var];
					
					if ( 'timestamp' === $var )
						$val = gmdate( 'Y-m-d H:i:s', ( $info['timestamp'] + ( get_option( 'gmt_offset' ) * 3600 ) ) );
					if ( 'site_url' === $var )
						$val = "<a href='" . esc_attr( $val ) . "'>$val</a>";
				?>
				<tr><th scope="row" style="padding-right:20px;"><?php echo $description; ?></th>
					<td><?php echo $val; ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<br />
		
		<p><?php _e( 'The data will be imported with the following settings:', 'it-l10n-Builder-Cohen' ); ?></p>
		
		<?php $form->start_form( array(), "import_run_guid_{$_REQUEST['guid']}" ); ?>
			<?php $import->show_data_source_import_process( $form ); ?>
			
			<p class="submit">
				<?php $form->add_submit( 'run', __( 'Run Import', 'it-l10n-Builder-Cohen' ) ); ?>
				<?php $form->add_submit( 'cancel', array( 'value' => __( 'Cancel', 'it-l10n-Builder-Cohen' ), 'class' => 'button-secondary' ) ); ?>
			</p>
			
			<?php $form->add_hidden_no_save( 'guid', $_REQUEST['guid'] ); ?>
			<?php $form->add_hidden_no_save( 'action', 'import_run' ); ?>
			<?php $form->add_hidden_no_save( 'editor_tab', 'import-export' ); ?>
		<?php $form->end_form(); ?>
	</div>
<?php
			
		}
		
		
		// Main Functions //////////////////////////////////////
		
		function _export() {
			ITForm::check_nonce( 'export_data' );
			
			
			if ( empty( $_POST['name'] ) ) {
				$redirect = "{$this->_parent->_self_link}&export_error=name#import-export-export";
				wp_redirect( $redirect );
				exit;
			}
			
			
			$post_data = ITForm::get_post_data();
			
			
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$exporter = new BuilderImportExport();
			
			$export = $exporter->get_export( $post_data['name'], $post_data['data_sources'] );
			
			
			$guid = $export['guid'];
			unset( $export['guid'] );
			
			$this->_exports['exports'][$guid] = $export;
			
			$this->_storage->save( $this->_exports );
			
			
			$redirect = "{$this->_parent->_self_link}&exported=$guid";
			wp_redirect( $redirect );
			exit;
		}
		
		function _delete() {
			$count = 0;
			$guids = array();
			
			if ( ! isset( $_REQUEST['guid'] ) )
				ITForm::check_nonce( 'site_exports' );
			else if ( is_array( $_REQUEST['guid'] ) || ! isset( $_REQUEST['guid'] ) ) {
				ITForm::check_nonce( 'site_exports' );
				$guids = $_REQUEST['guid'];
			}
			else {
				ITForm::check_nonce( "delete_guid_{$_GET['guid']}" );
				$guids = array( $_GET['guid'] );
			}
			
			$count = count( $guids );
			
			it_classes_load( 'it-file-utility.php' );
			
			foreach ( (array) $guids as $guid ) {
				if ( isset( $this->_exports['exports'][$guid] ) ) {
					ITFileUtility::delete_directory( dirname( $this->_exports['exports'][$guid]['file'] ) );
					
					unset( $this->_exports['exports'][$guid] );
				}
			}
			
			if ( $count > 0 )
				$this->_storage->save( $this->_exports );
			
			
			$redirect = "{$this->_parent->_self_link}&deleted=$count";
			wp_redirect( $redirect );
			exit;
		}
		
		function _import() {
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			
			if ( isset( $_POST['action'] ) ) {
				ITForm::check_nonce( 'import_data' );
				
				if ( ( 0 != $_FILES['import_file']['error'] ) || ! is_file( $_FILES['import_file']['tmp_name'] ) ) {
					$redirect = "{$this->_parent->_self_link}&import_error=file#import-export-import";
					wp_redirect( $redirect );
					exit;
				}
				
				
				$importer = new BuilderImportExport( $_FILES['import_file']['tmp_name'] );
				
				if ( true === $importer->had_error() ) {
					$this->_errors = $importer->get_errors();
					unset( $_REQUEST['action'] );
					$this->_parent->_nonce = 'import_data';
					
					return;
				}
				
				$guid = $importer->get_guid();
				$info = $importer->get_info();
				
				$this->_exports['exports'][$guid] = $info;
				$this->_storage->save( $this->_exports );
			}
			else {
				ITForm::check_nonce( "import_guid_{$_GET['guid']}" );
				
				if ( ! isset( $this->_exports['exports'][$_GET['guid']] ) ) {
					$redirect = "{$this->_parent->_self_link}&site_import_error=file#import-export-site_exports";
					wp_redirect( $redirect );
					exit;
				}
				
				
				$importer = new BuilderImportExport( $_GET['guid'] );
				
				$guid = $_GET['guid'];
			}
			
			
			$redirect = "{$this->_parent->_self_link}&action=import_methods&guid=$guid";
			$redirect = add_query_arg( '_wpnonce', wp_create_nonce( "import_methods_guid_$guid" ), $redirect );
			wp_redirect( $redirect );
			exit;
		}
		
		function run_import() {
			ITForm::check_nonce( "import_run_guid_{$_REQUEST['guid']}" );
			
			
			require_once( dirname( __FILE__ ) . '/class.builder-import-export.php' );
			$import = new BuilderImportExport( $_REQUEST['guid'] );
			
			$result = $import->run_import();
			
			$errors = '';
			if ( is_array( $result ) )
				$errors = '&errors=' . implode( ',', $result );
			
			$redirect = "{$this->_parent->_self_link}&imported={$_REQUEST['guid']}$errors";
//			echo "<p>Redirect: $redirect</p>\n";
			wp_redirect( $redirect );
			exit;
		}
		
		
		// Utility Functions //////////////////////////////////////
		
		function _sort_exports( $a, $b ) {
			return strcasecmp( $a['name'], $b['name'] );
		}
		
		function _get_export_destination_path( $guid ) {
			
		}
	}
}
