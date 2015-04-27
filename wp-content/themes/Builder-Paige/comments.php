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
		<h3><?php _e( 'Comments', 'it-l10n-Builder-Paige' ); ?></h3>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'avatar_size' => 72, 'max_depth' => 4 ) ); ?>
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
	<div id="respond">
		<h3><?php comment_form_title( __( 'Leave a Reply', 'it-l10n-Builder-Paige' ), __( 'Leave a Reply to %s', 'it-l10n-Builder-Paige' ) ); ?></h3>

		<div class="cancel-comment-reply">
			<small><?php cancel_comment_reply_link(); ?></small>
		</div>

		<?php if ( get_option( 'comment_registration' ) && ! is_user_logged_in() ) : ?>
			<p class="must-log-in"><?php printf( __( 'You must be <a href="%s">logged in</a> to post a comment.', 'it-l10n-Builder-Paige' ), $login_url ); ?></p>
		<?php else : ?>

			<form action="<?php echo site_url( '/wp-comments-post.php' ); ?>" method="post" id="commentform">
				<?php if ( is_user_logged_in() ) : ?>
					<p class="logged-in-as"><?php printf( __( 'Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'it-l10n-Builder-Paige' ), admin_url( 'profile.php' ), $user_identity, $logout_url ); ?></p>
				<?php else : ?>
					<p class="comment-form-author">
						<input type="text" name="author" id="author" value="<?php echo esc_attr( $commenter['comment_author'] ); ?>" size="22"<?php echo $aria_req; ?> />
						<label for="author"><small><?php _e( 'Name', 'it-l10n-Builder-Paige' ); ?> <?php if ( $req ) _e( "<span class='required'>*</span>", 'it-l10n-Builder-Paige' ); ?></small></label>
					</p>

					<p class="comment-form-email">
						<input type="text" name="email" id="email" value="<?php echo esc_attr(  $commenter['comment_author_email'] ); ?>" size="22"<?php echo $aria_req; ?> />
						<label for="email"><small><?php _e( 'Email', 'it-l10n-Builder-Paige' ); ?> <?php if ( $req ) _e( "<span class='required'>*</span>", 'it-l10n-Builder-Paige' ); ?></small></label>
					</p>

					<p class="comment-form-url">
						<input type="text" name="url" id="url" value="<?php echo esc_attr( $commenter['comment_author_url'] ); ?>" size="22" />
						<label for="url"><small><?php _e( 'Website', 'it-l10n-Builder-Paige' ); ?></small></label>
					</p>
				<?php endif; ?>

				<!--<p><small><strong>XHTML:</strong> You can use these tags: <code><?php echo allowed_tags(); ?></code></small></p>-->
				<p><textarea name="comment" id="comment" cols="45" rows="10"></textarea></p>

				<p class="comment-submit-wrapper clearfix">
					<input name="submit" type="submit" id="submit" value="<?php _e( 'Submit Comment', 'it-l10n-Builder-Paige' ); ?>" />
					<?php comment_id_fields(); ?>
				</p>

				<?php do_action( 'comment_form', $post->ID ); ?>
			</form>
		<?php endif; // If registration required and not logged in ?>
	</div>
	<!--end #respond-->
<?php else : // comments are closed ?>
	<?php echo builder_get_closed_comments_message( __( 'Comments are closed.', 'it-l10n-Builder-Paige' ) ); ?>
<?php endif; ?>
