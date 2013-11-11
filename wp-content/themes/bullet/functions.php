<?php
/**
 * The main functions file for WordPress Theme Features
 *
 * @link http://codex.wordpress.org/Theme_Features
 * @link http://codex.wordpress.org/Functions_File_Explained
 * @package WordPress
 * @subpackage Bullet
 */

/**
 * Theme setup
 */
function bullet_setup() {

	/* Includes * * * * * * * * * * * * * * * * * * */

	get_template_part( 'includes/functions-helpers' );
	get_template_part( 'includes/functions-register' );
	get_template_part( 'includes/functions-hooks' );
	get_template_part( 'includes/functions-widgets' );

	// Development and debugging functions. Comment out or hook into bullet_dev
	if( apply_filters( 'bull_dev', true ) )
		get_template_part( 'includes/functions-dev' );

	// We get fatal errors about the function already exists. So make a check first
	if( $GLOBALS['pagenow'] != 'plugins.php' )
		get_template_part( 'includes/functions-plugins' );

	// Include on admin and login page
	if( is_admin() || bullet_is_login_page() )
		get_template_part( 'includes/functions-admin' );

	// Loads theme translation files
	load_theme_textdomain( 'bullet', get_template_directory() . '/languages' );
	$locale = get_locale();
	$locale_file = get_template_directory() . '/languages/' . $locale;
	if ( is_readable( $locale_file ) )
		require_once $locale_file;


	/* Theme support * * * * * * * * * * * * * * * * * * */

	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'menus' );
	add_theme_support( 'automatic_feed_links' );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form' ) );
	add_theme_support( 'woocommerce' );


	/* Images * * * * * * * * * * * * * * * * * * */

	set_post_thumbnail_size( 125, 125, true ); // default thumb size
	add_image_size( 'bullet-small', 300, 300, true );
	add_image_size( 'bullet-large', 800, 800, false );


	/* Navigation * * * * * * * * * * * * * * * * * * */

	register_nav_menus(	array(
		'main_nav' => bullet__( 'Main Menu' )
	) );

}
add_action( 'after_setup_theme', 'bullet_setup' );

