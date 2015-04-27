<?php
/**
 * @package WordPress
 * @subpackage World
 * @since World 1.0
 */
?>

<?php if (get_option('sg_enable_back')==''): ?>
<?php endif; ?>
<?php if (get_option('sg_enable_back')=='Yes'): ?>
	<div id="toTop">Back to Top</div>
<?php endif; ?>

<div class="clr"></div>
	</div>
		<!-- END body -->
	</div><!-- END main -->

<?php wp_footer(); ?>
</div>

<!-- BEGIN footer -->
<div id="footer">
	<div class="fwrap">
		<?php dynamic_sidebar(2) ?>
		<div class="copyright">
			<p><?php echo get_option('sg_copyright'); ?></p>
		</div>
		<?php if(is_home()) { ?>
		<div class="designed">
			Designed by <a href="http://www.siteground.com/wordpress-hosting/wordpress-themes.htm" title="Wordpress Themes">SiteGround WordPress Themes</a>
		<div>
		<?php } ?>
		</div>
	</div>
</div>
<div class="clr"></div>
</body>
</html>