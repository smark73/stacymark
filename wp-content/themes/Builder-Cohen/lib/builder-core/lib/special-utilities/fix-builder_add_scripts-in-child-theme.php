<?php

/*
This special utility fixes child themes that define the builder_add_scripts function. Since this is provided in Builder 3.0, this conflict must be resolved. This utility fixes this by updating the child theme files to use 'builder_child_theme_add_scripts' instead.

Written by Chris Jean for iThemes.com
Version 1.0.0

Version History
	1.0.0 - 2011-07-06 - Chris Jean
		Release-ready
*/


if ( ! class_exists( 'BuilderFixBuilderAddScriptsInChildTheme' ) ) {
	class BuilderFixBuilderAddScriptsInChildTheme {
		function BuilderFixBuilderAddScriptsInChildTheme() {
			add_action( 'init', array( $this, 'repair_child_theme' ) );
		}
		
		function repair_child_theme() {
			$parent_theme_path = get_template_directory();
			$child_theme_path = get_stylesheet_directory();
			
			if ( $parent_theme_path == $child_theme_path )
				return;
			
			$functions_file_content = file_get_contents( "$child_theme_path/functions.php" );
			$updated_content = preg_replace( '/\bbuilder_add_scripts\b/', 'builder_child_theme_add_scripts', $functions_file_content );
			
			if ( $functions_file_content != $updated_content ) {
				if ( false === $this->update_file( "$child_theme_path/functions.php", $updated_content ) )
					ITError::admin_warn( 'unable_to_update_child_theme', __( 'In order for Builder to function properly, the child theme must have its <code>builder_add_scripts</code> function (and all references to that function, such as in <code>add_action</code> function calls) replaced with a new name such as <code>builder_child_theme_add_scripts</code>. Builder attempted to do this automatically, but it was unable to do so. This is likely due to file permissions. Either fix the file permissions so that Builder can handle this fix automatically or update your child theme\'s <code>functions.php</code> file manually.', 'it-l10n-Builder-Cohen' ) );
				
				return;
			}
		}
		
		function update_file( $filename, $content ) {
			if ( ! is_writable( $filename ) )
				return false;
			if ( false === ( $file = fopen( $filename, 'w' ) ) )
				return false;
			
			if ( false === fwrite( $file, $content ) ) {
				fclose( $file );
				return false;
			}
			
			fclose( $file );
			return true;
		}
	}
	
	new BuilderFixBuilderAddScriptsInChildTheme();
}
