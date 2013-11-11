<?php
/**
 * Helper functions for WordPress Development and Theming.
 *
 * Don't call these on published sites!
 *
 * @package WordPress
 * @subpackage Bullet
 */

/**
 * Krumo: Version 2.0 of print_r(); and var_dump();
 * @link http://krumo.sourceforge.net/
 */
get_template_part( 'includes/classes/krumo/class.krumo' );

/**
 * Include this function before <head> tag to debug custom queries
 * @see http://www.dev4press.com/2012/tutorials/wordpress/practical/debug-wordpress-rewrite-rules-matching/
 */
function bullet_debug_request() {
	global $wp, $template;

	echo '<!-- Request: ';
	echo empty( $wp->request ) ? 'None' : esc_html( $wp->request );
	echo ' -->' . PHP_EOL;
	echo '<!-- Matched Rewrite Rule: ';
	echo empty( $wp->matched_rule ) ? 'None' : esc_html( $wp->matched_rule );
	echo ' -->' . PHP_EOL;
	echo '<!-- Matched Rewrite Query: ';
	echo empty( $wp->matched_query ) ? 'None' : esc_html( $wp->matched_query );
	echo ' -->' . PHP_EOL;
	echo '<!-- Loaded Template: ';
	echo basename( $template );
	echo ' -->' . PHP_EOL;
}
