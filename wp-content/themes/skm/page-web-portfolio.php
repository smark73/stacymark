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
        <div class="skm-title web-portfolio-hdr">
            <a href="/web-portfolio" title="Stacy Mark - Web Projects">
                <span class="hdr-name" style="color:#f5f5f5;">STACY MARK  <span style="font-size:0.8em;padding:0 5px">|</span>  </span><span class="hdr-web">Latest Web Projects</span>
            </a>
        </div>
    <?php
}

function web_portfolio_loop(){
    ?>

    <div class="webport-wrap">
        
        <div class="webport">
            <div class="one-third first">
                <h3>Paladin Radio</h3>
                <a href="http://paladinradio.com" target="_blank">paladinradio.com</a>
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
                <a href="http://paladinradio.com" target="_blank">twinpeaksdigital.com</a>
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
                <h3>93-9 The Mountain</h3>
                <a href="http://939themountain.gcmaz.com" target="_blank">939themountain.com</a>
                <p>93-9 The Mountain is Northern Arizona's premiere rock station.  My work on this site has been ongoing and I have developed a very customized WordPress theme for the Great Circle Media group of websites of which this station is a part.  Since this is a radio station, BOLD and IN YOUR FACE is the style.  Every spot on the web page has potential to inject banner ads or page take over graphics.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'kmgn-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="webport">
            <div class="one-third first">
                <h3>92.9 KAFF Country</h3>
                <a href="http://kaff.gcmaz.com" target="_blank">kaff.com</a>
                <p>92.9 KAFF is Northern Arizona's number one modern country radio station.  My work on this site has been ongoing and I have developed a very customized WordPress theme for the Great Circle Media group of websites of which this station is a part.  Since this is a radio station, BOLD and IN YOUR FACE is the style.  Every spot on the web page has potential to inject banner ads or page take over graphics.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'kaff-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="webport">
            <div class="one-third first">
                <h3>KAFF News</h3>
                <a href="http://gcmaz.com/kaff-news" target="_blank">kaffnews.com</a>
                <p>KAFF News needed an easy to use CMS for the news team to post stories and interact with the audience, so I customized a WordPress theme for them.  High level SEO work has been done to optimize the news feeds and meet the specific requirements that Google sets for news sources.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'news-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="webport">
            <div class="one-third first">
                <h3>And more ...</h3>
                <p>My custom WordPress theme is used on multiple radio station websites.  This theme is highly 'ad-driven' for sales of banner ads and page takeovers.</p>
                <p class="webtech">HTML5, CSS3, LESS, Grunt, JavaScript, jQuery, PHP, MySQL, Git, WordPress, Photoshop, Illustrator, Flash, SEO, Google Analytics</p>
            </div>
            <div class="two-thirds">
                <?php genesis_widget_area( 'more-slider' );?>
            </div>
            <div class="clearfix"></div>
        </div>
        
    </div>
    <?php
}

// genesis child theme
genesis();

