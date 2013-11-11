<?php
/**
 * Register our theme options
 *
 * @package WordPress
 * @subpackage Bullet
 */
function bullet_options(){
	register_setting( 'bullet-options', 'bullet_option' );
}
add_action( 'admin_init', 'bullet_options' );

/**
 * Add our theme options page
 */
function bullet_add_options_page() {
	add_theme_page( __( 'Theme Options' ), __( 'Theme Options' ), 'edit_theme_options', 'bullet_options', 'bullet_options_page' );
}
add_action( 'admin_menu', 'bullet_add_options_page' );

/**
 * Shows an 'updated' message
 */
function bullet_message_updated ( $text, $request = false ) {
	if( isset( $request ) && ! isset ( $_REQUEST[$request] ) )
		return; ?>
	<div class="updated">
		<p>
			<strong>
				<?php echo $text ?>
			</strong>
		</p>
	</div><?php
}

/**
 * Create the options page
 */
function bullet_options_page() {
	// Check if the user have the required capability
	if ( ! current_user_can( 'edit_theme_options' ) )
		// You sir! Have not the required capability --> Die
		wp_die( 'You do not have sufficient permissions to access this page.' ); ?>

	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php bullet_e( 'Theme options' ); ?></h2>

		<?php bullet_message_updated( bullet__( 'Theme options updated.' ), 'settings-updated' ); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'bullet-options' ) ?>
			<?php do_settings_sections( 'bullet-options' ) ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="bullet_option"><?php bullet_e( 'Bullet option' ); ?></label>
					</th>

					<td>
						<input size="40" type="text" name="bullet_option" id="bullet_option" class="regular-text" value="<?php echo get_option( 'bullet_option' ) ?>" />
					</td>
				</tr>
			</table>

			<p>
				<?php submit_button( __( 'Update' ) ); // since WP 3.1 ?>
			</p>
		</form>
	</div>

	<?php
}

