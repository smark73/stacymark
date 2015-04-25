<?php
/**
 * Functions
 *
 * @package      stacymark.com
 * @author         Stacy Mark <stacy@ambitionsweb.com>
 * @copyright    Copyright (c) 2015, Stacy Mark
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 *
 */

/**
 * Theme Setup
 *
 * This setup function attaches all of the site-wide functions 
 * to the correct actions and filters. All the functions themselves
 * are defined below this setup function.
 *
 */
add_action('genesis_setup', 'child_theme_setup');
function child_theme_setup(){
    
    //* Start the engine
    include_once( get_template_directory() . '/lib/init.php' );

    //* Child theme (do not remove)
    define( 'CHILD_THEME_NAME', 'SKM' );
    define( 'CHILD_THEME_URL', 'http://ambitionsweb.com' );
    define( 'CHILD_THEME_VERSION', '1.0' );

    //* Enqueue Google Fonts
    add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
    function genesis_sample_google_fonts() {
            wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700', array(), CHILD_THEME_VERSION );
    }

    //* Add HTML5 markup structure
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
    //* Add viewport meta tag for mobile browsers
    add_theme_support( 'genesis-responsive-viewport' );
    //* Add support for custom background
    add_theme_support( 'custom-background' );
    //* Add support for 3-column footer widgets
    add_theme_support( 'genesis-footer-widgets', 3 );

    
    //* FOOTER Customization
    remove_action( 'genesis_footer', 'genesis_do_footer' );
    add_action( 'genesis_footer', 'skm_custom_footer' );
    function skm_custom_footer() {
        ?>
        <p>
            &copy; Copyright 2015 <a href="http://stacymark.com/">Stacy Mark</a> &middot; All Rights Reserved &middot; An <a href="http://ambitionsweb.com" target="_blank" title="Ambitions Website Design">AmbitionsWeb</a> Project</p>
        <?php
    }
    
    /**
     * Remove Genesis Page Templates
     *
     * @author Bill Erickson
     * @link http://www.billerickson.net/remove-genesis-page-templates
     *
     * @param array $page_templates
     * @return array
     */
    //function be_remove_genesis_page_templates( $page_templates ) {
        //unset( $page_templates['page_archive.php'] );
        //unset( $page_templates['page_blog.php'] );
        //return $page_templates;
    //}
    //add_filter( 'theme_page_templates', 'be_remove_genesis_page_templates' );
    
}


