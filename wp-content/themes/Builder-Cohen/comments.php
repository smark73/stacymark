<?php
	// Important, please do not delete
	if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && ( 'comments.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) ) )
		die( 'Please do not load this page directly. Thanks!' );

	if ( ! builder_show_comments() )
		return;

	$login_url = wp_login_url( apply_filters( 'the_permalink', get_permalink() ) );
	$logout_url = wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) );
	$req = get_option( 'require_name_email' );
	$aria_req = ( $req ) ? ' aria-required="true"' : '';
	$commenter = wp_get_current_commenter();

	if ( post_password_required() )
		return;
?>


<?php if ( have_comments() ) : ?>
	<div id="comments">
		<h3><?php _e( 'Comments', 'it-l10n-Builder-Cohen' ); ?></h3>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'avatar_size' => 72, 'max_depth' => 5 ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 ) : ?>
			<div class="navigation">
				<div class="alignleft"><?php previous_comments_link(); ?></div>
				<div class="alignright"><?php next_comments_link(); ?></div>
			</div>
		<?php endif; ?>
	</div>
	<!-- end #comments -->
<?php endif; ?>

<?php if ( comments_open() ) : ?>

	<?php comment_form(); ?>

	<!--end #respond-->
<?php else : // comments are closed ?>
	<?php echo builder_get_closed_comments_message( __( 'Comments are closed.', 'it-l10n-Builder-Cohen' ) ); ?>
<?php endif; ?>
