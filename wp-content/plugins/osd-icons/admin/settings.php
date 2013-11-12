<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Pco Base</h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'pco-settings' ); ?>
		<?php do_settings_sections( 'pco-settings' ) ?>
		<p></p>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><?php _pco_e( 'Pco Base' ); ?> <?php _e( 'page' ); ?></th>
				<td id="pco-base-page">
					<?php wp_dropdown_pages( array( 'name' => 'pco_base_plugin', 'show_option_none' => __( '&mdash; Select &mdash;' ), 'selected' => get_option( 'pco_base_plugin' ) ) ); ?>
					<p class="description">
						<?php _pco_e( 'Pco Base' ); ?>
					</p>
					<ul>
						<li>
						</li>
					</ul>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>