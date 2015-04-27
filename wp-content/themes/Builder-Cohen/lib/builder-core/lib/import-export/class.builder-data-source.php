<?php

/*
Interface class for all import export data source classes

Written by Chris Jean for iThemes.com
Version 0.0.1

Version History
	0.0.1 - 2010-12-20 - Chris Jean
		Initial version
*/


if ( ! class_exists( 'BuilderDataSource' ) ) {
	class BuilderDataSource {
		function get_name() {
			// return __( 'Data Name', 'it-l10n-Builder-Cohen' );
			return null;
		}
		
		function get_var() {
			// return 'data-var';
			return null;
		}
		
		function get_version() {
			// return builder_get_data_version( 'var' );
			return null;
		}
		
		function get_export_description() {
			// return __( 'Description', 'it-l10n-Builder-Cohen' );
			return '';
		}
		
		function get_export_data() {
			// return array( 'data' );
			return null;
		}
		
		function show_import_methods_form( $form, $info ) {
			$name = $this->get_name();
			
?>
	<p><label><?php $form->add_radio( 'method', 'replace' ); ?> <?php printf( __( '<strong>Replace:</strong> Delete the %1$s from this site and replace with the %1$s in the export file.', 'it-l10n-Builder-Cohen' ), $name ); ?></label></p>
	<p><label><?php $form->add_radio( 'method', 'skip' ); ?> <?php printf( __( '<strong>Skip:</strong> Do not import %1$s from the export file. The site\'s %1$s will remain unchanged.', 'it-l10n-Builder-Cohen' ), $name ); ?></label></p>
<?php
			
		}
	}
}
