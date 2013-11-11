<?php
/**
 * Calling functions inside the theme made by a plugin will cause fatal errors if the plugin is not activated.
 * Make sure to have a backup if the plugin is deactivated instead of breaking the whole site.
 *
 * @package WordPress
 * @subpackage Bullet
 *
 * @link http://codex.wordpress.org/Plugin_API
 */

// Bullet image field
if( !function_exists( 'pco_image_field' ) ) {
	function pco_image_field() {
		_pcoiw_e( "Error: Bullet Image Field plugin not activated." );
	}
}
