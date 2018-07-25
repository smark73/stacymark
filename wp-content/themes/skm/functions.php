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


    //* Enqueue 
    function skm_enqueue_reqs() {
        // fonts
        wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700|Montserrat:400|Oswald:700,400,300', array(), CHILD_THEME_VERSION );
        
        //jquery mobile
        wp_register_style('jquery-mobile-css', '//code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css');
        wp_enqueue_style('jquery-mobile-css');
        //jquery
        //wp_register_script('jquery', '//code.jquery.com/jquery-2.1.4.min.js');
        //wp_enqueue_script('jquery', true);
        wp_enqueue_script( 'jquery');

        //klass
        wp_register_script('klass', '//cdnjs.cloudflare.com/ajax/libs/klass/1.4.0/klass.min.js');
        wp_enqueue_script('klass', true);

        //photoswipe styles
        $src_pswp_css = get_stylesheet_directory_uri() . '/PhotoSwipe/dist/photoswipe.css';
        $src_pswp_skin = get_stylesheet_directory_uri() . '/PhotoSwipe/dist/default-skin/default-skin.css';
        wp_register_style('photoswipe-css', $src_pswp_css);
        wp_enqueue_style('photoswipe-css');
        wp_register_style('photoswipe-skin', $src_pswp_skin);
        wp_enqueue_style('photoswipe-skin');
        
        //photoswipe scripts
        $src_pswp_js = get_stylesheet_directory_uri() . '/PhotoSwipe/dist/photoswipe.min.js';
        $src_pswp_ui_js = get_stylesheet_directory_uri() . '/PhotoSwipe/dist/photoswipe-ui-default.min.js';
        wp_register_script('photoswipe', $src_pswp_js);
        wp_enqueue_script('photoswipe', false);
        wp_register_script('photoswipe-ui', $src_pswp_ui_js);
        wp_enqueue_script('photoswipe-ui', false);

        //isotope
        //$src_iso = get_stylesheet_directory_uri() . '/isotope/dist/isotope.pkgd.min.js';
        //wp_register_script('isotope', '//cdnjs.cloudflare.com/ajax/libs/jquery.isotope/2.2.0/isotope.pkgd.min.js');
        wp_register_script('isotope', '//cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.5/isotope.pkgd.min.js');
        wp_enqueue_script('isotope', true);
        
        // skm styles
        wp_register_style( 'skm-styles', get_stylesheet_directory_uri() . '/style.min.css', array(), CHILD_THEME_VERSION );
        wp_enqueue_style( 'skm-styles' );
        // skm scripts
        wp_register_script('skm-scripts', get_stylesheet_directory_uri(). '/js/script.min.js', true );
        wp_enqueue_script( 'skm-scripts' );

    }
    add_action( 'wp_enqueue_scripts', 'skm_enqueue_reqs' );


    //* Add HTML5 markup structure
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
    //* Add viewport meta tag for mobile browsers
    add_theme_support( 'genesis-responsive-viewport' );
    //* Add support for custom background
    add_theme_support( 'custom-background' );
    //* Add support for 3-column footer widgets
    add_theme_support( 'genesis-footer-widgets', 3 );

    
    // -------------- ENQUE STYLES and SCRIPTS -------------------
    

    function add_reqs_to_head() {
        
    }
    //add_action( 'genesis_meta', 'add_reqs_to_head' );
    
    //add_action('genesis_after_footer', 'add_scripts_to_body_btm');
    //function add_scripts_to_body_btm() {
    //}
    
    
    //---------- FAVICON-------
    add_filter( 'genesis_pre_load_favicon', 'child_favicon_filter' );
    function child_favicon_filter( $favicon_url ) {
            $our_favicon = get_stylesheet_directory_uri() . "/images/favicon.ico";
            return $our_favicon;
    }
    //--------END FAVICON------------
    
    
    //------- HEAD META ---------
    //* Add Viewport meta tag for mobile browsers (requires HTML5 theme support)
    add_theme_support( 'genesis-responsive-viewport' );
    
    add_action( 'genesis_meta', 'child_meta_info' );
    function child_meta_info(){
        ?>
        <meta name="author" content="Stacy Mark">
        <meta name="dcterms.dateCopyrighted" content="2000">
        <meta name="dcterms.rights" content="All Rights Reserved">
        <meta name="dcterms.rightsHolder" content="Stacy Mark">
        <?php
    }
    //--------  END HEAD META ---------
    
    
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
    genesis_register_sidebar( array(
        'id' => 'more-slider',
        'name' => 'More Slider',
        'description' => 'Widget Area to hold a Slider ',
    ));            
    genesis_register_sidebar( array(
        'id' => 'skm-slider',
        'name' => 'SKM Slider',
        'description' => 'Widget Area to hold a Slider ',
    )); 
    genesis_register_sidebar( array(
        'id' => 'icwnaz-slider',
        'name' => 'ICWNAZ Slider',
        'description' => 'Widget Area to hold a Slider ',
    )); 


   //-----------------------------------------
    // CUSTOMIZE OUR HEADER

    remove_action( 'genesis_site_title', 'genesis_seo_site_title' );
    remove_action( 'genesis_site_description', 'genesis_seo_site_description');

    
    
    //* THUMBNAIL Nav !!! NOT USED !!!
    //add_action( 'genesis_header', 'skm_thumbs_nav' );
    function skm_thumbs_nav() {
        //don't show thumbs on web-portfolio page
        if( !is_page('web-portfolio') ){
            if (!is_page('landing-page')){
            ?>
            <div class="skm-title">
                <a href="/" title="Stacy Mark - Paintings">
                    <span class="hdr-name">STACY MARK  <span style="font-size:0.8em;padding:0 5px">|</span>  </span><span class="hdr-paintings">Paintings</span>
                </a>
            </div>
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
    }
    // remove image dimensions
    add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10 );
    add_filter( 'image_send_to_editor', 'remove_thumbnail_dimensions', 10 );
    function remove_thumbnail_dimensions( $html ) {
        $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
        return $html;
    }
    
    //* END HEADER ------------------------------------
    
    
    //* FOOTER Customization
    remove_action( 'genesis_footer', 'genesis_do_footer' );
    add_action( 'genesis_footer', 'skm_custom_footer' );
    function skm_custom_footer() {
        if (!is_page('landing-page')){
            //store ambitionsweb in case want to put back
            $ambitionsweb = '  |  An <a href="http://ambitionsweb.com" target="_blank" title="Ambitions Website Design">Ambitions Web</a> Project</p>';
        ?>
            <p class="copyright" data-enhance="false" data-role="none"><?php echo do_shortcode( '[footer_copyright]');?> <a href="http://stacymark.com/" data-enhance="false" data-role="none">Stacy Mark</a> &middot; All Rights Reserved.  |  <a href="/contact-stacy">Contact Stacy</a>
        <?php
        }
    }
    
    //*------CUSTOM BODY CLASS ON WEB--------------
    add_filter('body_class', 'web_body_class');
    function web_body_class( $classes ){
        if (is_page( 'web-portfolio' )){
            $classes[] = 'web-portfolio';
        }
        if (is_page( 'landing-page' )){
            $classes[] = 'landing-page';
        }
        if (is_page( 'contact-stacy' )){
            $classes[] = 'contact-pg';
        }
        return $classes;
    }
    
    
    // !! NOT USED !!
    //* ----------CUSTOM NAV WALKER FOR SORT PAINTINGS MENU ----------------------
    // add custom data-filter attributes to our "Sort Paintings" menu items
    class sort_paintings_walker_nav_menu extends Walker_Nav_Menu {

        // add classes to ul sub-menus
        function start_lvl( &$output, $depth = 0, $args = array() ) {
            // depth dependent classes
            $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); // code indent
            $display_depth = ( $depth + 1); // because it counts the first submenu as 0
            $classes = array(
                'sub-menu',
                ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
                ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
                'menu-depth-' . $display_depth
                );
            $class_names = implode( ' ', $classes );

            // build html
            $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";
        }

        // add main/sub classes to li's and links
         function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            global $wp_query;
            $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

            // depth dependent classes
            //$depth_classes = array(
                //( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
                //( $depth >=2 ? 'sub-sub-menu-item' : '' ),
                //( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
                //'menu-item-depth-' . $depth
            //);
            //$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

            // passed classes
            $classes = empty( $item->classes ) ? array() : (array) $item->classes;
            $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

            // build html
            $output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . ' ' . $class_names . '">';

            // link attributes
            $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
            //$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
            //$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
            //$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
            $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link ptg-filter-btn' : 'sort-btn' ) . '"';
            
            //add data- attribute to submenu items
            // we enter the data-filter attribute in the WP link item's "url" field since it isn't used normally
            if($depth > 0){
                // cant input a period for the value of the data-filter attr in the WP link item url, so we used a # instead
                // need to replace the # with a .
                $filter_data_val_raw = esc_attr( $item->url );
                $filter_data_val = str_replace('#', '.', $filter_data_val_raw );
                $attributes .= ' data-filter="' . $filter_data_val . '"';
            }

            $item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
                $args->before,
                $attributes,
                $args->link_before,
                apply_filters( 'the_title', $item->title, $item->ID ),
                $args->link_after,
                $args->after
            );

            // build html
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }
    }
    

    //* -------------END ---------------------
    
    
    
    //check if  on DEV or LIVE site
    function live_or_local(){
        if( strpos( $_SERVER['HTTP_HOST'], '.dev') !== false ){
            //on .dev site
            $liveOrLocal = 'local';
        } else {
            $liveOrLocal = 'live';
        }
        return $liveOrLocal;
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


