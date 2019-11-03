<?php

// Remove page header for front page
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
//remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'child_custom_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

// use just name for header
add_action( 'genesis_header', 'web_portfolio_hdr' );

// Remove Page Title
remove_action( 'genesis_post_title', 'genesis_do_post_title' );
remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

// Content Area
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'web_portfolio_loop' );


function web_portfolio_hdr() {
    ?>
        <div class="web-portfolio-top-bar">
            <a href="/web-portfolio" title="Stacy Mark - Web Projects">
                STACY MARK  <span class="vert-sep"> | </span> <span class="hdr-web">Web Portfolio</span>
            </a>
            <span class="vert-sep">|</span> 
            <a href="/wp-content/uploads/stacy-mark-resume.pdf" target="_blank">
                Resum&eacute;
            </a>
            <span class="vert-sep">|</span> 
            <a href="https://github.com/smark73" title="GitHub" target="_blank">
                GitHub
            </a>
            <span class="vert-sep">|</span> 
            <a class="web-contact-toggle">
                Contact
            </a>
            <div class="web-contact web-contact-hide hidden">
                (928) 225-9830   |   <a href="mailto:stacy@stacymark.com">stacy@stacymark.com</a>
                <div class="web-contact-form">
                    <?php echo do_shortcode( '[gravityform id="1" title="false" description="false" ajax="true"]' );?>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php
}

function web_portfolio_loop(){

    global $post;

    // check if post password protection enabled and entered
    $need_pw = ( post_password_required() ) ? true : false;

    if( $need_pw === false || !post_password_required() ) {
    ?>

    <div class="web-portfolio-hdr">

        <div class="one-half first web-portfolio-left">
            <h2>About Stacy</h2>
            <p class="about-stacy">My background in web development and graphic design make me a well rounded asset for any web team.  I have an eye for detail and problem-solving aptitude that allows me to fill many roles competently.  For more than 10 years I have combined these skills to handle multiple tasks such as:<br/><span class="about-hl">UI/UX Design</span> &CenterDot; <span class="about-hl">Website Development</span> &CenterDot; <span class="about-hl">Custom WordPress Themes</span> &CenterDot; <span class="about-hl">WordPress Plugins</span> &CenterDot; <span class="about-hl">SEO and Analytics</span> &CenterDot; <span class="about-hl">Email Marketing Campaigns</span>
        </div>

        <div class="one-half web-portfolio-right">

            <div class="web-portfolio-right-tech">
                <h4 class="web-tech-hdr">Development and Design Experience:</h4>
                <div class="proficiency-list">
                    <ul class="prof-list-3">
                        <li>Linux</li>
                        <li>Apache</li>
                        <li>Vagrant</li>
                        <li>Adobe CC</li>
                        <li>Photoshop</li>
                        <li>Illustrator</li>
                        <li>Flash/ActionScript</li>
                    </ul>
                    <ul class="prof-list-1">
                        <li>React</li>
                        <li>WordPress</li>
                        <li>Bootstrap</li>
                        <li>Bourbon/Neat</li>
                        <li>Symfony2</li>
                    </ul>
                    <ul class="prof-list-2">
                        <li>HTML(5)</li>
                        <li>CSS(3)</li>
                        <li>SCSS/SASS/LESS</li>
                        <li>JavaScript</li>
                        <li>jQuery</li>
                        <li>PHP</li>
                        <li>MySQL</li>
                        <li>Gulp/Grunt</li>
                        <li>Git</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class='clearfix'></div>

    </div>
    <div class="webport-wrap">
        <h2 class="project-showcase">Projects</h2>


        <div class="webport">
            <div class="one-third first">
                <h3>Great Circle Media / KAFF News</h3>
                <a href="http://gcmaz.com" target="_blank">gcmaz.com &raquo;</a>
                <br/>
                <a href="http://gcmaz.com/kaff-news" target="_blank">kaffnews.com &raquo;</a>
                <p>Great Circle Media needed an easy to use CMS for their KAFF News team to post stories and interact with the audience, as well as a centralized CMS for all the radio stations in the media group.  This centralized WordPress CMS is used by the "promotions" teams to create posts for all the external radio station websites.</p>
                <p class="webtech">HTML5, CSS3, SCSS, Gulp, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'news-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="webport">
            <div class="one-third first">
                <h3>Stacy Mark - Paintings Portfolio</h3>
                <a href="/paintings-abstract-landscape/" target="_blank">Open Gallery &raquo;</a>
                <p>This is an online gallery of my artwork.  I wanted a touch-friendly interface and make use of JavaScript for the enhanced UI on mobile devices.  It makes use of a responsive grid and touch swipe capabilities for effectiveness on all screens.</p>
                <p class="webtech">HTML5, CSS3, JavaScript, jQuery, PhotoSwipe, Isotope Grid, Git, Mobile UI</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'skm-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="webport">
            <div class="one-third first">
                <h3>It Can Wait (Northern Arizona)</h3>
                <a href="http://itcanwaitnaz.com" target="_blank">itcanwaitnaz.com &raquo;</a>
                <p>Northern Arizona's "It Can Wait" Campaign to stop texting while triving.  Display's pledges in a responsive grid for viewability on all screens.  The UI encourages users to view and share pledges.</p>
                <p class="webtech">HTML5, CSS3, JavaScript, jQuery, Git</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'icwnaz-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="webport">
            <div class="one-third first">
                <h3>Paladin Radio</h3>
                <a href="http://paladinradio.com" target="_blank">paladinradio.com &raquo;</a>
                <p>Paladin Radio asked me for an update to their website which reflected their business in a professional way and would allow for easy content management.  The end result is a custom themed WordPress site which is mobile responsive and captures the awesomeness that is Paladin.</p>
                <p class="webtech">WordPress, HTML5, CSS3, JavaScript, jQuery, PHP, MySQL, Git, Photoshop</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'paladin-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>


        <div class="webport">
            <div class="one-third first">
                <h3>Twin Peaks Digital</h3>
                <a href="http://twinpeaksdigital.com" target="_blank">twinpeaksdigital.com &raquo;</a>
                <p>Twin Peaks Digital asked me to refresh their identity with a brighter, more modern look.  I supplied mood boards in which colors were chosen and updated their logo design.  Then a new site was created with flat-design and mobile responsiveness as the guide.  I have also worked aggressively on the SEO of this site, to maintain a high position in Google for specific search terms that are important to the business.</p>
                <p class="webtech">WordPress, HTML5, CSS3, JavaScript, jQuery, PHP, MySQL, Git, Photoshop, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'tpd-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>
        
        
        <div class="webport">
            <div class="one-third first">
                <h3>Landing Pages</h3>
                <a href="/web-portfolio/landing-page/" target="_blank">View Example &raquo;</a>
                <p>Here's an example of a landing page that demonstrates my understanding of a call-to-action with high probability of success in attaining customer information.</p>
                <p class="webtech">HTML5, CSS3, Photoshop, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <a href="/web-portfolio/landing-page/" target="_blank"><img src="/wp-content/uploads/slider-land-page.jpg" width="790" height="420 "style="width:auto;height:auto;margin:auto"/></a>
            </div>
            <div class="clearfix"></div>
        </div>
        

        <div class="webport">
            <div class="one-third first">
                <h3>93-9 The Mountain</h3>
                <a href="http://939themountain.gcmaz.com" target="_blank">939themountain.com &raquo;</a>
                <p>93-9 The Mountain is Northern Arizona's premiere rock station.  This website uses my customized <i>radio station</i> WordPress theme just like it's sister stations.  Just like "Nascar", radio stations can be excessive in their graphic design choices, and these themes reflect that.  Every spot on the web page has potential to inject banner ads or page take over graphics.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'kmgn-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>

        
        <div class="webport">
            <div class="one-third first">
                <h3>And more ...</h3>
                <p>My custom <i>radio station</i> WordPress theme is used on multiple radio station websites.  This theme is highly 'ad-driven' for sales of banner ads and page takeovers.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'more-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>
        
    </div>

    <?php
    } else {
        echo "<div style='width:60%;font-weight:500;margin:auto;'>";
        the_content();
        echo "</div>";
    }

}


// footer scripts
add_action('genesis_after_footer', 'add_scripts_to_btm');
function add_scripts_to_btm() {
    ?>
    <script type="text/javascript">
        // START toggle web-contact in navbar
        jQuery(function($){
            //store our targets in vars
            var $webContactToggle = jQuery(document).find('.web-contact-toggle');
            var $webContact = jQuery(document).find('.web-contact');
            var $webContactForm = jQuery(document).find('.web-contact-form');

            //init search-form styles and classes
            $webContactForm.addClass('hidden');
            $webContactForm.css({opacity:0});
            //$webContact.hide();

            //toggle function
            $webContactToggle.click(function(){
                //web-contact-nav is hidden until first click (otherwise shows on slow page loads)
                $webContact.removeClass('hidden');
                //$webContact.show();
                //
                $webContact.toggleClass('web-contact-hide web-contact-show');
                
                if(($webContactForm).hasClass('hidden')){
                    var webContactWait;
                    clearTimeout(webContactWait);
                    webContactWait = setTimeout(function(){$webContactForm.toggleClass('hidden visible').animate({opacity:1});} , 100);
                } else {
                    $webContactForm.animate({opacity:0}).toggleClass('hidden visible');
                }
            });
        });
        // END
    </script>
    <?php
}

//NOT USED EXTRA SAVED
//            <div class="webport">
//            <div class="one-third first">
//                <h3>92.9 KAFF Country</h3>
//                <a href="http://kaff.gcmaz.com" target="_blank">kaff.com</a>
//                <p>92.9 KAFF is Northern Arizona's number one modern country radio station.  My work on this site has been ongoing and I have developed a very customized WordPress theme for the Great Circle Media group of websites of which this station is a part.  Since this is a radio station, BOLD and IN YOUR FACE is the style.  Every spot on the web page has potential to inject banner ads or page take over graphics.</p>
//                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
//            </div>
//            <div class="two-thirds">
//                <?php genesis_widget_area( 'kaff-slider' ); ? //>
//            </div>
//            <div class="clearfix"></div>
//        </div>
    

// genesis child theme
genesis();
