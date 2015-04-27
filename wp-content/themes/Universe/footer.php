<?php
/**
 * @package WordPress
 * @subpackage Universe
 * @since Universe 1.0
 */
?>
<div class="clr"></div>
	</div>
		<!-- END body -->

			</div><!-- END main_t_bg -->
		</div><!-- END main -->
	</div><!-- END m_bg -->
<div class="b_bg"></div>
	<!-- BEGIN footer -->
	<div id="footer"><a rel="generator" title="Semantic Personal Publishing Platform" href="http://wordpress.org/">Proudly powered by WordPress.</a>
	<?php if(is_home()) { ?>
		Designed by <a href="http://www.siteground.com/wordpress-hosting/wordpress-themes.htm" title="Wordpress Themes">SiteGround Wordpress Themes</a></div>
	<?php } ?>
	<!-- END footer -->
<?php wp_footer(); ?>
</div>
<div class="clr"></div>

<script>
	$(document).ready(function(){
		// iSmallMenu
		jQuery.iSmallMenu("#navi");
		
		// prettyPhoto
		jQuery(".single a[rel^='prettyPhoto']").prettyPhoto({theme:'facebook'});
	});
</script>
</body>
</html>
