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

// add more buttons to editor
function add_more_buttons($buttons) {
 $buttons[] = 'hr';
 $buttons[] = 'del';
 $buttons[] = 'sub';
 $buttons[] = 'sup';
 $buttons[] = 'fontselect';
 $buttons[] = 'fontsizeselect';
 $buttons[] = 'cleanup';
 $buttons[] = 'styleselect';
 return $buttons;
}
add_filter("mce_buttons_3", "add_more_buttons");

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
            wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Montserrat:400|Rock+Salt', array(), CHILD_THEME_VERSION );
    }

    //* Add HTML5 markup structure
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
    //* Add viewport meta tag for mobile browsers
    add_theme_support( 'genesis-responsive-viewport' );
    //* Add support for custom background
    add_theme_support( 'custom-background' );
    //* Add support for 3-column footer widgets
    add_theme_support( 'genesis-footer-widgets', 3 );

    
    //---------- FAVICON-------
    add_filter( 'genesis_pre_load_favicon', 'child_favicon_filter' );
    function child_favicon_filter( $favicon_url ) {
            $our_favicon = get_stylesheet_directory_uri() . "/images/favicon.ico";
            return $our_favicon;
    }
    //--------END FAVICON------------
    
    
    //---- SLIDER WIDGETS ----------------
    genesis_register_sidebar( array(
        'id' => 'paladin-slider',
        'name' => 'Paladin Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));
    genesis_register_sidebar( array(
        'id' => 'tpd-slider',
        'name' => 'Twin Peaks Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));
    genesis_register_sidebar( array(
        'id' => 'kmgn-slider',
        'name' => 'KMGN Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));
    genesis_register_sidebar( array(
        'id' => 'kaff-slider',
        'name' => 'KAFF Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));
    genesis_register_sidebar( array(
        'id' => 'news-slider',
        'name' => 'News Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));
            
    
   //-----------------------------------------
    
    // CUSTOMIZE OUR HEADER
    // remove site title, site description
    // insert thumbnails
    remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
    remove_action( 'genesis_site_description', 'genesis_seo_site_description');
    

    //* THUMBNAIL Nav
    add_action( 'genesis_header', 'skm_thumbs_nav' );
    function skm_thumbs_nav() {
        //don't show thumbs on web-portfolio page
        if(!is_page('web-portfolio')){
        ?>
        <div class="skm-title">
            <a href="/" title="Stacy Mark - Paintings">
                <span class="hdr-name">STACY MARK  <span style="font-size:0.8em;padding:0 5px">|</span>  </span><span class="hdr-paintings">Paintings</span>
            </a>
        </div><!--skm-title-->
            <ul class="ptg-thumbs">
            <?php
            $thumbs = new WP_Query('category_name=Painting');
            $x = 0;
            while ( $thumbs->have_posts() ) {
                $thumbs->the_post();
                global $post;
                echo '<li class="ptg-thumb">';
                echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '</a>';
                echo '</li>';
            }
            echo '</ul><br class="clearfix"/>';
            wp_reset_query();
        }
    }
    
    // remove image dimensions
    add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
    add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );

    function remove_thumbnail_dimensions( $html ) {
        $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
        return $html;
    }
    
    
    //* FOOTER Customization
    remove_action( 'genesis_footer', 'genesis_do_footer' );
    add_action( 'genesis_footer', 'skm_custom_footer' );
    function skm_custom_footer() {
        $saved_aw_credit = ' &middot; An <a href="http://ambitionsweb.com" target="_blank" title="Ambitions Website Design">AmbitionsWeb</a> Project';
        ?>
            <p class="copyright">&copy; Copyright 2015 <a href="http://stacymark.com/">Stacy Mark</a> &middot; All Rights Reserved</p>
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


