<?php

// Remove page header for page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
//remove_action( 'genesis_header', 'skm_hdr_title' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
//remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );


// Remove Page Title
//remove_action( 'genesis_post_title', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'skm_ptg_loop' );


// custom page header
add_action( 'genesis_before_header', 'skm_cust_pg_hdr' );
function skm_cust_pg_hdr() {
    ?>
        <div class="ptgs-hdr">
            <div class="ptg-top-bar">

                <div class="ptg-top-bar-lft">
                    <a href="/" title="Stacy Mark - Paintings">
                        <span class="hdr-name">Stacy Mark</span><span class="hdr-vsep">|</span><span class="hdr-art">ART</span>
                    </a>
                    </div>
                </div>
                
                <div class="ptg-top-bar-rt">
                    <?php get_template_part('templates/paintings-menu') ?>
                </div>
                
                <div class="clearfix"></div>

            </div>
        </div>
    <?php
}



// custom content loop
function skm_ptg_loop() {
    
    global $post;
    //print_r($post);
    
    echo '<div class="clearfix ptg-display">';

	    echo '<a href="' . get_permalink() . '">' . get_the_post_thumbnail( $post->ID, 'full' ) . '</a>';
	    //get_template_part('templates/content', get_post_format());
	    //do_action('genesis_post_content');
	    //the_content();
	    //echo $post->post_title;
	    //$content = get_the_content();
	    //echo $content;
		//echo $post->post_content;
	    
	    echo '<section class="clearfix centered" style="color:#797471;padding: 5px 0 20px;">';

	    	$content = $post->post_content;
	    	echo strip_tags($content);
	    
	    echo '</section>';

	    do_action('genesis_post_content');

    echo '</div>';

}


add_action('genesis_after_footer', 'add_scripts_to_btm');
function add_scripts_to_btm() {
    ?>
        <script type="text/javascript" src="/wp-content/themes/skm/js/ptg-scripts.js"></script>
        <script type="text/javascript" src="/wp-content/themes/skm/js/menu-scripts.js"></script>
<?php
}


//* This file handles single entries, but only exists for the sake of child theme forward compatibility.
genesis();
